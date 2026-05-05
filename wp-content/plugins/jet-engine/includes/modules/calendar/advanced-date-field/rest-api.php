<?php
/**
 * Register Advanced date meta field type
 */

class Jet_Engine_Advanced_Date_Field_Rest_API extends Jet_Engine_Advanced_Date_Field_Data {

	public $field_type;

	/**
	 * Constructor for the class
	 */
	public function __construct( $field_type ) {

		$this->field_type = $field_type;

		add_filter(
			'jet-engine/meta-boxes/rest-api/fields/field-type',
			array( $this, 'prepare_rest_api_field_type' ),
			10, 2
		);

		add_filter(
			'jet-engine/meta-boxes/rest-api/fields/schema',
			array( $this, 'prepare_rest_api_schema' ),
			10, 3
		);

	}

	/**
	 * Adjust field type for registering advanced date field in Rest API
	 *
	 * @param  [type] $type  [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function prepare_rest_api_field_type( $type, $field ) {

		if ( $this->is_advanced_date_field( $field ) ) {
			$type = 'object';
		}

		return $type;

	}

	/**
	 * Setup advanced date field schema for rest API
	 *
	 * @param  [type] $schema     [description]
	 * @param  [type] $field_type [description]
	 * @param  [type] $field      [description]
	 * @return [type]             [description]
	 */
	public function prepare_rest_api_schema( $schema, $field_type, $field ) {

		if ( ! $this->is_advanced_date_field( $field ) ) {
			return $schema;
		}

		$schema = array(
			'type'             => 'object',
			'properties'       => array(
				'rrule' => array(
					'type' => 'string'
				),
				'dates' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'object',
						'properties' => array(
							'start' => array( 'type' => 'string' ),
							'end'   => array( 'type' => 'string' ),
						),
					),
				),
			),
			'prepare_callback' => function( $value, $request, $args ) {

				global $post;

				$result = array( 'rrule' => '', 'dates' => [] );

				if ( ! $post ) {
					return $result;
				}

				$post_id = $post->ID;
				$field   = $args['name'];
				$config  = $this->get_field_config( $post_id, $field, true );

				$result['rrule'] = $this->generate_rrule_from_config( $config );
				$result['dates'] = $this->get_next_dates( $post_id, $field );

				return $result;
			}
		);

		return $schema;
	}

	public function get_next_dates( $post_id, $field ) {

		$dates     = $this->get_dates( $post_id, $field );
		$end_dates = $this->get_end_dates( $post_id, $field );
		$result    = [];

		if ( empty( $dates ) ) {
			return $result;
		}

		$format = apply_filters( 'jet-engine/calendar/advanced-date/rest-api-date-format', false );
		$count  = 10;
		$now    = time();

		foreach ( $dates as $index => $date ) {

			if ( $date < $now ) {
				continue;
			}

			$item = [];

			$item['start'] = ( false !== $format ) ? date( $format, $date ) : $date;

			if ( ! empty( $end_dates ) && ! empty( $end_dates[ $index ] ) ) {
				$item['end'] = ( false !== $format ) ? date( $format, $end_dates[ $index ] ) : $end_dates[ $index ];
			}

			$result[] = $item;

			if ( $count === count( $result ) ) {
				break;
			}

		}

		return $result;

	}

	public function generate_rrule_from_config( $config ) {

		if ( ! $config ) {
			return null;
		}

		if ( ! class_exists( 'Jet_Engine_Advanced_Date_Recurring_Dates' ) ) {
			require_once jet_engine()->plugin_path( 'includes/modules/calendar/advanced-date-field/recurring-dates.php' );
		}

		$recurring_dates = new Jet_Engine_Advanced_Date_Recurring_Dates( (array) $config );

		return $recurring_dates->generate_rrule();
	}
}
