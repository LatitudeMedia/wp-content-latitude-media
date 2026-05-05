<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Query_Builder\MCP\Converters;

use Jet_Engine\Query_Builder\MCP\Converters\Common_Trait;
use Jet_Engine\Query_Builder\MCP\Converters\Converter_Interface;

class Custom_Content_Type implements Converter_Interface {

	use Common_Trait;

	public function convert( array $args ): array {
		$out = [];

		// Try to detect content type from various possible keys
		if ( isset( $args['content_type'] ) ) {
			$out['content_type'] = (string) $args['content_type'];
		} elseif ( isset( $args['post_type'] ) ) {
			$out['content_type'] = (string) $args['post_type'];
		} elseif ( isset( $args['custom_content_type'] ) ) {
			$out['content_type'] = (string) $args['custom_content_type'];
		} elseif ( isset( $args['cct'] ) ) {
			$out['content_type'] = (string) $args['cct'];
		} elseif ( isset( $args['cct_slug'] ) ) {
			$out['content_type'] = (string) $args['cct_slug'];
		} elseif ( isset( $args['custom_content_type_slug'] ) ) {
			$out['content_type'] = (string) $args['custom_content_type_slug'];
		} elseif ( isset( $args['custom_content_type_name'] ) ) {
			$out['content_type'] = (string) $args['custom_content_type_name'];
		}

		if ( isset( $args['posts_per_page'] ) && ! isset( $args['number'] ) ) {
			$args['number'] = $args['posts_per_page'];
		}
		if ( isset( $args['post_status'] ) && ! isset( $args['status'] ) ) {
			$args['status'] = $args['post_status'];
		}
		if ( isset( $args['s'] ) && ! isset( $args['search_query'] ) ) {
			$args['search_query'] = $args['s'];
		}

		$this->copy_as_string( $args, $out, [ 'number','offset','status','search_query' ] );

		$out['order'] = $this->map_order_cct( $args );

		$meta_index = [];
		if ( ! empty( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
			[ $meta_out, $meta_index ] = $this->map_meta_query_list_common(
				$args['meta_query'],
				'object',
				'object'
			);
			if ( $meta_out ) {
				$out['args'] = $this->rename_meta_to_args( $meta_out );
			}
			$rel = $this->detect_relation( $args['meta_query'] );
			if ( $rel ) {
				$out['relation'] = $rel;
			}
		}

		$out['__dynamic_custom-content-type'] = [
			'args' => $meta_index,
		];

		return $out;
	}

	private function map_order_cct( array $args ): array {
		$result = [];
		$orderby = $args['orderby'] ?? null;
		$order	 = $args['order'] ?? 'ASC';

		if ( is_array( $orderby ) ) {
			foreach ( $orderby as $field => $ord ) {
				$result[] = [
					'_id'		=> $this->gen_id(),
					'collapsed' => false,
					'orderby'	=> (string) $field,
					'order'		=> strtoupper( (string) $ord ?: 'ASC' ),
					'type'		=> '',
				];
			}
		} elseif ( is_string( $orderby ) && $orderby !== '' ) {
			$result[] = [
				'_id'		=> $this->gen_id(),
				'collapsed' => false,
				'orderby'	=> $orderby,
				'order'		=> strtoupper( (string) $order ?: 'ASC' ),
				'type'		=> '',
			];
		}

		return $result;
	}

	private function rename_meta_to_args( array $list ): array {
		$result = [];
		foreach ( $list as $clause ) {
			if ( isset( $clause['is_group'] ) && ! empty( $clause['args'] ) ) {
				$clause['args'] = $this->rename_meta_to_args( $clause['args'] );
				$result[] = $clause;
			} else {
				$result[] = [
					'_id'		=> $clause['_id'],
					'collapsed' => $clause['collapsed'],
					'field'		=> $clause['key'],
					'operator'	=> $clause['compare'],
					'value'		=> $clause['value'],
					'type'		=> $clause['type'],
				];
			}
		}
		return $result;
	}
}
