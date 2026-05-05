<?php
namespace Jet_Engine\Dynamic_Calendar\Advanced_Date_Field;

use JFB_Modules\Block_Parsers\Field_Data_Parser;
use JFB_Modules\Block_Parsers\Fields\Default_Parser;
use JFB_Modules\Block_Parsers\Interfaces\Multiple_Parsers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Field_Parser extends Field_Data_Parser implements Multiple_Parsers {

	public function type() {
		return 'advanced-date-field';
	}

	public function generate_parsers(): \Generator {

		$raw_value = $this->get_context()->get_request( $this->name );
		$new_value = '';

		if ( is_string( $raw_value ) ) {
			$raw_value = json_decode( $raw_value, true );
		}

		$save_as     = ! empty( $this->settings['save_as'] ) ? $this->settings['save_as'] : 'timestamp';
		$date_format = false;
		$time_format = false;
		// Default value of this option is true, so we need to check exactly in this way
		$allow_time  = isset( $this->settings['allow_timepicker'] ) ? $this->settings['allow_timepicker'] : true;
		$allow_time  = filter_var( $allow_time, FILTER_VALIDATE_BOOLEAN );

		if ( 'timestamp' !== $save_as ) {
			$date_format = ! empty( $this->settings['date_format'] ) ? $this->settings['date_format'] : 'Y-m-d';
			$time_format = ! empty( $this->settings['time_format'] ) ? $this->settings['time_format'] : 'H:i:s';
		}

		$config_parser = new Default_Parser();
		$config_parser->set_context( $this->get_context() );
		$config_parser->set_type( $this->type() . '__config' );
		$config_parser->set_name( $this->name . '__config' );

		if ( ! empty( $raw_value['dates'] ) ) {
			$raw_value['dates'] = array_values( $raw_value['dates'] );
		}

		$config_parser->set_value( json_encode( $raw_value ) );

		$is_reccuring = ! empty( $raw_value['is_recurring'] ) ? $raw_value['is_recurring'] : false;
		$is_reccuring = filter_var( $is_reccuring, FILTER_VALIDATE_BOOLEAN );

		if ( ! class_exists( '\Jet_Engine_Advanced_Date_Recurring_Dates' ) ) {
			require_once jet_engine()->plugin_path( 'includes/modules/calendar/advanced-date-field/recurring-dates.php' );
		}

		$date_parts = array(
			! empty( $raw_value['date'] ) ? $raw_value['date'] : '',
			! empty( $raw_value['time'] ) ? $raw_value['time'] : '00:00:00',
		);

		$raw_value['initial_timestamp'] = strtotime( implode( ' ', $date_parts ) );

		if ( false !== $date_format ) {

			$raw_value['generated_date_format'] = $date_format;

			if ( $allow_time && false !== $time_format ) {
				$raw_value['generated_date_format'] .= ' ' . $time_format;
			}
		}

		$recurring_dates = new \Jet_Engine_Advanced_Date_Recurring_Dates( $raw_value );

		$rrule_parser = new Default_Parser();
		$rrule_parser->set_context( $this->get_context() );
		$rrule_parser->set_type( $this->type() . '__rrule' );
		$rrule_parser->set_name( $this->name . '__rrule' );
		$rrule_parser->set_value( $recurring_dates->generate_rrule() );

		$has_empty_end = $this->has_empty_required_dates( $raw_value );

		if ( $has_empty_end ) {
			$new_value = '';
		} elseif ( $is_reccuring && ! empty( $raw_value['date'] ) ) {
			// Set recurrency dates into the new value
			$new_value = $recurring_dates->with_end_dates( $recurring_dates->with_start_date(
				$recurring_dates->generate( true )
			) );
		} else {
			$new_value = $recurring_dates->extract_manual_dates();
		}

		$this->set_value( $new_value );
		$this->get_context()->update_request_value( $this->name, $new_value );

		yield $this;
		yield $config_parser;
		yield $rrule_parser;
	}

	/**
	 * Validate required date fields.
	 *
	 * Rules:
	 * - If `required` is false → skip validation.
	 * - If `required` is true → check that `date` is not empty.
	 * - If both `required` and `end_date_required` are true → also check `end_date`.
	 *
	 * Works for both manual (multiple dates) and single date modes.
	 *
	 * @param array $raw_value Raw input data.
	 * @return bool True if required fields are empty (invalid), false otherwise.
	 */
	public function has_empty_required_dates( $raw_value ) {
		$required          = ! empty( $this->settings['required'] );
		$end_date_required = ! empty( $this->settings['end_date_required'] );

		// field is not required at all
		if ( ! $required ) {
			return false;
		}

		// field required, need validation
		if ( ! empty( $raw_value['dates'] ) && is_array( $raw_value['dates'] ) ) {
			foreach ( $raw_value['dates'] as $date_item ) {
				// Check "date"
				if ( empty( $date_item['date'] ) ) {
					return true;
				}

				// Check "end_date" only if end_date_required = true
				if ( $end_date_required && ! empty( $date_item['is_end_date'] ) && empty( $date_item['end_date'] ) ) {
					return true;
				}
			}
		} else {
			// Single date mode
			if ( empty( $raw_value['date'] ) ) {
				return true;
			}

			if ( $end_date_required && ! empty( $raw_value['is_end_date'] ) && empty( $raw_value['end_date'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return mixed
	 */
	public function get_value() {
		return $this->value;
	}
}
