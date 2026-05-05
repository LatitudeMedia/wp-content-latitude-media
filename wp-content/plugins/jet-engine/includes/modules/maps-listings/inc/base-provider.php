<?php
namespace Jet_Engine\Modules\Maps_Listings;

abstract class Base_Provider {

	public function __construct() {

		add_action( $this->settings_hook(), array( $this, 'settings_fields' ) );
		add_action( 'jet-engine/maps-listing/settings/before-assets', array( $this, 'settings_assets' ) );

		$this->init();

		if ( is_admin() ) {
			$this->admin_init();
		}

	}

	/**
	 * Returns some important information that should be shown in Map Field
	 * @return string
	 */
	public function get_map_field_notice() {
		return '';
	}

	/**
	 * Provider-specific settings fields template
	 *
	 * @return [type] [description]
	 */
	public function settings_fields() {
	}

	/**
	 * Enqueue settings specific assets
	 *
	 * @return [type] [description]
	 */
	public function settings_assets() {
	}

	/**
	 * Custom init
	 *
	 * @return [type] [description]
	 */
	public function init() {
	}

	/**
	 * Custom init
	 *
	 * @return [type] [description]
	 */
	public function admin_init() {
	}

	/**
	 * Returns provider system slug
	 *
	 * @return [type] [description]
	 */
	abstract public function get_id();

	/**
	 * Returns provider human-readable name
	 *
	 * @return [type] [description]
	 */
	abstract public function get_label();

}
