<?php
namespace Jet_Engine\Query_Builder\MCP\Converters;

class Terms implements Converter_Interface {

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

		/* =========================== taxonomy =========================== */
		if ( isset( $args['taxonomy'] ) ) {
			$out['taxonomy'] = $this->normalize_to_string_array( $args['taxonomy'] );
		}

		/* ===================== ordering / pagination ==================== */
		$this->copy_as_string( $args, $out, [ 'orderby', 'order' ] );
		if ( array_key_exists( 'number', $args ) ) {
			$out['number'] = $this->scalar_to_string( $args['number'] );
			$out['number_per_page'] = $out['number'];
		}
		if ( array_key_exists( 'offset', $args ) ) {
			$out['offset'] = $this->scalar_to_string( $args['offset'] );
		}

		/* ============================== flags =========================== */
		$this->copy_as_bool( $args, $out, [ 'hide_empty', 'hierarchical', 'childless' ] );

		/* ========================= filters / fields ===================== */
		$this->copy_as_string( $args, $out, [
			'search','name__like','description__like','slug','parent','child_of','geosearch_location',
		] );

		$this->copy_list_as_csv_string( $args, $out, [
			'name','include','exclude','exclude_tree','object_ids',
		] );

		/* ========================= __dynamic_terms ====================== */
		$out['__dynamic_terms'] = [
			'meta_query' => $meta_index,
		];

		return $out;
	}
}
