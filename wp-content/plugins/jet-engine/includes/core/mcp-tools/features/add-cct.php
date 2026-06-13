<?php
namespace Jet_Engine\MCP_Tools;

class Feature_Add_CCT {

	public function __construct() {
		Registry::instance()->add_feature( 'add-cct', array(
			'type'               => 'tool',
			'label'              => 'Add Custom Content Type',
			'description'        => 'This feature allows you to register a new Custom Content Type. Custom Content Type is a performant content storage, which can replace Custom Post Types for some cases. All the Custom Content Type data stored in the separate DB table, so it`s an efficient way to store large amounts of the information.',
			'input_schema'       => array(
				'name' => array(
					'type'        => 'string',
					'description' => 'The name of the Custom Content Type.',
				),
				'slug' => array(
					'type'        => 'string',
					'description' => 'The slug of the Custom Content Type. Should contain only lowercase letters and underscores.',
				),
				'fields' => array(
					'type'        => 'array',
					'description' => 'The fields of the Custom Content Type. Each field should have a name, type, is_key_field flag (yes/no) and optionally options for select, checkbox, and radio types.',
					'items'      => array(
						'type'       => 'object',
						'properties' => array(
							'field_name' => array(
								'type'        => 'string',
								'description' => 'The name of the field. Separate words with underscores.',
							),
							'is_key_field' => array(
								'type'        => 'string',
								'description' => 'Is the key field for describing the Custom Content Type item or not. Mark as key fields only most important Content Type fields',
								'enum'        => array( 'yes', 'no' ),
							),
							'field_type' => array(
								'type'        => 'string',
								'description' => 'The type of the field.',
								'enum'       => array( 'text', 'number', 'date', 'datetime', 'media', 'checkbox', 'select', 'radio', 'textarea' ),
							),
							'save_as_timestamp' => array(
								'type'        => 'boolean',
								'description' => 'Whether to save the date/datetime field as a timestamp. Applicable only for date and datetime field types.',
							),
							'value_format' => array(
								'type'        => 'string',
								'description' => 'For the media field type, specifies the format in which the value is stored in the DB (id - the attachment ID, url - the attachment URL, both - array with both ID and URL).',
								'enum'       => array( 'id', 'url', 'both' ),
							),
							'options' => array(
								'type'        => 'array',
								'description' => 'The options for the fields with select, checkbox and radio types.',
								'items'       => array( 'type' => 'string' ),
							),
						),
					),
				),
			),
			'output_schema'      => array(
				'success' => array(
					'type'        => 'boolean',
					'description' => 'Indicates whether the Custom Content Type was successfully created.',
				),
				'item_id' => array(
					'type'        => 'integer',
					'description' => 'The ID of the newly created Custom Content Type.',
				),
				'cct_settings_url' => array(
					'type'        => 'string',
					'description' => 'The URL to the settings page of the newly created Custom Content Type in the WordPress admin.',
				),
				'cct_items_list_url' => array(
					'type'        => 'string',
					'description' => 'The URL to the list of items for the newly created Custom Content Type in the WordPress admin.',
				),
				'notices' => array(
					'type'        => 'array',
					'description' => 'An array of notices generated during the creation process.',
					'items'       => array( 'type' => 'string' ),
				),
				'next_tool' => array(
					'type'        => 'string',
					'description' => 'The identifier of the next recommended tool to use after creating the Custom Content Type.',
				),
			),
			'execute_callback'  => [ $this, 'callback' ],
		) );
	}

	/**
	 * Callback to execute the feature.
	 *
	 * @param array $input The input data for the feature.
	 */
	public function callback( $input = [] ) {

		// check if CCT module is active, if not - activate
		if ( ! jet_engine()->modules->is_module_active( 'custom-content-types' ) ) {
			jet_engine()->modules->activate_module( 'custom-content-types' );
		}

		$cct_module = \Jet_Engine\Modules\Custom_Content_Types\Module::instance();

		$name   = isset( $input['name'] ) ? $input['name'] : '';
		$slug   = isset( $input['slug'] ) ? $input['slug'] : '';
		$fields = isset( $input['fields'] ) ? $input['fields'] : array();

		$args = [];
		$args['admin_columns'] = $this->get_admin_columns( $fields );

		$cct_module->manager->data->set_request( array(
			'name'        => $name,
			'slug'        => $slug,
			'args'        => $args,
			'meta_fields' => $this->prepare_meta_fields( $fields ),
		) );

		$item_id = $cct_module->manager->data->create_item( false );

		return array(
			'success' => ! empty( $item_id ),
			'item_id' => $item_id,
			'cct_settings_url' => admin_url( 'admin.php?page=jet-engine-cct&cct_action=edit&id=' . $item_id ),
			'cct_items_list_url' => admin_url( 'admin.php?page=jet-cct-' . $slug ),
			'notices' => $cct_module->manager->get_notices(),
			'next_tool' => 'tool-crocoblock/add-query',
		);
	}

	/**
	 * Get the admin columns for the Custom Content Type.
	 *
	 * @param array $fields The fields of the Custom Content Type.
	 * @return array The admin columns.
	 */
	public function get_admin_columns( $fields ) {

		$admin_columns = array(
			'_ID' => array (
				'enabled' => true,
				'is_sortable' => true,
				'is_num' => true,
				'prefix' => '#',
			),
			'cct_single_post_id' => array (
				'enabled' => false,
				'is_sortable' => false,
				'is_num' => false,
			),
			'cct_author_id' => array (
				'enabled' => false,
				'is_sortable' => false,
				'is_num' => false,
			),
			'cct_created' => array (
				'enabled' => false,
				'is_sortable' => false,
				'is_num' => false,
			),
			'cct_modified' => array (
				'enabled' => false,
				'is_sortable' => false,
				'is_num' => false,
			),
			'cct_status' =>  array (
				'enabled' => false,
				'is_sortable' => false,
				'is_num' => false,
			),
		);
		foreach ( $fields as $field ) {

			$add_column = ! empty( $field['is_key_field'] ) ? $field['is_key_field'] : false;
			$add_column = filter_var( $add_column, FILTER_VALIDATE_BOOLEAN );
			$field_type = ! empty( $field['field_type'] ) ? $field['field_type'] : 'text';
			$is_num     = in_array( $field_type, array( 'number', 'date', 'datetime', 'time' ), true );

			if ( ! empty( $field['field_name'] ) ) {
				$admin_columns[ $this->get_field_name( $field ) ] = array(
					'enabled'     => $add_column,
					'is_sortable' => $is_num,
					'is_num'      => $is_num,
				);
			}
		}

		return $admin_columns;
	}

	/**
	 * Get the formatted field name by the raw input.
	 *
	 * @param array $field The field data.
	 * @return string The sanitized field name.
	 */
	public function get_field_name( $field ) {

		$field['field_name'] = str_replace( [ ' ', '-' ], '_', $field['field_name'] );
		$name = sanitize_key( $field['field_name'] );

		return $name;
	}

	/**
	 * Prepares the meta fields for the Custom Content Type.
	 *
	 * @param array $fields The fields of the Custom Content Type.
	 * @return array The prepared meta fields.
	 */
	public function prepare_meta_fields( $fields ) {

		$meta_fields = array();
		foreach ( $fields as $field ) {
			if ( ! empty( $field['field_name'] ) ) {

				$name = $this->get_field_name( $field );
				$bulk_options = '';
				$options_source = 'manual_bulk';

				if (
					! empty( $field['options'] )
					&& is_array( $field['options'] )
					&& in_array( $field['field_type'], array( 'select', 'checkbox', 'radio' ), true )
				) {
						$glue = '';
						foreach ( $field['options'] as $option ) {
							$option = trim( $option );
							$bulk_options .= $glue . $option;
							$glue = "\n";
						}
				}

				$meta_fields[] = array(
					'title' => ucfirst( str_replace( '_', ' ', $name ) ),
					'name' => $name,
					'object_type' => 'field',
					'width' => '100%',
					'options' => array(),
					'bulk_options' => $bulk_options,
					'options_source' => $options_source,
					'is_timestamp' => ! empty( $field['save_as_timestamp'] ) ? filter_var( $field['save_as_timestamp'], FILTER_VALIDATE_BOOLEAN ) : false,
					'value_format' => ! empty( $field['value_format'] ) ? $field['value_format'] : 'id',
					'repeater-fields' => array(),
					'type' => ! empty( $field['field_type'] ) ? $field['field_type'] : 'text',
					'id' => rand( 1000, 9999 ),
					'isNested' => false,
					'quick_editable' => true,
				);
			}
		}

		return $meta_fields;
	}
}