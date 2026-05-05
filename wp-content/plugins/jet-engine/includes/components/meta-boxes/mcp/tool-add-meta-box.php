<?php
namespace Jet_Engine\Meta_Boxes\MCP;

use Jet_Engine\MCP_Tools\Registry;

class Tool_Add_Meta_Box {

	public function __construct() {
		Registry::instance()->add_feature( 'add-meta-box', array(
			'type'               => 'tool',
			'label'              => 'Add Meta Box',
			'description'        => 'Create a JetEngine Meta Box with custom fields. Always provide general_settings.name and describe the fields in meta_fields. The tool automatically enables the necessary visibility conditions when you supply their configuration.',
			'input_schema'       => array(
				'general_settings' => array(
					'type'                 => 'object',
					'description'          => 'General settings such as the title, target object, and condition configuration.',
					'properties'           => array(
						'name'                 => array(
							'type'        => 'string',
							'description' => 'Meta box title shown in the WordPress dashboard.',
						),
						'object_type'          => array(
							'type'        => 'string',
							'description' => 'Object type where the meta box should appear.',
							'enum'        => $this->get_object_type_enum(),
						),
						'show_edit_link'       => array(
							'type'        => 'boolean',
							'description' => 'Display the “Edit meta box” link in the toolbar.',
						),
						'hide_field_names'     => array(
							'type'        => 'boolean',
							'description' => 'Hide field names on the edit screen.',
						),
						'delete_metadata'      => array(
							'type'        => 'boolean',
							'description' => 'Delete stored metadata when the meta box or its fields are removed.',
						),
						'allowed_post_type'    => array(
							'type'        => 'array',
							'description' => 'Post type slugs that should display the meta box (only for object_type "post").',
							'items'       => array(
								'type' => 'string',
							),
						),
						'allowed_tax'          => array(
							'type'        => 'array',
							'description' => 'Taxonomy slugs that should display the meta box (only for object_type "taxonomy").',
							'items'       => array(
								'type' => 'string',
							),
						),
						'allowed_user_screens' => array(
							'type'        => 'string',
							'description' => 'For user meta boxes choose whether fields appear on Edit User only or Edit User & Profile.',
							'enum'        => array( 'edit', 'edit-profile' ),
						),
						'active_conditions'    => array(
							'type'        => 'array',
							'description' => 'List of enabled condition keys (allowed_posts, excluded_posts, include_roles, exclude_roles, post_has_terms, etc.). Missing keys are added automatically when related settings are provided.',
							'items'       => array(
								'type' => 'string',
							),
						),
						'allowed_posts'        => array(
							'type'        => 'array',
							'description' => 'Specific post IDs that should display the meta box (activates allowed_posts condition).',
							'items'       => array(
								'type' => 'integer',
							),
						),
						'excluded_posts'       => array(
							'type'        => 'array',
							'description' => 'Post IDs where the meta box must be hidden (activates excluded_posts condition).',
							'items'       => array(
								'type' => 'integer',
							),
						),
						'include_roles'        => array(
							'type'        => 'array',
							'description' => 'User roles that should see the meta box (activates include_roles condition).',
							'items'       => array(
								'type' => 'string',
							),
						),
						'exclude_roles'        => array(
							'type'        => 'array',
							'description' => 'User roles that should be prevented from seeing the meta box (activates exclude_roles condition).',
							'items'       => array(
								'type' => 'string',
							),
						),
						'post_has_terms__tax'  => array(
							'type'        => 'string',
							'description' => 'Taxonomy slug used for the post_has_terms condition.',
						),
						'post_has_terms__terms' => array(
							'type'        => 'array',
							'description' => 'Term IDs or slugs to compare in the post_has_terms condition.',
							'items'       => array(
								'type' => 'string',
							),
						),
					),
					'additionalProperties' => true,
				),
				'meta_fields' => array(
					'type'        => 'array',
					'description' => 'Meta field definitions saved inside the meta box. Pass full JetEngine field arrays (object_type => field) or simplified descriptors with name, type, label, options, etc.',
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'object_type'  => array(
								'type'        => 'string',
								'description' => 'Set to "field" when providing full JetEngine meta field data.',
							),
							'id'           => array(
								'type'        => 'integer',
								'description' => 'Optional field identifier. Generated automatically when omitted.',
							),
							'name'         => array(
								'type'        => 'string',
								'description' => 'Field key (snake_case).',
							),
							'field_name'   => array(
								'type'        => 'string',
								'description' => 'Alias for name when using simplified descriptors.',
							),
							'title'        => array(
								'type'        => 'string',
								'description' => 'Field label displayed in the UI.',
							),
							'label'        => array(
								'type' => 'string',
							),
							'type'         => array(
								'type'        => 'string',
								'description' => 'JetEngine field type slug (text, textarea, select, repeater, etc.).',
							),
							'field_type'   => array(
								'type'        => 'string',
								'description' => 'Alternative key for the field type.',
							),
							'description'  => array(
								'type' => 'string',
							),
							'placeholder'  => array(
								'type' => 'string',
							),
							'default'      => array(
								'type'        => 'string',
								'description' => 'Default value stored for the field.',
							),
							'is_required'  => array(
								'type' => 'boolean',
							),
							'quick_editable' => array(
								'type' => 'boolean',
							),
							'value_format' => array(
								'type'        => 'string',
								'description' => 'For media fields set to "id" or "url".',
							),
							'taxonomy'     => array(
								'type'        => 'string',
								'description' => 'Taxonomy slug for taxonomy fields or taxonomy option source.',
							),
							'appearance'   => array(
								'type'        => 'string',
								'description' => 'Taxonomy field appearance (select, radio, checkboxes).',
							),
							'multiple'     => array(
								'type'        => 'boolean',
								'description' => 'Allow multiple values for taxonomy/select fields.',
							),
							'allow_null'   => array(
								'type'        => 'boolean',
								'description' => 'Allow empty value for single-selection taxonomy fields.',
							),
							'return_value' => array(
								'type'        => 'string',
								'description' => 'Taxonomy return value (term_id, term_slug, term_name).',
							),
							'load_terms'   => array(
								'type'        => 'boolean',
								'description' => 'Prefill taxonomy field from current object term relationships.',
							),
							'save_terms'   => array(
								'type'        => 'boolean',
								'description' => 'Sync taxonomy field value into object-term relationships on save.',
							),
							'options'      => array(
								'type'        => 'array',
								'description' => 'Advanced options array when using manual options.',
								'items'       => array(
									'type' => 'object',
								),
							),
							'choices'      => array(
								'type'        => 'array',
								'description' => 'Alternative list of options (array of values or value::label strings).',
								'items'       => array(
									'type' => 'string',
								),
							),
							'fields'       => array(
								'type'        => 'array',
								'description' => 'Nested fields for repeater-like types.',
								'items'       => array(
									'type' => 'object',
								),
							),
							'conditions'   => array(
								'type'        => 'array',
								'description' => 'Optional field visibility conditions.',
								'items'       => array(
									'type' => 'object',
								),
							),
						),
						'additionalProperties' => true,
					),
				),
			),
			'output_schema'      => array(
				'success'              => array(
					'type'        => 'boolean',
					'description' => 'Indicates whether the meta box was created successfully.',
				),
				'item_id'              => array(
					'type'        => 'string',
					'description' => 'Internal ID of the created meta box.',
				),
				'meta_box_url'         => array(
					'type'        => 'string',
					'description' => 'Admin URL to edit the newly created meta box.',
				),
				'meta_boxes_list_url'  => array(
					'type'        => 'string',
					'description' => 'Admin URL that lists all JetEngine meta boxes.',
				),
				'notices'              => array(
					'type'        => 'array',
					'description' => 'JetEngine notices generated during creation (each notice contains type and message).',
					'items'       => array(
						'type' => 'object',
					),
				),
				'next_tool'            => array(
					'type'        => 'string',
					'description' => 'Suggested MCP tool to run after creating the meta box.',
				),
			),
			'execute_callback'   => array( $this, 'callback' ),
		) );
	}

	public function callback( $input = array() ) {
		if ( empty( jet_engine()->meta_boxes ) || empty( jet_engine()->meta_boxes->data ) ) {
			return new \WP_Error( 'component_unavailable', 'The Meta Boxes component is not available.' );
		}

		$general = $this->normalize_general_settings( isset( $input['general_settings'] ) ? $input['general_settings'] : array() );

		if ( empty( $general['name'] ) ) {
			return new \WP_Error( 'invalid_input', 'general_settings.name is required.' );
		}

		$fields      = isset( $input['meta_fields'] ) ? $input['meta_fields'] : array();
		$meta_fields = $this->prepare_meta_fields( $fields );

		jet_engine()->meta_boxes->data->set_request( array(
			'args'        => $general,
			'meta_fields' => $meta_fields,
		) );

		$item_id  = jet_engine()->meta_boxes->data->create_item( false );
		$edit_url = '';
		$list_url = admin_url( 'admin.php?page=jet-engine-meta' );

		if ( ! empty( $item_id ) ) {
			$edit_url = admin_url( 'admin.php?page=jet-engine-meta&cpt_meta_action=edit&id=' . $item_id );
		}

		return array(
			'success'             => ! empty( $item_id ),
			'item_id'             => $item_id,
			'meta_box_url'        => $edit_url,
			'meta_boxes_list_url' => $list_url,
			'notices'             => jet_engine()->meta_boxes->get_notices(),
			'next_tool'           => 'tool-crocoblock/add-listing',
		);
	}

	protected function normalize_general_settings( $settings ) {
		$settings = is_array( $settings ) ? $settings : array();

		$name        = isset( $settings['name'] ) ? sanitize_text_field( $settings['name'] ) : '';
		$object_type = isset( $settings['object_type'] ) ? sanitize_key( $settings['object_type'] ) : 'post';
		$allowed     = $this->get_object_type_enum();

		if ( ! in_array( $object_type, $allowed, true ) ) {
			$object_type = 'post';
		}

		$sanitized = array(
			'name'                 => $name,
			'object_type'          => $object_type,
			'show_edit_link'       => $this->sanitize_bool( isset( $settings['show_edit_link'] ) ? $settings['show_edit_link'] : false ),
			'hide_field_names'     => $this->sanitize_bool( isset( $settings['hide_field_names'] ) ? $settings['hide_field_names'] : false ),
			'delete_metadata'      => $this->sanitize_bool( isset( $settings['delete_metadata'] ) ? $settings['delete_metadata'] : false ),
			'allowed_post_type'    => $this->sanitize_slug_array( isset( $settings['allowed_post_type'] ) ? $settings['allowed_post_type'] : array() ),
			'allowed_tax'          => $this->sanitize_slug_array( isset( $settings['allowed_tax'] ) ? $settings['allowed_tax'] : array() ),
			'allowed_user_screens' => $this->sanitize_user_screen( isset( $settings['allowed_user_screens'] ) ? $settings['allowed_user_screens'] : '' ),
			'allowed_posts'        => $this->sanitize_post_ids( isset( $settings['allowed_posts'] ) ? $settings['allowed_posts'] : array() ),
			'excluded_posts'       => $this->sanitize_post_ids( isset( $settings['excluded_posts'] ) ? $settings['excluded_posts'] : array() ),
			'include_roles'        => $this->sanitize_slug_array( isset( $settings['include_roles'] ) ? $settings['include_roles'] : array() ),
			'exclude_roles'        => $this->sanitize_slug_array( isset( $settings['exclude_roles'] ) ? $settings['exclude_roles'] : array() ),
			'post_has_terms__tax'  => isset( $settings['post_has_terms__tax'] ) ? sanitize_key( $settings['post_has_terms__tax'] ) : '',
			'post_has_terms__terms' => $this->sanitize_terms_array( isset( $settings['post_has_terms__terms'] ) ? $settings['post_has_terms__terms'] : array() ),
			'active_conditions'    => $this->sanitize_slug_array( isset( $settings['active_conditions'] ) ? $settings['active_conditions'] : array() ),
		);

		$sanitized['active_conditions'] = $this->ensure_active_conditions( $sanitized );

		foreach ( $settings as $key => $value ) {
			if ( array_key_exists( $key, $sanitized ) ) {
				continue;
			}

			$sanitized[ $key ] = $this->sanitize_generic_value( $value );
		}

		return $sanitized;
	}

	protected function ensure_active_conditions( $settings ) {
		$active = isset( $settings['active_conditions'] ) ? $settings['active_conditions'] : array();

		if ( ! empty( $settings['allowed_posts'] ) ) {
			$active = $this->ensure_active_condition( $active, 'allowed_posts' );
		}

		if ( ! empty( $settings['excluded_posts'] ) ) {
			$active = $this->ensure_active_condition( $active, 'excluded_posts' );
		}

		if ( ! empty( $settings['include_roles'] ) ) {
			$active = $this->ensure_active_condition( $active, 'include_roles' );
		}

		if ( ! empty( $settings['exclude_roles'] ) ) {
			$active = $this->ensure_active_condition( $active, 'exclude_roles' );
		}

		if ( ! empty( $settings['post_has_terms__tax'] ) || ! empty( $settings['post_has_terms__terms'] ) ) {
			$active = $this->ensure_active_condition( $active, 'post_has_terms' );
		}

		return array_values( array_unique( $active ) );
	}

	protected function ensure_active_condition( $active, $key ) {
		if ( ! in_array( $key, $active, true ) ) {
			$active[] = $key;
		}

		return $active;
	}

	protected function prepare_meta_fields( $fields ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return array();
		}

		$prepared = array();

		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			if ( isset( $field['object_type'] ) && isset( $field['name'] ) ) {
				$prepared[] = $this->sanitize_existing_field( $field );
				continue;
			}

			$built = $this->build_field_from_descriptor( $field );

			if ( $built ) {
				$prepared[] = $built;
			}
		}

		return array_values( $prepared );
	}

	protected function sanitize_existing_field( $field ) {
		$field['name']        = $this->sanitize_field_name( isset( $field['name'] ) ? $field['name'] : '' );
		$field['title']       = isset( $field['title'] ) ? sanitize_text_field( $field['title'] ) : ( isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : $this->humanize( $field['name'] ) );
		$field['object_type'] = isset( $field['object_type'] ) ? sanitize_key( $field['object_type'] ) : 'field';
		$field['type']        = isset( $field['type'] ) ? sanitize_key( $field['type'] ) : 'text';
		$field['id']          = isset( $field['id'] ) ? absint( $field['id'] ) : wp_rand( 10000, 99999 );

		if ( isset( $field['conditions'] ) && is_array( $field['conditions'] ) ) {
			$field['conditions'] = array_values( array_map( array( $this, 'sanitize_generic_value' ), $field['conditions'] ) );
		}

		if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
			$field['options'] = array_values( array_map( array( $this, 'sanitize_generic_value' ), $field['options'] ) );
		}

		if ( isset( $field['repeater-fields'] ) && is_array( $field['repeater-fields'] ) ) {
			$field['repeater-fields'] = $this->prepare_meta_fields( $field['repeater-fields'] );
		}

		return $field;
	}

	protected function build_field_from_descriptor( $field ) {
		$name = '';

		if ( isset( $field['name'] ) ) {
			$name = $field['name'];
		} elseif ( isset( $field['field_name'] ) ) {
			$name = $field['field_name'];
		}

		$name = $this->sanitize_field_name( $name );

		if ( ! $name ) {
			return null;
		}

		$type = 'text';

		if ( isset( $field['type'] ) ) {
			$type = sanitize_key( $field['type'] );
		} elseif ( isset( $field['field_type'] ) ) {
			$type = sanitize_key( $field['field_type'] );
		}

		if ( 'relation' === $type ) {
			return null;
		}

		$title = '';

		if ( isset( $field['title'] ) ) {
			$title = sanitize_text_field( $field['title'] );
		} elseif ( isset( $field['label'] ) ) {
			$title = sanitize_text_field( $field['label'] );
		} else {
			$title = $this->humanize( $name );
		}

		$prepared = array(
			'title'         => $title,
			'name'          => $name,
			'object_type'   => 'field',
			'width'         => isset( $field['width'] ) ? sanitize_text_field( $field['width'] ) : '100%',
			'type'          => $type,
			'id'            => isset( $field['id'] ) ? absint( $field['id'] ) : wp_rand( 10000, 99999 ),
			'isNested'      => false,
			'is_nested'     => false,
			'options'       => array(),
			'quick_editable'=> $this->sanitize_bool( isset( $field['quick_editable'] ) ? $field['quick_editable'] : true, true ),
			'is_required'   => $this->sanitize_bool( isset( $field['is_required'] ) ? $field['is_required'] : false ),
			'default'       => $this->sanitize_generic_value( isset( $field['default'] ) ? $field['default'] : '' ),
			'description'   => isset( $field['description'] ) ? sanitize_text_field( $field['description'] ) : '',
			'placeholder'   => isset( $field['placeholder'] ) ? sanitize_text_field( $field['placeholder'] ) : '',
			'args'          => isset( $field['args'] ) && is_array( $field['args'] ) ? $this->sanitize_generic_value( $field['args'] ) : array(),
			'conditions'    => array(),
		);

		if ( isset( $field['conditions'] ) && is_array( $field['conditions'] ) ) {
			$prepared['conditions'] = array_values( array_map( array( $this, 'sanitize_generic_value' ), $field['conditions'] ) );
		}

		if ( 'media' === $type ) {
			$prepared['value_format'] = isset( $field['value_format'] ) ? sanitize_text_field( $field['value_format'] ) : 'id';
		}

		if ( in_array( $type, array( 'date', 'datetime', 'datetime-local', 'time' ), true ) ) {
			$prepared['input_type']   = $type;
			$prepared['autocomplete'] = 'off';
			$prepared['is_timestamp'] = $this->sanitize_bool( isset( $field['is_timestamp'] ) ? $field['is_timestamp'] : true, true );
		}

		if ( 'repeater' === $type ) {
			$prepared['repeater-fields'] = $this->prepare_meta_fields( isset( $field['fields'] ) ? $field['fields'] : array() );
		} else {
			$prepared['repeater-fields'] = array();
		}

		if ( 'taxonomy' === $type ) {
			$prepared['taxonomy']     = isset( $field['taxonomy'] ) ? sanitize_key( $field['taxonomy'] ) : '';
			$prepared['appearance']   = isset( $field['appearance'] ) ? sanitize_key( $field['appearance'] ) : 'select';
			$prepared['multiple']     = $this->sanitize_bool( isset( $field['multiple'] ) ? $field['multiple'] : false );
			$prepared['allow_null']   = $this->sanitize_bool( isset( $field['allow_null'] ) ? $field['allow_null'] : false );
			$prepared['return_value'] = isset( $field['return_value'] ) ? sanitize_key( $field['return_value'] ) : 'term_id';
			$prepared['load_terms']   = $this->sanitize_bool( isset( $field['load_terms'] ) ? $field['load_terms'] : false );
			$prepared['save_terms']   = $this->sanitize_bool( isset( $field['save_terms'] ) ? $field['save_terms'] : false );
		}

		$prepared = $this->maybe_apply_manual_options( $prepared, $field );

		return $prepared;
	}

	protected function maybe_apply_manual_options( $prepared, $source ) {
		$type = isset( $prepared['type'] ) ? $prepared['type'] : 'text';

		if ( ! in_array( $type, array( 'select', 'checkbox', 'radio' ), true ) ) {
			if ( 'taxonomy' === $type ) {
				return $prepared;
			}

			if ( empty( $prepared['options_source'] ) ) {
				$prepared['options_source'] = isset( $source['options_source'] ) ? sanitize_key( $source['options_source'] ) : 'manual';
			}

			return $prepared;
		}

		$options = array();

		if ( isset( $source['options'] ) ) {
			$options = $source['options'];
		} elseif ( isset( $source['choices'] ) ) {
			$options = $source['choices'];
		}

		if ( is_string( $options ) && '' !== trim( $options ) ) {
			$prepared['options_source'] = 'manual_bulk';
			$prepared['bulk_options']   = $options;
		} elseif ( is_array( $options ) && ! empty( $options ) ) {
			$prepared['options_source'] = 'manual_bulk';
			$prepared['bulk_options']   = $this->prepare_bulk_options( $options );
		} else {
			$prepared['options_source'] = isset( $source['options_source'] ) ? sanitize_key( $source['options_source'] ) : 'manual';
		}

		if ( 'checkbox' === $type ) {
			$prepared['is_array'] = true;
		}

		if ( 'select' === $type && isset( $source['is_multiple'] ) ) {
			$prepared['is_multiple'] = $this->sanitize_bool( $source['is_multiple'] );

			if ( ! empty( $prepared['is_multiple'] ) ) {
				$prepared['is_array'] = true;
			}
		}

		if ( isset( $prepared['options_source'] ) && 'taxonomy' === $prepared['options_source'] ) {
			$prepared['taxonomy']     = isset( $source['taxonomy'] ) ? sanitize_key( $source['taxonomy'] ) : '';
			$prepared['return_value'] = isset( $source['return_value'] ) ? sanitize_key( $source['return_value'] ) : 'term_id';
			$prepared['terms_orderby']    = isset( $source['terms_orderby'] ) ? sanitize_key( $source['terms_orderby'] ) : '';
			$prepared['terms_order']      = isset( $source['terms_order'] ) ? sanitize_text_field( $source['terms_order'] ) : '';
		}

		return $prepared;
	}

	protected function prepare_bulk_options( $options ) {
		$prepared = array();

		foreach ( $options as $value => $label ) {
			if ( is_array( $label ) ) {
				$option_value = isset( $label['value'] ) ? $label['value'] : ( isset( $label['label'] ) ? $label['label'] : '' );
				$option_label = isset( $label['label'] ) ? $label['label'] : $option_value;
				$is_checked   = ! empty( $label['is_checked'] );
			} else {
				$option_value = is_int( $value ) ? $label : $value;
				$option_label = $label;
				$is_checked   = false;
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

			$row = $option_value . '::' . $option_label;

			if ( $is_checked ) {
				$row .= '::checked';
			}

			$prepared[] = $row;
		}

		return implode( PHP_EOL, $prepared );
	}

	protected function sanitize_user_screen( $value ) {
		$value = sanitize_key( $value );

		if ( in_array( $value, array( 'edit', 'edit-profile' ), true ) ) {
			return $value;
		}

		return '';
	}

	protected function sanitize_slug_array( $values ) {
		if ( empty( $values ) || ! is_array( $values ) ) {
			return array();
		}

		$result = array();

		foreach ( $values as $value ) {
			$value = sanitize_key( $value );

			if ( '' === $value ) {
				continue;
			}

			$result[] = $value;
		}

		return array_values( array_unique( $result ) );
	}

	protected function sanitize_post_ids( $values ) {
		if ( empty( $values ) || ! is_array( $values ) ) {
			return array();
		}

		$result = array();

		foreach ( $values as $value ) {
			$value = absint( $value );

			if ( $value ) {
				$result[] = $value;
			}
		}

		return array_values( array_unique( $result ) );
	}

	protected function sanitize_terms_array( $values ) {
		if ( empty( $values ) || ! is_array( $values ) ) {
			return array();
		}

		$result = array();

		foreach ( $values as $value ) {
			if ( is_numeric( $value ) ) {
				$result[] = absint( $value );
			} else {
				$result[] = sanitize_text_field( (string) $value );
			}
		}

		return array_values( array_unique( $result ) );
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

	protected function sanitize_field_name( $name ) {
		$name = trim( (string) $name );

		if ( '' === $name ) {
			return '';
		}

		$name = str_replace( array( ' ', '-' ), '_', $name );

		return sanitize_key( $name );
	}

	protected function sanitize_generic_value( $value ) {
		if ( is_array( $value ) ) {
			$result = array();

			foreach ( $value as $key => $item ) {
				$result[ $key ] = $this->sanitize_generic_value( $item );
			}

			return $result;
		}

		if ( is_bool( $value ) || is_int( $value ) || is_float( $value ) ) {
			return $value;
		}

		if ( is_string( $value ) ) {
			return sanitize_text_field( $value );
		}

		return $value;
	}

	protected function humanize( $string ) {
		$string = str_replace( array( '-', '_' ), ' ', $string );
		$string = trim( $string );

		return $string ? ucwords( $string ) : '';
	}

	protected function get_object_type_enum() {
		if ( empty( jet_engine()->meta_boxes ) ) {
			return array( 'post', 'taxonomy', 'user' );
		}

		$sources = jet_engine()->meta_boxes->get_sources();
		$values  = array();

		foreach ( $sources as $source ) {
			if ( empty( $source['value'] ) ) {
				continue;
			}

			$values[] = sanitize_key( $source['value'] );
		}

		$values = array_values( array_unique( array_filter( $values ) ) );

		if ( empty( $values ) ) {
			$values = array( 'post', 'taxonomy', 'user' );
		}

		return $values;
	}
}
