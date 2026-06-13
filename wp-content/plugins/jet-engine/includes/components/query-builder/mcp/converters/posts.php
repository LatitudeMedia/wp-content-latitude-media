<?php
namespace Jet_Engine\Query_Builder\MCP\Converters;

class Posts implements Converter_Interface {

	use Common_Trait;

	/**
	 * Entry point.
	 */
	public function convert( array $args ): array {
		$out = [];

		// 1) orderby -> [{ _id, collapsed:false, orderby, order }]
		$out['orderby'] = $this->map_orderby_posts( $args );

		// 2) meta_query (root:{}, nested:{})
		$meta_index = [];
		if ( ! empty( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
			[ $meta_out, $meta_index ] = $this->map_meta_query_list_common(
				$args['meta_query'],
				'object', // root leaves → {}
				'object'  // nested leaves → {}
			);
			if ( $meta_out ) {
				$out['meta_query'] = $meta_out;
			}

			$rel = $this->detect_relation( $args['meta_query'] );

			if ( $rel ) {
				$out['meta_query_relation'] = strtolower( $rel );
			}
		}

		// 3) tax_query
		$tax_index = [];
		if ( ! empty( $args['tax_query'] ) && is_array( $args['tax_query'] ) ) {
			[ $tax_out, $tax_index ] = $this->map_tax_query_list_posts( $args['tax_query'] );
			if ( $tax_out ) {
				$out['tax_query'] = $tax_out;
			}
		}

		// 4) date_query
		$date_index = [];
		if ( ! empty( $args['date_query'] ) && is_array( $args['date_query'] ) ) {
			[ $date_out, $date_index ] = $this->map_date_query_list_common( $args['date_query'] );
			if ( $date_out ) {
				$out['date_query'] = $date_out;
			}
		}

		// 5) Scalars and lists (normalize to match UI)
		$this->map_common_args_posts( $args, $out );

		// 6) __dynamic_posts index
		$out['__dynamic_posts'] = [
			'meta_query' => $meta_index,
			'tax_query'  => $tax_index,
			'date_query' => $date_index,
		];

		return $out;
	}

	/* ======================= Posts specifics ======================= */

	private function map_orderby_posts( array $args ): array {

		$result  = [];
		$orderby = $args['orderby'] ?? null;
		$order   = $args['order']   ?? 'DESC';

		if ( is_array( $orderby ) ) {
			foreach ( $orderby as $field => $ord ) {

				if ( is_array( $ord ) && isset( $ord['_id'] ) ) {
					$result[] = $ord;
				} else {
					$result[] = [
						'_id'       => $this->gen_id(),
						'collapsed' => false,
						'orderby'   => (string) $field,
						'order'     => strtoupper( (string) $ord ?: 'DESC' ),
					];
				}
			}
		} elseif ( is_string( $orderby ) && $orderby !== '' ) {
			$result[] = [
				'_id'       => $this->gen_id(),
				'collapsed' => false,
				'orderby'   => $orderby,
				'order'     => strtoupper( (string) $order ?: 'DESC' ),
			];
		}
		return $result;
	}

	/**
	 * @return array{0: array<int,array>|null, 1: array<string,array>}
	 */
	private function map_tax_query_list_posts( array $list ): array {
		$out       = [];
		$index_map = [];

		$clauses = $this->strip_relation( $list );
		foreach ( $clauses as $clause ) {
			if ( ! is_array( $clause ) ) {
				continue;
			}
			$id = $this->gen_id();

			$taxonomy = isset( $clause['taxonomy'] ) ? (string) $clause['taxonomy'] : '';
			$field    = isset( $clause['field'] ) ? (string) $clause['field'] : 'term_id';
			$operator = isset( $clause['operator'] ) ? strtoupper( (string) $clause['operator'] ) : 'IN';
			$terms    = $clause['terms'] ?? [];

			if ( is_array( $terms ) ) {
				$terms = implode( ', ', array_map( 'strval', $terms ) );
			} else {
				$terms = (string) $terms;
			}

			$out[] = [
				'_id'       => $id,
				'collapsed' => false,
				'taxonomy'  => $taxonomy,
				'field'     => $field,
				'terms'     => $terms,
				'operator'  => $operator,
			];
			$index_map[ (string) $id ] = new \stdClass();
		}

		return [ $out ?: null, $index_map ?: new \stdClass() ];
	}

	private function map_common_args_posts( array $in, array &$out ): void {
		$as_arrays = [ 'post_type', 'post_status' ];
		foreach ( $as_arrays as $k ) {
			if ( isset( $in[ $k ] ) ) {
				$out[ $k ] = is_array( $in[ $k ] )
					? array_values( array_map( 'strval', $in[ $k ] ) )
					: [ (string) $in[ $k ] ];
			}
		}

		$this->copy_as_string( $in, $out, [
			's','post_password','post_name__in','name','p','page_id','pagename',
			'comment_count_value','comment_count_compare','posts_per_page','offset',
			'paged','page','author','author_name','post_parent','geosearch_location',
		] );

		$this->copy_as_bool( $in, $out, [
			'sentence','has_password','avoid_duplicates','ignore_sticky_posts','inclusive',
		] );

		$this->copy_list_as_csv_string( $in, $out, [
			'post__in','post__not_in','post_parent__in','post_parent__not_in',
			'author__in','author__not_in',
		] );
	}
}