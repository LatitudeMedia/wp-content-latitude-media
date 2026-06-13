<?php
/**
 * Calendar block type.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Calendar block type class
 */
class Jet_Listing_Multiday_Calendar_Block_Type extends \Jet_Listing_Calendar_Block_Type {

	/**
	 * Returns block name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'listing-multiday-calendar';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {

		$module = jet_engine()->modules->get_module( 'calendar' );

		return apply_filters( 'jet-engine/blocks-views/listing-calendar/attributes', array(
			'lisitng_id' => array(
				'type'    => 'string',
				'default' => '',
			),
			'group_by' => array(
				'type'    => 'string',
				'default' => 'post_date',
				'options' => $module->get_calendar_group_keys( true ),
			),
			'group_by_key' => array(
				'type'    => 'string',
				'default' => '',
			),
			'allow_multiday' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'end_date_key' => array(
				'type'    => 'string',
				'default' => '',
			),
			'week_days_format' => array(
				'type'    => 'string',
				'default' => 'short',
			),
			'custom_start_from' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'start_from_month' => array(
				'type'    => 'string',
				'default' => date( 'F' ),
			),
			'start_from_year' => array(
				'type'    => 'string',
				'default' => date( 'Y' ),
			),
			'show_posts_nearby_months' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'hide_past_events' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'allow_date_select' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'start_year_select' => array(
				'type'    => 'string',
				'default' => '1970',
			),
			'end_year_select' => array(
				'type'    => 'string',
				'default' => '2038',
			),
			'event_content' => array(
				'type'    => 'string',
				'default' => '%title%',
			),
			'event_marker' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'use_dynamic_styles' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'dynamic_badge_color' => array(
				'type'    => 'string',
				'default' => '',
			),
			'dynamic_badge_bg_color' => array(
				'type'    => 'string',
				'default' => '',
			),
			'dynamic_badge_border_color' => array(
				'type'    => 'string',
				'default' => '',
			),
			'dynamic_badge_dot_color' => array(
				'type'    => 'string',
				'default' => '',
			),
			'caption_layout' => array(
				'type'    => 'string',
				'default' => 'layout-1',
			),

			// Custom Query
			'custom_query' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'custom_query_id' => array(
				'type'    => 'string',
				'default' => '',
			),

			// Block Visibility
			'hide_widget_if' => array(
				'type'    => 'string',
				'default' => '',
			),

			// Block ID
			'_block_id' => array(
				'type'    => 'string',
				'default' => '',
			),

			// Element ID
			'_element_id' => array(
				'type'    => 'string',
				'default' => '',
			),
			'cache_enabled' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'max_cache' => array(
				'type'    => 'string',
				'default' => '12',
			),
			'cache_timeout' => array(
				'type'    => 'string',
				'default' => '60',
			),
		) );
	}

	/**
	 * Add style block options
	 *
	 * @return void
	 */
	public function add_style_manager_options() {

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_caption_style',
				'title' => esc_html__( 'Caption', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'caption_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'caption_txt_color',
				'label' => esc_html__( 'Label Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption__name' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'caption_txt_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'both',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption__name' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'caption_padding',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'caption_margin',
				'label' => esc_html__( 'Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'caption_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'caption_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'is_legacy' => true,
				'separator' => 'before',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'caption_gap',
				'label' => esc_html__( 'Gap between caption elements', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption__wrap' => 'gap: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_nav_style',
				'title' => esc_html__( 'Navigation Arrows', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_width',
				'label' => esc_html__( 'Width', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'width: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'   => 'nav_height',
				'label' => esc_html__( 'Height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_size',
				'label' => esc_html__( 'Arrow Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'font-size: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id'        => 'tabs_nav_prev_next_style',
				'separator' => 'after',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_nav_prev',
				'title' => esc_html__( 'Prev Arrow (Default)', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'nav_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'disable_radius' => true,
				'css_selector'   => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'nav_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'is_legacy' => true,
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_nav_next',
				'title' => esc_html__( 'Next Arrow', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'nav_border_next',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'disable_radius' => true,
				'css_selector'   => array(
					'{{WRAPPER}} .jet-calendar-nav__link.nav-link-next' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'nav_border_radius_next',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'is_legacy' => true,
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link.nav-link-next' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id' => 'tabs_nav_style',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_nav_normal',
				'title' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_background_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_nav_hover',
				'title' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_color_hover',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_background_color_hover',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_border_color_hover',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'section_week_style',
			array(
				'id'         => 'section_week_style',
				'title'      => esc_html__( 'Week Days', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'week_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type' => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__day' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'     => 'week_txt_color',
				'label'  => esc_html__( 'Text Color', 'jet-engine' ),
				'type'   => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__day' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'week_txt_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__day' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'week_days_gap',
				'type'       => 'range',
				'label'      => __( 'Gap Between Days', 'jet-engine' ),
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 50,
						),
					),
				),
				'css_selector'  => array(
					'{{WRAPPER}} .jet-md-calendar__days' => 'gap: {{VALUE}};',
					'{{WRAPPER}} .jet-md-calendar__events' => 'column-gap: {{VALUE}};',
					'{{WRAPPER}} .jet-md-calendar__week' => 'padding-top: {{VALUE}};',
					'{{WRAPPER}} .jet-md-calendar__days-ow' => 'gap: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'week_padding',
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => 'dimensions',
				'size_units' => array( 'px', '%', 'em' ),
				'css_selector'  => array(
					'{{WRAPPER}} .jet-md-calendar__day' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'week_border',
				'type'           => 'border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'css_selector'   => [
					'{{WRAPPER}} .jet-md-calendar__day' => '{{VALUE}}',
				],
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'section_do_week_style',
			array(
				'id'         => 'section_do_week_style',
				'title'      => esc_html__( 'Week Days Names', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'do_week_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type' => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__day-ow' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'     => 'do_week_txt_color',
				'label'  => esc_html__( 'Text Color', 'jet-engine' ),
				'type'   => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__day-ow' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'       => 'do_week_txt_typography',
				'type'      => 'typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__day-ow' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'           => 'do_week_padding',
				'label'        => esc_html__( 'Padding', 'jet-engine' ),
				'type'         => 'dimensions',
				'size_units'   => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__day-ow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'do_week_border',
				'type'           => 'border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'css_selector'   => [
					'{{WRAPPER}} .jet-md-calendar__day-ow' => '{{VALUE}}',
				],
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'section_event_badge_style',
			array(
				'id'         => 'section_event_badge_style',
				'title'      => esc_html__( 'Event Badge', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'event_badge_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'     => 'event_badge_color',
				'label'  => esc_html__( 'Text Color', 'jet-engine' ),
				'type'   => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => '--jet-mdc-c-event-text: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'event_badge_background_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => '--jet-mdc-c-event: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_badge_padding',
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => 'dimensions',
				'size_units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_badge_border_width',
				'label'      => esc_html__( 'Border Width', 'jet-engine' ),
				'type'       => 'range',
				'size_units' => array( 'px' ),
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 10,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => 'border-width: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'     => 'event_badge_border_color',
				'label'  => esc_html__( 'Border Color', 'jet-engine' ),
				'type'   => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => '--jet-mdc-c-event-bd: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_badge_marker_border_radius',
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => 'dimensions',
				'size_units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'     => 'event_badge_marker_color',
				'label'  => esc_html__( 'Dot Marker Color', 'jet-engine' ),
				'type'   => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event .jet-md-calendar__dot' => '--jet-mdc-c-dot: {{VALUE}};',
				),
				'condition' => array(
					'event_marker' => true,
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_badge_marker_size',
				'label'      => esc_html__( 'Dot Marker Size', 'jet-engine' ),
				'type'       => 'range',
				'size_units' => array( 'px' ),
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 4,
							'max'  => 40,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event .jet-md-calendar__dot' => 'width: {{VALUE}}; height: {{VALUE}};',
				),
				'condition' => array(
					'event_marker' => true,
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_badge_marker_radius',
				'label'      => esc_html__( 'Dot Marker Radius', 'jet-engine' ),
				'type'       => 'range',
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 40,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event .jet-md-calendar__dot' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'event_marker' => true,
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_badge_marker_gap',
				'label'      => esc_html__( 'Dot Marker Gap', 'jet-engine' ),
				'type'       => 'range',
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 20,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event' => 'column-gap: {{VALUE}};',
				),
				'condition' => array(
					'event_marker' => true,
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_badges_gap',
				'label'      => esc_html__( 'Gap Around Event Badges', 'jet-engine' ),
				'type'       => 'range',
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 20,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__events' => 'row-gap: {{VALUE}}; padding-bottom: {{VALUE}};',
					'{{WRAPPER}} .jet-md-calendar__event' => 'margin-left: {{VALUE}}; margin-right: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'section_event_content_style',
			array(
				'id'         => 'section_event_content_style',
				'title'      => esc_html__( 'Event Content Popup', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'event_content_bg_color',
				'label' => esc_html__( 'Overalay Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event-overlay' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_content_popup_width',
				'label'      => esc_html__( 'Popup Width', 'jet-engine' ),
				'type'       => 'range',
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 200,
							'max'  => 800,
						),
					),
					array(
						'value'     => 'vw',
						'intervals' => array(
							'step' => 1,
							'min'  => 20,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event-body' => 'width: {{VALUE}}{{UNIT}}; max-width: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_content_height',
				'label'      => esc_html__( 'Max Height', 'jet-engine' ),
				'type'       => 'range',
				'units'      => array( 'px', 'vh' ),
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 200,
							'max'  => 800,
						),
					),
					array(
						'value'     => 'vw',
						'intervals' => array(
							'step' => 1,
							'min'  => 20,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event-body' => 'max-height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'     => 'event_content_close_color',
				'label'  => esc_html__( 'Close Button Color', 'jet-engine' ),
				'type'   => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_content_close_size',
				'label'      => esc_html__( 'Close Button Size', 'jet-engine' ),
				'type'       => 'range',
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event-close' => 'width: {{VALUE}}; height: {{VALUE}}; font-size: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_content_close_h_position',
				'label'      => esc_html__( 'Close Button Horizontal Position', 'jet-engine' ),
				'type'       => 'range',
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => -50,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event-close' => 'right: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'         => 'event_content_close_v_position',
				'label'      => esc_html__( 'Close Button Vertical Position', 'jet-engine' ),
				'type'       => 'range',
				'units'      => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => -50,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-md-calendar__event-close' => 'top: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_section();
	}

	public function render_callback( $attributes = array() ) {

		$render = $this->get_render_instance( $attributes );

		jet_engine()->frontend->frontend_scripts();

		$this->_root['class'][] = 'jet-multiday-listing-calendar-block';
		$this->_root['data-element-id'] = $attributes['_block_id'];
		$this->_root['data-is-block'] = $this->get_block_name();

		if ( ! empty( $attributes['className'] ) ) {
			$this->_root['class'][] = $attributes['className'];
		}

		if ( ! empty( $attributes['_element_id'] ) ) {
			$this->_root['id'] = $attributes['_element_id'];
		}

		$result = sprintf(
			'<div %1$s>%2$s</div>',
			$this->get_root_attr_string(),
			$render->get_content()
		);

		return $result;
	}

}
