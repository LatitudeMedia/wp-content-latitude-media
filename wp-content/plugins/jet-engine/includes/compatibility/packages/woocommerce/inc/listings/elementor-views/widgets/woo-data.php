<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Elementor_Views\Widgets;

use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Data_Render;

class Woo_Data_Widget extends \Jet_Listing_Dynamic_Widget {

	public function get_name() {
		return 'jet-listing-woo-data';
	}

	public function get_title() {
		return __( 'Woo Data', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-woo-data';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return false;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_woo_data_general',
			array(
				'label' => __( 'Woo Data', 'jet-engine' ),
			)
		);

		$this->add_control(
			'data_type',
			array(
				'label'   => __( 'Data Type', 'jet-engine' ),
				'type'    => 'select',
				'default' => 'hook',
				'options' => Data_Render::instance()->get_data_types(),
			)
		);

		$this->add_control(
			'hook_name',
			array(
				'label'       => __( 'Hook Name', 'jet-engine' ),
				'type'        => 'select',
				'default'     => 'woocommerce_after_shop_loop_item_title',
				'options'     => Data_Render::instance()->get_allowed_hooks(),
				'condition'   => array(
					'data_type' => 'hook',
				),
				'description' => __( 'Woo hook to call. All callbacks attached to this hook will also be called.', 'jet-engine' ),
			)
		);

		$this->add_control(
			'core_callbacks',
			array(
				'label'        => __( 'Core Callbacks', 'jet-engine' ),
				'description'  => __( 'Disable this toggle to prevent default Woo callbacks from being called on this hook.', 'jet-engine' ),
				'type'         => 'switcher',
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'jet-engine' ) . ' ',
				'label_off'    => __( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'condition'    => array(
					'data_type' => 'hook',
				),
			)
		);

		$this->add_control(
			'template_function',
			array(
				'label'       => __( 'Template Function', 'jet-engine' ),
				'type'        => 'select',
				'default'     => 'woocommerce_template_loop_add_to_cart',
				'options'     => Data_Render::instance()->get_allowed_template_functions(),
				'condition'   => array(
					'data_type' => 'template_function',
				),
				'description' => __( 'Woo template function to call.', 'jet-engine' ),
			)
		);

		$link_allowed = Data_Render::instance()->get_functions_with_link_allowed();

		$this->add_control(
			'add_link',
			array(
				'label'       => __( 'Add link', 'jet-engine' ),
				'type'        => 'switcher',
				'default'     => '',
				'description' => __( 'Wrap the function result with a link to the single product page.', 'jet-engine' ),
				'condition'   => array(
					'data_type' => 'template_function',
					'template_function' => $link_allowed,
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		$settings['core_callbacks'] = ! empty( $settings['core_callbacks'] ) ? 'yes' : '';
		$settings['core_callbacks'] = filter_var( $settings['core_callbacks'], FILTER_VALIDATE_BOOLEAN );

		$settings['add_link'] = ! empty( $settings['add_link'] ) ? 'yes' : '';
		$settings['add_link'] = filter_var( $settings['add_link'], FILTER_VALIDATE_BOOLEAN );

		Data_Render::instance()->process( $settings );
	}
}
