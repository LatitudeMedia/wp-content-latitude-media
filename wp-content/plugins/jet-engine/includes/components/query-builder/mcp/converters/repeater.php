<?php
namespace Jet_Engine\Query_Builder\MCP\Converters;

class Repeater implements Converter_Interface {

	use Common_Trait;

	public function convert( array $args ): array {
		$out = [];

		/* ================== basic args ================== */
		$this->copy_as_string( $args, $out, [
			'source','jet_engine_field','jet_engine_option_field','custom_field',
			'object_id','fields_list','per_page','offset',
		] );
		$this->copy_as_bool( $args, $out, [ 'use_preview_settings' ] );

		/* ================== orderby ================== */
		$out['orderby'] = $this->map_orderby_repeater( $args );

		/* ================== meta_query ================== */
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

		$out['__dynamic_repeater'] = [
			'meta_query' => $meta_index,
		];

		return $out;
	}

	private function map_orderby_repeater( array $args ): array {
		$result	 = [];
		$orderby = $args['orderby'] ?? null;

		if ( is_array( $orderby ) ) {
			if ( $this->is_numerically_indexed( $orderby ) ) {
				foreach ( $orderby as $item ) {
					if ( ! is_array( $item ) ) {
						continue;
					}
					$result[] = [
						'_id'		=> $this->gen_id(),
						'collapsed' => false,
						'orderby'	=> isset( $item['orderby'] ) ? (string) $item['orderby'] : '',
						'field_name'=> isset( $item['field_name'] ) ? (string) $item['field_name'] : '',
						'order_type'=> isset( $item['order_type'] ) ? (string) $item['order_type'] : 'numeric',
						'order'		=> isset( $item['order'] ) ? strtoupper( (string) $item['order'] ) : 'ASC',
					];
				}
			} else {
				foreach ( $orderby as $field => $ord ) {
					$result[] = [
						'_id'		=> $this->gen_id(),
						'collapsed' => false,
						'orderby'	=> (string) $field,
						'field_name'=> '',
						'order_type'=> 'numeric',
						'order'		=> strtoupper( (string) $ord ?: 'ASC' ),
					];
				}
			}
		} elseif ( is_string( $orderby ) && $orderby !== '' ) {
			$order = isset( $args['order'] ) ? strtoupper( (string) $args['order'] ) : 'ASC';
			$result[] = [
				'_id'		=> $this->gen_id(),
				'collapsed' => false,
				'orderby'	=> $orderby,
				'field_name'=> '',
				'order_type'=> 'numeric',
				'order'		=> $order ?: 'ASC',
			];
		}

		return $result;
	}
}
