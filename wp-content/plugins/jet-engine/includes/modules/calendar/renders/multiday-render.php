<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Jet_Listing_Render_Multiday_Calendar' ) ) {

	class Jet_Listing_Render_Multiday_Calendar extends Jet_Listing_Render_Calendar {

		protected $_rendered_content = [];

		public function get_name() {
			return 'jet-listing-multiday-calendar';
		}

		public function default_settings() {

			$defaults = parent::default_settings();
			$defaults['event_content']  = '%title%';
			$defaults['allow_multiday'] = true;
			$defaults['event_marker'] = true;
			$defaults['use_dynamic_styles'] = false;
			$defaults['dynamic_badge_color'] = '';
			$defaults['dynamic_badge_bg_color'] = '';
			$defaults['dynamic_badge_border_color'] = '';
			$defaults['dynamic_badge_dot_color'] = '';

			return $defaults;
		}

		public function posts_template( $query, $settings ) {

			if ( ! wp_style_is( 'jet-engine-multiday-calendar', 'done' ) ) {

				wp_enqueue_style(
					'jet-engine-multiday-calendar',
					jet_engine()->plugin_url( 'includes/modules/calendar/assets/css/multiday-calendar.css' ),
					array(),
					jet_engine()->get_version()
				);

				wp_print_styles( 'jet-engine-multiday-calendar' );
			}

			global $wp_locale;

			$base_class    = 'jet-md-calendar';
			$current_month = $this->get_current_month();
			$month         = array(
				'start' => $current_month,
				'end'   => $this->get_current_month( true ),
			);

			$events      = $this->get_events( $query, $settings, $month );
			$days_num    = $this->get_days_num();
			$week_begins = (int) get_option( 'start_of_week' );
			$first_day   = (int) date( 'w', $current_month );
			$offset      = $first_day - $week_begins;

			if ( 0 > $offset ) {
				$offset = 7 - abs( $offset );
			}

			$weeks = array();
			$week  = array();

			for ( $i = 0; $i < $offset; $i++ ) {
				$week[] = 0 - ( $offset - $i );
			}

			for ( $day = 1; $day <= $days_num; $day++ ) {
				$week[] = $day;
				if ( 7 === count( $week ) ) {
					$weeks[] = $week;
					$week    = array();
				}
			}

			if ( ! empty( $week ) ) {
				$new_month = $days_num;
				while ( count( $week ) < 7 ) {
					$new_month += 1;
					$week[] = $new_month;
				}
				$weeks[] = $week;
			}

			$container_classes = [
				'jet-calendar',
				$base_class,
				'jet-listing-grid--' . absint( $settings['lisitng_id'] ), // for inline CSS consistency between differen views and listing widgets
			];

			$data_settings = $this->get_data_settings( $settings, $current_month, [
				'event_content',
				'event_marker',
				'use_dynamic_styles',
				'dynamic_badge_color',
				'dynamic_badge_bg_color',
				'dynamic_badge_border_color',
				'dynamic_badge_dot_color',
				'show_posts_nearby_months',
				'hide_past_events',
			] );

			$cache_enabled = filter_var( $settings['cache_enabled'], FILTER_VALIDATE_BOOLEAN );
			$cache_id      = false;

			$events_content = '';

			if ( $cache_enabled && isset( $data_settings['cache_id'] ) ) {
				$cache_id = sprintf( ' data-cache-id="%1$s"', $data_settings['cache_id'] );
			}

			printf(
				'<div class="%1$s" data-settings="%2$s" data-post="%3$d" data-listing-source="%4$s" data-query-id="%5$s" data-renderer="%7$s"%6$s>',
				esc_attr( implode( ' ', $container_classes ) ),
				$this->encode_engine_shortcode( htmlspecialchars( json_encode( $data_settings ) ) ),
				get_the_ID(),
				jet_engine()->listings->data->get_listing_source(),
				$this->listing_query_id,
				$cache_id ?? '',
				'listing-multiday-calendar'
			);

			echo '<div class="jet-calendar-caption">';
			$this->render_calendar_navigation( $settings, $current_month );
			echo '</div>';

			$days_format = ! empty( $settings['week_days_format'] ) ? $settings['week_days_format'] : 'short';

			echo '<div class="' . esc_attr( $base_class . '__days-ow' ) . '">';
				for ( $i = 0; $i < 7; $i++ ) {

					$day_index = ( $week_begins + $i ) % 7;
					$label     = $wp_locale->get_weekday( $day_index );

					switch ( $days_format ) {
						case 'short':
							$label = $wp_locale->get_weekday_abbrev( $label );
							break;

						case 'initial':
							$label = $wp_locale->get_weekday_initial( $label );
							break;
					}

					echo '<div class="' . esc_attr( $base_class . '__day-ow' ) . '">' . esc_html( $label ) . '</div>';
				}
			echo '</div>';

			$listing_id = isset( $settings['lisitng_id'] ) ? absint( $settings['lisitng_id'] ) : 0;

			foreach ( $weeks as $week_index => $days ) {

				$week_start_ts = strtotime( date( 'Y-m-d', $current_month ) . ' +' . ( $week_index * 7 - $offset ) . ' days' );
				$week_end_ts   = $week_start_ts + 6 * DAY_IN_SECONDS;

				echo '<section class="' . esc_attr( $base_class . '__week' ) . '">';

				echo '<div class="' . esc_attr( $base_class . '__days' ) . '">';

				foreach ( $days as $day ) {
					if ( $day > 0 && $day <= $days_num ) {
						echo '<div class="' . esc_attr( $base_class . '__day' ) . '"><span class="' . esc_attr( $base_class . '__date' ) . '">' . $day . '</span></div>';
					} else {

						if ( $days_num < $day ) {
							$nearby_day = $day - $days_num;
						} else {
							$prev_month = strtotime( date( 'Y-m-d', $current_month ) . ' -1 month' );
							$days_in_prev_month = (int) date( 't', $prev_month );
							$nearby_day = $days_in_prev_month + $day + 1;
						}

						echo '<div class="' . esc_attr( $base_class . '__day is-nearby-month' ) . '"><span class="' . esc_attr( $base_class . '__date' ) . '">' . $nearby_day . '</span></div>';
					}
				}
				echo '</div>';

				echo '<div class="' . esc_attr( $base_class . '__events' ) . '">';

				foreach ( $events as $event ) {

					$start_ts = $event['start'];
					$end_ts   = $event['end'];

					if ( $end_ts < $week_start_ts || $start_ts > $week_end_ts ) {
						continue;
					}

					$seg_start = max( $start_ts, $week_start_ts );
					$seg_end   = min( $end_ts, $week_end_ts );

					$start_col = floor( ( $seg_start - $week_start_ts ) / DAY_IN_SECONDS ) + 1;
					$end_col   = floor( ( $seg_end - $week_start_ts ) / DAY_IN_SECONDS ) + 2;

					$classes = $base_class . '__event';

					if ( $start_ts < $week_start_ts ) {
						$classes .= ' ' . $base_class . '__event--cont-left';
					}

					if ( $end_ts > $week_end_ts ) {
						$classes .= ' ' . $base_class . '__event--cont-right';
					}

					jet_engine()->listings->data->set_current_object( $event['post'] );

					$content = apply_filters(
						'jet-engine/calendar/multiday-event/badge-content',
						null,
						$event,
						$settings
					);

					if ( null === $content ) {
						$content = $settings['event_content'];
						$content = wp_kses_post( $settings['event_content'] );
						$content = jet_engine()->listings->macros->do_macros( $content );
						$content = do_shortcode( $this->decode_engine_shortcode( $content ) );
					}

					$dot = '';
					$event_marker = ! empty( $settings['event_marker'] ) ? filter_var( $settings['event_marker'], FILTER_VALIDATE_BOOLEAN ) : false;

					$event_badge_styles = [
						'grid-column' => sprintf( '%d / %d', $start_col, $end_col ),
					];

					$dynamic_styles = $this->get_event_badge_dynamic_styles( $settings );

					if ( ! empty( $dynamic_styles ) && is_array( $dynamic_styles ) ) {
						$event_badge_styles = array_merge( $event_badge_styles, $dynamic_styles );
					}

					$styles_string = '';

					foreach ( $event_badge_styles as $style => $value ) {
						$styles_string .= $style . ':' . $value . ';';
					}

					if ( $event_marker) {

						$dot_style = ! empty( $dynamic_styles['--jet-mdc-c-dot'] ) ? sprintf( ' style="background-color: %s;"', esc_attr( $dynamic_styles['--jet-mdc-c-dot'] ) ) : '';

						$dot = sprintf(
							'<span class="%1$s__dot"%2$s></span>',
							esc_attr( $base_class ),
							$dot_style
						);
					}

					echo sprintf(
						'<div class="%1$s" style="%2$s" data-object-id="%5$d">%4$s%3$s</div>',
						esc_attr( $classes ),
						$styles_string,
						$content,
						$dot,
						jet_engine()->listings->data->get_current_object_id()
					);

					$events_content .= $this->get_event_content( $listing_id );
				}

				echo '</div>';
				echo '</section>';

			}

			echo $events_content;

			echo '</div>';
		}

		/**
		 * Encode JetEngine shortcodes to prevent it's execution on page render.
		 *
		 * @param string $string Input string.
		 * @return string Encoded string.
		 */
		public function encode_engine_shortcode( $string ) {

			$encoded = str_replace( '[jet_engine', '[jet-engine', $string );
			$encoded = str_replace( '[/jet_engine]', '[/jet-engine]', $encoded );

			return $encoded;
		}

		/**
		 * Decode JetEngine shortcodes to allow it's execution.
		 *
		 * @param string $string Input string.
		 * @return string Decoded string.
		 */
		public function decode_engine_shortcode( $string ) {

			$decoded = str_replace( '[jet-engine', '[jet_engine', $string );
			$decoded = str_replace( '[/jet-engine]', '[/jet_engine]', $decoded );

			return wp_unslash( $decoded );
		}

		/**
		 * Get dynamic styles for the event badge.
		 *
		 * @param array $settings
		 * @return array
		 */
		protected function get_event_badge_dynamic_styles( $settings = [] ) {

			$styles = array();

			$use_dynamic_styles = ! empty( $settings['use_dynamic_styles'] ) ? filter_var( $settings['use_dynamic_styles'], FILTER_VALIDATE_BOOLEAN ) : false;

			if ( ! $use_dynamic_styles ) {
				return $styles;
			}

			$styles_map = array(
				'dynamic_badge_color'        => '--jet-mdc-c-event-text',
				'dynamic_badge_color'        => 'color',
				'dynamic_badge_bg_color'     => '--jet-mdc-c-event',
				'dynamic_badge_border_color' => '--jet-mdc-c-event-bd',
				'dynamic_badge_border_color' => 'border-color',
				'dynamic_badge_dot_color'    => '--jet-mdc-c-dot',
			);

			foreach ( $styles_map as $setting_key => $css_var ) {

				if ( empty( $settings[ $setting_key ] ) ) {
					continue;
				}

				$color = jet_engine()->listings->macros->do_macros( $settings[ $setting_key ] );
				$color = do_shortcode( $this->decode_engine_shortcode( $color ) );

				if ( ! $color ) {
					continue;
				}

				$styles[ $css_var ] = $color;
			}

			return $styles;
		}

		/**
		 * Get the content for a specific event.
		 *
		 * @param int $listing_id The ID of the listing.
		 * @return string The event content.
		 */
		protected function get_event_content( $listing_id ) {

			$content = '';

			$current_object_id = jet_engine()->listings->data->get_current_object_id();

			if ( $current_object_id && ! empty( $this->_rendered_content[ $current_object_id ] ) ) {
				return $content; // render content only once
			}

			if ( $listing_id ) {
				$content = jet_engine()->frontend->get_listing_item_content( $listing_id );
				$this->_rendered_content[ $current_object_id ] = true;
			}

			$close_button = apply_filters(
				'jet-engine/calendar/multiday-event/close-button-html',
				'<button class="jet-md-calendar__event-close" aria-label="' . esc_attr__( 'Close', 'jet-engine' ) . '"><svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 10.93 5.719-5.72c.146-.146.339-.219.531-.219.404 0 .75.324.75.749 0 .193-.073.385-.219.532l-5.72 5.719 5.719 5.719c.147.147.22.339.22.531 0 .427-.349.75-.75.75-.192 0-.385-.073-.531-.219l-5.719-5.719-5.719 5.719c-.146.146-.339.219-.531.219-.401 0-.75-.323-.75-.75 0-.192.073-.384.22-.531l5.719-5.719-5.72-5.719c-.146-.147-.219-.339-.219-.532 0-.425.346-.749.75-.749.192 0 .385.073.531.219z"/></svg></button>'
			);

			$uid = esc_attr( $current_object_id );

			return '<div class="jet-md-calendar__event-content jet-listing-dynamic-post-' . $uid . '" data-object-id="' . $uid . '"><div class="jet-md-calendar__event-body-container"><div class="jet-md-calendar__event-body">' . $content . '</div>' . $close_button . '</div><div class="jet-md-calendar__event-overlay"></div></div>';
		}

		protected function get_events( $query, $settings, $month ) {

			$events          = array();
			$group_by        = isset( $settings['group_by'] ) ? $settings['group_by'] : 'meta_date';
			$calendar_period = $this->get_date_period_for_query( $settings );
			$show_nearby     = isset( $settings['show_posts_nearby_months'] ) ? filter_var( $settings['show_posts_nearby_months'], FILTER_VALIDATE_BOOLEAN ) : true;
			$hide_past = isset( $settings['hide_past_events'] ) ? filter_var( $settings['hide_past_events'], FILTER_VALIDATE_BOOLEAN ) : false;

			if ( empty( $query ) ) {
				return $events;
			}

			foreach ( $query as $post ) {

				switch ( $group_by ) {
					case 'meta_date':
						$meta_key = esc_attr( $settings['group_by_key'] );
						$end_key  = isset( $settings['end_date_key'] ) ? $settings['end_date_key'] : false;

						if ( ! $end_key ) {
							$end_key = \Jet_Engine_Advanced_Date_Field::instance()->data->end_date_field_name( $meta_key );
						}

						if ( 'WP_Post' === get_class( $post ) ) {
							$starts    = \Jet_Engine_Advanced_Date_Field::instance()->data->get_dates( $post->ID, $meta_key );
							$end_dates = get_post_meta( $post->ID, $end_key, false );
						} else {
							$starts    = $meta_key ? jet_engine()->listings->data->get_meta( $meta_key, $post ) : false;
							$end_dates = $end_key ? jet_engine()->listings->data->get_meta( $end_key, $post ) : false;
						}
						break;

					case 'post_date':
						$starts    = isset( $post->post_date ) ? strtotime( $post->post_date ) : false;
						$end_dates = $starts;
						break;

					case 'post_mod':
						$starts    = isset( $post->post_modified ) ? strtotime( $post->post_modified ) : false;
						$end_dates = $starts;
						break;

					default:

						/**
						 * Make sure is compatible with 3rd-parties,
						 * which works with classic calendar.
						 */
						$starts = apply_filters(
							'jet-engine/listing/calendar/date-key',
							false, $post, $group_by, $this
						);

						/**
						 * Allow 3rd-party plugins to modify event start and end dates
						 * separately, for example Booking checkin-checkout period
						 */
						$starts = apply_filters(
							'jet-engine/calendar/get-event-start',
							$starts, $post, $settings
						);

						$end_dates = apply_filters(
							'jet-engine/calendar/get-event-end',
							$starts, $post, $settings
						);

						break;
				}

				if ( ! is_array( $starts ) ) {
					$starts = array( $starts );
				}

				if ( ! is_array( $end_dates ) ) {
					$end_dates = array( $end_dates );
				}

				foreach ( $starts as $index => $start ) {

					$end = ! empty( $end_dates[ $index ] ) ? $end_dates[ $index ] : false;

					if ( ! \Jet_Engine_Tools::is_valid_timestamp( $start ) || ! 	\Jet_Engine_Tools::is_valid_timestamp( $end ) ) {
						continue;
					}

					if ( $end < $start ) {
						continue;
					}

					$event_start = $start;
					$event_end   = $end;

					if ( $event_end < $event_start ) {
						continue;
					}

					if (
						$event_end < $calendar_period['start']
						|| $event_start > $calendar_period['end']
					) {
						continue;
					}

					if ( ! $show_nearby && ( $event_end < $month['start'] || $event_start > $month['end'] ) ) {
						continue;
					}

					$events[] = array(
						'post'  => $post,
						'start' => $event_start,
						'end'   => $event_end,
					);
				}
			}

			return $events;
		}
	}
}
