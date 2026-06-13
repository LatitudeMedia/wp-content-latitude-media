<?php
namespace Jet_Engine\MCP_Tools;

class Feature_Get_Macros {

	public function __construct() {
		Registry::instance()->add_feature( 'get-macros', array(
			'type' => 'resource',
			'label' => 'Get JetEngine Macros',
			'description' => 'Retrieve all JetEngine macros with their arguments and usage examples for AI assisted tooling.',
			'input_schema' => array(
				'exclude' => array(
					'type' => 'array',
					'description' => 'Optional list of macro IDs (tags) to exclude from the response.',
					'items' => array(
						'type' => 'string',
					),
				),
			),
			'output_schema' => array(
				'success' => array(
					'type' => 'boolean',
					'description' => 'Whether the macros information was gathered successfully.',
				),
				'macros' => array(
					'type' => 'array',
					'description' => 'Collection of JetEngine macros available in the current environment.',
					'items' => array(
						'type' => 'object',
						'properties' => array(
							'id' => array(
								'type' => 'string',
								'description' => 'Unique macros identifier (tag).',
							),
							'label' => array(
								'type' => 'string',
								'description' => 'Human readable macros name.',
							),
							'arguments' => array(
								'type' => 'array',
								'description' => 'Arguments supported by the macros, listed in the order they should be provided.',
								'items' => array(
									'type' => 'object',
									'properties' => array(
										'key' => array(
											'type' => 'string',
											'description' => 'Argument key.',
										),
										'label' => array(
											'type' => 'string',
											'description' => 'Human readable argument label.',
										),
										'type' => array(
											'type' => 'string',
											'description' => 'Expected argument input type.',
										),
										'description' => array(
											'type' => 'string',
											'description' => 'Additional details that describe the argument.',
										),
										'default' => array(
											'type' => 'string',
											'description' => 'Default value that will be used when the argument is omitted.',
										),
										'options' => array(
											'type' => 'array',
											'description' => 'Flat list of selectable options available for the argument.',
											'items' => array(
												'type' => 'object',
												'properties' => array(
													'value' => array(
														'type' => 'string',
														'description' => 'Option value that should be inserted into the macros string.',
													),
													'label' => array(
														'type' => 'string',
														'description' => 'Human readable option label.',
													),
												),
											),
										),
										'groups' => array(
											'type' => 'array',
											'description' => 'Grouped options when the argument exposes nested option collections.',
											'items' => array(
												'type' => 'object',
												'properties' => array(
													'label' => array(
														'type' => 'string',
														'description' => 'Group label.',
													),
													'options' => array(
														'type' => 'array',
														'items' => array(
															'type' => 'object',
															'properties' => array(
																'value' => array(
																	'type' => 'string',
																),
																'label' => array(
																	'type' => 'string',
																),
															),
														),
												),
											),
										),
											),
										),
								),
							),
							'example' => array(
								'type' => 'string',
								'description' => 'Example macros placeholder showing the expected usage.',
							),
						),
					),
				),
			),
			'execute_callback' => array( $this, 'callback' ),
		) );
	}

	public function callback( $input = array() ) {

		if ( ! function_exists( 'jet_engine' ) ) {
			return new \WP_Error(
				'jet_engine_unavailable',
				esc_html__( 'JetEngine is not initialized.', 'jet-engine' )
			);
		}

		$jet_engine = jet_engine();

		if ( empty( $jet_engine->listings ) || empty( $jet_engine->listings->macros ) || ! method_exists( $jet_engine->listings->macros, 'get_all' ) ) {
			return new \WP_Error(
				'jet_engine_macros_unavailable',
				esc_html__( 'JetEngine macros component is not available.', 'jet-engine' )
			);
		}

		$macros_list = $jet_engine->listings->macros->get_all( false, true );

		// Exclude some macros that are not relevant for AI-assisted tooling or legacy.
		$excluded = [
			'query_results',
			'query_count',
			'rel_get_item_meta',
			'related_children_between',
			'related_children_from',
			'related_parents_from',
			'jsf_seo_description',
			'jsf_seo_title',
			'get_users_for_store_item',
			'get_grandparent',
			'get_grandchild',
			'component_control_value',
		];

		if ( ! empty( $input['exclude'] ) && is_array( $input['exclude'] ) ) {
			$excluded = array_merge( $excluded, $this->prepare_filter_list( $input['exclude'] ) );
		}

		$prepared = array();

		foreach ( $macros_list as $id => $data ) {

			if ( in_array( $id, $excluded, true ) ) {
				continue;
			}

			if ( is_array( $data ) ) {
				$arguments = $this->prepare_arguments( isset( $data['args'] ) ? $data['args'] : array() );
				$prepared[] = array(
					'name' => $id,
					'label' => $this->stringify_label( isset( $data['label'] ) ? $data['label'] : $id ),
					'arguments' => $arguments,
					'example' => $this->build_example( $id, $arguments ),
				);
			} else {
				$prepared[] = array(
					'name' => $id,
					'label' => $this->stringify_label( $id ),
					'arguments' => array(),
					'example' => '%' . $id . '%',
				);
			}

		}

		return array(
			'success' => true,
			'macros' => array_values( $prepared ),
		);
	}

	protected function prepare_filter_list( $items ) {
		if ( empty( $items ) ) {
			return array();
		}

		if ( is_string( $items ) ) {
			$items = array( $items );
		}

		if ( ! is_array( $items ) ) {
			return array();
		}

		$items = wp_unslash( $items );
		$items = array_map( array( $this, 'sanitize_tag' ), $items );
		$items = array_filter( $items, function( $value ) {
			return '' !== $value;
		} );

		return array_values( array_unique( $items ) );
	}

	protected function sanitize_tag( $tag ) {
		$tag = is_scalar( $tag ) ? (string) $tag : '';
		$tag = trim( $tag );

		if ( '' === $tag ) {
			return '';
		}

		return preg_replace( '/[^A-Za-z0-9_\\-]/', '', $tag );
	}

	protected function prepare_arguments( $args ) {
		if ( empty( $args ) || ! is_array( $args ) ) {
			return array();
		}

		$prepared = array();

		foreach ( $args as $key => $argument ) {

			if ( ! is_array( $argument ) ) {
				$argument = array();
			}

			$prepared[] = array(
				'key' => $key,
				'label' => $this->stringify_label( isset( $argument['label'] ) ? $argument['label'] : $key ),
				'type' => isset( $argument['type'] ) ? $this->stringify_value( $argument['type'] ) : '',
				'description' => isset( $argument['description'] ) ? $this->stringify_label( $argument['description'] ) : '',
				'default' => isset( $argument['default'] ) ? $this->stringify_value( $argument['default'] ) : '',
				'options' => $this->prepare_options_list( isset( $argument['options'] ) ? $argument['options'] : array() ),
				'groups' => $this->prepare_option_groups( isset( $argument['groups'] ) ? $argument['groups'] : array() ),
			);

		}

		return $prepared;
	}

	protected function prepare_options_list( $options ) {
		if ( empty( $options ) ) {
			return array();
		}

		if ( is_callable( $options ) ) {
			$options = call_user_func( $options );
		}

		if ( ! is_array( $options ) ) {
			return array();
		}

		$result = array();

		if ( $this->is_associative( $options ) ) {
			foreach ( $options as $value => $label ) {
				if ( is_array( $label ) && isset( $label['value'], $label['label'] ) ) {
					$result[] = array(
						'value' => $this->stringify_value( $label['value'] ),
						'label' => $this->stringify_label( $label['label'] ),
					);
				} elseif ( is_scalar( $label ) ) {
					$result[] = array(
						'value' => $this->stringify_value( $value ),
						'label' => $this->stringify_label( $label ),
					);
				}
			}
		} else {
			foreach ( $options as $option ) {
				if ( is_array( $option ) && isset( $option['value'], $option['label'] ) ) {
					$result[] = array(
						'value' => $this->stringify_value( $option['value'] ),
						'label' => $this->stringify_label( $option['label'] ),
					);
				} elseif ( is_scalar( $option ) ) {
					$result[] = array(
						'value' => $this->stringify_value( $option ),
						'label' => $this->stringify_label( $option ),
					);
				}
			}
		}

		return $result;
	}

	protected function prepare_option_groups( $groups ) {
		if ( empty( $groups ) ) {
			return array();
		}

		if ( is_callable( $groups ) ) {
			$groups = call_user_func( $groups );
		}

		if ( ! is_array( $groups ) ) {
			return array();
		}

		$result = array();

		foreach ( $groups as $group ) {

			if ( is_string( $group ) ) {
				$result[] = array(
					'label' => '',
					'options' => array(
						array(
							'value' => '',
							'label' => $this->stringify_label( $group ),
						),
					),
				);
				continue;
			}

			if ( ! is_array( $group ) ) {
				continue;
			}

			$label = isset( $group['label'] ) ? $this->stringify_label( $group['label'] ) : '';
			$raw_options = array();

			if ( isset( $group['options'] ) && is_array( $group['options'] ) ) {
				$raw_options = $group['options'];
			} elseif ( isset( $group['values'] ) && is_array( $group['values'] ) ) {
				$raw_options = $group['values'];
			}

			$result[] = array(
				'label' => $label,
				'options' => $this->prepare_options_list( $raw_options ),
			);
		}

		return $result;
	}

	protected function build_example( $id, $arguments ) {
		if ( empty( $arguments ) ) {
			return '%' . $id . '%';
		}

		$values = array();

		foreach ( $arguments as $argument ) {
			$placeholder = $argument['default'];

			if ( '' === $placeholder ) {
				$placeholder = '{' . $argument['key'] . '}';
			}

			$values[] = $placeholder;
		}

		return '%' . $id . '|' . implode( '|', $values ) . '%';
	}

	protected function stringify_label( $value ) {
		if ( is_string( $value ) ) {
			return wp_strip_all_tags( $value );
		}

		if ( is_scalar( $value ) ) {
			return wp_strip_all_tags( (string) $value );
		}

		return '';
	}

	protected function stringify_value( $value ) {
		if ( is_bool( $value ) ) {
			return $value ? '1' : '0';
		}

		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		return '';
	}

	protected function is_associative( $array ) {
		if ( ! is_array( $array ) || empty( $array ) ) {
			return false;
		}

		return array_keys( $array ) !== range( 0, count( $array ) - 1 );
	}
}
