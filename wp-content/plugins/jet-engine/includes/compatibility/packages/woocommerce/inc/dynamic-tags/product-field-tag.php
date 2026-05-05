<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Dynamic_Tags;

use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Manager as Listings_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Field_Tag extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'jet-wc-product-field';
	}

	public function get_title() {
		return __( 'WooCommerce Product Field', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::NUMBER_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::URL_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::POST_META_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::COLOR_CATEGORY
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {

		$this->add_control(
			'product_field',
			array(
				'label'   => __( 'Field', 'jet-engine' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => Listings_Manager::instance()->get_product_fields_list(),
			)
		);

		$this->add_control(
			'product_field_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( '<b>Please note:</b> Works only with WC Query (from JetEngine Query Builder) or on single product page', 'jet-engine' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

	}

	public function render() {

		$field = $this->get_settings( 'product_field' );

		if ( empty( $field ) ) {
			return;
		}

		$product = Listings_Manager::instance()->get_current_product();

		if ( ! $product ) {
			// phpcs:disable
			_e( '<b>WooCommerce Product Field</b> works only with WC Query (from JetEngine Query Builder) or on single product page', 'jet-engine' );
			// phpcs:enable
			return;
		}

		add_filter( 'woocommerce_product_add_to_cart_url', [ $this, 'maybe_fix_add_to_cart_url' ], 10, 2 );

		$result = jet_engine()->listings->data->get_prop( $field, $product );

		remove_filter( 'woocommerce_product_add_to_cart_url', [ $this, 'maybe_fix_add_to_cart_url' ] );

		if ( \Jet_Engine_Tools::is_empty( $result ) ) {
			return;
		}

		echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	function maybe_fix_add_to_cart_url( $url, $product ) {
		// Avoid running if URL is already good.
		if ( ! function_exists( 'jet_smart_filters' ) || ! jet_smart_filters()->query->is_ajax_filter() ) {
			return $url;
		}

		if (
			! str_contains( $url, 'admin-ajax.php' )
			&& ! str_contains( $url, 'jsf_ajax=1' )
		) {
			return $url;
		}

		// Detect real frontend context URL.
		$current_url = '';

		// Fallback to referer
		if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
			$current_url = esc_url_raw( $_SERVER['HTTP_REFERER'] );
		}

		// Add to cart param
		return add_query_arg( 'add-to-cart', $product->get_id(), $current_url );
	}

}
