<?php
namespace Jet_Engine\MCP_Tools;

class Feature_Add_Glossary {

	public function __construct() {
		Registry::instance()->add_feature( 'add-glossary', array(
			'type'               => 'tool',
			'label'              => 'Add Glossary',
			'description'        => 'Register a new JetEngine glossary with manually provided options. Always include at least one field object with both value and label properties. Only manual glossaries are supported by this tool.',
			'input_schema'       => array(
				'name'   => array(
					'type'        => 'string',
					'description' => 'The human-readable name of the glossary.',
				),
				'source' => array(
					'type'        => 'string',
					'description' => 'The data source for the glossary. Only the "manual" source is supported when using this tool.',
					'enum'        => array( 'manual' ),
				),
				'fields' => array(
					'type'        => 'array',
					'description' => 'A list of glossary items. Each item must define both value and label. Up to 100 entries is recommended for optimal performance.',
					'items'       => array(
						'type'       => 'object',
						'required'   => array( 'value', 'label' ),
						'properties' => array(
							'value'      => array(
								'type'        => 'string',
								'description' => 'The stored value of the glossary option. Must be unique within the glossary whenever possible. Important - all values should contain only lowercase Latin letters, numbers and "-" or "_" chars, without spaces and other special characters',
							),
							'label'      => array(
								'type'        => 'string',
								'description' => 'The human-friendly label displayed for this option.',
							),
							'is_checked' => array(
								'type'        => 'boolean',
								'description' => 'Optional. Mark as true to keep the option pre-selected by default.',
							),
						),
					),
				),
			),
			'output_schema'      => array(
				'success'               => array(
					'type'        => 'boolean',
					'description' => 'Indicates whether the glossary was created successfully.',
				),
				'item_id'               => array(
					'type'        => 'integer',
					'description' => 'The ID of the newly created glossary.',
				),
				'glossary_settings_url' => array(
					'type'        => 'string',
					'description' => 'URL to manage the glossary inside the WordPress dashboard.',
				),
				'notices'               => array(
					'type'        => 'array',
					'description' => 'Any admin notices generated during the creation process.',
					'items'       => array(
						'type' => 'string',
					),
				),
			),
			'execute_callback'   => array( $this, 'callback' ),
		) );
	}

	public function callback( $input = array() ) {

		if ( ! isset( jet_engine()->glossaries ) || ! isset( jet_engine()->glossaries->data ) ) {
			return new \WP_Error(
				'glossaries_unavailable',
				esc_html__( 'JetEngine glossaries component is not available.', 'jet-engine' )
			);
		}

		$name = ! empty( $input['name'] ) ? sanitize_text_field( wp_unslash( $input['name'] ) ) : '';

		if ( '' === $name ) {
			return new \WP_Error(
				'invalid_glossary_name',
				esc_html__( 'The glossary name is required to create a glossary.', 'jet-engine' )
			);
		}

		$source = ! empty( $input['source'] ) ? sanitize_key( $input['source'] ) : 'manual';

		if ( 'manual' !== $source ) {
			return new \WP_Error(
				'unsupported_glossary_source',
				esc_html__( 'Only the manual glossary source is supported by this tool.', 'jet-engine' )
			);
		}

		$fields = $this->prepare_fields( $input );

		if ( empty( $fields ) ) {
			return new \WP_Error(
				'invalid_glossary_fields',
				esc_html__( 'Provide at least one glossary item with both value and label.', 'jet-engine' )
			);
		}

		$request = array(
			'name'   => $name,
			'source' => 'manual',
			'fields' => $fields,
		);

		jet_engine()->glossaries->data->set_request( $request );

		$item_id = jet_engine()->glossaries->data->create_item( false );

		$notices = $this->extract_notices( jet_engine()->glossaries->get_notices() );

		if ( method_exists( jet_engine()->glossaries->data, 'clear_cache' ) ) {
			jet_engine()->glossaries->data->clear_cache();
		}

		return array(
			'success'               => ! empty( $item_id ),
			'item_id'               => $item_id,
			'glossary_settings_url' => admin_url( 'admin.php?page=jet-engine#glossaries' ),
			'notices'               => $notices,
		);
	}

	protected function prepare_fields( $input ) {

		$fields     = array();
		$raw_fields = isset( $input['fields'] ) && is_array( $input['fields'] ) ? $input['fields'] : array();

		foreach ( $raw_fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$value = isset( $field['value'] ) ? $this->sanitize_field_value( $field['value'] ) : '';
			$label = isset( $field['label'] ) ? $this->sanitize_field_value( $field['label'] ) : '';

			if ( '' === $value || '' === $label ) {
				continue;
			}

			// Ensure value contains only allowed characters
			$value = preg_replace( '/[^a-z0-9\-_]/', '', strtolower( $value ) );

			$fields[] = array(
				'value'      => $value,
				'label'      => $label,
				'is_checked' => isset( $field['is_checked'] ) ? filter_var( $field['is_checked'], FILTER_VALIDATE_BOOLEAN ) : false,
			);
		}

		return $fields;
	}

	protected function sanitize_field_value( $value ) {

		if ( is_bool( $value ) ) {
			$value = $value ? '1' : '0';
		} elseif ( is_float( $value ) || is_int( $value ) ) {
			$value = (string) $value;
		} elseif ( is_array( $value ) || is_object( $value ) ) {
			return '';
		}

		return sanitize_text_field( wp_unslash( $value ) );
	}

	protected function extract_notices( $notices ) {

		if ( empty( $notices ) || ! is_array( $notices ) ) {
			return array();
		}

		return array_values( array_filter( array_map( function( $notice ) {
			if ( is_array( $notice ) && ! empty( $notice['message'] ) ) {
				return $notice['message'];
			}

			if ( is_string( $notice ) ) {
				return $notice;
			}

			return null;
		}, $notices ) ) );
	}
}
