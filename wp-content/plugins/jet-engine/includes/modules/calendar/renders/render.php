<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Jet_Listing_Render_Calendar' ) ) {

	class Jet_Listing_Render_Calendar extends Jet_Engine_Render_Listing_Grid {

		public $is_first        = false;
		public $data            = false;
		public $first_day       = false;
		public $last_day        = false;
		public $multiday_events = array();
		public $posts_cache     = array();
		public $start_from      = false;

		public $prev_month_posts = array();
		public $next_month_posts = array();

		public $query_instance = null;

		public function get_name() {
			return 'jet-listing-calendar';
		}

		public function default_settings() {
			return apply_filters( 'jet-engine/calendar/render/default-settings', array(
				'lisitng_id'               => '',
				'group_by'                 => 'post_date',
				'group_by_key'             => '',
				'allow_multiday'           => '',
				'end_date_key'             => '',
				'week_days_format'         => 'short',
				'custom_start_from'        => '',
				'start_from_month'         => date( 'F' ),
				'start_from_year'          => date( 'Y' ),
				'show_posts_nearby_months' => 'yes',
				'hide_past_events'         => '',
				'allow_date_select'        => '',
				'start_year_select'        => '1970',
				'end_year_select'          => '2038',
				'posts_query'              => array(),
				'meta_query_relation'      => 'AND',
				'tax_query_relation'       => 'AND',
				'hide_widget_if'           => '',
				'caption_layout'           => 'layout-1',
				'use_custom_post_types'    => '',
				'custom_post_types'        => array(),
				'custom_query'             => false,
				'custom_query_id'          => null,
				'_element_id'              => '',
				'cache_enabled'            => false,
				'cache_timeout'            => 60,
				'max_cache'                => 12,
			));
		}

		/**
		 * Get posts
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function get_posts( $settings ) {

			add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_calendar_query' ) );
			$args  = $this->build_posts_query_args_array( $settings );
			remove_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_calendar_query' ) );

			$query = new \WP_Query( $args );

			return $query->posts;
		}

		public function render_posts() {

			add_action( 'jet-engine/query-builder/listings/on-query', array( $this, 'add_date_args_to_custom_query' ), 9 );
			parent::render_posts();
			remove_action( 'jet-engine/query-builder/listings/on-query', array( $this, 'add_date_args_to_custom_query' ), 9 );

			wp_cache_delete( 'jet_engine_calendar_requested_dates' );
		}

		/**
		 * 
		 * @param \Jet_Engine\Query_Builder\Queries\Base_Query $query
		 */
		public function add_date_args_to_custom_query( $query ) {
			$this->query_instance = $query;

			/**
			 * Blocks editor renders the block multiple times on saving the post.
			 * This leads to excess meta clauses added to meta query, which sometimes results in too complex SQL queries made.
			 * Flag is added to $query->final_query to adjust for this quirk.
			 * (Also, this may happen when Calendar widget is used with exact same settings multiple times on a page)
			 * 
			 * @see https://github.com/Crocoblock/issues-tracker/issues/18948
			 */
			if ( empty( $query->final_query['_calendar_query_processed'] ) ) {
				$query->final_query['_calendar_query_processed'] = true;
				$query->final_query = $this->add_calendar_query( $query->final_query );
			}

			// Reset query if it was stored before.
			$query->reset_query();
		}

		/**
		 * Prepare date query
		 *
		 * @return array
		 */
		public function add_calendar_query( $args ) {

			$settings = $this->get_settings();
			$group_by = $settings['group_by'];
			$args     = apply_filters( 'jet-engine/listing/calendar/query', $args, $group_by, $this );

			return $args;

		}

		public function get_date_period_for_query( $settings ) {

			$calendar_month = $this->get_current_month();

			$start = $calendar_month;
			$end   = $this->get_current_month( true );

			$show_posts_nearby_months = isset( $settings['show_posts_nearby_months'] ) ? filter_var( $settings['show_posts_nearby_months'], FILTER_VALIDATE_BOOLEAN ) : true;
			$hide_past_events         = isset( $settings['hide_past_events'] ) ? filter_var( $settings['hide_past_events'], FILTER_VALIDATE_BOOLEAN ) : false;

			if ( $show_posts_nearby_months ) {
				$week_begins = (int) get_option( 'start_of_week' );
				$first_day   = date( 'w', $start );
				$prev_days   = $first_day - $week_begins;
				$prev_days   = ( 0 > $prev_days ) ? 7 - abs( $prev_days ) : $prev_days;
				$next_days   = 7 - ( ( $prev_days + $this->get_days_num() ) % 7 );

				if ( $prev_days ) {
					$start = $start - $prev_days * 24 * 60 * 60;
				}

				if ( $next_days ) {
					$end = $end + $next_days * 24 * 60 * 60;
				}
			}

			if ( $hide_past_events ) {
				$today = strtotime( date_i18n( 'Y-m-d' ) );

				if ( $today > $start ) {
					$start = $today;
				}
			}

			$result = array(
				'start' => $start,
				'end'   => $end,
			);

			wp_cache_set( 'jet_engine_calendar_requested_dates', $result );

			return $result;
		}

		/**
		 * Prepare posts for calendar
		 *
		 * @since 3.3.0 added recurring dates support
		 *
		 * @param array $query
		 * @param array $settings
		 * @param array $month
		 * @return array
		 */
		public function prepare_posts_for_calendar( $query, $settings, $month ) {

			$prepared_posts = array();
			$group_by       = $settings['group_by'];
			$key            = false;

			if ( empty( $query ) ) {
				return $prepared_posts;
			}

			foreach ( $query as $post ) {

				switch ( $group_by ) {

					case 'post_date':
						$keys = strtotime( $post->post_date );
						break;

					case 'post_mod':
						$keys = strtotime( $post->post_modified );
						break;

					case 'meta_date':

						$meta_key = esc_attr( $settings['group_by_key'] );
						$multiday = isset( $settings['allow_multiday'] ) ? $settings['allow_multiday'] : '';
						$end_key  = isset( $settings['end_date_key'] ) ? $settings['end_date_key'] : false;

						if ( ! $end_key ) {
							$end_key = Jet_Engine_Advanced_Date_Field::instance()->data->end_date_field_name(
								$meta_key
							);
						}

						if ( 'WP_Post' === get_class( $post ) ) {

							$keys = Jet_Engine_Advanced_Date_Field::instance()->data->get_dates(
								$post->ID, $meta_key
							);

							$end_dates = get_post_meta( $post->ID, $end_key, false );

						} else {
							$keys = $meta_key ? jet_engine()->listings->data->get_meta( $meta_key, $post ) : false;
							$end_dates = $end_key ? jet_engine()->listings->data->get_meta( $end_key, $post ) : false;
						}

						// Try to get data from object if returned empty val
						if ( null === $keys || empty( $keys ) ) {
							$keys = jet_engine()->listings->data->get_prop( $meta_key, $post );
						}

						if ( $end_key && null === $end_dates ) {
							$end_dates = jet_engine()->listings->data->get_prop( $end_key, $post );
						}

						if ( ! is_array( $keys ) ) {
							$keys = [ $keys ];
						}

						if ( $end_dates && ! is_array( $end_dates ) ) {
							$end_dates = [ $end_dates ];
						}

						$calendar_period = $this->get_date_period_for_query( $settings );

						if ( ! empty( $keys ) && ! empty( $end_dates ) && $multiday ) {

							foreach ( $keys as $index => $key ) {

								$end_date = ! empty( $end_dates[ $index ] ) ? $end_dates[ $index ] : false;

								if ( ! Jet_Engine_Tools::is_valid_timestamp( $key )
									|| ! Jet_Engine_Tools::is_valid_timestamp( $end_date )
									|| $end_date < $key
									|| $end_date < $calendar_period['start']
								) {
									continue;
								}

								// Normalize dates to midnight in WP timezone to correctly calculate day span
								// when dates contain time values (e.g. 01.01.2023 15:00 → 04.01.2023 11:00).
								$start_day_ts = jet_engine_datetime()->to_time(
									jet_engine_datetime()->date( 'Y-m-d', $key )
								);

								$end_day_ts = jet_engine_datetime()->to_time(
									jet_engine_datetime()->date( 'Y-m-d', $end_date )
								);

								$days = absint( $end_day_ts - $start_day_ts ) / DAY_IN_SECONDS;

								// if event starts before calendar period,
								// adjust days count to process it by multiday envents logic
								$start_from = 1;

								if ( $key < $calendar_period['start'] ) {
									$start_from = 0;
								}

								$base_day_ts = jet_engine_datetime()->to_time(
									jet_engine_datetime()->date( 'Y-m-d', $key )
								);

								for ( $i = $start_from; $i <= $days; $i++ ) {
									$day = $base_day_ts + ( $i * DAY_IN_SECONDS );

									// Skip day if out of calendar period
									if ( $day > $calendar_period['end'] ) {
										continue;
									}

									$day_of_month = absint( jet_engine_datetime()->date( 'j', $day ) );

									if ( $day < $month['start'] ) {

										if ( empty( $this->prev_month_posts[ $day_of_month ] ) ) {
											$this->prev_month_posts[ $day_of_month ] = array( $post );
										} elseif ( ! in_array(
											$post, $this->prev_month_posts[ $day_of_month ], true
										) ) {
											$this->prev_month_posts[ $day_of_month ][] = $post;
										}

										continue;
									}

									if ( $day > $month['end'] ) {

										if ( empty( $this->next_month_posts[ $day_of_month ] ) ) {
											$this->next_month_posts[ $day_of_month ] = array( $post );
										} elseif ( ! in_array(
											$post, $this->next_month_posts[ $day_of_month ], true
										) ) {
											$this->next_month_posts[ $day_of_month ][] = $post;
										}

										continue;
									}

									if ( empty( $this->multiday_events[ $day_of_month ] ) ) {
										$this->multiday_events[ $day_of_month ] = array( $post );
									} elseif ( ! in_array(
										$post, $this->multiday_events[ $day_of_month ], true
									) ) {
										$this->multiday_events[ $day_of_month ][] = $post;
									}

									$this->posts_cache[ jet_engine()->listings->data->get_current_object_id( $post ) ] = false;
								}

								if ( $key < $calendar_period['start'] ) {
									$keys[ $index ] = false;
								}
							}

						}

						// Filter `$keys` by `$calendar_period`
						$keys = array_filter( $keys, function ( $key ) use ( $calendar_period ) {
							return $key >= $calendar_period['start'] && $key <= $calendar_period['end'];
						} );

						break;

					default:

						/**
						 * Should return timestamp of required month day
						 * @var int
						 */
						$keys = apply_filters(
							'jet-engine/listing/calendar/date-key',
							false, $post, $group_by, $this
						);
						break;

				}

				if ( ! is_array( $keys ) ) {
					$keys = [ $keys ];
				}

				foreach ( $keys as $key ) {

					if ( is_numeric( $key ) ) {

						$key     = jet_engine_datetime()->date( 'j-n', $key );
						$item_id = jet_engine()->listings->data->get_current_object_id( $post );

						if ( isset( $prepared_posts[ $key ] ) ) {
							$prepared_posts[ $key ][ $item_id ] = $post;
						} else {
							$prepared_posts[ $key ] = array( $item_id => $post );
						}

					}

				}

			}

			return $prepared_posts;

		}

		/**
		 * Returns current month
		 *
		 * @param  bool $last_day
		 * @return bool|false|int
		 */
		public function get_current_month( $last_day = false ) {

			if ( false !== $this->first_day && ! $last_day ) {
				return $this->first_day;
			}

			if ( false !== $this->last_day && $last_day ) {
				return $this->last_day;
			}

			if ( isset( $_REQUEST['month'] ) ) {
				$month = date( '1 F Y', strtotime( $_REQUEST['month'] ) );
			} elseif ( $this->start_from ) {
				$month = date( '1 F Y', strtotime( $this->start_from ) );
			} else {
				$month = date_i18n( 'Y-m-1' );
			}

			$month = strtotime( $month );

			if ( ! $last_day ) {
				$this->first_day = $month;
				return $this->first_day;
			} else {
				$this->last_day = strtotime( date( 'Y-m-t 23:59:59', $month ) );
				return $this->last_day;
			}

		}

		/**
		 * Get days number for passed month
		 *
		 * @return false|string
		 */
		public function get_days_num() {
			return date( 't', $this->get_current_month() );
		}

		/**
		 * Get list of options for month select field.
		 * @return string   $result   A piece of HTML
		 */
		public function get_month_options() {
			$current_month = $this->get_current_month();

			$current_month_read = ( int ) date( 'n', $current_month );

			$result = '';

			for ( $month = 1; $month <= 12; $month++ ) {

				$month_timestamp = strtotime( "01.{$month}.2000" );

				$month_read = date( 'F', $month_timestamp );

				$option_value = "value=\"{$month_read}\"";

				if ( $month === $current_month_read ) {
					$option_value .= " selected";
				}

				$month_label = date_i18n( 'F', $month_timestamp );

				$result .= "<option {$option_value}>{$month_label}</option>";
			}

			return $result;
		}

		/**
		 * Get list of options for year select field.
		 *
		 * @param  int      $from     Start year
		 * @param  int      $to       End year
		 * @return string   $result   A piece of HTML
		 */
		public function get_year_options( $from, $to ) {
			$current_month = $this->get_current_month();

			$current_year_read = ( int ) date( 'Y', $current_month );

			$result = '';

			for ( $year = $from; $year <= $to; $year++ ) {
				$option_value = "value=\"{$year}\"";
				if ( $year === $current_year_read ) {
					$option_value .= " selected";
				}
				$result .= "<option {$option_value}>{$year}</option>";
			}

			return $result;
		}

		public function get_item_attrs( $item ) {
			$item_id = jet_engine()->listings->data->get_current_object_id( $item );

			$item_attrs = array(
				'data-item-object' => $item_id,
				'data-render-type' => 'jet-engine-calendar',

			);

			$item_attrs = apply_filters(
				'jet-engine/calendar/render/item-attrs',
				$item_attrs, $item, $this
			);

			unset( $item_attrs['class'] );
			unset( $item_attrs['data-post-id'] );
			unset( $item_attrs['data-item-object'] );
			unset( $item_attrs['data-render-type'] );
			unset( $item_attrs['style'] );

			return \Jet_Engine_Tools::get_attr_string( $item_attrs );
		}

		/**
		 * Render calendar navigation
		 *
		 * @param array $settings Calendar settings
		 * @param int   $current_month Current month timestamp
		 */
		public function render_calendar_navigation( $settings, $current_month ) {

			$allow_select     = filter_var( $settings['allow_date_select'] ?? false, FILTER_VALIDATE_BOOLEAN );
			$hide_past_events = filter_var( $settings['hide_past_events'] ?? false, FILTER_VALIDATE_BOOLEAN );

			$human_read_month = date( 'F Y', $current_month );
			$prev_month       = strtotime( $human_read_month . ' - 1 month' );
			$human_read_prev  = date( 'F Y', $prev_month );
			$human_read_next  = date( 'F Y', strtotime( $human_read_month . ' + 1 month' ) );

			if ( $allow_select ) {
				if ( $hide_past_events ) {
					$start_year = wp_date( 'Y' );
				} else {
					$start_year = ! empty( $settings['start_year_select'] ) ? $settings['start_year_select'] : 1970;
					$start_year = jet_engine()->listings->macros->do_macros( $start_year );

					if ( false !== strpos( $start_year, 'year' ) && false !== strtotime( $start_year ) ) {
						$start_year = ( int ) wp_date( 'Y', strtotime( $start_year ) );
					}

					if ( ! is_numeric( $start_year ) ) {
						$start_year = 1970;
					}
				}

				$end_year = ! empty( $settings['end_year_select'] ) ? $settings['end_year_select'] : 2038;
				$end_year = jet_engine()->listings->macros->do_macros( $end_year );

				if ( false !== strpos( $end_year, 'year' ) && false !== strtotime( $end_year ) ) {
					$end_year = ( int ) wp_date( 'Y', strtotime( $end_year ) );
				}

				if ( ! is_numeric( $end_year ) ) {
					$end_year = 2038;
				}

				if ( $end_year < $start_year ) {
					list( $start_year, $end_year ) = array( $end_year, $start_year );
				}
			}

			$allowed_layouts = array(
				'layout-1',
				'layout-2',
				'layout-3',
				'layout-4',
			);

			$caption_layout = ! empty( $settings['caption_layout'] ) && in_array( $settings['caption_layout'], $allowed_layouts ) ? $settings['caption_layout'] : 'layout-1';

			?>
			<div class="jet-calendar-caption__wrap wrap-<?php echo esc_attr( $caption_layout ); ?>">
				<?php if ( $allow_select ): ?>
					<div class="jet-calendar-caption__name jet-calendar-caption__dates">
						<div class="jet-calendar-caption__select-wrapper">
							<select class="jet-calendar-caption__date-select select-month">
								<?php echo $this->get_month_options(); ?>
							</select>
							<div class="jet-calendar-caption__date-select-label select-month"><?php
								echo date_i18n( 'F', $current_month );
							?></div>
						</div>
						<div class="jet-calendar-caption__select-wrapper">
							<select class="jet-calendar-caption__date-select select-year">
								<?php echo $this->get_year_options( $start_year, $end_year ); ?>
							</select>
							<div class="jet-calendar-caption__date-select-label select-year"><?php
								echo date_i18n( 'Y', $current_month );
							?></div>
						</div>
					</div>
				<?php else: ?>
					<div class="jet-calendar-caption__name"><?php echo date_i18n( 'F Y', $current_month ); ?></div>
				<?php endif ?>
				<div class="jet-calendar-nav__link nav-link-prev" data-month="<?php echo $human_read_prev; ?>">
					<svg viewBox="0 0 90 179" xmlns="http://www.w3.org/2000/svg"><path transform="scale(0.1,-0.1) translate(0,-1536)" d="M627 992q0 -13 -10 -23l-393 -393l393 -393q10 -10 10 -23t-10 -23l-50 -50q-10 -10 -23 -10t-23 10l-466 466q-10 10 -10 23t10 23l466 466q10 10 23 10t23 -10l50 -50q10 -10 10 -23z" /></svg>
				</div>
				<div class="jet-calendar-nav__link nav-link-next" data-month="<?php echo $human_read_next; ?>">
					<svg viewBox="0 0 90 179" xmlns="http://www.w3.org/2000/svg"><path transform="scale(0.1,-0.1) translate(0,-1536)" d="M627 992q0 -13 -10 -23l-393 -393l393 -393q10 -10 10 -23t-10 -23l-50 -50q-10 -10 -23 -10t-23 10l-466 466q-10 10 -10 23t10 23l466 466q10 10 23 10t23 -10l50 -50q10 -10 10 -23z" /></svg>
				</div>
			</div>
			<?php
		}

		/**
		 * Get data settings for the calendar widget.
		 *
		 * @param array $settings Widget settings.
		 * @param int   $current_month Current month.
		 * @param array $additional_settings Render-specific settings.
		 *
		 * @return array Data settings.
		 */
		public function get_data_settings( $settings, $current_month, $additional_settings = [] ) {

			$days_format  = isset( $settings['week_days_format'] ) ? $settings['week_days_format'] : 'short';
			$multiday     = isset( $settings['allow_multiday'] ) ? $settings['allow_multiday'] : '';
			$end_date_key = isset( $settings['end_date_key'] ) ? $settings['end_date_key'] : false;

			$allowed_layouts = array(
				'layout-1',
				'layout-2',
				'layout-3',
				'layout-4',
			);

			$caption_layout = ! empty( $settings['caption_layout'] ) && in_array( $settings['caption_layout'], $allowed_layouts ) ? $settings['caption_layout'] : 'layout-1';

			$human_read_month = date( 'F Y', $current_month );

			$data_settings = apply_filters( 'jet-engine/calendar/render/widget-settings', array(
				'lisitng_id'               => isset( $settings['lisitng_id'] ) ? absint( $settings['lisitng_id'] ) : false,
				'week_days_format'         => $days_format,
				'allow_multiday'           => $multiday,
				'end_date_key'             => $end_date_key,
				'group_by'                 => isset( $settings['group_by'] ) ? $settings['group_by'] : false,
				'group_by_key'             => isset( $settings['group_by_key'] ) ? $settings['group_by_key'] : false,
				'posts_query'              => isset( $settings['posts_query'] ) ? $settings['posts_query'] : array(),
				'meta_query_relation'      => isset( $settings['meta_query_relation'] ) ? $settings['meta_query_relation'] : false,
				'tax_query_relation'       => isset( $settings['tax_query_relation'] ) ? $settings['tax_query_relation'] : false,
				'hide_widget_if'           => isset( $settings['hide_widget_if'] ) ? $settings['hide_widget_if'] : false,
				'caption_layout'           => $caption_layout,
				'show_posts_nearby_months' => isset( $settings['show_posts_nearby_months'] ) ? $settings['show_posts_nearby_months'] : true,
				'hide_past_events'         => isset( $settings['hide_past_events'] ) ? $settings['hide_past_events'] : false,
				'allow_date_select'        => isset( $settings['allow_date_select'] ) ? $settings['allow_date_select'] : false,
				'start_year_select'        => isset( $settings['start_year_select'] ) ? $settings['start_year_select'] : 1970,
				'end_year_select'          => isset( $settings['end_year_select'] ) ? $settings['end_year_select'] : 2038,
				'use_custom_post_types'    => isset( $settings['use_custom_post_types'] ) ? $settings['use_custom_post_types'] : false,
				'custom_post_types'        => isset( $settings['custom_post_types'] ) ? $settings['custom_post_types'] : array(),
				'custom_query'             => isset( $settings['custom_query'] ) ? $settings['custom_query'] : false,
				'custom_query_id'          => isset( $settings['custom_query_id'] ) ? $settings['custom_query_id'] : false,
				'_element_id'              => isset( $settings['_element_id'] ) ? $settings['_element_id'] : '',
				'cache_enabled'            => isset( $settings['cache_enabled'] ) ? $settings['cache_enabled'] : false,
				'cache_timeout'            => isset( $settings['cache_timeout'] ) ? $settings['cache_timeout'] : 60,
				'max_cache'                => isset( $settings['max_cache'] ) ? $settings['max_cache'] : 12,
			), $settings );

			$cache_enabled = filter_var( $settings['cache_enabled'], FILTER_VALIDATE_BOOLEAN );

			if ( $cache_enabled ) {
				$data_settings['cache_id'] = $_REQUEST['settings']['cache_id'] ?? round( microtime( true ) * 10000 );
				$data_settings['prev_month'] = $human_read_month;
			}

			if ( ! empty( $additional_settings ) && is_array( $additional_settings ) ) {
				foreach ( $additional_settings as $setting ) {
					$data_settings[ $setting ] = isset( $settings[ $setting ] ) ? $settings[ $setting ] : false;
				}
			}

			return $data_settings;
		}

		/**
		 * Render posts template.
		 * Moved to separate function to be rewritten by other layouts
		 *
		 * @param  array  $query    Query array.
		 * @param  array  $settings Settings array.
		 * @return void
		 */
		public function posts_template( $query, $settings ) {

			$base_class       = $this->get_name();
			$current_month    = $this->get_current_month();
			$month            = array(
				'start' => $current_month,
				'end'   => $this->get_current_month( true ),
			);
			$prepared_posts   = $this->prepare_posts_for_calendar( $query, $settings, $month );
			$days_num         = $this->get_days_num();
			$week_begins      = (int) get_option( 'start_of_week' );
			$first_week       = true;
			$human_read_month = date( 'F Y', $current_month );
			$first_day        = date( 'w', $current_month );
			$inc              = 0;
			$pad              = $first_day - $week_begins;
			$prev_month       = strtotime( $human_read_month . ' - 1 month' );
			$prev_month       = date( 't', $prev_month );

			if ( 0 > $pad ) {
				$pad = 7 - abs( $pad );
			}

			$data_settings = $this->get_data_settings( $settings, $current_month );

			$cache_enabled = filter_var( $settings['cache_enabled'], FILTER_VALIDATE_BOOLEAN );

			if ( $cache_enabled && isset( $data_settings['cache_id'] ) ) {
				$cache_id = sprintf(
					' data-cache-id="%1$s"',
					esc_attr( $data_settings['cache_id'] )
				);
			}

			$container_classes = [
				'jet-calendar',
				$base_class,
				'jet-listing-grid--' . absint( $settings['lisitng_id'] ), // for inline CSS consistency between differen views and listing widgets
			];

			printf(
				'<div class="%1$s" data-settings="%2$s" data-post="%3$d" data-listing-source="%4$s" data-query-id="%5$s"%6$s>',
				implode( ' ', $container_classes ),
				htmlspecialchars( json_encode( $data_settings ) ),
				get_the_ID(),
				jet_engine()->listings->data->get_listing_source(),
				$this->listing_query_id,
				$cache_id ?? ''
			);

			do_action( 'jet-engine/listing/grid/before', $this );

			do_action( 'jet-engine/listing/calendar/before', $settings, $this );

			echo '<table class="jet-calendar-grid" >';

			include jet_engine()->modules->get_module( 'calendar' )->get_template( 'header.php' );

			echo '<tbody>';

			jet_engine()->frontend->set_listing( absint( $settings['lisitng_id'] ) );

			$fallback = 1;
			$today_date        = date_i18n( 'j-n-Y' );
			$current_year      = (int) date( 'Y', $current_month );
			$current_month_num = (int) date( 'n', $current_month );
			$prev_month_num    = $current_month_num - 1;
			$prev_month_num    = ( 0 >= $prev_month_num ) ? $prev_month_num + 12 : $prev_month_num;;
			$next_month_num    = $current_month_num + 1;
			$next_month_num    = ( 12 < $next_month_num ) ? $next_month_num - 12 : $next_month_num;

			// Add last days of previous month
			if ( 0 < $pad ) {

				for ( $i = 0; $i < $pad; $i++ ) {

					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-start.php' );

					$num                     = $prev_month - $pad + $i + 1;
					$key                     = $num . '-' . $prev_month_num;
					$posts                   = ! empty( $prepared_posts[ $key ] ) ? $prepared_posts[ $key ] : array();
					$padclass                = ! empty( $posts ) ? ' day-pad has-events' : ' day-pad';
					$current_multiday_events = ! empty( $this->prev_month_posts[ $num ] ) ? $this->prev_month_posts[ $num ] : array();

					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'date.php' );
					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-end.php' );

					$inc++;
				}

			}

			// Current month
			for ( $i = 1; $i <= $days_num; $i++ ) {

				include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-start.php' );

				$num      = $i;
				$key      = $num . '-' . $current_month_num;
				$posts    = ! empty( $prepared_posts[ $key ] ) ? $prepared_posts[ $key ] : array();
				$padclass = ! empty( $posts ) ? ' has-events' : '';

				$current_multiday_events = array();

				if ( ! empty( $this->multiday_events[ $i ] ) ) {
					$current_multiday_events = $this->multiday_events[ $i ];

					if ( ! $padclass ) {
						$padclass = ' has-events';
					}

				}

				$current_date = $key . '-' . $current_year;

				if ( $current_date === $today_date ) {
					$padclass .= ' current-day';
				}

				include jet_engine()->modules->get_module( 'calendar' )->get_template( 'date.php' );
				include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-end.php' );

				$inc++;

			}

			// Add first days of next month
			$days_left = 7 - ( $inc % 7 );

			if ( 0 < $days_left ) {

				$fallback = $days_num;

				for ( $i = 1; $i <= $days_left; $i++ ) {

					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-start.php' );

					$num                     = $i;
					$key                     = $num . '-' . $next_month_num;
					$posts                   = ! empty( $prepared_posts[ $key ] ) ? $prepared_posts[ $key ] : array();
					$padclass                = ! empty( $posts ) ? ' day-pad has-events' : ' day-pad';
					$current_multiday_events = ! empty( $this->next_month_posts[ $num ] ) ? $this->next_month_posts[ $num ] : array();

					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'date.php' );
					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-end.php' );

					$inc++;

				}

			}

			$this->multiday_events   = array();
			$this->posts_cache       = array();
			$current_multiday_events = array();

			jet_engine()->frontend->reset_listing();

			echo '</tbody>';

			echo '</table>';

			do_action( 'jet-engine/listing/grid/after', $this );

			do_action( 'jet-engine/listing/calendar/after', $settings, $this );

			echo '</div>';

		}

		public function maybe_set_listing( $listing_id ) {

			if ( null === jet_engine()->frontend->get_listing_id() ) {
				jet_engine()->frontend->set_listing( $listing_id );
			}

		}

	}

}
