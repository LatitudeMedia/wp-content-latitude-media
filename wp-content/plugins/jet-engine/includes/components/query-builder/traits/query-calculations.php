<?php
namespace Jet_Engine\Query_Builder\Traits;

use Jet_Engine\Query_Builder\Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait Query_Calculations_Trait {

	public function get_title() {
		return esc_html__( 'Query Calculations', 'jet-engine' );
	}

	public function get_args() {
		return array(
			'query_id' => array(
				'label'   => esc_html__( 'Query', 'jet-engine' ),
				'type'    => 'select',
				'options' => Manager::instance()->get_queries_for_options(),
			),
			'query_property' => array(
				'label'       => esc_html__( 'Property', 'jet-engine' ),
				'label_block' => true,
				'type'        => 'text',
				'default'     => '',
				'description' => esc_html__( 'Query property to calculate value for', 'jet-engine' ),
			),
			'calc_function' => array(
				'label'   => esc_html__( 'Calculation Function', 'jet-engine' ),
				'type'    => 'select',
				'options' => array(
					'sum'      => esc_html__( 'Sum', 'jet-engine' ),
					'avg'      => esc_html__( 'Average', 'jet-engine' ),
					'min'      => esc_html__( 'Minimum', 'jet-engine' ),
					'max'      => esc_html__( 'Maximum', 'jet-engine' ),
					'first'    => esc_html__( 'First', 'jet-engine' ),
					'last'     => esc_html__( 'Last', 'jet-engine' ),
					'std_dev'  => esc_html__( 'Standard Deviation (how dispersed a data set is around its mean)', 'jet-engine' ),
					'variance' => esc_html__( 'Variance (the average of the squared differences from the Mean)', 'jet-engine' ),
					'median'   => esc_html__( 'Median (the middle value in a list of numbers)', 'jet-engine' ),
					'mode'     => esc_html__( 'Mode (the most frequently occurring value in a dataset)', 'jet-engine' ),
				),
				'default' => 'sum',
			),
			'decimals' => array(
				'type'    => 'text',
				'label'   => esc_html__( 'Decimals', 'jet-engine' ),
				'default' => 2,
			),
			'decimals_separator' => array(
				'type'    => 'text',
				'label'   => esc_html__( 'Decimals Separator', 'jet-engine' ),
				'default' => '.',
			),
			'thousands_separator' => array(
				'type'    => 'text',
				'label'   => esc_html__( 'Thousands Separator', 'jet-engine' ),
				'default' => ',',
			),
		);
	}

	public function get_result( $settings = array() ) {
		$settings = wp_parse_args(
			$settings,
			array(
				'query_id'            => '',
				'query_property'      => '',
				'calc_function'       => 'sum',
				'decimals'            => 2,
				'decimals_separator'  => '.',
				'thousands_separator' => ',',
			)
		);

		$query_id            = $settings['query_id'];
		$query_property      = $settings['query_property'];
		$calc_function       = ! empty( $settings['calc_function'] ) ? $settings['calc_function'] : 'sum';
		$decimals            = is_numeric( $settings['decimals'] ) ? intval( $settings['decimals'] ) : 2;
		$decimals_separator  = ! empty( $settings['decimals_separator'] ) ? $settings['decimals_separator'] : '.';
		$thousands_separator = is_string( $settings['thousands_separator'] ) ? $settings['thousands_separator'] : '';

		if ( ! $query_id || ! $query_property || ! $calc_function ) {
			return $this->format_number( 0, $decimals, $decimals_separator, $thousands_separator );
		}

		$query_instance = Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query_instance ) {
			return $this->format_number( 0, $decimals, $decimals_separator, $thousands_separator );
		}

		$items = $query_instance->get_items();

		if ( empty( $items ) ) {
			return $this->format_number( 0, $decimals, $decimals_separator, $thousands_separator );
		}

		$values = array();

		foreach ( $items as $item ) {
			if ( is_array( $item ) && isset( $item[ $query_property ] ) ) {
				$values[] = floatval( $item[ $query_property ] );
			} elseif ( is_object( $item ) && isset( $item->{$query_property} ) ) {
				$values[] = floatval( $item->{$query_property} );
			}
		}

		if ( empty( $values ) ) {
			return $this->format_number( 0, $decimals, $decimals_separator, $thousands_separator );
		}

		$values_count = count( $values );
		$result       = 0;

		switch ( $calc_function ) {
			case 'sum':
				$result = array_sum( $values );
				break;
			case 'avg':
				$result = $values_count ? ( array_sum( $values ) / $values_count ) : 0;
				break;
			case 'min':
				$result = min( $values );
				break;
			case 'max':
				$result = max( $values );
				break;
			case 'first':
				$result = $values[0];
				break;
			case 'last':
				$result = $values[ $values_count - 1 ];
				break;
			case 'std_dev':
				if ( $values_count ) {
					$mean             = array_sum( $values ) / $values_count;
					$sum_squared_diff = 0;

					foreach ( $values as $value ) {
						$sum_squared_diff += pow( $value - $mean, 2 );
					}

					$result = sqrt( $sum_squared_diff / $values_count );
				}
				break;
			case 'variance':
				if ( $values_count ) {
					$mean             = array_sum( $values ) / $values_count;
					$sum_squared_diff = 0;

					foreach ( $values as $value ) {
						$sum_squared_diff += pow( $value - $mean, 2 );
					}

					$result = $sum_squared_diff / $values_count;
				}
				break;
			case 'median':
				sort( $values );
				$middle = floor( ( $values_count - 1 ) / 2 );

				if ( $values_count % 2 ) {
					$result = $values[ $middle ];
				} else {
					$result = ( $values[ $middle ] + $values[ $middle + 1 ] ) / 2;
				}
				break;
			case 'mode':
				$string_values = array_map( 'strval', $values );
				$counts        = array_count_values( $string_values );

				if ( empty( $counts ) ) {
					$result = 0;
				} else {
					$max_count = max( $counts );
					$modes     = array_keys( $counts, $max_count, true );
					$result    = floatval( reset( $modes ) );
				}
				break;
			default:
				$result = 0;
				break;
		}

		return $this->format_number( $result, $decimals, $decimals_separator, $thousands_separator );
	}

	protected function format_number( $value, $decimals, $decimals_separator, $thousands_separator ) {
		return wp_kses_post( number_format( (float) $value, $decimals, $decimals_separator, $thousands_separator ) );
	}
}
