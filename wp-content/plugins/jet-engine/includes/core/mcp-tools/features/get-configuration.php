<?php
namespace Jet_Engine\MCP_Tools;

class Feature_Get_Configuration {

	public function __construct() {
		Registry::instance()->add_feature( 'get-configuration', array(
			'type' => 'resource',
			'label' => 'Get JetEngine Configuration',
			'description' => 'Retrieve an overview of JetEngine post types, taxonomies, meta boxes, options pages, queries, relations, custom content types, REST API endpoints, and glossaries in JSON format.',
			'input_schema' => array(
				'parts' => array(
					'type' => 'object',
					'description' => 'Specific parts of the configuration to retrieve in the format config: true/false. If empty or not provided, empty array will be returned.',
					'properties' => array(
						'post_types' => array(
							'type' => 'boolean',
							'description' => 'Include registered JetEngine post types.',
						),
						'taxonomies' => array(
							'type' => 'boolean',
							'description' => 'Include registered JetEngine taxonomies.',
						),
						'meta_boxes' => array(
							'type' => 'boolean',
							'description' => 'Include meta boxes created with JetEngine.',
						),
						'options_pages' => array(
							'type' => 'boolean',
							'description' => 'Include JetEngine options pages.',
						),
						'queries' => array(
							'type' => 'boolean',
							'description' => 'Include Query Builder queries.',
						),
						'relations' => array(
							'type' => 'boolean',
							'description' => 'Include registered JetEngine relations.',
						),
						'custom_content_types' => array(
							'type' => 'boolean',
							'description' => 'Include Custom Content Types registered in JetEngine.',
						),
						'rest_api_endpoints' => array(
							'type' => 'boolean',
							'description' => 'Include REST API endpoints configured via the Rest API Listings module.',
						),
						'glossaries' => array(
							'type' => 'boolean',
							'description' => 'Include JetEngine glossaries.',
						),
					),
				),
			),
			'output_schema' => array(
				'success' => array(
					'type' => 'boolean',
					'description' => 'Indicates whether the configuration data was collected successfully.',
				),
				'post_types' => array(
					'type' => 'array',
					'description' => 'Registered JetEngine post types.',
					'items' => array( 'type' => 'object' ),
				),
				'taxonomies' => array(
					'type' => 'array',
					'description' => 'Registered JetEngine taxonomies.',
					'items' => array( 'type' => 'object' ),
				),
				'meta_boxes' => array(
					'type' => 'array',
					'description' => 'Meta boxes created with JetEngine.',
					'items' => array( 'type' => 'object' ),
				),
				'options_pages' => array(
					'type' => 'array',
					'description' => 'JetEngine options pages.',
					'items' => array( 'type' => 'object' ),
				),
				'queries' => array(
					'type' => 'array',
					'description' => 'Query Builder queries.',
					'items' => array( 'type' => 'object' ),
				),
				'relations' => array(
					'type' => 'array',
					'description' => 'Registered JetEngine relations.',
					'items' => array( 'type' => 'object' ),
				),
				'custom_content_types' => array(
					'type' => 'array',
					'description' => 'Custom Content Types registered in JetEngine.',
					'items' => array( 'type' => 'object' ),
				),
				'rest_api_endpoints' => array(
					'type' => 'array',
					'description' => 'REST API endpoints configured via the Rest API Listings module.',
					'items' => array( 'type' => 'object' ),
				),
				'glossaries' => array(
					'type' => 'array',
					'description' => 'JetEngine glossaries.',
					'items' => array( 'type' => 'object' ),
				),
			),
			'execute_callback' => array( $this, 'callback' ),
		) );
	}

	/**
	 * Check if a specific part is enabled in the input.
	 *
	 * @param string $part The part to check.
	 * @param array  $parts The parts array from input.
	 * @return bool
	 */
	public function is_part_enabled( $part, $parts ) {
		$is_enabled = isset( $parts[ $part ] ) ? $parts[ $part ] : false;
		$is_enabled = filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );

		return $is_enabled;
	}

	/**
	 * The main callback to retrieve the configuration.
	 *
	 * @param array $input The input parameters.
	 * @return array The configuration data.
	 */
	public function callback( $input = array() ) {

		$parts = ! empty( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : array();

		$result = array_filter( array(
			'success' => true,
			'post_types' => $this->get_post_types( $this->is_part_enabled( 'post_types', $parts ) ),
			'taxonomies' => $this->get_taxonomies( $this->is_part_enabled( 'taxonomies', $parts ) ),
			'meta_boxes' => $this->get_meta_boxes( $this->is_part_enabled( 'meta_boxes', $parts ) ),
			'options_pages' => $this->get_options_pages( $this->is_part_enabled( 'options_pages', $parts ) ),
			'queries' => $this->get_queries( $this->is_part_enabled( 'queries', $parts ) ),
			'relations' => $this->get_relations( $this->is_part_enabled( 'relations', $parts ) ),
			'custom_content_types' => $this->get_custom_content_types( $this->is_part_enabled( 'custom_content_types', $parts ) ),
			'rest_api_endpoints' => $this->get_rest_api_endpoints( $this->is_part_enabled( 'rest_api_endpoints', $parts ) ),
			'glossaries' => $this->get_glossaries( $this->is_part_enabled( 'glossaries', $parts ) ),
		) );

		return $result;
	}

	/**
	 * Remove specified fields from an associative array.
	 *
	 * @param array $item The associative array to clean.
	 * @param array $fields The list of fields to remove.
	 * @return void
	 */
	private function clean_fields( &$item, $fields ) {
		if ( ! is_array( $item ) || empty( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			if ( isset( $item[ $field ] ) ) {
				unset( $item[ $field ] );
			}
		}
	}

	/**
	 * Recursively clean meta fields, removing unnecessary properties.
	 *
	 * @param array $meta_fields The array of meta fields to clean.
	 * @return array The cleaned array of meta fields.
	 */
	private function clean_meta_fields( $meta_fields ) {

		if ( ! is_array( $meta_fields ) || empty( $meta_fields ) ) {
			return array();
		}

		$cleaned_meta_fields = array();

		foreach ( $meta_fields as $meta_field ) {

			if ( ! is_array( $meta_field ) ) {
				continue;
			}

			if ( ! empty( $meta_field['object_type'] ) && 'field' !== $meta_field['object_type'] ) {
				continue;
			}

			$this->clean_fields(
				$meta_field,
				array( 'width', 'id', 'isNested', 'collapsed', 'object_type' )
			);

			if ( ! empty( $meta_field['repeater-fields'] )
				&& is_array( $meta_field['repeater-fields'] )
			) {
				$meta_field['repeater-fields'] = $this->clean_meta_fields(
					$meta_field['repeater-fields']
				);
			}

			$cleaned_meta_fields[] = $meta_field;
		}

		return $cleaned_meta_fields;
	}

	/**
	 * Get the list of post types registered by JetEngine.
	 *
	 * @return array|null
	 */
	private function get_post_types( $is_enabled = false ) {

		if ( ! $is_enabled ) {
			return null;
		}

		$post_types = $this->get_items_from_component( isset( jet_engine()->cpt ) ? jet_engine()->cpt : null );

		// Add settings_url and items_url for each post type
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as &$post_type ) {

				// Cleanup unneccessary fields to reduce the output size
				$this->clean_fields( $post_type, array( 'show_ui', 'show_in_menu', 'show_in_nav_menus', 'show_in_rest', 'query_var', 'rewrite', 'map_meta_cap', 'has_archive', 'hierarchical', 'exclude_from_search', 'with_front', 'show_edit_link', 'custom_storage', 'hide_field_names', 'delete_metadata', 'capability_type', 'menu_position', 'menu_icon' ) );

				// Ensure we keep only the necessary fields from 'labels'
				if ( isset( $post_type['labels'] ) && is_array( $post_type['labels'] ) ) {
					$allowed_labels = [];

					if ( isset( $post_type['labels']['name'] ) ) {
						$allowed_labels[] = $post_type['labels']['name'];
					}

					if ( isset( $post_type['labels']['singular_name'] ) ) {
						$allowed_labels[] = $post_type['labels']['singular_name'];
					}

					$post_type['labels'] = $allowed_labels;
				}

				if ( ! empty( $post_type['meta_fields'] ) ) {
					$post_type['meta_fields'] = $this->clean_meta_fields( $post_type['meta_fields'] );
				}

				$post_type['settings_url'] = admin_url( 'admin.php?page=jet-engine-cpt&cpt_action=edit&id=' . $post_type['id'] );
				$post_type['items_url'] = admin_url( 'edit.php?post_type=' . $post_type['slug'] );
			}
		}

		return $post_types;
	}

	private function get_taxonomies(  $is_enabled = false ) {

		if ( ! $is_enabled ) {
			return null;
		}

		$taxonomies = $this->get_items_from_component( isset( jet_engine()->taxonomies ) ? jet_engine()->taxonomies : null );

		// Add settings_url and items_url for each taxonomy
		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as &$taxonomy ) {

				$this->clean_fields( $taxonomy, array( 'show_ui', 'show_in_menu', 'show_in_nav_menus', 'show_in_rest', 'rewrite', 'hierarchical', 'with_front', 'show_edit_link', 'hide_field_names', 'delete_metadata', 'query_var', 'capability_type' ) );

				// Ensure we keep only the necessary fields from 'labels'
				if ( isset( $taxonomy['labels'] ) && is_array( $taxonomy['labels'] ) ) {
					$allowed_labels = [];

					if ( isset( $taxonomy['labels']['name'] ) ) {
						$allowed_labels[] = $taxonomy['labels']['name'];
					}

					$taxonomy['labels'] = $allowed_labels;
				}

				if ( ! empty( $taxonomy['meta_fields'] ) ) {
					$taxonomy['meta_fields'] = $this->clean_meta_fields( $taxonomy['meta_fields'] );
				}

				$taxonomy['settings_url'] = admin_url( 'admin.php?page=jet-engine-cpt-tax&cpt_tax_action=edit&id=' . $taxonomy['id'] );
				$taxonomy['items_url'] = admin_url( 'edit-tags.php?taxonomy=' . $taxonomy['slug'] . '&post_type=' . $taxonomy['object_type'][0] );
			}
		}

		return $taxonomies;
	}

	private function get_meta_boxes( $is_enabled = false ) {

		if ( ! $is_enabled ) {
			return null;
		}

		$meta_boxes = $this->get_items_from_component( isset( jet_engine()->meta_boxes ) ? jet_engine()->meta_boxes : null );

		// Add settings_url for each meta box
		if ( ! empty( $meta_boxes ) ) {
			foreach ( $meta_boxes as &$meta_box ) {

				if ( ! empty( $meta_box['meta_fields'] ) ) {
					$meta_box['meta_fields'] = $this->clean_meta_fields( $meta_box['meta_fields'] );
				}

				$meta_box['settings_url'] = admin_url( 'admin.php?page=jet-engine-meta&cpt_meta_action=edit&id=' . $meta_box['id'] );
			}
		}

		return $meta_boxes;
	}

	private function get_options_pages( $is_enabled = false ) {

		if ( ! $is_enabled ) {
			return null;
		}

		$options_pages = $this->get_items_from_component( isset( jet_engine()->options_pages ) ? jet_engine()->options_pages : null );

		// Add settings_url and page url
		if ( ! empty( $options_pages ) ) {
			foreach ( $options_pages as &$options_page ) {

				// Cleanup unneccessary fields to reduce the output size
				$this->clean_fields( $options_page, array( 'icon', 'position', 'parent', 'capability', 'hide_field_names' ) );

				if ( ! empty( $options_page['fields'] ) ) {
					$options_page['fields'] = $this->clean_meta_fields( $options_page['fields'] );
				}
				$options_page['settings_url'] = admin_url( 'admin.php?page=jet-engine-options-pages&cpt_action=edit&id=' . $options_page['id'] );
				$options_page['page_url'] = admin_url( 'admin.php?page=' . $options_page['slug'] );
			}
		}

		return $options_pages;
	}

	private function get_relations( $is_enabled = false ) {

		if ( ! $is_enabled ) {
			return null;
		}

		$relations = $this->get_items_from_component( isset( jet_engine()->relations ) ? jet_engine()->relations : null );

		// Add settings_url for each relation
		if ( ! empty( $relations ) ) {
			foreach ( $relations as &$relation ) {

				// Cleanup unneccessary fields to reduce the output size
				$this->clean_fields( $relation, array( 'slug', 'status', 'is_legacy', 'labels', 'meta_fields' ) );

				$this->clean_fields( $relation['args'], array( 'db_table', 'parent_control', 'child_control', 'parent_manager', 'child_manager', 'parent_allow_delete', 'child_allow_delete', 'is_legacy', 'rest_get_enabled', 'rest_post_enabled', 'parents_required', 'children_required', 'name', 'legacy_id', 'rest_get_access', 'rest_post_access', 'cct' ) );

				$relation['settings_url'] = admin_url( 'admin.php?page=jet-engine-relations&cpt_relation_action=edit&id=' . $relation['id'] );
			}
		}

		return $relations;
	}

	private function get_queries( $is_enabled = false ) {

		if ( ! $is_enabled ) {
			return null;
		}

		if ( ! class_exists( '\Jet_Engine\Query_Builder\Manager' ) ) {
			return array();
		}

		$manager = \Jet_Engine\Query_Builder\Manager::instance();
		$data = isset( $manager->data ) ? $manager->data : null;

		$queries = $this->get_items_from_data_object( $data );

		// Add settings_url for each query
		if ( ! empty( $queries ) ) {
			foreach ( $queries as &$query ) {

				// Cleanup unneccessary fields to reduce the output size
				$this->clean_fields( $query, array( 'show_preview', 'cache_query', 'avoid_duplicates', 'preview_page', 'preview_page_title', 'preview_query_string', 'api_access', 'api_access_cap', 'api_access_role', 'cache_expires' ) );

				$query['settings_url'] = admin_url( 'admin.php?page=jet-engine-query&query_action=edit&id=' . $query['id'] );
			}
		}

		return $queries;
	}

	private function get_custom_content_types( $is_enabled = false ) {

		if ( ! $is_enabled ) {
			return null;
		}

		if ( ! jet_engine()->modules->is_module_active( 'custom-content-types' ) ) {
			return array();
		}

		if ( ! class_exists( '\Jet_Engine\Modules\Custom_Content_Types\Module' ) ) {
			return array();
		}

		$module = \Jet_Engine\Modules\Custom_Content_Types\Module::instance();
		$data = isset( $module->manager->data ) ? $module->manager->data : null;

		$content_types = $this->get_items_from_data_object( $data );

		// Add settings_url and items_url for each content type
		if ( ! empty( $content_types ) ) {
			foreach ( $content_types as &$content_type ) {

				// Cleanup unnecessary args
				$this->clean_fields( $content_type['args'], array( 'has_single', 'create_index', 'rest_get_enabled', 'rest_put_enabled', 'rest_post_enabled', 'rest_delete_enabled', 'hide_field_names', 'position', 'icon', 'capability', 'related_post_type_title', 'related_post_type_content', 'rest_get_access', 'rest_put_access', 'rest_post_access', 'rest_delete_access' ) );

				if ( ! empty( $content_type['meta_fields'] ) ) {
					$content_type['meta_fields'] = $this->clean_meta_fields(
						$content_type['meta_fields']
					);
				}

				$content_type['settings_url'] = admin_url( 'admin.php?page=jet-engine-cct&cct_action=edit&id=' . $content_type['id'] );
				$content_type['items_url'] = admin_url( 'admin.php?page=jet-cct-' . $content_type['args']['slug'] );
			}
		}

		return $content_types;
	}

	private function get_rest_api_endpoints(  $is_enabled = false ) {
		if ( ! $is_enabled ) {
			return null;
		}

		if ( empty( jet_engine()->modules ) || ! method_exists( jet_engine()->modules, 'is_module_active' ) ) {
			return array();
		}

		if ( ! jet_engine()->modules->is_module_active( 'rest-api-listings' ) ) {
			return array();
		}

		if ( ! class_exists( '\Jet_Engine\Modules\Rest_API_Listings\Module' ) ) {
			return array();
		}

		$module = \Jet_Engine\Modules\Rest_API_Listings\Module::instance();
		$data = isset( $module->data ) ? $module->data : null;

		return $this->get_items_from_data_object( $data );
	}

	private function get_glossaries( $is_enabled = false ) {
		if ( ! $is_enabled ) {
			return null;
		}

		return $this->get_items_from_component( isset( jet_engine()->glossaries ) ? jet_engine()->glossaries : null );
	}

	private function get_items_from_component( $component, $data_property = 'data', $normalize = true ) {
		if ( ! is_object( $component ) ) {
			return array();
		}

		$data_object = null;

		if ( isset( $component->{$data_property} ) && is_object( $component->{$data_property} ) ) {
			$data_object = $component->{$data_property};
		}

		return $this->get_items_from_data_object( $data_object, $normalize );
	}

	private function get_items_from_data_object( $data_object, $normalize = true ) {
		if ( ! is_object( $data_object ) || ! method_exists( $data_object, 'get_item_for_register' ) ) {
			return array();
		}

		$items = $data_object->get_item_for_register();

		if ( is_wp_error( $items ) ) {
			return array();
		}

		if ( ! is_array( $items ) || empty( $items ) ) {
			return array();
		}

		if ( $normalize ) {
			$items = array_values( $items );
		}

		return array_map( array( $this, 'normalize_value' ), $items );
	}

	private function normalize_value( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $nested ) {
				$value[ $key ] = $this->normalize_value( $nested );
			}
			return $value;
		}

		if ( is_object( $value ) ) {
			return $this->normalize_value( (array) $value );
		}

		return $value;
	}
}
