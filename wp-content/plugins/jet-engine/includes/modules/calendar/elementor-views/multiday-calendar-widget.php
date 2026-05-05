<?php
namespace Jet_Engine\Modules\Calendar\Elementor_Views;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Multiday_Calendar_Widget extends \Elementor\Jet_Listing_Grid_Widget {

	public $is_first        = false;
	public $data            = false;
	public $first_day       = false;
	public $last_day        = false;
	public $multiday_events = array();
	public $posts_cache     = array();
	public $start_from      = false;

	public $prev_month_posts = array();
	public $next_month_posts = array();

	public function get_name() {
		return 'jet-listing-multiday-calendar';
	}

	public function get_title() {
		return __( 'Multi-Day Calendar', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-multiday-calendar';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetengine-calendar-listing-functionality-how-to-add-a-dynamic-calendar/?utm_source=jetengine&utm_medium=listing-calendar&utm_campaign=need-help';
	}

	protected function register_controls() {

		$this->register_general_settings();
		$this->register_query_settings();
		$this->register_visibility_settings();
		$this->register_style_settings();

	}

	public function register_general_settings() {

		$module = jet_engine()->modules->get_module( 'calendar' );

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'jet-engine' ),
			)
		);

		$this->add_control(
			'lisitng_id',
			array(
				'label'      => __( 'Listing to open', 'jet-engine' ),
				'type'       => 'jet-query',
				'query_type' => 'post',
				'description' => __( 'Listing item that will be used to display full event content in the popup by click on the event badge in the calendar.', 'jet-engine' ),
				'query'      => array(
					'post_type' => jet_engine()->post_type->slug(),
				),
				'edit_button' => array(
					'active' => true,
					'label'  => __( 'Edit Listing', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'query_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( '<b>Please note:</b> For non-post listings (users, terms, CCT etc.) set the Query with Custom Query settings', 'jet-engine' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'group_by',
			array(
				'label'   => __( 'Group posts by', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => $module->get_calendar_group_keys(),
			)
		);

		$this->add_control(
			'group_by_key',
			array(
				'label'       => esc_html__( 'Meta field name', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Could be meta field or item property field (depends on query used). This field must contain date to group items by. Works only if "Save as timestamp" option for this field is active', 'jet-engine' ),
				'condition'   => array(
					'group_by' => 'meta_date'
				),
			)
		);

		$this->add_control(
			'end_date_key',
			array(
				'label'       => esc_html__( 'End date field name', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'If you used "Advanced Datetime" meta field type you can leave this field empty. This field must contain date when event ends. Works only if "Save as timestamp" option for meta field is active.', 'jet-engine' ),
				'condition'   => array(
					'group_by' => 'meta_date',
				),
			)
		);

		$this->add_control(
			'week_days_format',
			array(
				'label'   => __( 'Week days format', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'short',
				'options' => array(
					'full'    => __( 'Full', 'jet-engine' ),
					'short'   => __( 'Short', 'jet-engine' ),
					'initial' => __( 'Initial letter', 'jet-engine' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'custom_start_from',
			array(
				'label'        => __( 'Start from custom month', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => __( 'Yes', 'jet-engine' ),
				'label_off'    => __( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'start_from_month',
			array(
				'label'     => __( 'Start from month', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => date( 'F' ),
				'options'   => $this->get_months(),
				'condition' => array(
					'custom_start_from' => 'yes',
				),
			)
		);

		$this->add_control(
			'start_from_year',
			array(
				'label'     => __( 'Start from year', 'jet-engine' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => date( 'Y' ),
				'condition' => array(
					'custom_start_from' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_posts_nearby_months',
			array(
				'label'        => __( 'Show posts from the nearby months', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'jet-engine' ),
				'label_off'    => __( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'hide_past_events',
			array(
				'label'        => __( 'Hide past events', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'jet-engine' ),
				'label_off'    => __( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'allow_date_select',
			array(
				'label'        => __( 'Allow date select', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'jet-engine' ),
				'label_off'    => __( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'start_year_select',
			array(
				'label'     => __( 'Min select year', 'jet-engine' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '1970',
				'condition' => array(
					'allow_date_select' => 'yes',
					'hide_past_events!' => 'yes',
				),
			)
		);

		$this->add_control(
			'end_year_select',
			array(
				'label'       => __( 'Max select year', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '2038',
				'description' => __( 'You may use JetEngine macros in min/max select year. Also, you may use strings like \'+3years\', \'-1year\', \'this year\' to set year value relative to the curent year.' ),
				'condition'   => array(
					'allow_date_select' => 'yes',
				),
			)
		);

		$this->add_control(
			'cache_enabled',
			array(
				'label'        => __( 'Cache Calendar', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'jet-engine' ),
				'label_off'    => __( 'No', 'jet-engine' ),
				'separator'    => 'before',
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'cache_timeout',
			array(
				'label'       => esc_html__( 'Cache Timeout', 'jet-engine' ),
				'description' => __( 'Cache timeout in seconds. Set -1 for unlimited.', 'jet-engine' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => -1,
				'max'         => 86400,
				'step'        => 1,
				'default'     => 60,
				'condition'   => array(
					'cache_enabled' => 'yes',
				),
			)
		);

		$this->add_control(
			'max_cache',
			array(
				'label'       => esc_html__( 'Maximum Cache Size', 'jet-engine' ),
				'description' => __( 'Maximum cache size (months). If number of cached month exceeds this number - the oldest month will be deleted from cache.', 'jet-engine' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 120,
				'step'        => 1,
				'default'     => 12,
				'condition'   => array(
					'cache_enabled' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$macros_generator_link = admin_url( 'admin.php?page=jet-engine#macros_generator' );
		$shortocode_generator_link = admin_url( 'admin.php?page=jet-engine#shortcode_generator' );

		$this->start_controls_section(
			'section_event_content',
			array(
				'label' => __( 'Event Badge Content', 'jet-engine' ),
			)
		);

		$this->add_control(
			'event_content',
			array(
				'label'       => __( 'Badge Content', 'jet-engine' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default'     => '%title%',
				'description' => 'Supports HTML tags, JetEngine <a href="' . $macros_generator_link . '" target="_blank">macros</a> and <a href="' . $shortocode_generator_link . '" target="_blank">shortcodes</a>.',
			)
		);

		$this->add_control(
			'event_marker',
			array(
				'label'       => __( 'Badge Marker', 'jet-engine' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => __( 'Show event badge dot marker for each event in the calendar', 'jet-engine' ),
			)
		);

		$this->add_control(
			'use_dynamic_styles',
			array(
				'label'       => __( 'Use Dynamic Styles', 'jet-engine' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => '',
				'description' => __( 'Allows setting badge color, background, border color and dot color based on the specific event data.', 'jet-engine' ),
			)
		);

		$this->add_control(
			'use_dynamic_styles_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => 'Specific event badge styles could be set only by using JetEngine <a href="' . $macros_generator_link . '" target="_blank">macros</a> or <a href="' . $shortocode_generator_link . '" target="_blank">shortcodes</a>. Generated macro/shortcode must return a color value. Put generated macro/shortcode in the appropriate field below and event-specific color value will be applied on the front-end.',
				'condition' => array(
					'use_dynamic_styles' => 'yes',
				),
			)
		);

		$this->add_control(
			'dynamic_badge_color',
			array(
				'label'       => __( 'Badge Text Color', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'description' => __( 'Defines the text color for the event badge.', 'jet-engine' ),
				'condition' => array(
					'use_dynamic_styles' => 'yes',
				),
			)
		);

		$this->add_control(
			'dynamic_badge_bg_color',
			array(
				'label'       => __( 'Badge Background Color', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'description' => __( 'Defines the background color for the event badge.', 'jet-engine' ),
				'condition' => array(
					'use_dynamic_styles' => 'yes',
				),
			)
		);

		$this->add_control(
			'dynamic_badge_border_color',
			array(
				'label'       => __( 'Badge Border Color', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'description' => __( 'Defines the border color for the event badge (the remaining border styles can be set in the <b>Styles</b> tab).', 'jet-engine' ),
				'condition' => array(
					'use_dynamic_styles' => 'yes',
				),
			)
		);

		$this->add_control(
			'dynamic_badge_dot_color',
			array(
				'label'       => __( 'Badge Dot Color', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'description' => __( 'Defines the dot color for the event badge.', 'jet-engine' ),
				'condition' => array(
					'use_dynamic_styles' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	public function register_query_settings() {

		$this->start_controls_section(
			'section_custom_query',
			array(
				'label' => __( 'Custom Query', 'jet-engine' ),
			)
		);

		$this->add_control(
			'custom_query',
			array(
				'label'        => __( 'Use Custom Query', 'jet-engine' ),
				'description'  => __( 'Allow to use custom query from Query Builder as items source', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'jet-engine' ),
				'label_off'    => __( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'custom_query_id',
			array(
				'label'   => __( 'Custom Query', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => '',
				'options' => \Jet_Engine\Query_Builder\Manager::instance()->get_queries_for_options(),
				'condition' => array(
					'custom_query' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Returns available months list
	 *
	 * @return [type] [description]
	 */
	public function get_months() {
		return array(
			'January'   => __( 'January', 'jet-engine' ),
			'February'  => __( 'February', 'jet-engine' ),
			'March'     => __( 'March', 'jet-engine' ),
			'April'     => __( 'April', 'jet-engine' ),
			'May'       => __( 'May', 'jet-engine' ),
			'June'      => __( 'June', 'jet-engine' ),
			'July'      => __( 'July', 'jet-engine' ),
			'August'    => __( 'August', 'jet-engine' ),
			'September' => __( 'September', 'jet-engine' ),
			'October'   => __( 'October', 'jet-engine' ),
			'November'  => __( 'November', 'jet-engine' ),
			'December'  => __( 'December', 'jet-engine' ),
		);
	}

	/**
	 * Register style settings
	 * @return [type] [description]
	 */
	public function register_style_settings() {

		$this->start_controls_section(
			'section_caption_style',
			array(
				'label'      => esc_html__( 'Caption', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'caption_layout',
			array(
				'label'   => __( 'Layout', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'layout-1',
				'options' => array(
					'layout-1' => __( 'Layout 1', 'jet-engine' ),
					'layout-2' => __( 'Layout 2', 'jet-engine' ),
					'layout-3' => __( 'Layout 3', 'jet-engine' ),
					'layout-4' => __( 'Layout 4', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'caption_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'caption_txt_color',
			array(
				'label'  => esc_html__( 'Label Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-caption__name' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'caption_txt_typography',
				'selector' => '{{WRAPPER}} .jet-calendar-caption__name',
			)
		);

		$this->add_responsive_control(
			'caption_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'caption_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'caption_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} .jet-calendar-caption',
			)
		);

		$this->add_responsive_control(
			'caption_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'caption_gap',
			array(
				'label' => esc_html__( 'Gap between caption elements', 'jet-engine' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-caption__wrap' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_nav_style',
			array(
				'label'      => esc_html__( 'Navigation Arrows', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'nav_width',
			array(
				'label' => esc_html__( 'Width', 'jet-engine' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nav_height',
			array(
				'label' => esc_html__( 'Height', 'jet-engine' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nav_size',
			array(
				'label' => esc_html__( 'Arrow Size', 'jet-engine' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_nav_prev_next_style' );

		$this->start_controls_tab(
			'tab_nav_prev',
			array(
				'label' => esc_html__( 'Prev Arrow (Default)', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'nav_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} .jet-calendar-nav__link',
			)
		);

		$this->add_responsive_control(
			'nav_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_nav_next',
			array(
				'label' => esc_html__( 'Next Arrow', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'nav_border_next',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} .jet-calendar-nav__link.nav-link-next',
			)
		);

		$this->add_responsive_control(
			'nav_border_radius_next',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-calendar-nav__link.nav-link-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->start_controls_tabs( 'tabs_nav_style' );

		$this->start_controls_tab(
			'tab_nav_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_background_color',
			array(
				'label'  => esc_html__( 'Background Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_nav_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'nav_color_hover',
			array(
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_background_color_hover',
			array(
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_border_color_hover',
			array(
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'nav_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_week_style',
			array(
				'label'      => esc_html__( 'Week Days', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'week_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__day' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'week_txt_color',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__day' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'week_txt_typography',
				'selector' => '{{WRAPPER}} .jet-md-calendar__day',
			)
		);

		$this->add_responsive_control(
			'week_days_gap',
			array(
				'label'      => __( 'Gap Between Days', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__days' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-md-calendar__events' => 'column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-md-calendar__week' => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-md-calendar__days-ow' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'week_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__day' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'week_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} .jet-md-calendar__day',
			)
		);

		$this->add_responsive_control(
			'week_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'custom' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__day' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_do_week_style',
			array(
				'label'      => esc_html__( 'Week Days Names', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'do_week_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__day-ow' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'do_week_txt_color',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__day-ow' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'do_week_txt_typography',
				'selector' => '{{WRAPPER}} .jet-md-calendar__day-ow',
			)
		);

		$this->add_responsive_control(
			'do_week_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__day-ow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'do_week_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} .jet-md-calendar__day-ow',
			)
		);

		$this->add_responsive_control(
			'do_week_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'custom' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__day-ow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_event_badge_style',
			array(
				'label'      => esc_html__( 'Event Badge', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'event_badge_typography',
				'selector' => '{{WRAPPER}} .jet-md-calendar__event',
			)
		);

		$this->add_control(
			'event_badge_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => '--jet-mdc-c-event-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'event_badge_background_color',
			array(
				'label'  => esc_html__( 'Background Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => '--jet-mdc-c-event: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'event_badge_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__event' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'event_badge_border_width',
			array(
				'label'      => esc_html__( 'Border Width', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__event' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'event_badge_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => '--jet-mdc-c-event-bd: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'event_badge_marker_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'custom' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__event' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'event_badge_marker_color',
			array(
				'label'     => esc_html__( 'Dot Marker Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__dot' => '--jet-mdc-c-dot: {{VALUE}};',
				),
				'condition' => array(
					'event_marker' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'event_badge_marker_size',
			array(
				'label'      => esc_html__( 'Dot Marker Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 4,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'event_marker' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'event_badge_marker_radius',
			array(
				'label'      => esc_html__( 'Dot Marker Radius', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__dot' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'event_marker' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'event_badge_marker_gap',
			array(
				'label'      => esc_html__( 'Dot Marker Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__event' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'event_marker' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'event_badges_gap',
			array(
				'label'      => esc_html__( 'Gap Around Event Badges', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__events' => 'row-gap: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-md-calendar__event' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_event_content_style',
			array(
				'label'      => esc_html__( 'Event Content Popup', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'event_content_bg_color',
			array(
				'label' => esc_html__( 'Overalay Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__event-overlay' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'event_content_popup_width',
			array(
				'label' => esc_html__( 'Popup Width', 'jet-engine' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range' => array(
					'px' => array(
						'min' => 200,
						'max' => 800,
					),
					'vw' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__event-body' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'event_content_height',
			array(
				'label'      => esc_html__( 'Max Height', 'jet-engine' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range' => array(
					'px' => array(
						'min' => 200,
						'max' => 800,
					),
					'vh' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__event-body' => 'max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'event_content_close_color',
			array(
				'label'      => esc_html__( 'Close Button Color', 'jet-engine' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .jet-md-calendar__event-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'event_content_close_size',
			array(
				'label' => esc_html__( 'Close Button Size', 'jet-engine' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__event-close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'event_content_close_h_position',
			array(
				'label' => esc_html__( 'Close Button Horizontal Position', 'jet-engine' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__event-close' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'event_content_close_v_position',
			array(
				'label' => esc_html__( 'Close Button Vertical Position', 'jet-engine' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-md-calendar__event-close' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render grid posts
	 *
	 * @return void
	 */
	public function render_posts() {

		wp_enqueue_style(
			'jet-engine-multiday-calendar',
			jet_engine()->plugin_url( 'includes/modules/calendar/assets/css/multiday-calendar.css' ),
			array(),
			jet_engine()->get_version()
		);

		$instance = jet_engine()->listings->get_render_instance(
			'listing-multiday-calendar',
			$this->get_widget_settings()
		);

		$instance->render_content();
	}

	protected function render() {
		$this->render_posts();
	}
}
