<?php
namespace Jet_Engine\MCP_Tools;

class Feature_Manage_Modules {

	public function __construct() {
		Registry::instance()->add_feature( 'manage-modules', array(
			'type'               => 'tool',
			'label'              => 'Manage JetEngine Modules',
			'description'        => 'List active modules or activate/deactivate JetEngine modules.',
			'input_schema'       => array(
				'operation' => array(
					'type'        => 'string',
					'description' => 'Operation to perform: list active modules, activate modules, or deactivate modules.',
					'enum'        => array( 'list', 'activate', 'deactivate' ),
					'default'     => 'list',
				),
				'modules' => array(
					'type'        => 'array',
					'description' => 'Module slugs to activate or deactivate when the operation requires it.',
					'items'       => array(
						'type' => 'string',
					),
				),
			),
			'output_schema'      => array(
				'success'         => array(
					'type'        => 'boolean',
					'description' => 'Indicates whether the requested operation was performed successfully.',
				),
				'active_modules'  => array(
					'type'        => 'array',
					'description' => 'The list of currently active JetEngine modules.',
					'items'       => array(
						'type' => 'object',
						'properties' => array(
							'slug' => array(
								'type'        => 'string',
								'description' => 'The slug of the active module.',
							),
							'name' => array(
								'type'        => 'string',
								'description' => 'The human-readable name of the active module.',
							),
							'description' => array(
								'type'        => 'string',
								'description' => 'A brief description of the active module.',
							),
						),
					),
				),
				'all_modules'     => array(
					'type'        => 'array',
					'description' => 'The list of all available JetEngine modules.',
					'items'       => array(
						'type' => 'object',
						'properties' => array(
							'slug' => array(
								'type'        => 'string',
								'description' => 'The slug of the module.',
							),
							'name' => array(
								'type'        => 'string',
								'description' => 'The human-readable name of the module.',
							),
							'description' => array(
								'type'        => 'string',
								'description' => 'A brief description of the module.',
							),
						),
					),
				),
				'updated_modules' => array(
					'type'        => 'array',
					'description' => 'Modules that were changed during the operation.',
					'items'       => array(
						'type' => 'string',
					),
				),
				'invalid_modules' => array(
					'type'        => 'array',
					'description' => 'Modules that were requested but not recognized.',
					'items'       => array(
						'type' => 'string',
					),
				),
				'message'         => array(
					'type'        => 'string',
					'description' => 'Additional information about the operation result.',
				),
			),
			'execute_callback'  => array( $this, 'callback' ),
		) );
	}

	/**
	 * Execute the manage modules operation.
	 *
	 * @param array $input The input data for the feature.
	 * @return array|\WP_Error
	 */
	public function callback( $input = array() ) {

		$modules_manager = jet_engine()->modules;

		if ( ! $modules_manager ) {
			return new \WP_Error(
				'jet-engine-mcp-manage-modules-missing-manager',
				esc_html__( 'JetEngine modules manager is not available.', 'jet-engine' )
			);
		}

		$operation          = isset( $input['operation'] ) ? sanitize_key( $input['operation'] ) : 'list';
		$allowed_operations = array( 'list', 'activate', 'deactivate' );

		if ( ! in_array( $operation, $allowed_operations, true ) ) {
			return new \WP_Error(
				'jet-engine-mcp-manage-modules-invalid-operation',
				esc_html__( 'Invalid operation requested. Allowed operations are list, activate, or deactivate.', 'jet-engine' )
			);
		}

		if ( 'list' === $operation ) {
			return $this->format_response(
				true,
				$modules_manager->get_active_modules(),
				array(),
				array(),
				esc_html__( 'Active modules fetched successfully.', 'jet-engine' )
			);
		}

		$modules           = $this->prepare_modules_input( $input );
		$available_modules = array_keys( $modules_manager->get_all_modules() );
		$updated_modules   = array();
		$invalid_modules   = array();
		$active_modules    = $modules_manager->get_active_modules();

		if ( empty( $modules ) ) {
			return new \WP_Error(
				'jet-engine-mcp-manage-modules-empty-list',
				esc_html__( 'No modules were provided for this operation.', 'jet-engine' )
			);
		}

		foreach ( $modules as $module ) {

			if ( ! in_array( $module, $available_modules, true ) ) {
				$invalid_modules[] = $module;
				continue;
			}

			if ( 'activate' === $operation ) {

				if ( in_array( $module, $active_modules, true ) ) {
					continue;
				}

				$modules_manager->activate_module( $module );
				$modules_manager->init_module( $module );

				$active_modules[]  = $module;
				$updated_modules[] = $module;

				continue;
			}

			if ( ! in_array( $module, $active_modules, true ) ) {
				continue;
			}

			$modules_manager->deactivate_module( $module );
			$active_modules = array_values( array_diff( $active_modules, array( $module ) ) );
			$updated_modules[] = $module;

			$this->remove_module_from_runtime( $modules_manager, $module );
		}

		$success = ! empty( $updated_modules );
		$message = $this->build_message( $operation, $updated_modules, $invalid_modules, $success );

		return $this->format_response(
			$success,
			$modules_manager->get_active_modules(),
			$updated_modules,
			$invalid_modules,
			$message
		);
	}

	/**
	 * Normalize modules input to a clean list of slugs.
	 *
	 * @param array $input Raw input data.
	 * @return array
	 */
	protected function prepare_modules_input( $input ) {

		$modules = isset( $input['modules'] ) ? $input['modules'] : array();

		if ( is_string( $modules ) ) {
			$modules = array( $modules );
		}

		if ( ! is_array( $modules ) ) {
			return array();
		}

		$result = array();

		foreach ( $modules as $module ) {

			if ( ! is_string( $module ) || '' === $module ) {
				continue;
			}

			$module = sanitize_key( $module );

			if ( '' === $module ) {
				continue;
			}

			$result[] = $module;
		}

		return array_values( array_unique( $result ) );
	}

	/**
	 * Adjust the runtime list of active modules after deactivation.
	 *
	 * @param object $modules_manager JetEngine modules manager instance.
	 * @param string $module          Module slug to remove.
	 * @return void
	 */
	protected function remove_module_from_runtime( $modules_manager, $module ) {

		try {
			$reflection = new \ReflectionClass( $modules_manager );

			if ( ! $reflection->hasProperty( 'active_modules' ) ) {
				return;
			}

			$property = $reflection->getProperty( 'active_modules' );
			$property->setAccessible( true );

			$current = $property->getValue( $modules_manager );

			if ( ! is_array( $current ) ) {
				return;
			}

			$index = array_search( $module, $current, true );

			if ( false === $index ) {
				return;
			}

			unset( $current[ $index ] );
			$property->setValue( $modules_manager, array_values( $current ) );

		} catch ( \ReflectionException $exception ) {
			// Silence reflection issues.
		}
	}

	/**
	 * Build a human readable message about the operation result.
	 *
	 * @param string $operation       Operation type.
	 * @param array  $updated_modules Updated modules list.
	 * @param array  $invalid_modules Invalid modules list.
	 * @param bool   $success         Operation success flag.
	 * @return string
	 */
	protected function build_message( $operation, $updated_modules, $invalid_modules, $success ) {

		$messages = array();

		if ( $success ) {
			if ( 'activate' === $operation ) {
				$messages[] = esc_html__( 'The requested modules have been activated.', 'jet-engine' );
			} else {
				$messages[] = esc_html__( 'The requested modules have been deactivated.', 'jet-engine' );
			}
		} else {
			if ( 'activate' === $operation ) {
				$messages[] = esc_html__( 'All provided modules are already active.', 'jet-engine' );
			} else {
				$messages[] = esc_html__( 'All provided modules are already inactive.', 'jet-engine' );
			}
		}

		if ( ! empty( $invalid_modules ) ) {
			$messages[] = sprintf(
				esc_html__( 'Unknown modules: %s.', 'jet-engine' ),
				esc_html( implode( ', ', $invalid_modules ) )
			);
		}

		return implode( ' ', $messages );
	}

	/**
	 * Format the response payload.
	 *
	 * @param bool   $success         Operation success flag.
	 * @param array  $active_modules  List of active modules.
	 * @param array  $updated_modules Updated modules list.
	 * @param array  $invalid_modules Invalid modules list.
	 * @param string $message         Human readable message.
	 * @return array
	 */
	protected function format_response( $success, $active_modules, $updated_modules, $invalid_modules, $message ) {

		return array(
			'success'         => (bool) $success,
			'active_modules'  => $this->explode_modules_list( array_values( $active_modules ) ),
			'all_modules'     => $this->explode_modules_list(),
			'updated_modules' => array_values( $updated_modules ),
			'invalid_modules' => array_values( $invalid_modules ),
			'message'         => $message,
		);
	}

	/**
	 * Convert module slugs to detailed module info.
	 *
	 * @param array $modules List of module slugs.
	 * @return array
	 */
	protected function explode_modules_list( $modules = array() ) {

		$prepared_modules = array();

		if ( empty( $modules ) ) {
			$all_modules = jet_engine()->modules->get_all_modules();
			$modules     = array_keys( $all_modules );
		}

		foreach ( $modules as $module_slug ) {

			$module_instance = jet_engine()->modules->get_module( $module_slug );

			if ( ! $module_instance ) {
				continue;
			}

			// Keep only internal modules
			if ( $module_instance->external_slug() ) {
				continue;
			}

			$prepared_modules[] = array(
				'slug'        => $module_instance->module_id(),
				'name'        => $module_instance->module_name(),
				'description' => $module_instance->get_module_details(),
			);
		}

		return $prepared_modules;
	}
}
