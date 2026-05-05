<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Blocks_Views\Block_Types;

use Jet_Engine\Blocks_Views\Dynamic_Content\Data;
use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Data_Render;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Woo Data block type
 */
class Woo_Data extends \Jet_Engine_Blocks_Views_Type_Base {

	/**
	 * Returns block name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return 'woo-data';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {
		return array(
			'data_type' => array(
				'type' => 'string',
				'default' => 'hook',
			),
			'hook_name' => array(
				'type' => 'string',
				'default' => 'woocommerce_after_shop_loop_item_title',
			),
			'core_callbacks' => array(
				'type' => 'boolean',
				'default' => true,
			),
			'template_function' => array(
				'type' => 'string',
				'default' => 'woocommerce_template_loop_add_to_cart',
			),
			'add_link' => array(
				'type' => 'boolean',
				'default' => false,
			),
		);
	}

	public function get_render_instance( $attributes ) {

		$attributes['core_callbacks'] = ! empty( $attributes['core_callbacks'] ) ? true : '';
		$attributes['core_callbacks'] = filter_var( $attributes['core_callbacks'], FILTER_VALIDATE_BOOLEAN );

		$attributes['add_link'] = ! empty( $attributes['add_link'] ) ? true : '';
		$attributes['add_link'] = filter_var( $attributes['add_link'], FILTER_VALIDATE_BOOLEAN );

		return Data_Render::instance()->set_attributes( $attributes );
	}

}
