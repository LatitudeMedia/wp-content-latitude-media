<?php
namespace Jet_Engine\Taxonomies\MCP;

use Jet_Engine\MCP_Tools\Registry;

class Tool_Add_Taxonomy {
	public function __construct() {
		Registry::instance()->add_feature( 'add-taxonomy', [
			'type' => 'tool',
			'label' => 'Add Taxonomy',
			'description' => 'Register a new taxonomy in JetEngine. Provide the taxonomy name inside general_settings.name and assign it to one or more post types via general_settings.object_type.',
			'input_schema' => [
				'general_settings' => [
					'type' => 'object',
					'description' => 'General settings such as the taxonomy label, slug, and assigned post types.',
					'properties' => [
						'name' => [
							'type' => 'string',
							'description' => 'Plural label displayed in the WordPress admin.',
						],
						'slug' => [
							'type' => 'string',
							'description' => 'Taxonomy slug (max 32 lowercase characters). Generated from the name when omitted.',
						],
						'object_type' => [
							'type' => 'array',
							'description' => 'List of post type slugs to attach this taxonomy to. Accepts a single string or array.',
							'items' => [
								'type' => 'string',
							],
						],
						'show_edit_link' => [
							'type' => 'boolean',
							'description' => 'Display the “Edit Taxonomy” toolbar link.',
						],
						'hide_field_names' => [
							'type' => 'boolean',
							'description' => 'Hide field names inside the edit form.',
						],
						'delete_metadata' => [
							'type' => 'boolean',
							'description' => 'Remove stored term metadata when the taxonomy is deleted.',
						],
					],
					'additionalProperties' => true,
				],
				'labels' => [
					'type' => 'object',
					'description' => 'Optional taxonomy labels that override the WordPress defaults.',
					'properties' => [
						'singular_name' => [ 'type' => 'string' ],
						'menu_name' => [ 'type' => 'string' ],
						'all_items' => [ 'type' => 'string' ],
						'edit_item' => [ 'type' => 'string' ],
						'view_item' => [ 'type' => 'string' ],
						'update_item' => [ 'type' => 'string' ],
						'add_new_item' => [ 'type' => 'string' ],
						'new_item_name' => [ 'type' => 'string' ],
						'parent_item' => [ 'type' => 'string' ],
						'parent_item_colon' => [ 'type' => 'string' ],
						'search_items' => [ 'type' => 'string' ],
						'popular_items' => [ 'type' => 'string' ],
						'separate_items_with_commas' => [ 'type' => 'string' ],
						'add_or_remove_items' => [ 'type' => 'string' ],
						'choose_from_most_used' => [ 'type' => 'string' ],
						'not_found' => [ 'type' => 'string' ],
						'back_to_items' => [ 'type' => 'string' ],
					],
					'additionalProperties' => true,
				],
				'advanced_settings' => [
					'type' => 'object',
					'description' => 'Advanced visibility, rewrite, and capability settings.',
					'properties' => [
						'public' => [ 'type' => 'boolean' ],
						'publicly_queryable' => [ 'type' => 'boolean' ],
						'show_ui' => [ 'type' => 'boolean' ],
						'show_in_menu' => [ 'type' => 'boolean' ],
						'show_in_nav_menus' => [ 'type' => 'boolean' ],
						'show_in_rest' => [ 'type' => 'boolean' ],
						'query_var' => [ 'type' => [ 'boolean', 'string' ] ],
						'rewrite' => [ 'type' => [ 'boolean', 'object' ] ],
						'with_front' => [ 'type' => 'boolean' ],
						'capability_type' => [ 'type' => 'string' ],
						'hierarchical' => [ 'type' => 'boolean' ],
						'rewrite_slug' => [ 'type' => 'string' ],
						'rewrite_hierarchical' => [ 'type' => 'boolean' ],
						'description' => [ 'type' => 'string' ],
					],
					'additionalProperties' => true,
				],
				'meta_fields' => [
					'type' => 'array',
					'description' => 'Meta field definitions saved for taxonomy terms. Provide JetEngine field objects or simplified descriptors with name, type, label, options, etc.',
					'items' => [
						'type' => 'object',
						'properties' => [
							'object_type' => [ 'type' => 'string' ],
							'id' => [ 'type' => 'integer' ],
							'name' => [ 'type' => 'string' ],
							'field_name' => [ 'type' => 'string' ],
							'title' => [ 'type' => 'string' ],
							'label' => [ 'type' => 'string' ],
							'type' => [ 'type' => 'string' ],
							'field_type' => [ 'type' => 'string' ],
							'description' => [ 'type' => 'string' ],
							'placeholder' => [ 'type' => 'string' ],
							'default' => [ 'type' => 'string' ],
							'is_required' => [ 'type' => 'boolean' ],
							'value_format' => [ 'type' => 'string' ],
							'options' => [
								'type' => 'array',
								'items' => [ 'type' => [ 'string', 'object' ] ],
							],
							'choices' => [
								'type' => 'array',
								'items' => [ 'type' => 'string' ],
							],
							'fields' => [
								'type' => 'array',
								'items' => [ 'type' => 'object' ],
							],
						],
						'additionalProperties' => true,
					],
				],
			],
			'output_schema' => [
				'success' => [
					'type' => 'boolean',
					'description' => 'Indicates whether the taxonomy was created successfully.',
				],
				'item_id' => [
					'type' => 'integer',
					'description' => 'Internal database ID of the taxonomy.',
				],
				'taxonomy_settings_url' => [
					'type' => 'string',
					'description' => 'Link to edit the taxonomy inside the WordPress dashboard.',
				],
				'taxonomy_terms_list_url' => [
					'type' => 'string',
					'description' => 'Link to manage the taxonomy terms in WordPress.',
				],
				'notices' => [
					'type' => 'array',
					'description' => 'Array of JetEngine notices triggered during creation.',
				],
				'next_tool' => [
					'type' => 'string',
					'description' => 'Suggested MCP tool to run after creating the taxonomy.',
				],
			],
			'execute_callback' => [ $this, 'callback' ],
		] );
	}

	public function callback( $input = [] ) {
		if ( empty( jet_engine()->taxonomies ) || empty( jet_engine()->taxonomies->data ) ) {
			return new \WP_Error( 'component_unavailable', 'The Taxonomies component is not available.' );
		}

		$general = $this->normalize_general_settings( $input );
		$name = $general['name'];
		$slug = $general['slug'];
		$object_type = $general['object_type'];

		if ( ! $name ) {
			return new \WP_Error( 'invalid_input', 'general_settings.name is required.' );
		}

		if ( empty( $object_type ) ) {
			return new \WP_Error( 'invalid_input', 'general_settings.object_type must include at least one post type slug.' );
		}

		if ( ! $slug ) {
			return new \WP_Error( 'invalid_input', 'Unable to create a slug for the taxonomy. Please provide general_settings.slug.' );
		}

		$labels = $this->normalize_labels( $input, $name );
		$advanced = $this->normalize_advanced_settings( $input, $slug );
		$meta_fields = $this->prepare_meta_fields( isset( $input['meta_fields'] ) ? $input['meta_fields'] : [] );

		jet_engine()->taxonomies->data->set_request( [
			'name' => $name,
			'slug' => $slug,
			'object_type' => $object_type,
			'show_edit_link' => $general['show_edit_link'],
			'hide_field_names' => $general['hide_field_names'],
			'delete_metadata' => $general['delete_metadata'],
			'singular_name' => isset( $labels['singular_name'] ) ? $labels['singular_name'] : '',
			'menu_name' => isset( $labels['menu_name'] ) ? $labels['menu_name'] : '',
			'all_items' => isset( $labels['all_items'] ) ? $labels['all_items'] : '',
			'edit_item' => isset( $labels['edit_item'] ) ? $labels['edit_item'] : '',
			'view_item' => isset( $labels['view_item'] ) ? $labels['view_item'] : '',
			'update_item' => isset( $labels['update_item'] ) ? $labels['update_item'] : '',
			'add_new_item' => isset( $labels['add_new_item'] ) ? $labels['add_new_item'] : '',
			'new_item_name' => isset( $labels['new_item_name'] ) ? $labels['new_item_name'] : '',
			'parent_item' => isset( $labels['parent_item'] ) ? $labels['parent_item'] : '',
			'parent_item_colon' => isset( $labels['parent_item_colon'] ) ? $labels['parent_item_colon'] : '',
			'search_items' => isset( $labels['search_items'] ) ? $labels['search_items'] : '',
			'popular_items' => isset( $labels['popular_items'] ) ? $labels['popular_items'] : '',
			'separate_items_with_commas' => isset( $labels['separate_items_with_commas'] ) ? $labels['separate_items_with_commas'] : '',
			'add_or_remove_items' => isset( $labels['add_or_remove_items'] ) ? $labels['add_or_remove_items'] : '',
			'choose_from_most_used' => isset( $labels['choose_from_most_used'] ) ? $labels['choose_from_most_used'] : '',
			'not_found' => isset( $labels['not_found'] ) ? $labels['not_found'] : '',
			'back_to_items' => isset( $labels['back_to_items'] ) ? $labels['back_to_items'] : '',
			'public' => $advanced['public'],
			'publicly_queryable' => $advanced['publicly_queryable'],
			'show_ui' => $advanced['show_ui'],
			'show_in_menu' => $advanced['show_in_menu'],
			'show_in_nav_menus' => $advanced['show_in_nav_menus'],
			'show_in_rest' => $advanced['show_in_rest'],
			'query_var' => $advanced['query_var'],
			'rewrite' => $advanced['rewrite'],
			'with_front' => $advanced['with_front'],
			'capability_type' => $advanced['capability_type'],
			'hierarchical' => $advanced['hierarchical'],
			'rewrite_slug' => $advanced['rewrite_slug'],
			'rewrite_hierarchical' => $advanced['rewrite_hierarchical'],
			'description' => $advanced['description'],
			'meta_fields' => $meta_fields,
		] );

		try {
			$item_id = jet_engine()->taxonomies->data->create_item( false );
		} catch ( \Exception $exception ) {
			return new \WP_Error( 'taxonomy_create_failed', $exception->getMessage() );
		}

		$notices = method_exists( jet_engine()->taxonomies, 'get_notices' ) ? jet_engine()->taxonomies->get_notices() : [];
		$settings_url = '';
		$terms_url = '';

		if ( $item_id ) {
			$settings_url = admin_url( 'admin.php?page=jet-engine-cpt-tax&cpt_tax_action=edit&id=' . $item_id );
			$terms_url = admin_url( 'edit-tags.php?taxonomy=' . $slug );
			if ( ! empty( $object_type ) ) {
				$terms_url = add_query_arg( 'post_type', $object_type[0], $terms_url );
			}
		}

		return [
			'success' => ! empty( $item_id ),
			'item_id' => $item_id,
			'taxonomy_settings_url' => $settings_url,
			'taxonomy_terms_list_url' => $terms_url,
			'notices' => $notices,
			'next_tool' => 'tool-crocoblock/add-query',
		];
	}

	protected function normalize_general_settings( $input ) {
		$general = $this->get_array( $input, 'general_settings' );

		$map = [ 'name', 'slug', 'object_type', 'post_type', 'post_types', 'show_edit_link', 'hide_field_names', 'delete_metadata' ];

		foreach ( $map as $key ) {
			if ( isset( $input[ $key ] ) && ! isset( $general[ $key ] ) ) {
				$general[ $key ] = $input[ $key ];
			}
		}

		$general = array_merge( [
			'name' => '',
			'slug' => '',
			'object_type' => [],
			'show_edit_link' => false,
			'hide_field_names' => false,
			'delete_metadata' => false,
		], $general );

		$general['name'] = $general['name'] ? sanitize_text_field( $general['name'] ) : '';

		if ( $general['slug'] ) {
			$general['slug'] = sanitize_title( $general['slug'] );
		} elseif ( $general['name'] ) {
			$general['slug'] = sanitize_title( $general['name'] );
		} else {
			$general['slug'] = '';
		}

		$general['object_type'] = $this->prepare_object_type_values( $general, $input );
		unset( $general['post_type'], $general['post_types'] );

		$general['show_edit_link'] = $this->sanitize_bool( $general['show_edit_link'] );
		$general['hide_field_names'] = $this->sanitize_bool( $general['hide_field_names'] );
		$general['delete_metadata'] = $this->sanitize_bool( $general['delete_metadata'] );

		return $general;
	}

	protected function prepare_object_type_values( $general, $input ) {
		$candidates = [];

		if ( isset( $general['object_type'] ) ) {
			$candidates[] = $general['object_type'];
		}
		if ( isset( $general['post_type'] ) ) {
			$candidates[] = $general['post_type'];
		}
		if ( isset( $general['post_types'] ) ) {
			$candidates[] = $general['post_types'];
		}
		foreach ( [ 'object_type', 'post_type', 'post_types' ] as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$candidates[] = $input[ $key ];
			}
		}

		$values = [];

		foreach ( $candidates as $candidate ) {
			if ( is_string( $candidate ) ) {
				$parts = array_map( 'trim', explode( ',', $candidate ) );
			} elseif ( is_array( $candidate ) ) {
				$parts = [];
				foreach ( $candidate as $item ) {
					if ( is_string( $item ) && false !== strpos( $item, ',' ) ) {
						$parts = array_merge( $parts, array_map( 'trim', explode( ',', $item ) ) );
					} else {
						$parts[] = $item;
					}
				}
			} else {
				$parts = [];
			}

			foreach ( $parts as $part ) {
				if ( is_string( $part ) ) {
					$values[] = sanitize_key( $part );
				}
			}
		}

		$values = array_values( array_unique( array_filter( $values ) ) );

		return $values;
	}

	protected function normalize_labels( $input, $name ) {
		$labels = $this->get_array( $input, 'labels' );
		$keys = [
			'singular_name',
			'menu_name',
			'all_items',
			'edit_item',
			'view_item',
			'update_item',
			'add_new_item',
			'new_item_name',
			'parent_item',
			'parent_item_colon',
			'search_items',
			'popular_items',
			'separate_items_with_commas',
			'add_or_remove_items',
			'choose_from_most_used',
			'not_found',
			'back_to_items',
		];

		foreach ( $keys as $key ) {
			if ( isset( $input[ $key ] ) && ! isset( $labels[ $key ] ) ) {
				$labels[ $key ] = $input[ $key ];
			}
			if ( isset( $labels[ $key ] ) && '' !== $labels[ $key ] ) {
				$labels[ $key ] = sanitize_text_field( $labels[ $key ] );
			}
		}

		if ( $name ) {
			if ( empty( $labels['singular_name'] ) ) {
				$labels['singular_name'] = $this->humanize( $name );
			}
			if ( empty( $labels['menu_name'] ) ) {
				$labels['menu_name'] = $name;
			}
			if ( empty( $labels['all_items'] ) ) {
				$labels['all_items'] = $name;
			}
		}

		return $labels;
	}

	protected function normalize_advanced_settings( $input, $slug ) {
		$advanced = $this->get_array( $input, 'advanced_settings' );

		$defaults = [
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'show_in_rest' => true,
			'query_var' => $slug,
			'rewrite' => true,
			'with_front' => true,
			'capability_type' => '',
			'hierarchical' => false,
			'rewrite_slug' => $slug,
			'rewrite_hierarchical' => false,
			'description' => '',
		];

		foreach ( $defaults as $key => $value ) {
			if ( isset( $input[ $key ] ) && ! isset( $advanced[ $key ] ) ) {
				$advanced[ $key ] = $input[ $key ];
			}
		}

		$advanced = array_merge( $defaults, $advanced );

		$bools = [ 'public', 'publicly_queryable', 'show_ui', 'show_in_menu', 'show_in_nav_menus', 'show_in_rest', 'rewrite', 'with_front', 'hierarchical', 'rewrite_hierarchical' ];

		foreach ( $bools as $key ) {
			$advanced[ $key ] = $this->sanitize_bool( $advanced[ $key ], $defaults[ $key ] );
		}

		if ( is_array( $advanced['rewrite'] ) ) {
			if ( isset( $advanced['rewrite']['slug'] ) && empty( $advanced['rewrite_slug'] ) ) {
				$advanced['rewrite_slug'] = $advanced['rewrite']['slug'];
			}
			if ( isset( $advanced['rewrite']['with_front'] ) ) {
				$advanced['with_front'] = $this->sanitize_bool( $advanced['rewrite']['with_front'], $advanced['with_front'] );
			}
			if ( isset( $advanced['rewrite']['hierarchical'] ) ) {
				$advanced['rewrite_hierarchical'] = $this->sanitize_bool( $advanced['rewrite']['hierarchical'], $advanced['rewrite_hierarchical'] );
			}
			$advanced['rewrite'] = true;
		}

		$advanced['rewrite_slug'] = $advanced['rewrite_slug'] ? sanitize_title( $advanced['rewrite_slug'] ) : $slug;
		$advanced['capability_type'] = $advanced['capability_type'] ? sanitize_key( $advanced['capability_type'] ) : '';
		$advanced['description'] = $advanced['description'] ? sanitize_textarea_field( $advanced['description'] ) : '';
		$advanced['query_var'] = $this->normalize_query_var( $advanced['query_var'], $slug );

		return $advanced;
	}

	protected function normalize_query_var( $value, $slug ) {
		if ( is_bool( $value ) ) {
			return $value ? $slug : false;
		}

		if ( is_int( $value ) ) {
			$value = (string) $value;
		}

		if ( is_string( $value ) ) {
			$value = trim( $value );
			if ( '' === $value ) {
				return $slug;
			}
			if ( in_array( strtolower( $value ), [ 'true', 'yes', 'on' ], true ) ) {
				return $slug;
			}
			if ( in_array( strtolower( $value ), [ 'false', 'no', 'off' ], true ) ) {
				return false;
			}
			return sanitize_title( $value );
		}

		if ( null === $value ) {
			return $slug;
		}

		return $slug;
	}

	protected function prepare_meta_fields( $fields ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return [];
		}

		$prepared = [];

		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			if ( isset( $field['object_type'] ) && isset( $field['name'] ) ) {
				$prepared[] = $field;
				continue;
			}

			$name = $this->sanitize_field_name( isset( $field['name'] ) ? $field['name'] : ( isset( $field['field_name'] ) ? $field['field_name'] : '' ) );

			if ( ! $name ) {
				continue;
			}

			$type = isset( $field['type'] ) ? $field['type'] : ( isset( $field['field_type'] ) ? $field['field_type'] : 'text' );
			$type = sanitize_key( $type );

			if ( 'relation' === $type ) {
				continue;
			}

			$title = isset( $field['title'] ) ? sanitize_text_field( $field['title'] ) : ( isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : $this->humanize( $name ) );

			$item = [
				'title' => $title,
				'name' => $name,
				'object_type' => 'field',
				'width' => '100%',
				'type' => $type,
				'id' => rand( 10000, 99999 ),
				'isNested' => false,
				'is_nested' => false,
				'options' => [],
				'quick_editable' => true,
				'is_required' => $this->sanitize_bool( isset( $field['is_required'] ) ? $field['is_required'] : false ),
				'default' => isset( $field['default'] ) ? $field['default'] : '',
				'description' => isset( $field['description'] ) ? sanitize_text_field( $field['description'] ) : '',
				'placeholder' => isset( $field['placeholder'] ) ? sanitize_text_field( $field['placeholder'] ) : '',
				'args' => [],
				'conditions' => [],
			];

			if ( 'media' === $type ) {
				$item['value_format'] = isset( $field['value_format'] ) ? sanitize_text_field( $field['value_format'] ) : 'id';
			}

			if ( in_array( $type, [ 'date', 'datetime', 'datetime-local', 'time' ], true ) ) {
				$item['input_type'] = $type;
				$item['autocomplete'] = 'off';
				$item['is_timestamp'] = true;
			}

			if ( 'repeater' === $type && ! empty( $field['fields'] ) && is_array( $field['fields'] ) ) {
				$item['repeater-fields'] = $this->prepare_meta_fields( $field['fields'] );
			} else {
				$item['repeater-fields'] = [];
			}

			if ( in_array( $type, [ 'select', 'checkbox', 'radio' ], true ) ) {
				$options = isset( $field['options'] ) ? $field['options'] : ( isset( $field['choices'] ) ? $field['choices'] : [] );
				if ( ! empty( $options ) && is_array( $options ) ) {
					$item['options_source'] = 'manual_bulk';
					$item['bulk_options'] = $this->prepare_bulk_options( $options );
					if ( 'checkbox' === $type ) {
						$item['is_array'] = true;
					}
				}
			}

			$prepared[] = $item;
		}

		return $prepared;
	}

	protected function prepare_bulk_options( $options ) {
		$prepared = [];

		foreach ( $options as $value => $label ) {
			if ( is_array( $label ) ) {
				$option_value = isset( $label['value'] ) ? $label['value'] : ( isset( $label['label'] ) ? $label['label'] : '' );
				$option_label = isset( $label['label'] ) ? $label['label'] : $option_value;
			} else {
				$option_value = is_int( $value ) ? $label : $value;
				$option_label = $label;
			}

			$option_value = trim( (string) $option_value );
			$option_label = trim( (string) $option_label );

			if ( '' === $option_value ) {
				$option_value = $option_label;
			}

			if ( '' === $option_label ) {
				$option_label = $option_value;
			}

			$prepared[] = $option_value . '::' . $option_label;
		}

		return implode( "\n", $prepared );
	}

	protected function sanitize_field_name( $name ) {
		$name = trim( (string) $name );

		if ( '' === $name ) {
			return '';
		}

		$name = str_replace( [ ' ', '-' ], '_', $name );

		return sanitize_key( $name );
	}

	protected function sanitize_bool( $value, $default = false ) {
		if ( null === $value ) {
			return $default;
		}

		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( '' === $value ) {
			return $default;
		}

		$filtered = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

		if ( null === $filtered ) {
			return $default;
		}

		return $filtered;
	}

	protected function get_array( $source, $key ) {
		if ( isset( $source[ $key ] ) && is_array( $source[ $key ] ) ) {
			return $source[ $key ];
		}

		return [];
	}

	protected function humanize( $string ) {
		$string = str_replace( [ '-', '_' ], ' ', $string );
		$string = trim( $string );

		return $string ? ucwords( $string ) : '';
	}
}
