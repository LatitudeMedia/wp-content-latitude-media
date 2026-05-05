<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Bricks_Views;

use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Package;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Integration {

	public function __construct() {
		add_action( 'init', [ $this, 'register_elements' ], 30 );
	}

	/**
	 * Register elements
	 *
	 * @return void
	 */
	public function register_elements() {

		/**
		 * We can't register elements directly on jet-engine/bricks-views/register-elements
		 * because it is called before the package is loaded.
		 * So we need to check if hook jet-engine/bricks-views/register-elements was called to ensure
		 * that the Bricks views is loaded.
		 */
		if ( ! did_action( 'jet-engine/bricks-views/register-elements' ) ) {
			return;
		}

		$woo_el_file = Package::instance()->package_path( 'listings/bricks-views/elements/woo-data.php' );
		\Bricks\Elements::register_element( $woo_el_file );
	}
}