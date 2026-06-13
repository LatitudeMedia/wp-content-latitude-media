<?php
namespace Jet_Engine\Query_Builder\MCP\Converters;

class Users implements Converter_Interface {

	use Common_Trait;

	public function convert( array $args ): array {
		$out = [];

		/* ================ meta_query (root:{}, nested:[]) ================ */
		$meta_index = [];
		if ( ! empty( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
			[ $meta_out, $meta_index ] = $this->map_meta_query_list_common(
				$args['meta_query'],
				'object', // root leaves → {}
				'array'   // nested leaves → []
			);
			if ( $meta_out ) {
				$out['meta_query'] = $meta_out;
			}
			$rel = $this->detect_relation( $args['meta_query'] );
			if ( $rel ) {
				$out['meta_query_relation'] = strtolower( $rel );
			}
		}

		/* ========================= date_query =========================== */
		$date_index = [];
		if ( ! empty( $args['date_query'] ) && is_array( $args['date_query'] ) ) {
			[ $date_out, $date_index ] = $this->map_date_query_list_common( $args['date_query'] );
			if ( $date_out ) {
				$out['date_query'] = $date_out;
			}
		}

		/* ============================= roles ============================ */
		if ( array_key_exists( 'role', $args ) ) {
			$out['role'] = $this->normalize_to_string_array( $args['role'] );
		}
		if ( array_key_exists( 'role__in', $args ) ) {
			$out['role__in'] = $this->normalize_to_string_array( $args['role__in'] );
		}
		if ( array_key_exists( 'role__not_in', $args ) ) {
			$out['role__not_in'] = $this->normalize_to_string_array( $args['role__not_in'] );
		}

		/* ====================== include / exclude ======================= */
		$this->copy_list_as_csv_string( $args, $out, [ 'include', 'exclude' ] );

		/* ==================== search & columns ========================== */
		$this->copy_as_string( $args, $out, [ 'search', 'geosearch_location' ] );
		if ( array_key_exists( 'search_columns', $args ) ) {
			$out['search_columns'] = $this->normalize_to_string_array( $args['search_columns'] );
		}

		/* ==================== limits / pagination ======================= */
		$this->copy_as_string( $args, $out, [ 'number', 'offset', 'paged', 'orderby', 'order' ] );

		/* ========================= __dynamic_users ====================== */
		$out['__dynamic_users'] = [
			'meta_query' => $meta_index,
			'date_query' => $date_index,
		];

		return $out;
	}
}
