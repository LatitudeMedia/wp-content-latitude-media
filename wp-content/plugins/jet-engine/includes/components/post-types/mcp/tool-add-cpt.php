<?php
namespace Jet_Engine\Post_Types\MCP;

use Jet_Engine\MCP_Tools\Registry;

class Tool_Add_CPT {

	public function __construct() {
		Registry::instance()->add_feature( 'add-cpt', [
			'type' => 'tool',
			'label' => 'Add Post Type',
			'description' => 'Register a new Post Type (CPT) with JetEngine. Provide the post type name inside general_settings.name. The slug will be generated from the name if you omit it. Select this tool only when USER clearly mentioned that he need a new custom post type. In other cases - look for more specific tools.',
			'input_schema' => [
				'general_settings' => [
					'type' => 'object',
					'description' => 'General settings for the post type such as the public label, slug and storage flags.',
					'properties' => [
						'name' => [
							'type' => 'string',
							'description' => 'Plural label shown in the WordPress admin.',
						],
						'slug' => [
							'type' => 'string',
							'description' => 'Custom post type slug (max 20 lowercase characters). Generated from the name when omitted.',
						],
						'custom_storage' => [
							'type' => 'boolean',
							'description' => 'Store CPT data inside a dedicated custom database table.',
						],
						'show_edit_link' => [
							'type' => 'boolean',
							'description' => 'Display the “Edit Post Type” link inside the WordPress toolbar.',
						],
						'hide_field_names' => [
							'type' => 'boolean',
							'description' => 'Hide field names on the edit screen to make the UI cleaner.',
						],
						'delete_metadata' => [
							'type' => 'boolean',
							'description' => 'Delete all related metadata when the post type is removed.',
						],
					],
				],
				'labels' => [
					'type' => 'object',
					'description' => 'Optional labels that override the default WordPress labels for the CPT.',
				],
				'advanced_settings' => [
					'type' => 'object',
					'description' => 'Advanced visibility and capability settings (public, supports, menu_icon, etc.).',
				],
				'meta_fields' => [
					'type'        => 'array',
					'description' => 'Meta field definitions. Pass JetEngine meta field objects or simplified items with name, type, title, options, etc.',
					'items'       => [
						'type'       => 'object',
						'properties' => [
							// allow both JetEngine objects and simplified items
							'object_type' => [ 'type' => 'string', 'description' => 'If present with value "field", item is treated as a raw JetEngine field object.' ],
							'id'          => [ 'type' => 'integer' ],
							'name'        => [ 'type' => 'string', 'description' => 'Field key (snake_case). Alias: field_name.' ],
							'field_name'  => [ 'type' => 'string' ],
							'title'       => [ 'type' => 'string' ],
							'label'       => [ 'type' => 'string' ],
							'type'        => [ 'type' => 'string', 'enum' => [ 'text','number','date','datetime','datetime-local','time','media','checkbox','select','radio','textarea','repeater' ] ],
							'field_type'  => [ 'type' => 'string' ],
							'description' => [ 'type' => 'string' ],
							'placeholder' => [ 'type' => 'string' ],
							'default'     => [ 'type' => 'string' ],
							'is_required' => [ 'type' => 'boolean' ],
							'value_format'=> [ 'type' => 'string', 'description' => 'For media fields: id|url.' ],
							'options'     => [
								'type'  => 'array',
								'items' => [ 'type' => 'string' ],
								'description' => 'For select/checkbox/radio (simple list or "value::label" pairs).',
							],
							'choices'     => [
								'type'  => 'array',
								'items' => [ 'type' => 'string' ],
								'description' => 'Alternative to options.',
							],
							'fields'      => [ // for repeater
								'type'  => 'array',
								'items' => [ 'type' => 'object' ],
							],

							// admin columns helpers
							'add_to_admin_column'   => [ 'type' => 'boolean' ],
							'admin_column_position' => [ 'type' => 'integer' ],
							'column_prefix'         => [ 'type' => 'string' ],
							'column_suffix'         => [ 'type' => 'string' ],
							'is_sortable'           => [ 'type' => 'boolean' ],
							'sort_by_field'         => [ 'type' => 'string' ],
						],
					],
				],
				'admin_columns' => [
					'type'        => 'array',
					'description' => 'Optional admin columns configuration. Leave empty to auto-generate from meta_fields flagged with add_to_admin_column.',
					'items'       => [
						'type'       => 'object',
						'properties' => [
							'title'        => [ 'type' => 'string' ],
							'type'         => [ 'type' => 'string', 'description' => 'e.g. meta_value, taxonomy, callback' ],
							'meta_field'   => [ 'type' => 'string' ],
							'taxonomy'     => [ 'type' => 'string' ],
							'callback'     => [ 'type' => 'string' ],
							'position'     => [ 'type' => 'integer' ],
							'prefix'       => [ 'type' => 'string' ],
							'suffix'       => [ 'type' => 'string' ],
							'is_sortable'  => [ 'type' => 'boolean' ],
							'sort_by_field'=> [ 'type' => 'string' ],
							'is_num'       => [ 'type' => 'boolean' ],
						],
					],
				],
				'admin_filters' => [
					'type'        => 'array',
					'description' => 'Optional admin filters configuration for the post list screen.',
					'items'       => [
						'type' => 'object',
					],
				],
			],
			'output_schema' => [
				'success' => [
					'type' => 'boolean',
					'description' => 'Indicates whether the CPT was created successfully.',
				],
				'item_id' => [
					'type' => 'integer',
					'description' => 'Internal database ID of the created CPT record.',
				],
				'cpt_settings_url' => [
					'type' => 'string',
					'description' => 'Link to edit the CPT inside the WordPress dashboard.',
				],
				'cpt_items_list_url' => [
					'type' => 'string',
					'description' => 'Link to the WordPress list table that displays posts of the new CPT.',
				],
				'notices' => [
					'type' => 'array',
					'description' => 'Array of JetEngine notices triggered during creation (each notice contains type and message).',
				],
				'next_tool' => [
					'type' => 'string',
					'description' => 'Suggested MCP tool to run after creating the CPT.',
				],
			],
			'execute_callback' => [ $this, 'callback' ],
		] );
	}

	public function callback( $input = [] ) {
		if ( empty( jet_engine()->cpt ) || empty( jet_engine()->cpt->data ) ) {
			return new \WP_Error( 'component_unavailable', 'The Custom Post Types component is not available.' );
		}

		$general = $this->normalize_general_settings( $input );
		$name = $general['name'];
		$slug = $general['slug'];

		if ( ! $name ) {
			return new \WP_Error( 'invalid_input', 'general_settings.name is required.' );
		}

		if ( ! $slug ) {
			return new \WP_Error( 'invalid_input', 'Unable to create a slug for the post type. Please provide general_settings.slug.' );
		}

		$labels = $this->normalize_labels( $input, $name );
		$advanced = $this->normalize_advanced_settings( $input, $slug );
		$meta = $this->prepare_meta_fields( isset( $input['meta_fields'] ) ? $input['meta_fields'] : [] );
		$admin_columns = isset( $input['admin_columns'] ) ? $this->normalize_admin_columns( $input['admin_columns'] ) : [];
		$admin_filters = $this->normalize_admin_filters( isset( $input['admin_filters'] ) ? $input['admin_filters'] : [] );

		if ( empty( $admin_columns ) && ! empty( $meta['admin_columns'] ) ) {
			$admin_columns = $this->normalize_admin_columns( $meta['admin_columns'] );
		}

		jet_engine()->cpt->data->set_request( [
			'name' => $name,
			'slug' => $slug,
			'custom_storage' => $general['custom_storage'],
			'show_edit_link' => $general['show_edit_link'],
			'hide_field_names' => $general['hide_field_names'],
			'delete_metadata' => $general['delete_metadata'],
			'singular_name' => isset( $labels['singular_name'] ) ? $labels['singular_name'] : '',
			'menu_name' => isset( $labels['menu_name'] ) ? $labels['menu_name'] : '',
			'name_admin_bar' => isset( $labels['name_admin_bar'] ) ? $labels['name_admin_bar'] : '',
			'add_new' => isset( $labels['add_new'] ) ? $labels['add_new'] : '',
			'add_new_item' => isset( $labels['add_new_item'] ) ? $labels['add_new_item'] : '',
			'new_item' => isset( $labels['new_item'] ) ? $labels['new_item'] : '',
			'edit_item' => isset( $labels['edit_item'] ) ? $labels['edit_item'] : '',
			'view_item' => isset( $labels['view_item'] ) ? $labels['view_item'] : '',
			'all_items' => isset( $labels['all_items'] ) ? $labels['all_items'] : '',
			'search_items' => isset( $labels['search_items'] ) ? $labels['search_items'] : '',
			'parent_item_colon' => isset( $labels['parent_item_colon'] ) ? $labels['parent_item_colon'] : '',
			'not_found' => isset( $labels['not_found'] ) ? $labels['not_found'] : '',
			'not_found_in_trash' => isset( $labels['not_found_in_trash'] ) ? $labels['not_found_in_trash'] : '',
			'featured_image' => isset( $labels['featured_image'] ) ? $labels['featured_image'] : '',
			'set_featured_image' => isset( $labels['set_featured_image'] ) ? $labels['set_featured_image'] : '',
			'remove_featured_image' => isset( $labels['remove_featured_image'] ) ? $labels['remove_featured_image'] : '',
			'use_featured_image' => isset( $labels['use_featured_image'] ) ? $labels['use_featured_image'] : '',
			'archives' => isset( $labels['archives'] ) ? $labels['archives'] : '',
			'insert_into_item' => isset( $labels['insert_into_item'] ) ? $labels['insert_into_item'] : '',
			'uploaded_to_this_item' => isset( $labels['uploaded_to_this_item'] ) ? $labels['uploaded_to_this_item'] : '',
			'public' => $advanced['public'],
			'exclude_from_search' => $advanced['exclude_from_search'],
			'publicly_queryable' => $advanced['publicly_queryable'],
			'show_ui' => $advanced['show_ui'],
			'show_in_menu' => $advanced['show_in_menu'],
			'show_in_nav_menus' => $advanced['show_in_nav_menus'],
			'show_in_rest' => $advanced['show_in_rest'],
			'query_var' => $advanced['query_var'],
			'rewrite' => $advanced['rewrite'],
			'with_front' => $advanced['with_front'],
			'has_archive' => $advanced['has_archive'],
			'hierarchical' => $advanced['hierarchical'],
			'rewrite_slug' => $advanced['rewrite_slug'],
			'capability_type' => $advanced['capability_type'],
			'map_meta_cap' => $advanced['map_meta_cap'],
			'menu_position' => $advanced['menu_position'],
			'menu_icon' => $advanced['menu_icon'],
			'supports' => $advanced['supports'],
			'admin_columns' => $admin_columns,
			'admin_filters' => $admin_filters,
			'meta_fields' => $meta['fields'],
		] );

		try {
			$item_id = jet_engine()->cpt->data->create_item( false );
		} catch ( \Exception $exception ) {
			return new \WP_Error( 'cpt_create_failed', $exception->getMessage() );
		}

		$notices = method_exists( jet_engine()->cpt, 'get_notices' ) ? jet_engine()->cpt->get_notices() : [];
		$settings_url = '';
		$list_url = '';

		if ( $item_id ) {
			$settings_url = admin_url( 'admin.php?page=jet-engine-cpt&cpt_action=edit&id=' . $item_id );
			$list_url = admin_url( 'edit.php?post_type=' . $slug );
		}

		return [
			'success' => ! empty( $item_id ),
			'item_id' => $item_id,
			'cpt_settings_url' => $settings_url,
			'cpt_items_list_url' => $list_url,
			'notices' => $notices,
			'next_tool' => 'tool-crocoblock/add-query',
		];
	}

	protected function normalize_general_settings( $input ) {
		$general = $this->get_array( $input, 'general_settings' );

		$map = [ 'name', 'slug', 'custom_storage', 'show_edit_link', 'hide_field_names', 'delete_metadata' ];

		foreach ( $map as $key ) {
			if ( isset( $input[ $key ] ) && ! isset( $general[ $key ] ) ) {
				$general[ $key ] = $input[ $key ];
			}
		}

		$general = array_merge( [
			'name' => '',
			'slug' => '',
			'custom_storage' => false,
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

		$general['custom_storage'] = $this->sanitize_bool( $general['custom_storage'] );
		$general['show_edit_link'] = $this->sanitize_bool( $general['show_edit_link'] );
		$general['hide_field_names'] = $this->sanitize_bool( $general['hide_field_names'] );
		$general['delete_metadata'] = $this->sanitize_bool( $general['delete_metadata'] );

		return $general;
	}

	protected function normalize_labels( $input, $name ) {
		$labels = $this->get_array( $input, 'labels' );
		$keys = [
			'singular_name',
			'menu_name',
			'name_admin_bar',
			'add_new',
			'add_new_item',
			'new_item',
			'edit_item',
			'view_item',
			'all_items',
			'search_items',
			'parent_item_colon',
			'not_found',
			'not_found_in_trash',
			'featured_image',
			'set_featured_image',
			'remove_featured_image',
			'use_featured_image',
			'archives',
			'insert_into_item',
			'uploaded_to_this_item',
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
			if ( empty( $labels['name_admin_bar'] ) ) {
				$labels['name_admin_bar'] = $name;
			}
		}

		return $labels;
	}

	protected function normalize_advanced_settings( $input, $slug ) {
		$advanced = $this->get_array( $input, 'advanced_settings' );

		$defaults = [
			'public' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'show_in_rest' => true,
			'query_var' => true,
			'rewrite' => true,
			'with_front' => true,
			'has_archive' => true,
			'hierarchical' => false,
			'rewrite_slug' => $slug,
			'capability_type' => 'post',
			'map_meta_cap' => true,
			'menu_position' => null,
			'menu_icon' => 'dashicons-format-standard',
			'supports' => [ 'title', 'editor' ],
		];

		foreach ( $defaults as $key => $value ) {
			if ( isset( $input[ $key ] ) && ! isset( $advanced[ $key ] ) ) {
				$advanced[ $key ] = $input[ $key ];
			}
		}

		$advanced = array_merge( $defaults, $advanced );

		$bools = [ 'public', 'exclude_from_search', 'publicly_queryable', 'show_ui', 'show_in_menu', 'show_in_nav_menus', 'show_in_rest', 'query_var', 'rewrite', 'with_front', 'has_archive', 'hierarchical', 'map_meta_cap' ];

		foreach ( $bools as $key ) {
			$advanced[ $key ] = $this->sanitize_bool( $advanced[ $key ] );
		}

		$advanced['capability_type'] = $advanced['capability_type'] ? sanitize_text_field( $advanced['capability_type'] ) : 'post';
		$advanced['menu_icon'] = $advanced['menu_icon'] ? sanitize_text_field( $advanced['menu_icon'] ) : '';
		$advanced['rewrite_slug'] = $advanced['rewrite_slug'] ? sanitize_title( $advanced['rewrite_slug'] ) : $slug;
		$advanced['supports'] = $this->normalize_supports( $advanced['supports'] );

		if ( null !== $advanced['menu_position'] && '' !== $advanced['menu_position'] ) {
			$advanced['menu_position'] = is_numeric( $advanced['menu_position'] ) ? absint( $advanced['menu_position'] ) : null;
		} else {
			$advanced['menu_position'] = null;
		}

		return $advanced;
	}

	protected function normalize_supports( $supports ) {
		if ( is_string( $supports ) ) {
			$supports = array_map( 'trim', explode( ',', $supports ) );
		}

		if ( ! is_array( $supports ) ) {
			$supports = [];
		}

		$supports = array_filter( array_map( 'sanitize_key', $supports ) );

		if ( empty( $supports ) ) {
			$supports = [ 'title', 'editor' ];
		}

		return array_values( array_unique( $supports ) );
	}

	protected function prepare_meta_fields( $fields ) {
		$result = [
			'fields' => [],
			'admin_columns' => [],
		];

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return $result;
		}

		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			if ( isset( $field['object_type'] ) && isset( $field['name'] ) ) {
				$result['fields'][] = $field;
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

			$prepared = [
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
				$prepared['value_format'] = isset( $field['value_format'] ) ? sanitize_text_field( $field['value_format'] ) : 'id';
			}

			if ( in_array( $type, [ 'date', 'datetime', 'datetime-local', 'time' ], true ) ) {
				$prepared['input_type'] = $type;
				$prepared['autocomplete'] = 'off';
				$prepared['is_timestamp'] = true;
			}

			if ( 'repeater' === $type && ! empty( $field['fields'] ) && is_array( $field['fields'] ) ) {
				$nested = $this->prepare_meta_fields( $field['fields'] );
				$prepared['repeater-fields'] = $nested['fields'];
				$result['admin_columns'] = array_merge( $result['admin_columns'], $nested['admin_columns'] );
			} else {
				$prepared['repeater-fields'] = [];
			}

			if ( in_array( $type, [ 'select', 'checkbox', 'radio' ], true ) ) {
				$options = isset( $field['options'] ) ? $field['options'] : ( isset( $field['choices'] ) ? $field['choices'] : [] );
				if ( ! empty( $options ) && is_array( $options ) ) {
					$prepared['options_source'] = 'manual_bulk';
					$prepared['bulk_options'] = $this->prepare_bulk_options( $options );
					if ( 'checkbox' === $type ) {
						$prepared['is_array'] = true;
					}
				}
			}

			$result['fields'][] = $prepared;

			if ( ! empty( $field['add_to_admin_column'] ) && $this->sanitize_bool( $field['add_to_admin_column'] ) ) {
				$result['admin_columns'][] = $this->build_admin_column_from_field( $prepared, $field );
			}
		}

		return $result;
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

			if ( '' === $option_value && '' === $option_label ) {
				continue;
			}

			if ( '' === $option_value ) {
				$option_value = $option_label;
			}

			if ( '' === $option_label ) {
				$option_label = $option_value;
			}

			$prepared[] = $option_value . '::' . $option_label;
		}

		return implode( "
", $prepared );
	}

	protected function build_admin_column_from_field( $field, $raw_field ) {
		$is_num = $this->is_numeric_field_type( $field['type'] );

		return [
			'title' => $field['title'],
			'type' => 'meta_value',
			'meta_field' => $field['name'],
			'position' => isset( $raw_field['admin_column_position'] ) ? absint( $raw_field['admin_column_position'] ) : 0,
			'prefix' => isset( $raw_field['column_prefix'] ) ? sanitize_text_field( $raw_field['column_prefix'] ) : '',
			'suffix' => isset( $raw_field['column_suffix'] ) ? sanitize_text_field( $raw_field['column_suffix'] ) : '',
			'is_sortable' => $this->sanitize_bool( isset( $raw_field['is_sortable'] ) ? $raw_field['is_sortable'] : false ),
			'sort_by_field' => isset( $raw_field['sort_by_field'] ) ? $this->sanitize_field_name( $raw_field['sort_by_field'] ) : '',
			'is_num' => $is_num,
			'collapsed' => false,
		];
	}

	protected function normalize_admin_columns( $columns ) {
		if ( empty( $columns ) || ! is_array( $columns ) ) {
			return [];
		}

		$sanitized = [];

		foreach ( $columns as $column ) {
			if ( ! is_array( $column ) ) {
				continue;
			}

			$type = isset( $column['type'] ) ? sanitize_key( $column['type'] ) : 'meta_value';
			$title = isset( $column['title'] ) ? sanitize_text_field( $column['title'] ) : '';
			$meta_field = isset( $column['meta_field'] ) ? $this->sanitize_field_name( $column['meta_field'] ) : ( isset( $column['field'] ) ? $this->sanitize_field_name( $column['field'] ) : '' );
			$taxonomy = isset( $column['taxonomy'] ) ? sanitize_key( $column['taxonomy'] ) : ( isset( $column['tax'] ) ? sanitize_key( $column['tax'] ) : '' );
			$callback = isset( $column['callback'] ) ? sanitize_text_field( $column['callback'] ) : '';
			$position = isset( $column['position'] ) ? absint( $column['position'] ) : 0;
			$prefix = isset( $column['prefix'] ) ? sanitize_text_field( $column['prefix'] ) : '';
			$suffix = isset( $column['suffix'] ) ? sanitize_text_field( $column['suffix'] ) : '';
			$is_sortable = $this->sanitize_bool( isset( $column['is_sortable'] ) ? $column['is_sortable'] : ( isset( $column['sortable'] ) ? $column['sortable'] : false ) );
			$sort_by_field = isset( $column['sort_by_field'] ) ? $this->sanitize_field_name( $column['sort_by_field'] ) : ( isset( $column['sort_field'] ) ? $this->sanitize_field_name( $column['sort_field'] ) : '' );
			$is_num = $this->sanitize_bool( isset( $column['is_num'] ) ? $column['is_num'] : ( isset( $column['numeric'] ) ? $column['numeric'] : false ) );

			if ( ! $title ) {
				$title = $meta_field ? $this->humanize( $meta_field ) : $this->humanize( $type );
			}

			$sanitized[] = [
				'title' => $title,
				'type' => $type,
				'meta_field' => $meta_field,
				'taxonomy' => $taxonomy,
				'callback' => $callback,
				'position' => $position,
				'prefix' => $prefix,
				'suffix' => $suffix,
				'is_sortable' => $is_sortable,
				'sort_by_field' => $sort_by_field,
				'is_num' => $is_num,
				'collapsed' => false,
			];
		}

		return $sanitized;
	}

	protected function normalize_admin_filters( $filters ) {
		if ( empty( $filters ) || ! is_array( $filters ) ) {
			return [];
		}

		return array_values( array_filter( $filters, 'is_array' ) );
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

	protected function is_numeric_field_type( $type ) {
		return in_array( $type, [ 'number', 'date', 'datetime', 'datetime-local', 'time' ], true );
	}
}
