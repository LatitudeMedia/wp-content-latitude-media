<?php
namespace Jet_Engine\Query_Builder\Rest;

use Jet_Engine\Query_Builder\Manager;

class Update_Preview extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'update-query-preview';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {
		Manager::instance()->include_factory();

		$params = $request->get_params();
		$type = ! empty( $params['query_type'] ) ? $params['query_type'] : false;

		if ( ! $type ) {
			return $this->response_error();
		}

		$is_sql_query = $this->should_capture_sql( $type );

		if ( $is_sql_query ) {
			$this->start_sql_capture();
		}

		$this->setup_preview( $params['preview'] ?? [] );

		$query = $this->get_query_instance( $params, $type );

		if ( ! $query ) {
			return $this->response_query_not_found();
		}

		$items = $query->get_items();

		if ( $is_sql_query ) {
			$sql_preview = $this->generate_sql_preview();
		}

		$count = $query->get_items_total_count();

		$items = $this->slice_items( $items, $count, $params['preview']['query_count'] ?? 10 );
		$debug_info = $query->debug_info();

		$response = array(
			'success' => true,
			'count'   => $count,
			'data'    => $this->stringify_data( $query, $items['items'], $items['more'] ),
		);

		if ( ! empty( $debug_info ) ) {
			$response['debug_info'] = $debug_info;
		}

		if ( $is_sql_query && ! empty( $sql_preview ) ) {
			$response['sql_preview'] = $sql_preview;
		}

		return rest_ensure_response( $response );
	}

	public function setup_preview( $preview = array() ) {

		if ( ! empty( $preview['page'] ) ) {

			global $wp_query, $post;

			$pid = absint( $preview['page'] );
			$post = get_post( $pid );

			if ( $post && 'page' === $post->post_type ) {
				$wp_query = new \WP_Query( array( 'page_id' => $pid ) );
			} elseif ( $post ) {
				$wp_query = new \WP_Query( array( 'p' => $pid ) );
			}

		} elseif ( ! empty( $preview['page_url'] ) && false !== strpos( $_SERVER['REQUEST_URI'], '/wp-json/' ) ) {

			// Raw URL processing is allowed only for pretty permalinks
			$_SERVER['REQUEST_URI'] = preg_replace(
				'/wp-json\/.*/',
				ltrim( $preview['page_url'], '/' ),
				$_SERVER['REQUEST_URI']
			);

			global $wp;

			$wp->parse_request();
			$wp->query_posts();
			wp_reset_postdata();

		}

		if ( ! empty( $preview['query_string'] ) ) {

			parse_str( $preview['query_string'], $query_array );

			if ( ! empty( $query_array ) ) {
				foreach ( $query_array as $key => $value ) {
					$_GET[ $key ]     = $value;
					$_REQUEST[ $key ] = $value;
				}
			}

		}

	}

	public function should_capture_sql( $type ) {
		$sql_ignored_types = [ 'rest-api' ];
		return ! in_array( $type, $sql_ignored_types, true );
	}

	public function start_sql_capture() {
		global $wpdb;
		$wpdb->queries = [];

		if ( ! defined( 'SAVEQUERIES' ) ) {
			define( 'SAVEQUERIES', true );
		}
	}

	public function generate_sql_preview() {
		global $wpdb;

		$out        = [];
		$total_time = 0;

		foreach ( $wpdb->queries as $i => $q ) {
			$sql     = $q[0] ?? '';
			$time    = isset( $q[1] ) ? round( (float) $q[1], 4 ) : 0;
			$total_time += $time;

			if ( ! $sql ) {
				continue;
			}

			$clean = $this->clean_sql( $sql );

			$out[] = "#".( $i + 1 )." ({$time} s)\n{$clean};\n";
		}

		$out[] = 'Total Queries: ' . count( $wpdb->queries );
		$out[] = 'Total Time: ' . round( $total_time, 4 ) . ' s';

		return implode( "\n", $out );
	}

	public function get_query_instance( $params, $type ) {
		$factory = new \Jet_Engine\Query_Builder\Query_Factory( [
			'id'     => $params['query_id'],
			'labels' => [
				'name' => ! empty( $params['general_settings']['name'] )
					? sprintf( 'Preview (%s)', $params['general_settings']['name'] )
					: 'Preview',
			],
			'args'   => [
				'query_type'         => $type,
				$type                => $params['query'],
				'__dynamic_' . $type => $params['dynamic_query'],
			],
		] );

		return $factory->get_query();
	}

	public function slice_items( $items, $count, $limit ) {
		$limit = absint( $limit ) ?: 10;
		$more  = '';

		if ( $limit < $count ) {
			$items = array_slice( $items, 0, $limit );
			$more  = "\r\n...";
		}

		return [
			'items' => $items,
			'more'  => $more,
		];
	}


	public function response_error() {
		return rest_ensure_response([
			'success' => false,
			'data'    => null,
		]);
	}

	public function response_query_not_found() {
		return rest_ensure_response([
			'success' => true,
			'count'   => 0,
			'data'    => __( 'Can`t find the query object', 'jet-engine' ),
		]);
	}

	public function stringify_data( $query = null, $items = array(), $more = '' ) {
		ob_start();
		$query->before_preview_body();
		print_r( $items );
		return ob_get_clean() . $more;
	}

	/**
	 * Format and clean raw SQL string for readable output.
	 *
	 * @param string $sql Raw SQL string.
	 *
	 * @return string Formatted SQL.
	 */
	public function clean_sql( $sql ) {
		// Replace tabs with spaces for consistent formatting.
		$sql = str_replace( "\t", ' ', $sql );

		// Collapse multiple whitespace characters into a single space.
		$sql = preg_replace( '/\s+/', ' ', $sql );

		// Insert line breaks before common SQL structural keywords.
		$sql = preg_replace(
			'/\b(FROM|WHERE|INNER JOIN|LEFT JOIN|RIGHT JOIN|ORDER BY|GROUP BY|LIMIT|HAVING)\b/i',
			"\n$1",
			$sql
		);

		// Trim extra whitespace from the beginning and end.
		return trim( $sql );
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

}
