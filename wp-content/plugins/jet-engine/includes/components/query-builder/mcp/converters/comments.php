<?php
namespace Jet_Engine\Query_Builder\MCP\Converters;

class Comments implements Converter_Interface {

	use Common_Trait;

	public function convert( array $args ): array {
		$out = [];

		/* ====================== meta_query ====================== */
		$meta_index = [];
		if ( ! empty( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
			[ $meta_out, $meta_index ] = $this->map_meta_query_list_common(
				$args['meta_query'],
				'object',
				'object'
			);
			if ( $meta_out ) {
				$out['meta_query'] = $meta_out;
			}
			$rel = $this->detect_relation( $args['meta_query'] );
			if ( $rel ) {
				$out['meta_query_relation'] = strtolower( $rel );
			}
		}

		/* ====================== date_query ====================== */
		$date_index = [];
		if ( ! empty( $args['date_query'] ) && is_array( $args['date_query'] ) ) {
			[ $date_out, $date_index ] = $this->map_date_query_list_common( $args['date_query'] );
			if ( $date_out ) {
				$out['date_query'] = $date_out;
			}
			$rel = $this->detect_relation( $args['date_query'] );
			if ( $rel ) {
				$out['date_query_relation'] = strtolower( $rel );
			}
		}

		/* ================== scalar and list args ================= */
		$this->copy_as_string( $args, $out, [
			'number','paged','offset','orderby','meta_key','post_id','post_parent',
			'post_status','post_type','post_name','search','status','type',
			'author_email','author_url','parent','order','geosearch_location',
		] );

		$this->copy_list_as_csv_string( $args, $out, [
			'comment__in','comment__not_in','parent__in','parent__not_in',
			'author__in','author__not_in','post_author__in','post_author__not_in',
			'post__in','post__not_in','type__in','type__not_in',
		] );

		$out['__dynamic_comments'] = [
			'meta_query' => $meta_index,
			'date_query' => $date_index,
		];

		return $out;
	}
}
