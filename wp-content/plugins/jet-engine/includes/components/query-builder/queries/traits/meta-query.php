<?php
namespace Jet_Engine\Query_Builder\Queries\Traits;

trait Meta_Query_Trait {

	/**
	 * Prepare Meta Query arguments by initial arguments list
	 *
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function prepare_meta_query_args( $args = array() ) {

		$raw        = $args['meta_query'];
		$meta_query = array();

		$custom_meta_query = array();

		if ( ! empty( $args['meta_query_relation'] ) ) {
			$meta_query['relation'] = $args['meta_query_relation'];
		}

		foreach ( $raw as $query_row ) {

			if (
				! empty( $query_row['is_group'] )
				&& ! empty( $query_row['args'] )
			) {

				$relation = ! empty( $query_row['relation'] ) ? $query_row['relation'] : 'AND';

				$meta_query[] = array_merge(
					[ 'relation' => strtoupper( $relation ) ],
					$this->prepare_meta_query_args( [
						'meta_query' => $query_row['args']
					] )
				);

				continue;
			}

			$exclude_empty = ! empty( $query_row['exclude_empty'] ) ? $query_row['exclude_empty'] : false;
			$exclude_empty = filter_var( $exclude_empty, FILTER_VALIDATE_BOOLEAN );

			if ( $exclude_empty && \Jet_Engine_Tools::is_empty( $query_row, 'value' ) ) {
				continue;
			}

			if ( ! empty( $query_row['compare'] )
				 && in_array( $query_row['compare'], array( 'IN', 'NOT IN' ) )
				 && ! is_array( $query_row['value'] )
			) {
				$query_row['value'] = explode( ',', $query_row['value'] );
				$query_row['value'] = array_map( 'trim', $query_row['value'] );
			}

			if ( ! empty( $query_row['type'] ) && 'TIMESTAMP' === $query_row['type'] ) {
				$query_row['type']  = 'NUMERIC';
				$query_row['value'] = \Jet_Engine_Tools::is_valid_timestamp( $query_row['value'] ) ? $query_row['value'] : strtotime( $query_row['value'] );
			}

			if ( ! empty( $query_row['custom'] ) ) {
				unset( $query_row['custom'] );
				$custom_meta_query[] = $query_row;
				continue;
			}

			if ( ! empty( $query_row['clause_name'] ) ) {
				$meta_query[ $query_row['clause_name'] ] = $query_row;
			} else {
				$meta_query[] = $query_row;
			}

		}

		$is_or_relation = ! empty( $meta_query['relation'] ) && 'or' === $meta_query['relation'];

		if ( ! empty( $custom_meta_query ) ) {

			if ( $is_or_relation ) {
				$meta_query = array_merge( array( $meta_query ), $custom_meta_query );
			} else {
				$meta_query = array_merge( $meta_query, $custom_meta_query );
			}

		}

		return $meta_query;

	}

	/**
	 * Replace filtered arguments in the final query array
	 *
	 * @param  array  $rows [description]
	 * @return [type]       [description]
	 */
	public function replace_meta_query_row( $rows = array() ) {

		foreach ( $rows as $row_index => $row ) {
			$rows[ $row_index ] = $this->maybe_unslash_regexp_meta_query_row( $row );
		}

		$replaced_rows = array();

		if ( ! empty( $this->final_query['meta_query'] ) ) {

			$replace_rows = apply_filters( 'jet-engine/query-builder/meta-query/replace-rows', true, $this );

			if ( $replace_rows ) {
				foreach ( $this->final_query['meta_query'] as $index => $existing_row ) {
					foreach ( $rows as $row_index => $row ) {
						if ( isset( $row['key'] )
							&& isset( $existing_row['key'] )
							&& $existing_row['key'] === $row['key']
						) {

							if ( ! empty( $existing_row['clause_name'] ) ) {
								$row['clause_name'] = $existing_row['clause_name'];
							}

							$this->final_query['meta_query'][ $index ] = $row;
							$replaced_rows[] = $row_index;
							break;
						}
					}
				}
			}

		} else {
			$this->final_query['meta_query'] = array();
		}

		foreach ( $rows as $row_index => $row ) {
			if ( ! in_array( $row_index, $replaced_rows ) && is_array( $row ) ) {
				$row['custom'] = true;
				$this->final_query['meta_query'][] = $row;
			}
		}

	}

	public function get_dates_range_meta_query( $args = array(), $dates_range = array(), $settings = array() ) {

		$meta_key = $settings['group_by_key'] ? esc_attr( $settings['group_by_key'] ) : false;

		if ( isset( $args['meta_query'] ) ) {
			$meta_query = $args['meta_query'];
		} else {
			$meta_query = array();
		}

		$calendar_meta_query = array();

		if ( $meta_key ) {

			$calendar_meta_query = array_merge( $calendar_meta_query, array(
				array(
					'key'     => $meta_key,
					'value'   => array( $dates_range['start'], $dates_range['end'] ),
					'compare' => 'BETWEEN',
				),
			) );

		}

		if ( $meta_key && ! empty( $settings['allow_multiday'] ) && ! empty( $settings['end_date_key'] ) ) {

			$calendar_meta_query = array_merge( $calendar_meta_query, array(
				array(
					'key'     => esc_attr( $settings['end_date_key'] ),
					'value'   => array( $dates_range['start'], $dates_range['end'] ),
					'compare' => 'BETWEEN',
				),
				array(
					'relation' => 'AND',
					array(
						'key'     => $meta_key,
						'value'   => $dates_range['start'],
						'compare' => '<'
					),
					array(
						'key'     => esc_attr( $settings['end_date_key'] ),
						'value'   => $dates_range['end'],
						'compare' => '>'
					)
				),
			) );

			$calendar_meta_query['relation'] = 'OR';

		}

		$meta_query[] = $calendar_meta_query;

		return $meta_query;

	}

	/**
	 * Maybe unslash meta query row if the 'compare' operator is 'REGEXP'.
	 *
	 * Added by Photon to fix an issue with regex meta queries.
	 * Previous fix only addressed filtering by checkbox meta fields.
	 *
	 * @see https://github.com/Crocoblock/issues-tracker/issues/1199
	 * @see https://github.com/Crocoblock/issues-tracker/issues/16249
	 *
	 * @since 1.0.0
	 *
	 * @param array $row Meta query row.
	 * @return array Processed meta query row.
	 */
	public function maybe_unslash_regexp_meta_query_row( $row ) {
		$has_regexp_compare = isset( $row['compare'] ) && 'REGEXP' === $row['compare'];
		$has_relation       = ! empty( $row['relation'] );

		if ( $has_regexp_compare || $has_relation ) {
			$row = wp_unslash( $row );
		}

		return $row;
	}
}
