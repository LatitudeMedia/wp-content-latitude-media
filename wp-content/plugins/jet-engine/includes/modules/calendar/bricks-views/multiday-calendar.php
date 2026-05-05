<?php

namespace Jet_Engine\Modules\Calendar\Bricks_Views;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Multiday_Calendar extends Calendar {

	public $category = 'jetengine';
	public $name = 'jet-listing-multiday-calendar';
	public $icon = 'jet-engine-icon-multiday-calendar';
	public $css_selector = '.jet-md-calendar';
	public $scripts = [ 'jetEngineBricks' ];

	public $jet_element_render = 'listing-multiday-calendar';

	public function get_label() {
		return esc_html__( 'Multi-Day Calendar', 'jet-engine' );
	}

	public function set_control_groups() {

		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_event_content',
			[
				'title' => esc_html__( 'Event Badge Content', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_group_query_settings();
		$this->register_group_visibility_settings();

		$this->register_jet_control_group(
			'section_caption_style',
			[
				'title' => esc_html__( 'Caption', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_nav_style',
			[
				'title' => esc_html__( 'Navigation Arrows', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_week_style',
			[
				'title' => esc_html__( 'Week Days', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_do_week_style',
			[
				'title' => esc_html__( 'Week Days Names', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_event_badge_style',
			[
				'title' => esc_html__( 'Event Badge', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_event_content_style',
			[
				'title' => esc_html__( 'Event Content Popup', 'jet-engine' ),
				'tab'   => 'style',
			]
		);
	}

	public function set_controls() {

		$this->start_jet_control_group( 'section_general' );

		$this->register_general_calendar_controls(
			[
				'lisitng_id' => [
					'label' => esc_html__( 'Listing to open', 'jet-engine' ),
				],
				'group_by' => [
					'before_controls' => [
						'query_notice' => [
							'tab'   => 'content',
							'label' => esc_html__( 'Please note: For non-post listings (users, terms, CCT etc.) set the Query with Custom Query settings.', 'jet-engine' ),
							'type'  => 'info',
						],
					],
				],
				'group_by_key' => [
					'description' => esc_html__( 'Could be meta field or item property field (depends on query used). This field must contain date to group items by. Works only if "Save as timestamp" option for this field is active', 'jet-engine' ),
				],
				'end_date_key' => [
					'description' => esc_html__( 'If you used "Advanced Datetime" meta field type you can leave this field empty. This field must contain date when event ends. Works only if "Save as timestamp" option for meta field is active.', 'jet-engine' ),
				],
				'start_year_select' => [
					'required' => [
						[ 'allow_date_select', '=', true ],
						[ 'hide_past_events', '=', false ],
					],
				],
				'cache_timeout' => [
					'step' => 1,
				],
				'max_cache' => [
					'step' => 1,
				],
				'use_custom_post_types' => false,
				'custom_post_types' => false,
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_event_content' );

		$macros_generator_link     = admin_url( 'admin.php?page=jet-engine#macros_generator' );
		$shortcode_generator_link = admin_url( 'admin.php?page=jet-engine#shortcode_generator' );

		$this->register_jet_control(
			'event_content',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Badge Content', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => '%title%',
				'description' => sprintf(
					__( 'Supports HTML tags, JetEngine <a href="%1$s" target="_blank">macros</a> and <a href="%2$s" target="_blank">shortcodes</a>.', 'jet-engine' ),
					esc_url( $macros_generator_link ),
					esc_url( $shortcode_generator_link )
				),
			]
		);

		$this->register_jet_control(
			'event_marker',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Badge Marker', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => true,
				'description' => esc_html__( 'Show event badge dot marker for each event in the calendar', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'use_dynamic_styles',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Use Dynamic Styles', 'jet-engine' ),
				'type'        => 'checkbox',
				'description' => esc_html__( 'Allows setting badge color, background, border color and dot color based on the specific event data.', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'use_dynamic_styles_notice',
			[
				'tab'         => 'content',
				'description' => sprintf(
					__( 'Specific event badge styles could be set only by using JetEngine <a href="%1$s" target="_blank">macros</a> or <a href="%2$s" target="_blank">shortcodes</a>. Generated macro/shortcode must return a color value. Put generated macro/shortcode in the appropriate field below and event-specific color value will be applied on the front-end.', 'jet-engine' ),
					esc_url( $macros_generator_link ),
					esc_url( $shortcode_generator_link )
				),
				'type'     => 'info',
				'required' => [ 'use_dynamic_styles', '=', true ],
			]
		);

		$this->register_jet_control(
			'dynamic_badge_color',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Badge Text Color', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Defines the text color for the event badge.', 'jet-engine' ),
				'required'    => [ 'use_dynamic_styles', '=', true ],
			]
		);

		$this->register_jet_control(
			'dynamic_badge_bg_color',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Badge Background Color', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Defines the background color for the event badge.', 'jet-engine' ),
				'required'    => [ 'use_dynamic_styles', '=', true ],
			]
		);

		$this->register_jet_control(
			'dynamic_badge_border_color',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Badge Border Color', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Defines the border color for the event badge (the remaining border styles can be set in the Styles tab).', 'jet-engine' ),
				'required'    => [ 'use_dynamic_styles', '=', true ],
			]
		);

		$this->register_jet_control(
			'dynamic_badge_dot_color',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Badge Dot Color', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Defines the dot color for the event badge.', 'jet-engine' ),
				'required'    => [ 'use_dynamic_styles', '=', true ],
			]
		);

		$this->end_jet_control_group();

		$this->register_controls_query_settings();
		$this->register_controls_visibility_settings();

		$this->start_jet_control_group( 'section_caption_style' );

		$this->register_jet_control(
			'caption_layout',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Layout', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'layout-1' => esc_html__( 'Layout 1', 'jet-engine' ),
					'layout-2' => esc_html__( 'Layout 2', 'jet-engine' ),
					'layout-3' => esc_html__( 'Layout 3', 'jet-engine' ),
					'layout-4' => esc_html__( 'Layout 4', 'jet-engine' ),
				],
				'default' => 'layout-1',
			]
		);

		$this->register_jet_control(
			'caption_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-caption',
					],
				],
			]
		);

		$this->register_jet_control(
			'caption_txt_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Label Color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-calendar-caption__name',
					],
				],
			]
		);

		$this->register_jet_control(
			'caption_txt_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => '.jet-calendar-caption__name',
					],
				],
			]
		);

		$this->register_jet_control(
			'caption_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-calendar-caption',
					],
				],
			]
		);

		$this->register_jet_control(
			'caption_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => '.jet-calendar-caption',
					],
				],
			]
		);

		$this->register_jet_control(
			'caption_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-caption',
					],
				],
			]
		);

		$this->register_jet_control(
			'caption_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap between caption elements', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => 0,
				'max'   => 100,
				'css'   => [
					[
						'property' => 'gap',
						'selector' => '.jet-calendar-caption__wrap',
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_nav_style' );

		$this->register_jet_control(
			'nav_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box Width', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => 10,
				'max'   => 100,
				'css'   => [
					[
						'property' => 'width',
						'selector' => '.jet-calendar-nav__link',
					],
				],
			]
		);

		$this->register_jet_control(
			'nav_height',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box Height', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => 10,
				'max'   => 100,
				'css'   => [
					[
						'property' => 'height',
						'selector' => '.jet-calendar-nav__link',
					],
				],
			]
		);

		$this->register_jet_control(
			'nav_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Arrow size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => 10,
				'max'   => 100,
				'css'   => [
					[
						'property' => 'font-size',
						'selector' => '.jet-calendar-nav__link',
					],
				],
			]
		);

		$this->register_jet_control(
			'nav_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-nav__link',
					],
				],
			]
		);

		$this->register_jet_control(
			'nav_border_next',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border (Next Arrow)', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-nav__link.nav-link-next',
					],
				],
			]
		);

		$this->register_jet_control(
			'nav_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-calendar-nav__link',
					],
				],
			]
		);

		$this->register_jet_control(
			'nav_background_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-nav__link',
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_week_style' );

		$this->register_jet_control(
			'week_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-md-calendar__day',
					],
				],
			]
		);

		$this->register_jet_control(
			'week_txt_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-md-calendar__day',
					],
				],
			]
		);

		$this->register_jet_control(
			'week_txt_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => '.jet-md-calendar__date'
					],
				],
			]
		);

		$this->register_jet_control(
			'icon_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap Between Days', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => 0,
				'max'   => 50,
				'css'   => [
					[
						'property' => 'gap',
						'selector' => '.jet-md-calendar__days',
					],
					[
						'property' => 'column-gap',
						'selector' => '.jet-md-calendar__events',
					],
					[
						'property' => 'padding-top',
						'selector' => '.jet-md-calendar__week',
					],
					[
						'property' => 'gap',
						'selector' => '.jet-md-calendar__days-ow',
					],
				],
			]
		);

		$this->register_jet_control(
			'week_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-md-calendar__day',
					],
				],
			]
		);

		$this->register_jet_control(
			'week_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-md-calendar__day',
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_do_week_style' );

		$this->register_jet_control(
			'do_week_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-md-calendar__day-ow',
					],
				],
			]
		);

		$this->register_jet_control(
			'do_week_txt_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-md-calendar__day-ow',
					],
				],
			]
		);

		$this->register_jet_control(
			'do_week_txt_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => '.jet-md-calendar__day-ow',
					],
				],
			]
		);

		$this->register_jet_control(
			'do_week_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-md-calendar__day-ow',
					],
				],
			]
		);

		$this->register_jet_control(
			'do_week_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-md-calendar__day-ow',
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_event_badge_style' );

		$this->register_jet_control(
			'event_badge_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => '.jet-md-calendar__event',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_badge_background_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => '--jet-mdc-c-event',
						'selector' => '.jet-md-calendar__event',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_badge_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-md-calendar__event',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_badge_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-md-calendar__event',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_badge_marker_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Dot marker color', 'jet-engine' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => '--jet-mdc-c-dot',
						'selector' => '.jet-md-calendar__dot',
					],
				],
				'required' => [ 'event_marker', '=', true ],
			]
		);

		$this->register_jet_control(
			'event_badge_marker_size',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Dot marker size', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'min'      => 4,
				'max'      => 40,
				'css'      => [
					[
						'property' => 'width',
						'selector' => '.jet-md-calendar__dot',
					],
					[
						'property' => 'height',
						'selector' => '.jet-md-calendar__dot',
					],
				],
				'required' => [ 'event_marker', '=', true ],
			]
		);

		$this->register_jet_control(
			'event_badge_marker_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Dot Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-md-calendar__dot',
					],
				],
				'required' => [ 'event_marker', '=', true ],
			]
		);

		$this->register_jet_control(
			'event_badge_marker_gap',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Dot marker gap', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'min'      => 0,
				'max'      => 20,
				'css'      => [
					[
						'property' => 'column-gap',
						'selector' => '.jet-md-calendar__event',
					],
				],
				'required' => [ 'event_marker', '=', true ],
			]
		);

		$this->register_jet_control(
			'event_badges_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap around event badges', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => 0,
				'max'   => 20,
				'css'   => [
					[
						'property' => 'row-gap',
						'selector' => '.jet-md-calendar__events',
					],
					[
						'property' => 'padding-bottom',
						'selector' => '.jet-md-calendar__events',
					],
					[
						'property' => 'margin-left',
						'selector' => '.jet-md-calendar__event',
					],
					[
						'property' => 'margin-right',
						'selector' => '.jet-md-calendar__event',
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_event_content_style' );

		$this->register_jet_control(
			'event_content_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Overlay color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-md-calendar__event-overlay',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_content_popup_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Popup width', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => 200,
				'max'   => 800,
				'css'   => [
					[
						'property' => 'width',
						'selector' => '.jet-md-calendar__event-body',
					],
					[
						'property' => 'max-width',
						'selector' => '.jet-md-calendar__event-body',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_content_height',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Max height', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => 200,
				'max'   => 800,
				'css'   => [
					[
						'property' => 'max-height',
						'selector' => '.jet-md-calendar__event-body',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_content_close_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Close button color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-md-calendar__event-close',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_content_close_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Close button size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => 10,
				'max'   => 50,
				'css'   => [
					[
						'property' => 'width',
						'selector' => '.jet-md-calendar__event-close',
					],
					[
						'property' => 'height',
						'selector' => '.jet-md-calendar__event-close',
					],
					[
						'property' => 'font-size',
						'selector' => '.jet-md-calendar__event-close',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_content_close_h_position',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Close button horizontal position', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => -50,
				'max'   => 50,
				'css'   => [
					[
						'property' => 'right',
						'selector' => '.jet-md-calendar__event-close',
					],
				],
			]
		);

		$this->register_jet_control(
			'event_content_close_v_position',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Close button vertical position', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'min'   => -50,
				'max'   => 50,
				'css'   => [
					[
						'property' => 'top',
						'selector' => '.jet-md-calendar__event-close',
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function enqueue_scripts() {
		parent::enqueue_scripts();
		wp_enqueue_style(
			'jet-engine-multiday-calendar',
			jet_engine()->plugin_url( 'includes/modules/calendar/assets/css/multiday-calendar.css' ),
			[],
			jet_engine()->get_version()
		);
	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs = parent::parse_jet_render_attributes( $attrs );

		$attrs['show_posts_nearby_months']   = $attrs['show_posts_nearby_months'] ?? false;
		$attrs['hide_past_events']           = $attrs['hide_past_events'] ?? false;
		$attrs['use_dynamic_styles']         = $attrs['use_dynamic_styles'] ?? false;
		$attrs['event_content']              = $attrs['event_content'] ?? '%title%';
		$attrs['dynamic_badge_color']        = $attrs['dynamic_badge_color'] ?? '';
		$attrs['dynamic_badge_bg_color']     = $attrs['dynamic_badge_bg_color'] ?? '';
		$attrs['dynamic_badge_border_color'] = $attrs['dynamic_badge_border_color'] ?? '';
		$attrs['dynamic_badge_dot_color']    = $attrs['dynamic_badge_dot_color'] ?? '';
		$attrs['cache_enabled']              = $attrs['cache_enabled'] ?? false;
		$attrs['cache_timeout']              = $attrs['cache_timeout'] ?? 60;
		$attrs['max_cache']                  = $attrs['max_cache'] ?? 12;

		$attrs['event_marker'] = isset( $attrs['event_marker'] ) ? filter_var( $attrs['event_marker'], FILTER_VALIDATE_BOOLEAN ) : false;

		return $attrs;
	}

}
