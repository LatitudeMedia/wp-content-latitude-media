<?php
namespace Jet_Engine\Listings\MCP;

use Jet_Engine\MCP_Tools\Registry;
use Jet_Engine\Query_Builder\Manager as Query_Manager;

class Tool_Add_Listing {

	/**
	 * Backup of request variables that were overridden.
	 *
	 * @var array
	 */
	protected $request_backup = array();

	public function __construct() {
		Registry::instance()->add_feature( 'add-listing', [
			'type' => 'tool',
			'label' => 'Add Listing Grid Item',
			'description' => 'Create a JetEngine listing item connected to an existing Query Builder query.',
			'input_schema' => [
				'title' => [
					'type' => 'string',
					'description' => 'Listing title shown in the WordPress dashboard. When omitted the query name is used.',
				],
				'query_id' => [
					'type' => 'integer',
					'description' => 'Existing Query Builder ID that will be used as the listing data source.',
				],
				'view_type' => [
					'type' => 'string',
					'description' => 'Optional builder slug (elementor, bricks, twig, blocks). Use "auto" to pick the first available builder integration.',
					'enum' => $this->get_view_type_enum(),
				],
			],
			'execute_callback' => [ $this, 'callback' ],
		] );
	}

	public function callback( $input = [] ) {

		if ( empty( jet_engine()->listings ) || empty( jet_engine()->listings->post_type ) || empty( jet_engine()->listings->post_type->admin_screen ) ) {
			return new \WP_Error( 'component_unavailable', __( 'The Listings component is not available.', 'jet-engine' ) );
		}

		if ( empty( $input['query_id'] ) ) {
			return new \WP_Error( 'invalid_input', __( 'The query_id field is required.', 'jet-engine' ) );
		}

		$query_id = absint( $input['query_id'] );

		if ( ! $query_id ) {
			return new \WP_Error( 'invalid_input', __( 'The query_id must reference an existing Query Builder entry.', 'jet-engine' ) );
		}

		if ( ! class_exists( '\Jet_Engine\Query_Builder\Manager' ) ) {
			return new \WP_Error( 'component_unavailable', __( 'The Query Builder module is not available.', 'jet-engine' ) );
		}

		$query = Query_Manager::instance()->get_query_by_id( $query_id );
		if ( ! $query ) {
			return new \WP_Error( 'not_found', sprintf( __( 'Query with ID %d was not found.', 'jet-engine' ), $query_id ) );
		}

		$query->setup_query();
		$sample_item = $this->get_sample_item( $query );
		$context_type = $this->detect_context_from_item( $sample_item, $query->get_query_type() );

		if ( 'post' === $context_type && function_exists( 'wp_reset_postdata' ) ) {
			wp_reset_postdata();
		}

		$title = ! empty( $input['title'] ) ? sanitize_text_field( $input['title'] ) : $this->get_default_title( $query, $query_id );
		$requested_view = ! empty( $input['view_type'] ) ? sanitize_key( $input['view_type'] ) : 'auto';
		$view_type = $this->determine_view_type( $requested_view );

		if ( is_wp_error( $view_type ) ) {
			return $view_type;
		}

		$request_args = $this->build_request_args( $query_id, $title, $view_type, $query, $context_type );
		$request_args = apply_filters( 'jet-engine/mcp-tools/add-listing/request-args', $request_args, $query, $context_type, $title, $view_type );

		$this->push_request_args( $request_args );
		$template_data = jet_engine()->listings->post_type->admin_screen->create_listing_template( $request_args );
		$this->pop_request_args( $request_args );

		$listing_id = ! empty( $template_data['template_id'] ) ? absint( $template_data['template_id'] ) : 0;

		if ( ! $listing_id ) {
			return new \WP_Error( 'listing_creation_failed', __( 'Unable to create the listing item.', 'jet-engine' ) );
		}

		$content = $this->prepare_listing_content( $query, $context_type, $sample_item );

		if ( ! empty( $content ) ) {
			do_action( 'jet-engine/listing/set-content/' . $view_type, $content, $listing_id );
		}

		return [
			'success' => true,
			'item_id' => $listing_id,
			'listing_url' => ! empty( $template_data['edit_url'] ) ? $template_data['edit_url'] : '',
			'next_tool' => '',
		];
	}

	protected function push_request_args( array $args ) {
		foreach ( $args as $key => $value ) {
			if ( array_key_exists( $key, $_REQUEST ) ) {
				$this->request_backup[ $key ] = $_REQUEST[ $key ];
			} else {
				$this->request_backup[ $key ] = null;
			}

			$_REQUEST[ $key ] = $value;
		}
	}

	protected function pop_request_args( array $args ) {
		foreach ( $args as $key => $value ) {
			if ( array_key_exists( $key, $this->request_backup ) ) {
				if ( null === $this->request_backup[ $key ] ) {
					unset( $_REQUEST[ $key ] );
				} else {
					$_REQUEST[ $key ] = $this->request_backup[ $key ];
				}
			} else {
				unset( $_REQUEST[ $key ] );
			}
		}

		$this->request_backup = array();
	}

	protected function build_request_args( $query_id, $title, $view_type, $query, $context_type ) {
		$post_type = 'post';
		$taxonomy = 'category';
		$cct_type = '';

		switch ( $context_type ) {
			case 'post':
				$post_type = $this->extract_query_value( $query, [ 'post_type' ], 'post' );
				break;

			case 'term':
				$taxonomy = $this->extract_query_value( $query, [ 'taxonomy', 'taxonomies' ], 'category' );
				break;

			case 'comment':
				$post_type = $this->extract_query_value( $query, [ 'post_type' ], 'post' );
				break;

			case 'cct':
				$cct_type = $this->get_query_cct_slug( $query );
				break;
		}

		return [
			'listing_source' => 'query',
			'listing_post_type' => $post_type,
			'listing_tax' => $taxonomy,
			'_query_id' => $query_id,
			'repeater_source' => 'jet_engine',
			'repeater_field' => '',
			'repeater_option' => '',
			'cct_type' => $cct_type,
			'cct_repeater_field' => '',
			'template_name' => $title,
			'listing_view_type' => $view_type,
			'_is_ajax_form' => true,
			'template_entry_type' => 'listing',
		];
	}

	protected function prepare_listing_content( $query, $context_type, $sample_item ) {

		$content         = $this->get_default_content_for_context( $context_type );
		$context_widgets = $this->get_context_dynamic_fields( $query, $context_type, $sample_item );

		if ( ! empty( $context_widgets ) ) {
			$content = $this->merge_content_with_context_fields( $content, $context_widgets );
		}

		return apply_filters(
			'jet-engine/mcp-tools/add-listing/content', $content, $context_type, $query
		);
	}

	protected function normalize_content_items( array $items ) {
		$result = array();

		foreach ( $items as $item ) {
			if ( is_object( $item ) ) {
				$item = (array) $item;
			}

			if ( empty( $item['type'] ) ) {
				continue;
			}

			$type = (string) $item['type'];
			$settings = array();

			if ( isset( $item['settings'] ) ) {
				$settings = is_object( $item['settings'] ) ? (array) $item['settings'] : $item['settings'];
				if ( ! is_array( $settings ) ) {
					$settings = array();
				}
			}

			$prepared_settings = array();

			foreach ( $settings as $key => $value ) {
				$prepared_settings[ (string) $key ] = $value;
			}

			$result[] = [
				'type' => $type,
				'settings' => $settings,
			];
		}

		return $result;
	}

	protected function get_context_dynamic_fields( $query, $context_type, $sample_item = null ) {
		$widgets = array();

		switch ( $context_type ) {
			case 'post':
				if ( empty( jet_engine()->meta_boxes ) || ! method_exists( jet_engine()->meta_boxes, 'get_meta_fields_for_object' ) ) {
					break;
				}

				$post_types = $this->extract_query_values( $query, [ 'post_type' ] );
				if ( empty( $post_types ) ) {
					$post_types = array( $this->extract_query_value( $query, [ 'post_type' ], 'post' ) );
				}

				foreach ( $post_types as $post_type ) {
					if ( ! $post_type || 'any' === $post_type ) {
						continue;
					}

					$fields = jet_engine()->meta_boxes->get_meta_fields_for_object( $post_type );
					$widgets = array_merge( $widgets, $this->map_meta_fields_to_widgets( $fields ) );
				}
				break;

			case 'term':
				if ( empty( jet_engine()->meta_boxes ) || ! method_exists( jet_engine()->meta_boxes, 'get_meta_fields_for_object' ) ) {
					break;
				}

				$taxonomies = $this->extract_query_values( $query, [ 'taxonomy', 'taxonomies' ] );

				foreach ( $taxonomies as $taxonomy ) {
					if ( ! $taxonomy ) {
						continue;
					}

					$fields = jet_engine()->meta_boxes->get_meta_fields_for_object( $taxonomy );
					$widgets = array_merge( $widgets, $this->map_meta_fields_to_widgets( $fields ) );
				}
				break;

			case 'user':
				if ( empty( jet_engine()->meta_boxes ) || ! method_exists( jet_engine()->meta_boxes, 'get_fields_for_context' ) ) {
					break;
				}

				$groups = jet_engine()->meta_boxes->get_fields_for_context( 'user' );
				if ( ! empty( $groups ) && is_array( $groups ) ) {
					foreach ( $groups as $fields ) {
						$widgets = array_merge( $widgets, $this->map_meta_fields_to_widgets( $fields ) );
					}
				}
				break;

			case 'cct':
				$widgets = array_merge( $widgets, $this->get_cct_field_widgets( $query ) );
				break;

			default:

				$skip_cols = [
					'sql_query_item_id',
					'_rest_api_item_id',
					'is_rest_api_endpoint',
				];

				$reserverd_prefixes = [
					'is_rest_api_endpoint' => 'rest_api__',
				];

				// Add all props from the query columns as dynamic fields with 'object' source.
				if ( ! empty( $query->query['default_columns'] ) ) {

					$apply_prefix = false;

					foreach ( $reserverd_prefixes as $apply_if_has_col => $prefix ) {
						if ( in_array(
							$apply_if_has_col, $query->query['default_columns'], true
						) ) {
							$apply_prefix = $prefix;
							break;
						}
					}

					foreach ( $query->query['default_columns'] as $column ) {

						if ( in_array( $column, $skip_cols, true ) ) {
							continue;
						}

						$column = trim( (string) $column );

						if ( $apply_prefix ) {
							$column = $apply_prefix . $column;
						}

						$widgets[] = [
							'type' => 'jet-listing-dynamic-field',
							'settings' => [
								'dynamic_field_source' => 'object',
								'dynamic_field_post_object' => $column,
								'field_fallback' => $column,
							],
						];
					}
				} elseif ( $sample_item ) {
					$sample_array = array();

					if ( is_object( $sample_item ) ) {
						$sample_array = (array) $sample_item;
					} elseif ( is_array( $sample_item ) ) {
						$sample_array = $sample_item;
					}

					$apply_prefix = false;

					foreach ( $reserverd_prefixes as $apply_if_has_col => $prefix ) {
						if ( isset( $sample_array[ $apply_if_has_col ] ) ) {
							$apply_prefix = $prefix;
							break;
						}
					}

					foreach ( $sample_array as $key => $value ) {

						if ( in_array( $key, $skip_cols, true ) ) {
							continue;
						}

						$key = trim( (string) $key );

						if ( $apply_prefix ) {
							$key = $apply_prefix . $key;
						}

						$widgets[] = [
							'type' => 'jet-listing-dynamic-field',
							'settings' => [
								'dynamic_field_source' => 'object',
								'dynamic_field_post_object' => $key,
								'field_fallback' => $key,
							],
						];
					}
				}

				break;
		}

		return $widgets;
	}

	protected function merge_content_with_context_fields( array $content, array $context_widgets ) {
		if ( empty( $context_widgets ) ) {
			return $content;
		}

		$existing = $this->collect_existing_dynamic_keys( $content );

		foreach ( $context_widgets as $widget ) {
			$identifier = $this->get_widget_identifier( $widget );

			if ( $identifier && isset( $existing[ $identifier ] ) ) {
				continue;
			}

			$content[] = $widget;

			if ( $identifier ) {
				$existing[ $identifier ] = true;
			}
		}

		return $content;
	}

	protected function collect_existing_dynamic_keys( array $content ) {
		$identifiers = array();

		foreach ( $content as $widget ) {
			$identifier = $this->get_widget_identifier( $widget );

			if ( $identifier ) {
				$identifiers[ $identifier ] = true;
			}
		}

		return $identifiers;
	}

	protected function get_widget_identifier( $widget ) {
		if ( ! is_array( $widget ) ) {
			return '';
		}

		$type = isset( $widget['type'] ) ? $widget['type'] : '';
		if ( 'jet-listing-dynamic-field' !== $type ) {
			return '';
		}

		$settings = isset( $widget['settings'] ) && is_array( $widget['settings'] ) ? $widget['settings'] : array();

		if ( ! empty( $settings['dynamic_field_post_meta_custom'] ) ) {
			return 'custom:' . $settings['dynamic_field_post_meta_custom'];
		}

		$source = ! empty( $settings['dynamic_field_source'] ) ? $settings['dynamic_field_source'] : '';

		if ( 'meta' === $source && ! empty( $settings['dynamic_field_post_meta'] ) ) {
			return 'meta:' . $settings['dynamic_field_post_meta'];
		}

		if ( false !== strpos( $source, '__' ) ) {
			return 'cct:' . $source;
		}

		if ( 'object' === $source && ! empty( $settings['dynamic_field_post_object'] ) ) {
			return 'object:' . $settings['dynamic_field_post_object'];
		}

		return '';
	}

	protected function map_meta_fields_to_widgets( $fields ) {
		$widgets = array();

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return $widgets;
		}

		foreach ( $fields as $field ) {
			if ( is_object( $field ) ) {
				$field = (array) $field;
			}

			if ( empty( $field['name'] ) ) {
				continue;
			}

			$name = (string) $field['name'];

			if ( isset( $field['type'] ) && in_array( $field['type'], array( 'repeater', 'group', 'html' ), true ) ) {
				continue;
			}

			$label = $this->normalize_meta_label( isset( $field['title'] ) ? $field['title'] : '', $name );

			$widgets[] = [
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'meta',
					'dynamic_field_post_meta' => $name,
					'field_fallback' => $label,
				],
			];
		}

		return $widgets;
	}

	protected function normalize_meta_label( $label, $name ) {
		$label = is_string( $label ) ? trim( $label ) : '';

		if ( $label ) {
			return $label;
		}

		$name = is_string( $name ) ? $name : '';

		if ( ! $name ) {
			return '';
		}

		$name = str_replace( array( '_', '-' ), ' ', $name );

		return ucwords( $name );
	}

	protected function extract_query_values( $query, array $keys ) {
		$values = array();
		$sources = array();

		if ( ! empty( $query->query_type ) && ! empty( $query->query ) && isset( $query->query[ $query->query_type ] ) && is_array( $query->query[ $query->query_type ] ) ) {
			$sources[] = $query->query[ $query->query_type ];
		}

		if ( ! empty( $query->query ) && is_array( $query->query ) ) {
			$sources[] = $query->query;
		}

		if ( ! empty( $query->final_query ) && is_array( $query->final_query ) ) {
			$sources[] = $query->final_query;
		}

		foreach ( $sources as $source ) {
			foreach ( $keys as $key ) {
				if ( empty( $source[ $key ] ) ) {
					continue;
				}

				$value = $source[ $key ];

				if ( is_array( $value ) ) {
					foreach ( $value as $single ) {
						if ( is_string( $single ) && '' !== $single ) {
							$values[] = $single;
						}
					}
				} elseif ( is_string( $value ) && '' !== $value ) {
					$values[] = $value;
				}
			}
		}

		if ( empty( $values ) ) {
			return array();
		}

		$values = array_map( 'trim', $values );
		$values = array_filter( $values, function( $value ) {
			return '' !== $value && 'any' !== $value;
		} );

		return array_values( array_unique( $values ) );
	}

	protected function get_cct_field_widgets( $query ) {
		$widgets = array();

		if ( ! class_exists( '\Jet_Engine\Modules\Custom_Content_Types\Module' ) ) {
			return $widgets;
		}

		$cct_slug = $this->get_query_cct_slug( $query );

		if ( ! $cct_slug ) {
			return $widgets;
		}

		$module = \Jet_Engine\Modules\Custom_Content_Types\Module::instance();

		if ( empty( $module ) || empty( $module->manager ) ) {
			return $widgets;
		}

		$content_type = $module->manager->get_content_types( $cct_slug );

		if ( ! $content_type ) {
			return $widgets;
		}

		$fields = $content_type->get_fields_list( 'all' );

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return $widgets;
		}

		foreach ( $fields as $name => $label ) {
			$name = is_string( $name ) ? trim( $name ) : '';

			if ( ! $name ) {
				continue;
			}

			$label = $this->normalize_meta_label( $label, $name );

			$widgets[] = [
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => $cct_slug . '__' . $name,
					'field_fallback' => $label,
				],
			];
		}

		return $widgets;
	}

	protected function get_query_cct_slug( $query ) {
		if ( method_exists( $query, 'get_query_meta' ) ) {
			$meta = $query->get_query_meta();

			if ( ! empty( $meta['content_type'] ) ) {
				return sanitize_key( $meta['content_type'] );
			}
		}

		$slug = $this->extract_query_value( $query, [ 'content_type' ], '' );

		return $slug ? sanitize_key( $slug ) : '';
	}

	protected function get_default_content_for_context( $context_type ) {
		switch ( $context_type ) {
			case 'post':
				return $this->get_post_default_elements();

			case 'term':
				return $this->get_term_default_elements();

			case 'user':
				return $this->get_user_default_elements();

			case 'comment':
				return $this->get_comment_default_elements();

			default:
				return [];
		}
	}

	protected function get_post_default_elements() {
		return [
			[
				'type' => 'jet-listing-dynamic-image',
				'settings' => [
					'dynamic_image_source' => 'post_thumbnail',
					'dynamic_image_size' => 'large',
				],
			],
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'post_title',
					'field_fallback' => __( 'Post Title', 'jet-engine' ),
				],
			],
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'post_excerpt',
					'dynamic_field_wp_excerpt' => true,
					'dynamic_excerpt_more' => '...',
					'dynamic_excerpt_length' => 20,
					'field_fallback' => __( 'Excerpt', 'jet-engine' ),
				],
			],
			[
				'type' => 'jet-listing-dynamic-link',
				'settings' => [
					'dynamic_link_source' => '_permalink',
					'link_label' => __( 'Read More', 'jet-engine' ),
				],
			],
		];
	}

	protected function get_term_default_elements() {
		return [
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'name',
					'field_fallback' => __( 'Term Name', 'jet-engine' ),
				],
			],
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'description',
					'field_fallback' => __( 'Term Description', 'jet-engine' ),
				],
			],
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'count',
					'field_fallback' => __( 'Items Count', 'jet-engine' ),
					'dynamic_field_format' => __( 'Items: %s', 'jet-engine' ),
				],
			],
		];
	}

	protected function get_user_default_elements() {
		return [
			[
				'type' => 'jet-listing-dynamic-image',
				'settings' => [
					'dynamic_image_source' => 'user_avatar',
					'dynamic_avatar_size' => [ 'size' => 96 ],
				],
			],
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'display_name',
					'field_fallback' => __( 'Display Name', 'jet-engine' ),
				],
			],
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'user_email',
					'field_fallback' => __( 'Email', 'jet-engine' ),
				],
			],
		];
	}

	protected function get_comment_default_elements() {
		$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

		return [
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'comment_author',
					'field_fallback' => __( 'Comment Author', 'jet-engine' ),
				],
			],
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'comment_date',
					'dynamic_field_filter' => true,
					'filter_callback' => 'jet_engine_date',
					'date_format' => $date_format,
					'field_fallback' => __( 'Comment Date', 'jet-engine' ),
				],
			],
			[
				'type' => 'jet-listing-dynamic-field',
				'settings' => [
					'dynamic_field_source' => 'object',
					'dynamic_field_post_object' => 'comment_content',
					'field_fallback' => __( 'Comment', 'jet-engine' ),
				],
			],
		];
	}

	protected function extract_query_value( $query, array $keys, $fallback ) {
		$sources = array();

		if ( ! empty( $query->query_type ) && ! empty( $query->query ) && isset( $query->query[ $query->query_type ] ) && is_array( $query->query[ $query->query_type ] ) ) {
			$sources[] = $query->query[ $query->query_type ];
		}

		if ( ! empty( $query->query ) && is_array( $query->query ) ) {
			$sources[] = $query->query;
		}

		if ( ! empty( $query->final_query ) && is_array( $query->final_query ) ) {
			$sources[] = $query->final_query;
		}

		foreach ( $sources as $source ) {
			foreach ( $keys as $key ) {
				if ( empty( $source[ $key ] ) ) {
					continue;
				}

				$value = $source[ $key ];

				if ( is_array( $value ) ) {
					$value = reset( $value );
				}

				if ( is_string( $value ) && '' !== $value ) {
					return $value;
				}
			}
		}

		return $fallback;
	}

	protected function get_sample_item( $query ) {
		$items = $query->get_items();

		if ( empty( $items ) ) {
			return null;
		}

		$items = array_values( $items );

		return isset( $items[0] ) ? $items[0] : null;
	}

	protected function detect_context_from_item( $item, $fallback_type ) {

		if ( is_object( $item ) && isset( $item->cct_slug ) ) {
			return 'cct';
		}

		if ( $item instanceof \WP_Post ) {
			return 'post';
		}

		if ( $item instanceof \WP_Term ) {
			return 'term';
		}

		if ( $item instanceof \WP_User ) {
			return 'user';
		}

		if ( $item instanceof \WP_Comment ) {
			return 'comment';
		}

		if ( is_array( $item ) ) {
			if ( isset( $item['cct_slug'] ) ) {
				return 'cct';
			}

			if ( isset( $item['post_type'] ) && isset( $item['ID'] ) ) {
				return 'post';
			}

			if ( isset( $item['taxonomy'] ) && isset( $item['name'] ) ) {
				return 'term';
			}

			if ( isset( $item['user_email'] ) && isset( $item['ID'] ) ) {
				return 'user';
			}

			if ( isset( $item['comment_content'] ) && isset( $item['comment_author'] ) ) {
				return 'comment';
			}
		}

		if ( is_object( $item ) ) {
			$properties = array_map( 'strtolower', array_keys( get_object_vars( $item ) ) );

			if ( in_array( 'post_type', $properties ) && in_array( 'id', $properties ) ) {
				return 'post';
			}

			if ( in_array( 'taxonomy', $properties ) && in_array( 'name', $properties ) ) {
				return 'term';
			}

			if ( in_array( 'user_email', $properties ) && in_array( 'id', $properties ) ) {
				return 'user';
			}

			if ( in_array( 'comment_content', $properties ) && in_array( 'comment_author', $properties ) ) {
				return 'comment';
			}
		}

		switch ( $fallback_type ) {
			case 'posts':
				return 'post';

			case 'terms':
				return 'term';

			case 'users':
				return 'user';

			case 'comments':
				return 'comment';

			case 'cct':
			case 'custom_content_type':
			case 'custom-content-type':
				return 'cct';
		}

		return 'generic';
	}

	protected function get_default_title( $query, $query_id ) {
		$name = '';

		if ( ! empty( $query->name ) ) {
			$name = $query->name;
		} elseif ( ! empty( $query->query ) && is_array( $query->query ) && ! empty( $query->query['name'] ) ) {
			$name = $query->query['name'];
		}

		$name = is_string( $name ) ? trim( $name ) : '';

		if ( $name ) {
			return sprintf( __( '%s Listing', 'jet-engine' ), $name );
		}

		return sprintf( __( 'Listing for Query %d', 'jet-engine' ), $query_id );
	}

	protected function determine_view_type( $requested ) {
		$requested = $requested ? strtolower( (string) $requested ) : 'auto';
		$available = $this->get_available_views( true );

		if ( 'auto' === $requested || '' === $requested ) {
			if ( ! empty( $available ) ) {
				return $available[0];
			}

			return new \WP_Error( 'no_view_available', __( 'No listing view integrations are currently available.', 'jet-engine' ) );
		}

		if ( in_array( $requested, $available, true ) ) {
			return $requested;
		}

		$known = $this->get_available_views( false );

		if ( in_array( $requested, $known, true ) ) {
			if ( ! empty( $available ) ) {
				return $available[0];
			}

			return new \WP_Error( 'no_view_available', __( 'No listing view integrations are currently available.', 'jet-engine' ) );
		}

		return new \WP_Error( 'invalid_view_type', __( 'The requested listing view type is not recognized.', 'jet-engine' ) );
	}

	protected function get_view_type_enum() {
		return [ 'auto', 'elementor', 'bricks', 'twig', 'blocks' ];
	}

	protected function get_available_views( $only_enabled = true ) {
		if ( ! $only_enabled ) {
			return [ 'elementor', 'bricks', 'twig', 'blocks' ];
		}

		$views = array();

		if ( jet_engine()->has_elementor() ) {
			$views[] = 'elementor';
		}

		if ( $this->has_bricks() ) {
			$views[] = 'bricks';
		}

		if ( $this->has_timber() ) {
			$views[] = 'twig';
		}

		if ( $this->has_blocks() ) {
			$views[] = 'blocks';
		}

		return $views;
	}

	protected function has_timber() {
		if ( ! class_exists( '\Jet_Engine\Timber_Views\Integration' ) ) {
			return false;
		}

		$integration = new \Jet_Engine\Timber_Views\Integration();

		return ( $integration->is_enabled() && $integration->has_timber() );
	}

	protected function has_bricks() {
		return isset( jet_engine()->bricks_views ) && method_exists( jet_engine()->bricks_views, 'has_bricks' ) && jet_engine()->bricks_views->has_bricks();
	}

	protected function has_blocks() {
		return class_exists( '\Jet_Engine\Modules\Performance\Module' ) && \Jet_Engine\Modules\Performance\Module::instance()->is_tweak_active( 'enable_blocks_views' );
	}
}
