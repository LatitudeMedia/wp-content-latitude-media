<?php
/**
 * Class: Jet_Listing_Calendar_Multiday_Provider
 * Name: JetEngine Multiday Calendar
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (
	! class_exists( 'Jet_Smart_Filters_Provider_Jet_Engine_Calendar' )
	&& file_exists( jet_smart_filters()->plugin_path( 'includes/providers/jet-engine-calendar.php' ) )
) {
	require_once jet_smart_filters()->plugin_path( 'includes/providers/jet-engine-calendar.php' );
}

/**
 * Define Jet_Listing_Calendar_Multiday_Provider class
 */
class Jet_Listing_Calendar_Multiday_Provider extends Jet_Smart_Filters_Provider_Jet_Engine_Calendar {

	/**
	 * Add widget settings
	 */
	public function add_month_settings( $settings, $widget ) {

		if ( 'jet-listing-multiday-calendar' !== $widget->get_name() ) {
			return $settings;
		}

		if ( ! empty( $_REQUEST['settings'] ) ) {
			return $_REQUEST['settings'];
		} else {
			return $settings;
		}
	}

	/**
	 * Store default query args
	 */
	public function store_default_query( $args, $widget ) {

		if ( 'jet-listing-multiday-calendar' !== $widget->get_name() ) {
			return $args;
		}

		$settings = $widget->get_settings();

		if ( empty( $settings['_element_id'] ) ) {
			$query_id = false;
		} else {
			$query_id = $settings['_element_id'];
		}

		jet_smart_filters()->query->store_provider_default_query( $this->get_id(), $args, $query_id );

		if ( is_callable( array( $widget, 'get_required_settings' ) ) ) {
			$provider_settings = call_user_func( array( $widget, 'get_required_settings' ) );
		} else {
			$provider_settings = array(
				'lisitng_id'          => isset( $settings['lisitng_id'] ) ? $settings['lisitng_id'] : false,
				'group_by'            => isset( $settings['group_by'] ) ? $settings['group_by'] : false,
				'group_by_key'        => isset( $settings['group_by_key'] ) ? $settings['group_by_key'] : false,
				'allow_multiday'      => isset( $settings['allow_multiday'] ) ? $settings['allow_multiday'] : false,
				'end_date_key'        => isset( $settings['end_date_key'] ) ? $settings['end_date_key'] : false,
				'custom_start_from'   => isset( $settings['custom_start_from'] ) ? $settings['custom_start_from'] : false,
				'week_days_format'    => isset( $settings['week_days_format'] ) ? $settings['week_days_format'] : false,
				'start_from_month'    => isset( $settings['start_from_month'] ) ? $settings['start_from_month'] : date( 'F' ),
				'start_from_year'     => isset( $settings['start_from_year'] ) ? $settings['start_from_year'] : date( 'Y' ),
				'hide_widget_if'      => isset( $settings['hide_widget_if'] ) ? $settings['hide_widget_if'] : false,
				'caption_layout'      => isset( $settings['caption_layout'] ) ? $settings['caption_layout'] : 'layout-1',
				'event_content' 	=> isset( $settings['event_content'] ) ? $settings['event_content'] : '',
				'event_marker'       => isset( $settings['event_marker'] ) ? $settings['event_marker'] : false,
				'use_dynamic_styles' => isset( $settings['use_dynamic_styles'] ) ? $settings['use_dynamic_styles'] : false,
				'dynamic_badge_color' => isset( $settings['dynamic_badge_color'] ) ? $settings['dynamic_badge_color'] : false,
				'dynamic_badge_bg_color' => isset( $settings['dynamic_badge_bg_color'] ) ? $settings['dynamic_badge_bg_color'] : false,
				'dynamic_badge_border_color' => isset( $settings['dynamic_badge_border_color'] ) ? $settings['dynamic_badge_border_color'] : false,
				'dynamic_badge_dot_color' => isset( $settings['dynamic_badge_dot_color'] ) ? $settings['dynamic_badge_dot_color'] : false,
				'show_posts_nearby_months' => isset( $settings['show_posts_nearby_months'] ) ? $settings['show_posts_nearby_months'] : false,
				'hide_past_events' => isset( $settings['hide_past_events'] ) ? $settings['hide_past_events'] : false,
			);
		}

		jet_smart_filters()->providers->store_provider_settings( $this->get_id(), $provider_settings, $query_id );

		$args['suppress_filters']  = false;
		$args['jet_smart_filters'] = jet_smart_filters()->query->encode_provider_data(
			$this->get_id(),
			$query_id
		);

		return $args;
	}

	/**
	 * Get provider name
	 */
	public function get_name() {
		return __( 'JetEngine Multi-Day Calendar', 'jet-engine' );
	}

	/**
	 * Get provider ID
	 */
	public function get_id() {
		return 'jet-engine-multiday-calendar';
	}

	/**
	 * Get filtered provider content
	 */
	public function ajax_get_content() {

		if ( ! function_exists( 'jet_engine' ) ) {
			return;
		}

		add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 2 );
		add_filter( 'jet-engine/listing/grid/custom-settings', array( $this, 'add_settings' ), 10, 2 );

		$attrs  = $this->sanitize_settings( jet_smart_filters()->query->get_query_settings() );
		$render = jet_engine()->listings->get_render_instance( 'listing-multiday-calendar', $attrs );

		$render->render();

	}

	/**
	 * Get provider wrapper selector
	 */
	public function get_wrapper_selector() {
		return '.jet-md-calendar';
	}

	/**
	 * Action for wrapper selector - 'insert' into it or 'replace'
	 */
	public function get_wrapper_action() {
		return 'replace';
	}

	/**
	 * If added unique ID this parameter will determine - search selector inside this ID, or is the same element
	 */
	public function in_depth() {
		return true;
	}

	/**
	 * Add custom settings for AJAX request
	 */
	public function add_settings( $settings, $widget ) {

		if ( 'jet-listing-multiday-calendar' !== $widget->get_name() ) {
			return $settings;
		}

		return jet_smart_filters()->query->get_query_settings();
	}

	/**
	 * Add custom query arguments
	 */
	public function add_query_args( $args, $widget ) {

		if ( 'jet-listing-multiday-calendar' !== $widget->get_name() ) {
			return $args;
		}

		if ( ! jet_smart_filters()->query->is_ajax_filter() && ! $this->is_month_request() ) {

			$settings = $widget->get_settings();

			if ( empty( $settings['_element_id'] ) ) {
				$query_id = 'default';
			} else {
				$query_id = $settings['_element_id'];
			}

			$request_query_id = jet_smart_filters()->query->get_current_provider( 'query_id' );

			if ( $query_id !== $request_query_id ) {
				return $args;
			}

		}

		if ( $this->is_month_request() ) {
			jet_smart_filters()->query->get_query_from_request( isset( $_REQUEST['query'] ) ? $_REQUEST : array() );
		}

		return array_merge( $args, jet_smart_filters()->query->get_query_args() );
	}
}
