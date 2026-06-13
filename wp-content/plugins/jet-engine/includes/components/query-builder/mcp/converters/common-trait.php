<?php
namespace Jet_Engine\Query_Builder\MCP\Converters;

/**
 * Shared helpers for converting WP_Query / WP_Term_Query args
 * into Crocoblock-like JSON storage structures.
 */
trait Common_Trait {

	/* ============================== IDs ============================== */

	protected function gen_id(): int {
		// 6-digit ID to resemble your UI examples.
		return random_int( 100000, 999999 );
	}

	/* ========================== Array checks ========================= */

	protected function is_assoc( array $arr ): bool {
		return $arr !== [] && array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}

	protected function is_numerically_indexed( array $arr ): bool {
		return array_keys( $arr ) === range( 0, count( $arr ) - 1 );
	}

	/* ========================= Scalar casting ======================== */

	protected function scalar_to_string( $val ): string {
		if ( is_array( $val ) ) {
			return implode( ', ', array_map( 'strval', $val ) );
		}
		if ( is_bool( $val ) ) {
			return $val ? '1' : '0';
		}
		return (string) $val;
	}

	/* ======================== Copy helpers =========================== */

	protected function copy_as_string( array $in, array &$out, array $keys ): void {
		foreach ( $keys as $k ) {
			if ( array_key_exists( $k, $in ) && $in[ $k ] !== null ) {
				$out[ $k ] = $this->scalar_to_string( $in[ $k ] );
			}
		}
	}

	protected function copy_as_bool( array $in, array &$out, array $keys ): void {
		foreach ( $keys as $k ) {
			if ( array_key_exists( $k, $in ) ) {
				$out[ $k ] = (bool) $in[ $k ];
			}
		}
	}

	protected function copy_list_as_csv_string( array $in, array &$out, array $keys ): void {
		foreach ( $keys as $k ) {
			if ( isset( $in[ $k ] ) ) {
				$val = $in[ $k ];
				if ( is_array( $val ) ) {
					$out[ $k ] = implode( ', ', array_map( 'strval', $val ) );
				} else {
					$out[ $k ] = (string) $val;
				}
			}
		}
	}

	protected function normalize_to_string_array( $val ): array {
		if ( is_array( $val ) ) {
			return array_values( array_map( 'strval', $val ) );
		}
		if ( $val === null || $val === '' ) {
			return [];
		}
		return [ (string) $val ];
	}

	/* ==================== Relation / list utilities ================== */

	protected function detect_relation( array $arr ): ?string {
		$rel = $arr['relation'] ?? null;
		if ( is_string( $rel ) && $rel ) {
			$rel = strtolower( $rel );
			return in_array( $rel, [ 'and', 'or' ], true ) ? strtoupper( $rel ) : 'AND';
		}
		return null;
	}

	protected function strip_relation( array $arr ): array {
		if ( isset( $arr['relation'] ) ) {
			unset( $arr['relation'] );
		}
		if ( $this->is_assoc( $arr ) ) {
			$arr = array_values( $arr );
		}
		return $arr;
	}

	/* ===================== Meta query mapping ======================== */

	protected function map_meta_query_list_common( array $list ): array {

		$out       = [];
		$index_map = [];

		$clauses = $this->strip_relation( $list );

		foreach ( $clauses as $clause ) {
			if ( ! is_array( $clause ) ) {
				continue;
			}

			if ( $this->looks_like_meta_group( $clause ) ) {
				$group_id = $this->gen_id();
				[ $sub_out, $sub_index ] = $this->map_meta_query_list_common(
					$clause,
					true
				);

				$out[] = [
					'is_group'  => true,
					'relation'  => strtolower( $this->detect_relation( $clause ) ?: 'and' ),
					'args'      => $sub_out ?: [],
					'_id'       => $group_id,
					'collapsed' => false,
				];

				$index_map[ (string) $group_id ] = $sub_index ?: [];
			} else {

				$leaf = $this->map_meta_clause_common( $clause );
				$out[] = $leaf;
				$dynamic_value = [];

				// Check if value looks like macro - %anything%
				if (
					! empty( $leaf['value'] )
					&& is_string( $leaf['value'] )
					&& preg_match( '/^%.*%$/', $leaf['value'] )
				) {
					$dynamic_value['value'] = $leaf['value'];
				}

				$index_map[ (string) $leaf['_id'] ] = $dynamic_value;
			}
		}

		return [ $out ?: null, $index_map ?: [] ];
	}

	protected function map_meta_clause_common( array $clause ): array {
		$key     = isset( $clause['key'] ) ? (string) $clause['key'] : '';
		$compare = isset( $clause['compare'] ) ? strtoupper( (string) $clause['compare'] ) : '=';
		$type    = isset( $clause['type'] ) ? strtoupper( (string) $clause['type'] ) : '';
		$value   = $clause['value'] ?? '';

		if ( is_array( $value ) ) {
			$value = implode( ', ', array_map( 'strval', $value ) );
		} else {
			$value = (string) $value;
		}

		return [
			'_id'         => $this->gen_id(),
			'collapsed'   => false,
			'key'         => $key,
			'compare'     => $compare,
			'value'       => $value,
			'type'        => $type,
			'clause_name' => $key,
		];
	}

	protected function looks_like_meta_group( array $maybe ): bool {
		if ( empty( $maybe ) ) {
			return false;
		}
		if ( isset( $maybe['relation'] ) ) {
			$rest = $this->strip_relation( $maybe );
			foreach ( $rest as $r ) {
				if ( is_array( $r ) ) {
					return true;
				}
			}
		}
		return $this->is_numerically_indexed( $maybe )
			&& isset( $maybe[0] )
			&& is_array( $maybe[0] )
			&& ( isset( $maybe[0]['key'] ) || isset( $maybe[0]['relation'] ) );
	}

	/* ===================== Date query mapping ======================== */

	protected function map_date_query_list_common( array $list ): array {
		$out       = [];
		$index_map = [];

		$clauses = $this->strip_relation( $list );
		foreach ( $clauses as $clause ) {
			if ( ! is_array( $clause ) ) {
				continue;
			}
			$id = $this->gen_id();

			$out[] = [
				'_id'       => $id,
				'collapsed' => false,
				'year'      => isset( $clause['year'] )  ? (string) $clause['year']  : '',
				'month'     => isset( $clause['month'] ) ? (string) $clause['month'] : '',
				'day'       => isset( $clause['day'] )   ? (string) $clause['day']   : '',
				'after'     => isset( $clause['after'] ) ? $this->scalar_to_string( $clause['after'] )  : '',
				'before'    => isset( $clause['before'] )? $this->scalar_to_string( $clause['before'] ) : '',
				'inclusive' => isset( $clause['inclusive'] ) ? (bool) $clause['inclusive'] : false,
				'compare'   => isset( $clause['compare'] ) ? strtoupper( (string) $clause['compare'] ) : '',
				'column'    => isset( $clause['column'] )  ? (string) $clause['column'] : '',
			];
			$index_map[ (string) $id ] = new \stdClass();
		}

		return [ $out ?: null, $index_map ?: new \stdClass() ];
	}
}
