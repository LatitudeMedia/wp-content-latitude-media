<?php
/**
 * Interface Builder module
 *
 * Version: 1.0.0
 */
namespace Crocoblock\Agent_UI;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Agent UI module.
 * Registers and renders the Command Center page with the Agent Chat UI.
 *
 * @since 1.0.0
 */
class Module {

	/**
	 * Module directory path.
	 *
	 * @since 1.5.0
	 * @access protected
	 * @var srting.
	 */
	protected $path;

	/**
	 * Module directory URL.
	 *
	 * @since 1.5.0
	 * @access protected
	 * @var srting.
	 */
	protected $url;

	/**
	 * Module version
	 *
	 * @var string
	 */
	protected $version = '1.0.0';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object
	 */
	private static $instance = null;

	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Cherry_Interface_Builder constructor.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->url  = trailingslashit( plugin_dir_url( __FILE__ ) );

		require_once $this->path . 'includes/storage.php';
		require_once $this->path . 'includes/proxi-api.php';

		Proxi_API::instance();
		Proxi_API::instance()->set_current_path( $this->path );

		add_action( 'admin_menu', [ $this, 'register_admin_page' ] );
		add_action( 'in_admin_header', [ $this, 'clear_notices' ] );
	}

	/**
	 * Clear all admin notices to prevent page layou break
	 *
	 * @return void
	 */
	public function clear_notices() {
		if ( ! empty( $_GET['page'] ) && 'jet-command-center' === $_GET['page'] ) {
			remove_all_actions( 'network_admin_notices' );
			remove_all_actions( 'user_admin_notices' );
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Registers the admin page for the Agent UI.
	 *
	 * @return void
	 */
	public function register_admin_page() {
		add_submenu_page(
			'jet-dashboard',
			esc_html__( 'Command Center', 'jet-engine' ),
			esc_html__( 'Command Center', 'jet-engine' ),
			'manage_options',
			'jet-command-center',
			[ $this, 'render_admin_page' ]
		);
	}

	/**
	 * Renders the admin page for the Agent UI.
	 *
	 * @return void
	 */
	public function render_admin_page() {
		$this->assets();
		echo '<div class="wrap"><div id="croco_agent_ui"></div></div>';
	}

	public function assets() {

		$assets_file = $this->path . 'assets/build/agent-ui.asset.php';

		if ( ! file_exists( $assets_file ) ) {
			return;
		}

		$assets = require $assets_file;

		wp_enqueue_script(
			'croco-agent-ui',
			$this->url . 'assets/build/agent-ui.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);

		$rtl = is_rtl() ? '-rtl' : '';

		wp_enqueue_style(
			'croco-agent-ui',
			$this->url . 'assets/build/agent-ui' . $rtl . '.css',
			[ 'wp-components' ],
			$assets['version']
		);

		$api_key = Storage::instance()->get_key();

		wp_localize_script(
			'croco-agent-ui',
			'crocoAgentUI',
			[
				'hasKey'              => ! empty( $api_key ) ? true : false,
				'nonce'               => wp_create_nonce( Proxi_API::NONCE_KEY ),
				'model'               => Storage::instance()->get_model(),
				'api_key_endpoint'    => Proxi_API::instance()->rest_url( 'key' ),
				'models_endpoint'     => Proxi_API::instance()->rest_url( 'models' ),
				'save_model_endpoint' => Proxi_API::instance()->rest_url( 'model' ),
				'base_endpoint'       => Proxi_API::instance()->rest_url( '' ),
				'site_key'            => $this->get_site_key(),
			]
		);
	}

	public function get_site_key() {
		$site_url = get_site_url();
		return substr( md5( $site_url ), 16, 31 );
	}
}
