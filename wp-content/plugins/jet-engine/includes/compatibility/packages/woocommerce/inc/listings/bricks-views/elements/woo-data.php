<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Bricks_Views\Elements;

use Jet_Engine\Bricks_Views\Elements\Base;
use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Data_Render;
use Jet_Engine\Bricks_Views\Helpers\Preview;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Data extends Base {
	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-woo-data'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-woo-data'; // Themify icon font class
	public $css_selector = ''; // Default CSS selector
	public $scripts = []; // Script(s) run when element is rendered on frontend or updated in builder
	public $jet_element_render = 'woo-data';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Woo Data', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {
		$this->register_general_group();
	}

	// Set builder controls
	public function set_controls() {
		$this->register_general_controls();
	}

	public function register_general_group() {
		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);
	}

	public function register_general_controls() {

		$this->start_jet_control_group( 'section_general' );

		$this->register_jet_control(
			'data_type',
			[
				'tab'        => 'content',
				'label'      => esc_html__( 'Data Type', 'jet-engine' ),
				'type'       => 'select',
				'options'    => Data_Render::instance()->get_data_types(),
				'searchable' => false,
				'default'    => 'hook',
			]
		);

		$this->register_jet_control(
			'hook_name',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Hook Name', 'jet-engine' ),
				'type'        => 'select',
				'options'     => Data_Render::instance()->get_allowed_hooks(),
				'default'     => 'woocommerce_after_shop_loop_item_title',
				'required'    => [ 'data_type', '=', 'hook' ],
				'description' => __( 'Woo hook to call. All callbacks attached to this hook will also be called.', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'core_callbacks',
			[
				'tab'         => 'content',
				'label'       => __( 'Core Callbacks', 'jet-engine' ),
				'description' => __( 'Disable this toggle to prevent default Woo callbacks from being called on this hook.', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => true,
				'required'    => [ 'data_type', '=', 'hook' ],
			]
		);

		$this->register_jet_control(
			'template_function',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Template Function', 'jet-engine' ),
				'type'     => 'select',
				'default'  => 'woocommerce_template_loop_add_to_cart',
				'options'  => Data_Render::instance()->get_allowed_template_functions(),
				'required' => [
					[ 'data_type', '=', 'template_function' ],
				],
			]
		);

		$link_allowed = Data_Render::instance()->get_functions_with_link_allowed();

		$this->register_jet_control(
			'add_link',
			[
				'tab'         => 'content',
				'label'       => __( 'Add link', 'jet-engine' ),
				'description' => __( 'Wrap the function result with a link to the single product page.', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => false,
				'required'    => [
					[ 'data_type', '=', 'template_function' ],
					[ 'template_function', '=', $link_allowed ],
				],
			]
		);

		$this->end_jet_control_group();
	}

	// Render element HTML
	public function render() {

		parent::render();

		$settings = $this->get_jet_settings();
		$settings['core_callbacks'] = ! empty( $settings['core_callbacks'] ) ? 'yes' : '';
		$settings['core_callbacks'] = filter_var( $settings['core_callbacks'], FILTER_VALIDATE_BOOLEAN );

		$settings['add_link'] = ! empty( $settings['add_link'] ) ? 'yes' : '';
		$settings['add_link'] = filter_var( $settings['add_link'], FILTER_VALIDATE_BOOLEAN );

		$post_id = $this->get_post_id();

		if ( $post_id && $this->is_requested_element() ) {
			$preview = new Preview( $post_id );
			$preview->setup_preview_for_render( Data_Render::instance()->set_attributes( $settings ) );
		}

		$is_qty_input = false;

		if (
			! empty( $settings['data_type'] )
			&& 'template_function' === $settings['data_type']
			&& ! empty( $settings['template_function'] )
			&& 'woocommerce_quantity_input' === $settings['template_function']
		) {
			$is_qty_input = true;
		}

		echo "<div {$this->render_attributes( '_root' )}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $is_qty_input ) {
			echo '<form class="cart">';
		}
		Data_Render::instance()->process( $settings );

		if ( $is_qty_input ) {
			echo '</form>';
		}

		echo "</div>";
	}
}
