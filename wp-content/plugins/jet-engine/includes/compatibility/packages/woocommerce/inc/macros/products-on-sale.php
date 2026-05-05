<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Macros;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

class Products_On_Sale extends \Jet_Engine_Base_Macros {

	/**
	 * Returns macros tag
	 *
	 * @return string
	 */
	public function macros_tag() {
		return 'wc_get_products_on_sale';
	}

	/**
	 * Returns macros name
	 *
	 * @return string
	 */
	public function macros_name() {
		return __( 'WC Products On Sale', 'jet-engine' );
	}

	/**
	 * Callback function to return macros value.
	 *
	 * @return string
	 */
	public function macros_callback( $args = [] ) {

		if ( ! function_exists( 'WC' ) ) {
			return false;
		}

		$products = wc_get_product_ids_on_sale();

		if ( empty( $products ) ) {
			$products = [ PHP_INT_MAX ];
		}

		return implode( ',', array_unique( $products ) );
	}

}
