<?php
namespace Jet_Engine\MCP_Tools;

class Registry {

	protected $features = [];
	protected $was_registered = false;

	/**
	 * The singleton instance of the Registry.
	 *
	 * @var Registry|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance of the Registry.
	 *
	 * @return Registry
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the registry.
	 */
	public function init() {

		$features_api_enabled = $this->is_features_api_enabled();

		if ( ! $features_api_enabled ) {
			return;
		}

		add_action( 'plugins_loaded', [ $this, 'load' ] );
		add_action( 'rest_api_init', [ $this, 'register_features_api' ] );
	}

	/**
	 * Check if the Features API is enabled in settings.
	 * jet_engine()->misc_settings is not available at the time of Registry initialization,
	 * so we need to check this setting directly in the options table.
	 *
	 * @return bool
	 */
	public function is_features_api_enabled() {

		$misc_settings = get_option( 'jet-engine-misc-settings', [] );
		$misc_settings = $this->ensure_settings( $misc_settings );

		$is_enabled = false;

		if ( ! empty( $misc_settings['enable_features_api'] ) ) {
			$is_enabled = filter_var(
				$misc_settings['enable_features_api'],
				FILTER_VALIDATE_BOOLEAN
			);
		}

		return $is_enabled;
	}

	/**
	 * Load the necessary files and initialize features.
	 *
	 * @return void
	 */
	public function load() {
		do_action( 'qm/start', 'croco-mcp-load' );

		require_once $this->component_path( 'feature.php' );
		$this->load_core_features();

		require_once $this->component_path( 'rest-api/get-controller.php' );
		require_once $this->component_path( 'rest-api/run-controller.php' );
		require_once $this->component_path( 'rest-api/mcp-controller.php' );

		// Enable tool debugger for direct access via URL parameter.
		// Disabled by default for security reasons.
		//add_action( 'init', [ $this, 'tool_debuger' ], 9999 );

		add_filter( 'jet-engine/misc-settings/get-settings', [ $this, 'ensure_settings' ] );
		add_filter( 'jet-engine/dashboard/config', [ $this, 'tools_list_to_dashboard' ] );

		do_action( 'qm/stop', 'croco-mcp-load' );
	}

	/**
	 * Add tools list to dashboard config.
	 *
	 * @param array $config The current dashboard config.
	 * @return array The modified dashboard config.
	 */
	public function tools_list_to_dashboard( $config ) {

		$tools = $this->get_features_array();
		$shortand_tools_list = array_map( function( $tool ) {
			return [
				'label'       => $tool['label'],
				'name'        => $tool['name'],
				'description' => $tool['description'],
			];
		}, $tools );

		$config['mcp_tools'] = $shortand_tools_list;

		return $config;
	}

	/**
	 * Ensure that misc settings have default values.
	 *
	 * @param array $settings The current settings.
	 * @return array The ensured settings.
	 */
	public function ensure_settings( $settings ) {

		$defaults = [
			'enable_mcp_server'   => true,
			'enable_features_api' => true,
		];

		return wp_parse_args( $settings, $defaults );
	}

	/**
	 * Tool debuger for direct access via URL parameter.
	 * Disabled by default for security reasons.
	 *
	 * @return void
	 */
	public function tool_debuger() {

		if ( ! isset( $_GET['jet_mcp_tool_debug'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$tool = sanitize_text_field( wp_unslash( $_GET['jet_mcp_tool_debug'] ) );
		$runner = $this->get_feature( $tool );

		if ( ! $runner ) {
			wp_send_json_error( [ 'message' => 'Tool not found' ] );
		}

		$result = $runner->run();
		var_dump( $result ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		die();
	}

	/**
	 * Get the path to a component file.
	 *
	 * @param string $path The relative path within the component directory.
	 * @return string The full path to the component file.
	 */
	public function component_path( $path = '' ) {
		return jet_engine()->plugin_path( 'includes/core/mcp-tools/' . ltrim( $path, '/' ) );
	}

	/**
	 * Load core features.
	 *
	 * @return void
	 */
	public function load_core_features() {

		require_once $this->component_path( 'features/add-cct.php' );
		require_once $this->component_path( 'features/get-configuration.php' );
		require_once $this->component_path( 'features/get-website-config.php' );
		require_once $this->component_path( 'features/get-macros.php' );
		require_once $this->component_path( 'features/add-glossary.php' );
		require_once $this->component_path( 'features/manage-modules.php' );

		new Feature_Add_CCT();
		new Feature_Get_Configuration();
		new Feature_Get_Website_Config();
		new Feature_Get_Macros();
		new Feature_Add_Glossary();
		new Feature_Manage_Modules();
	}

	/**
	 * Add a feature to the registry.
	 *
	 * @param string $id The unique identifier for the feature.
	 * @param array  $args The arguments for the feature.
	 * @return void
	 */
	public function add_feature( $id, $args = [] ) {
		$this->features[] = new Feature( $id, $args );
	}

	/**
	 * Get the registered features.
	 *
	 * @return Feature[]
	 */
	public function get_features() {

		if ( ! $this->was_registered ) {
			do_action( 'jet-engine/mcp-tools/register-features', $this );
			$this->was_registered = true;
		}

		return $this->features;
	}

	/**
	 * Get a feature by its name.
	 *
	 * @param string $name The full name of the feature (type/id).
	 * @return Feature|null
	 */
	public function get_feature( $name ) {

		foreach ( $this->get_features() as $feature ) {
			if ( $feature->get_name() === $name ) {
				return $feature;
			}
		}

		return null;
	}

	/**
	 * Get the features as an array.
	 *
	 * @param bool $as_mcp_tools Whether to format the output as MCP tools or just as plain array.
	 * @return array
	 */
	public function get_features_array( $as_mcp_tools = false ) {
		return array_map( function( $feature ) use ( $as_mcp_tools ) {
			return $feature->to_array( $as_mcp_tools );
		}, $this->get_features() );
	}

	/**
	 * Register features with the WP Feature API.
	 */
	public function register_features_api() {

		new Rest_API\Get_Controller();
		new Rest_API\Run_Controller();

		if ( jet_engine()->misc_settings->get_settings( 'enable_mcp_server' ) ) {
			new Rest_API\MCP_Controller();
		}
	}
}