<?php
/**
 * Register Advanced date meta field type
 */

class Jet_Engine_Advanced_Date_Recurring_Dates {

	private $data = [];

	// English only weekdays to use for new dates generation
	private $weekdays = [
		1 => 'Monday',
		2 => 'Tuesday',
		3 => 'Wednesday',
		4 => 'Thursday',
		5 => 'Friday',
		6 => 'Saturday',
		7 => 'Sunday',
	];

	// English only months
	private $months = [
		1 => 'January',
		2 => 'February',
		3 => 'March',
		4 => 'April',
		5 => 'May',
		6 => 'June',
		7 => 'July',
		8 => 'August',
		9 => 'September',
		10 => 'October',
		11 => 'November',
		12 => 'December'
	];

	public function __construct( $data = [] ) {
		$defaults = array(
			'date' => '', // Initial date in string format
			'initial_timestamp' => 0, // Initial date as a timestamp
			'generated_date_format' => 'timestamp', // Format for generated dates
			'is_end_date' => false, // Whether an end date is specified
			'end_date' => '', // End date in string format
			'is_recurring' => false, // Whether the event is recurring
			'recurring' => 'daily', // Recurring frequency (daily, weekly, monthly, yearly)
			'recurring_period' => 1, // Recurring interval
			'week_days' => [], // Array of weekdays for weekly recurrence
			'month_day_type' => 'first', // Type of day in the month (first, second, etc.)
			'month_day_type_value' => 'day', // Day type value (day, weekday, etc.)
			'monthly_type' => 'on_day_type', // Monthly recurrence type
			'month_day' => 1, // Specific day of the month
			'month' => 1, // Month for yearly recurrence
			'end' => 'after', // End condition (after, on_date)
			'end_after' => 1, // Number of occurrences for 'after' end condition
			'end_after_date' => '', // End date for 'on_date' end condition
		);

		$this->data = array_merge( $defaults, $data );
	}

	/**
	 * Add initial date to already generated dates list
	 *
	 * @param array $dates
	 * @return array
	 */
	public function with_start_date( $dates = [] ) {

		$initial_date = $this->get_initial_date();
		$initial_date = $this->prepare_date_for_result( $initial_date );

		if ( ! in_array( $initial_date, $dates ) ) {
			array_unshift( $dates, $initial_date );
		}

		return $dates;
	}

	/**
	 * Prepare date for result
	 *
	 * @param  string $date [description]
	 * @return [type]       [description]
	 */
	public function prepare_date_for_result( $date ) {

		$date_format = ! empty( $this->data['generated_date_format'] ) ? $this->data['generated_date_format'] : 'timestamp';

		if ( 'timestamp' !== $date_format ) {
			$date = wp_date( $date_format, $date );
		}

		return $date;
	}

	/**
	 * Prepare date for RRULE string
	 *
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public function prepare_date_for_rrule( $date ) {
		return date( 'Ymd', strtotime( $date) ) . 'T000000Z';
	}

	/**
	 * Extract manually added dates
	 *
	 * @return array
	 */
	public function extract_manual_dates() {
		$result = [];

		// If multiple dates exist
		$dates_to_process = ! empty( $this->data['dates'] ) ? $this->data['dates'] : [];

		// If single date exists, append to array for uniform processing
		if ( ! empty( $this->data['date'] ) ) {
			$single_date        = $this->data;
			$dates_to_process[] = $single_date;
		}

		foreach ( $dates_to_process as $date ) {
			$prepared = $this->build_date_result( $date );
			if ( $prepared !== null ) {
				$result[] = $prepared;
			}
		}

		return $result;
	}

	/**
	 * Build a date result from field data
	 *
	 * Returns:
	 * - ['start' => ..., 'end' => ...] if end date exists
	 * - timestamp if single date
	 * - null if start date missing
	 *
	 * @param array $date
	 * @return array|int|null
	 */
	public function build_date_result( $date ) {
		// Skip if no start date is provided.
		if ( empty( $date['date'] ) ) {
			return null;
		}

		$full_date = $date['date'];

		if ( ! empty( $date['time'] ) ) {
			$full_date .= ' ' . $date['time'];
		}

		$full_date = strtotime( $full_date );

		// Handle case when end date is enabled.
		if ( ! empty( $date['is_end_date'] ) ) {
			$full_end_date = $date['end_date'];

			if ( ! empty( $date['end_time'] ) ) {
				$full_end_date .= ' ' . $date['end_time'];
			}

			$full_end_date = strtotime( $full_end_date );

			return [
				'start' => $this->prepare_date_for_result( $full_date ),
				'end'   => $this->prepare_date_for_result( $full_end_date ),
			];
		}

		// Single date
		return $this->prepare_date_for_result( $full_date );
	}

	/**
	 * Generate RRULE string for current config.
	 *
	 * @return string
	 */
	public function generate_rrule() {

		$result = [];

		$config = $this->data;

		$result[] = 'DTSTART=' . $this->prepare_date_for_rrule( $config['date'] );

		if ( $config && ! empty( $config['is_end_date'] ) && ! empty( $config['end_date'] ) ) {
			$result[] = 'DTEND=' . $this->prepare_date_for_rrule( $config['end_date'] );
		}

		if ( ! empty( $config['is_recurring'] ) ) {

			$rrule = [];

			$rrule[] = 'FREQ=' . strtoupper( $config['recurring'] );
			$rrule[] = 'INTERVAL=' . $config['recurring_period'];

			switch ( $config['recurring'] ) {

				case 'weekly':

					$days = [];

					foreach ( $config['week_days'] as $day ) {
						$days[] = $this->get_shorten_weekday( $day );
					}

					$rrule[] = 'BYDAY=' . implode( ',', $days );

					break;

				case 'monthly':
				case 'yearly':

					$days = [];

					if ( 'day' === $config['month_day_type_value'] ) {
						for ( $i = 1; $i <= 7; $i++ ) {
							$days[] = $this->get_shorten_weekday( $i );
						}
					} else {
						$days[] = $this->get_shorten_weekday( $config['month_day_type_value'] );
					}

					if ( 'on_day_type' === $config['monthly_type'] ) {
						$rrule[] = 'BYSETPOS=' . $this->date_type_to_num( $config['month_day_type'] ) . ';BYDAY=' . implode( ',',  $days );
					} else {
						$rrule[] = 'BYMONTHDAY=' . $config['month_day'];
					}

					if ( 'yearly' === $config['recurring'] ) {
						$rrule[] = 'BYMONTH=' . $config['month'];
					}

					break;
			}

			if ( 'on_date' === $config['end'] ) {
				$rrule[] = 'UNTIL=' . $this->prepare_date_for_rrule( $config['end_after_date'] );
			} else {
				$rrule[] = 'COUNT=' . $config['end_after'];
			}

			$result[] = implode( ';', $rrule );
		}

		return implode( ';', $result );
	}

	/**
	 * Get result with end dates
	 *
	 * @param  array $start_dates List of start dates.
	 * @return array
	 */
	public function with_end_dates( $start_dates ) {

		$has_end_date = ! empty( $this->data['is_end_date'] ) && ! empty( $this->data['end_date'] );

		if ( ! $has_end_date ) {
			return $start_dates;
		}

		$all_dates = [];
		$end_date  = ! empty( $this->data['end_date'] ) ? $this->data['end_date'] : false;

		if ( ! empty( $this->data['end_time'] ) ) {
			$end_date .= ' ' . $this->data['end_time'];
		}

		$end_ts      = strtotime( $end_date );
		$start_ts    = $this->get_initial_date();
		$diff        = $end_ts - $start_ts;
		$date_format = ! empty( $this->data['generated_date_format'] ) ? $this->data['generated_date_format'] : 'timestamp';
		$is_ts       = 'timestamp' === $date_format;

		foreach ( $start_dates as $date ) {
			if ( is_array( $date ) ) {
				$all_dates[] = [
					'start' => $date['start'],
					'end'   => $date['end'],
				];
			} else {

				$date_ts = $is_ts ? $date : strtotime( $date );

				if ( $date_ts == $start_ts ) {
					$all_dates[] = [
						'start' => $date,
						'end'   => $this->prepare_date_for_result( $end_ts ),
					];
				} else {
					$all_dates[] = [
						'start' => $date,
						'end'   => $this->prepare_date_for_result( $date_ts + $diff ),
					];
				}
			}
		}

		return $all_dates;
	}

	/**
	 * Generate recurring dates list by given config
	 *
	 * @return [type] [description]
	 */
	public function generate() {

		$dates = [];

		while ( $this->has_next_date( $dates ) ) {
			switch ( $this->data['recurring'] ) {
				case 'daily':
					$next_date = $this->generate_next_daily_recurring( $dates );
					break;

				case 'weekly':
					$next_date = $this->generate_next_weekly_recurring( $dates );
					break;

				case 'monthly':
					$next_date = $this->generate_next_monthly_recurring( $dates );
					break;

				case 'yearly':
					$next_date = $this->generate_next_yearly_recurring( $dates );
					break;
			}

			// if ends on date we need to do one more check to ensure new date also fits range
			if ( 'on_date' === $this->data['end'] && ! $this->is_date_in_range( $next_date ) ) {
				break;
			}

			$dates[] = $next_date;

		}

		$date_format = ! empty( $this->data['generated_date_format'] ) ? $this->data['generated_date_format'] : 'timestamp';

		if ( 'timestamp' === $date_format ) {
			return array_filter( $dates );
		} else {
			$formatted_dates = [];

			foreach ( $dates as $date ) {
				if ( ! empty( $date ) ) {
					$formatted_dates[] = wp_date( $date_format, $date );
				}
			}

			return $formatted_dates;
		}
	}

	/**
	 * Check if need to generate one more date
	 *
	 * @param  array   $dates [description]
	 * @return boolean        [description]
	 */
	public function has_next_date( $dates = [] ) {

		$res = false;

		switch ( $this->data['end'] ) {

			case 'after':

				$num = absint( $this->data['end_after'] );

				if ( count( $dates ) < ( $num - 1 ) ) {
					$res = true;
				}

				break;

			case 'on_date':
				$res = $this->is_date_in_range( $this->get_last_date( $dates ) );
				break;
		}

		return $res;

	}

	/**
	 * Check if given date is before end date of recurring range
	 *
	 * @param  [type]  $date [description]
	 * @return boolean       [description]
	 */
	public function is_date_in_range( $date ) {

		$end_on_date = strtotime( $this->data['end_after_date'] );

		if ( $end_on_date >= $date ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Get last date from already generated
	 *
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function get_last_date( $dates = [] ) {
		$dates = array_filter( $dates );
		return ! empty( $dates ) ? end( $dates ) : $this->get_initial_date();
	}

	/**
	 * Returns timestamp of initial date according to input data.
	 *
	 * @return [type] [description]
	 */
	public function get_initial_date() {
		return ! empty( $this->data['initial_timestamp'] ) ? $this->data['initial_timestamp'] : strtotime( $this->data['date'] );
	}

	/**
	 * Get recurring period (each N days, weeks, months, years)
	 *
	 * @return [type] [description]
	 */
	public function get_recurring_period() {
		return ! empty( $this->data['recurring_period'] ) ? absint( $this->data['recurring_period'] ) : 1;
	}

	/**
	 * Generate next date for daily recurrings
	 *
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function generate_next_daily_recurring( $dates = [] ) {
		$last_date = $this->get_last_date( $dates );
		$period    = $this->get_recurring_period();
		return $last_date + $period * DAY_IN_SECONDS;
	}

	/**
	 * Generate next date for weekly recurrings
	 *
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function generate_next_weekly_recurring( $dates = [] ) {

		$last_date = $this->get_last_date( $dates );
		$period    = $this->get_recurring_period();

		$weekdays = ! empty( $this->data['week_days'] ) ? $this->data['week_days'] : [];

		if ( empty( $this->data['week_days'] ) ) {
			return false;
		}

		$last_date_dow = date( 'N', $last_date );
		$new_date = false;

		if ( in_array( $last_date_dow, $weekdays ) ) {
			$d_index = array_search( $last_date_dow, $weekdays );
			$d_index++;
		} else {
			$d_index = 0;
			foreach ( $weekdays as $index => $dow ) {
				if ( absint( $dow ) > absint( $last_date_dow ) ) {
					$d_index = $index;
					break;
				}
			}
		}

		$next_day  = isset( $weekdays[ $d_index ] ) ? $weekdays[ $d_index ] : $weekdays[0];
		$diff      = $next_day - $last_date_dow;
		$next_week = false;

		if ( 0 >= $diff ) {
			$diff = 7 - $last_date_dow + $next_day;
			$next_week = true;
		}

		$new_date = $last_date + $diff * DAY_IN_SECONDS;

		if ( $next_week ) {
			$new_date += 7 * DAY_IN_SECONDS * ( $period - 1 );
		}

		return $new_date;

	}

	/**
	 * Generate next date for monthly recurrings
	 *
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function generate_next_monthly_recurring( $dates = [] ) {

		$last_date = $this->get_last_date( $dates );
		$period    = $this->get_recurring_period();
		$new_date  = false;
		$dt        = new DateTime( '@' . $last_date );

		switch ( $this->data['monthly_type'] ) {

			case 'on_day_type':

				for ( $i = 1; $i <= $period; $i++ ) {
					$dt->modify( sprintf(
						'%1$s %2$s of next month',
						$this->get_day_type(),
						$this->get_day_type_value()
					) );
				}

				break;

			default:

				$day = ! empty( $this->data['month_day'] ) ? absint( $this->data['month_day'] ) : 1;

				for ( $i = 1; $i <= $period; $i++ ) {
					$dt->modify( 'first day of next month' );
				}

				$total_days_num = absint( $dt->format( 't' ) );
				$day = ( $total_days_num > $day ) ? $day : $total_days_num;
				$dt->setDate( $dt->format( 'Y' ), $dt->format( 'n' ), $day );

				break;
		}

		$new_date = $dt->getTimestamp();

		return $new_date;
	}

	/**
	 * Generate next date for yearly recurrings
	 *
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function generate_next_yearly_recurring( $dates = [] ) {

		$last_date = $this->get_last_date( $dates );
		$period    = $this->get_recurring_period();
		$new_date  = false;
		$dt        = new DateTime( '@' . $last_date );
		$month     = ! empty( $this->data['month'] ) ? absint( $this->data['month'] ) : 1;
		$month     = ! empty( $this->months[ $month ] ) ? $this->months[ $month ] : 'January';

		switch ( $this->data['monthly_type'] ) {

			case 'on_day_type':

				for ( $i = 1; $i <= $period; $i++ ) {

					$dt->modify( 'first day of next year' );

					$dt->modify( sprintf(
						'%1$s %2$s of %3$s',
						 $this->get_day_type(),
						$this->get_day_type_value(),
						$month
					) );

				}

				break;

			default:

				$day = ! empty( $this->data['month_day'] ) ? absint( $this->data['month_day'] ) : 1;
				$total_days_num = absint( date( 't', strtotime( '1st ' . $month . ' of next year' ) ) );
				$day = ( $total_days_num > $day ) ? $day : $total_days_num;
				$suffixes = [ 'th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th' ];

				if ( ( $day % 100 ) >= 11 && ( $day % 100 ) <= 13 ) {
					$suffix = 'th';
				} else {
					$suffix = $suffixes[ $day % 10 ];
				}


				for ( $i = 1; $i <= $period; $i++ ) {
					$dt->modify( sprintf(
						'%1$s%2$s %3$s, next year',
						$day,
						$suffix,
						$month
					) );
				}

				break;
		}

		$new_date = $dt->getTimestamp();

		return $new_date;

	}

	/**
	 * Extract day type value from config
	 *
	 * @return [type] [description]
	 */
	public function get_day_type_value() {

		$day_val = ! empty( $this->data['month_day_type_value'] ) ? $this->data['month_day_type_value'] : 1;

		if ( 'day' !== $day_val ) {
			$day_val = absint( $day_val );
			$day_val = ! empty( $this->weekdays[ $day_val ] ) ? $this->weekdays[ $day_val ] : 'Monday';
		}

		return $day_val;

	}

	/**
	 * Get shorten weekday name by number
	 *
	 * @param  [type] $day_index [description]
	 * @return [type]            [description]
	 */
	public function get_shorten_weekday( $day_index ) {
		$day_val = ! empty( $this->weekdays[ $day_index ] ) ? $this->weekdays[ $day_index ] : 'Monday';
		return strtoupper( substr( $day_val, 0, 2 ) );
	}

	/**
	 * Return day type number by string name
	 *
	 * @param  string $day_type [description]
	 * @return [type]           [description]
	 */
	public function date_type_to_num( $day_type = 'first' ) {
		switch( $day_type ) {
			case 'second':
				return 2;
			case 'third':
				return 3;
			case 'fourth':
				return 4;
			case 'last':
				return -1;
			default:
				return 1;
		}
	}

	/**
	 * Extract day type from config
	 *
	 * @return [type] [description]
	 */
	public function get_day_type() {
		return ! empty( $this->data['month_day_type'] ) ? $this->data['month_day_type'] : 'first';
	}

}
