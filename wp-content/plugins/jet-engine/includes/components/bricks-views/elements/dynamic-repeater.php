<?php

namespace Jet_Engine\Bricks_Views\Elements;

use Bricks\Element;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;
use Jet_Engine\Bricks_Views\Helpers\Controls_Hook_Bridge;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Dynamic_Repeater extends Base {

	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-listing-dynamic-repeater'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-dynamic-repeater'; // Themify icon font class
	public $css_selector = '.jet-listing-dynamic-repeater'; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'dynamic-repeater';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Dynamic Repeater', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {
		$this->register_items_style_group();
		$this->register_item_style_group();
		$this->register_delimiter_style_group();
		$this->register_counter_style_group();
	}

	// Set builder controls
	public function set_controls() {
		$this->register_general_controls();
		$this->register_items_style_controls();
		$this->register_item_style_controls();
		$this->register_delimiter_style_controls();
		$this->register_counter_style_controls();
	}

	public function register_general_controls() {

		$this->register_jet_control(
			'repeater_notice',
			[
				'tab'     => 'content',
				'content' => esc_html__( 'This widget could process only repeater meta fields created with JetEngine or ACF plugins. Alternatively, you may use JetEngine query of any type as a source.', 'jet-engine' ),
				'type'    => 'info',
			]
		);

		$repeater_fields = $this->get_repeater_fields();

		if ( jet_engine()->options_pages ) {
			$repeater_fields['options_page'] = __( 'Options', 'jet-engine' );
		}

		$repeater_fields['je_query'] = __( 'JetEngine Query', 'jet-engine' );

		$this->register_jet_control(
			'dynamic_field_source',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Source', 'jet-engine' ),
				'type'    => 'select',
				'options' => Options_Converter::convert_select_groups_to_options( $repeater_fields ),
				'default' => '',
			]
		);

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

			if ( ! empty( $options_pages_select ) ) {

				$this->register_jet_control(
					'dynamic_link_option',
					[
						'tab'      => 'content',
						'label'    => esc_html__( 'Option', 'jet-engine' ),
						'type'     => 'select',
						'options'  => Options_Converter::convert_select_groups_to_options( $options_pages_select ),
						'required' => [ 'dynamic_field_source', '=', 'options_page' ],
					]
				);
			}
		}

		$queries_for_options = \Jet_Engine\Query_Builder\Manager::instance()->get_queries_for_options( false );

		$this->register_jet_control(
			'je_query_id',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Query ID', 'jet-engine' ),
				'type'        => 'select',
				'options'     => Options_Converter::convert_select_groups_to_options( $queries_for_options ),
				'description' => sprintf(
					esc_html__( 'You may use %s with the Query source, but if you need to filter the value obtained by a macro, do it by this example - %s', 'jet-engine' ),
					'<a href="' . admin_url( 'admin.php?page=jet-engine#macros_generator' ) . '" target="_blank">JetEngine macros</a>',
					'<code>%current_meta|image_field%{"filter":"img_url_by_id"}</code>'
				),
				'required'    => [ 'dynamic_field_source', '=', 'je_query' ],
			]
		);

		$hooks = new Controls_Hook_Bridge( $this );
		$hooks->do_action( 'jet-engine/listings/dynamic-repeater/source-controls' );

		$this->register_jet_control(
			'dynamic_field_format',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Field format', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => '<span>%name%</span>',
				'description' => esc_html__( 'You can render repeater fields values with macros %repeater field name%', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'item_tag',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Item HTML tag', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'div' => 'DIV',
					'tr'  => 'tr',
					'li'  => 'li',
				],
				'default' => 'div',
			]
		);

		$this->register_jet_control(
			'items_delimiter',
			[
				'tab'   => 'content',
				'label' => esc_html__( 'Items delimiter', 'jet-engine' ),
				'type'  => 'text',
			]
		);

		$this->register_jet_control(
			'dynamic_field_before',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Before items markup', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => '',
				'description' => esc_html__( 'HTML to output before repeater items', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'dynamic_field_after',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'After items markup', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => '',
				'description' => esc_html__( 'HTML to output after repeater items', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'dynamic_field_counter',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Add counter to repeater items', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'dynamic_field_leading_zero',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Add leading zero before counter items', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'dynamic_field_counter', '=', true ],
			]
		);

		$this->register_jet_control(
			'dynamic_field_counter_after',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Text after counter number', 'jet-engine' ),
				'type'     => 'text',
				'required' => [ 'dynamic_field_counter', '=', true ],
			]
		);

		$this->register_jet_control(
			'dynamic_field_counter_position',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Position', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'above'    => esc_html__( 'Above items', 'jet-engine' ),
					'at-left'  => esc_html__( 'At the left of the items', 'jet-engine' ),
					'at-right' => esc_html__( 'At the right of the items', 'jet-engine' ),
				],
				'default'  => 'at-left',
				'required' => [ 'dynamic_field_counter', '=', true ],
			]
		);

		$this->register_jet_control(
			'hide_if_empty',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Hide if value is empty', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,

			]
		);

		$this->register_jet_control(
			'dynamic_field_fallback',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Fallback', 'jet-engine' ),
				'type'     => 'text',
				'required' => [ 'hide_if_empty', '!=', true ],
			]
		);

		$this->register_jet_control(
			'object_context',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Context', 'jet-engine' ),
				'type'    => 'select',
				'options' => jet_engine()->listings->allowed_context_list(),
				'default' => 'default_object',
			]
		);
	}

	public function register_items_style_group() {

		$this->register_jet_control_group(
			'section_items_style',
			[
				'title' => esc_html__( 'Items', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

	}

	public function register_items_style_controls() {

		$this->start_jet_control_group( 'section_items_style' );

		$this->register_jet_control(
			'items_direction',
			[
				'tab'       => 'style',
				'label'     => esc_html__( 'Direction', 'jet-engine' ),
				'type'      => 'direction',
				'direction' => 'column',
				'css'       => [
					[
						'property' => 'flex-direction',
						'selector' => $this->css_selector( '__items' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'items_align_justify_content',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Horizontal alignment', 'jet-engine' ),
				'type'     => 'justify-content',
				'exclude'  => [
					'space-between',
					'space-around',
					'space-evenly',
				],
				'css'      => [
					[
						'property' => 'justify-content',
						'selector' => $this->css_selector( '__items' ),
					],
				],
				'required' => [ 'items_direction', '=', 'row' ],
			]
		);

		$this->register_jet_control(
			'items_align_align_items',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Horizontal alignment', 'jet-engine' ),
				'type'     => 'align-items',
				'css'      => [
					[
						'property' => 'align-items',
						'selector' => $this->css_selector( '__items' ),
					],
				],
				'required' => [ 'items_direction', '=', 'column' ],
			]
		);

		$this->register_jet_control(
			'items_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-engine' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '20px',
				'css'     => [ [ 'property' => '--je-repeater-items-gap' ] ],
			]
		);

		$this->end_jet_control_group();

	}

	public function register_item_style_group() {

		$this->register_jet_control_group(
			'section_item_style',
			[
				'title' => esc_html__( 'Item', 'jet-engine' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_item_style_controls() {

		$this->start_jet_control_group( 'section_item_style' );

		$this->register_jet_control(
			'item_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-engine' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '20px',
				'css'     => [ [ 'property' => '--je-repeater-item-gap' ] ],
			]
		);

		$this->register_jet_control(
			'item_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Width', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [ [ 'property' => '--je-repeater-item-width' ] ],
			]
		);

		$this->register_jet_control(
			'item_height',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Height', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [ [ 'property' => '--je-repeater-item-height' ] ],
			]
		);

		$this->register_jet_control(
			'item_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [ [ 'property' => '--je-repeater-item-bg' ] ],
			]
		);

		$this->register_jet_control(
			'item_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'spacing',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $this->css_selector( '__item > *' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'item_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $this->css_selector( '__item > *' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'item_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-engine' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $this->css_selector( '__item > *' ),
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_delimiter_style_group() {

		$this->register_jet_control_group(
			'section_delimiter_style',
			[
				'title' => esc_html__( 'Delimiter', 'jet-engine' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_delimiter_style_controls() {

		$this->start_jet_control_group( 'section_delimiter_style' );

		$this->register_jet_control(
			'delimiter_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'selector' => $this->css_selector( '__delimiter' ),
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_counter_style_group() {

		$this->register_jet_control_group(
			'section_counter_style',
			[
				'title'    => esc_html__( 'Counter', 'jet-engine' ),
				'tab'      => 'style',
				'required' => [ 'dynamic_field_counter', '!=', false ],
			]
		);
	}

	public function register_counter_style_controls() {

		$this->start_jet_control_group( 'section_counter_style' );

		$this->register_jet_control(
			'counter_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Width', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $this->css_selector( '__counter' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'counter_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'selector' => $this->css_selector( '__counter' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'counter_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $this->css_selector( '__counter' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'counter_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'spacing',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $this->css_selector( '__counter' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'counter_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $this->css_selector( '__counter' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'counter_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-engine' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $this->css_selector( '__counter' ),
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
		wp_enqueue_style( 'jet-engine-frontend' );
	}

	// Render element HTML
	public function render() {

		parent::render();

		$this->enqueue_scripts();

		$render = $this->get_jet_render_instance();

		// STEP: Dynamic repeater renderer class not found: Show placeholder text
		if ( ! $render ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Dynamic repeater renderer class not found', 'jet-engine' )
				]
			);
		}

		echo "<div {$this->render_attributes( '_root' )}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$render->render_content();
		echo "</div>";
	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', '.jet-listing-dynamic-repeater', $mod );
	}

	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_repeater_fields() {

		if ( jet_engine()->meta_boxes ) {
			$result = jet_engine()->meta_boxes->get_fields_for_select( 'repeater' );
		} else {
			$result = array();
		}

		return apply_filters( 'jet-engine/listings/dynamic-repeater/fields', $result );

	}
}