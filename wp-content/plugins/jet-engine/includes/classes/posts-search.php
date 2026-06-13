<?php
/**
 * Posts search handler class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Engine_Posts_Search_Handler {

	public function __construct() {
		// Force search posts control to work
		add_action( 'wp_ajax_cx_search_posts', array( $this, 'process_posts_search' ) );
		add_action( 'wp_ajax_jet_engine_meta_box_posts', array( $this, 'process_posts_search' ) );
	}

	/**
	 * Perform posts search by arguments list
	 *
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function search_posts( $args = array() ) {

		add_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

		$type    = ! empty( $args['post_type'] ) ? $args['post_type'] : false;
		$query   = ! empty( $args['q'] ) ? $args['q'] : false;
		$exclude = ! empty( $args['exclude'] ) ? $args['exclude'] : false;

		if ( $type && ! is_array( $type ) ) {
			$type = explode( ',', $type );
		}

		if ( $exclude && ! is_array( $exclude ) ) {
			$exclude = explode( ',', $exclude );
		}

		$query_args = array(
			'ignore_sticky_posts' => true,
			'posts_per_page'      => -1,
			'suppress_filters'    => false,
			's_title'             => $query,
		);

		if ( $type && in_array( 'attachment', $type ) ) {
			$query_args['post_status'] = array( 'publish', 'inherit' );
		}

		if ( $exclude ) {
			$query_args['post__not_in'] = $exclude;
		}

		if ( $type ) {
			$query_args['post_type'] = $type;
		} else {
			$query_args['post_type'] = 'any';
		}

		$posts = get_posts( $query_args );

		remove_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

		$result = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$result[] = array(
					'id'   => $post->ID,
					'text' => $post->post_title,
				);
			}
		}

		return $result;

	}

	/**
	 * Ajax callback to search posts from request and send result as JSON
	 *
	 * @return void
	 */
	public function process_posts_search() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$post_type_raw    = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
		$query_raw   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
		$exclude_raw = isset( $_GET['exclude'] ) ? sanitize_text_field( wp_unslash( $_GET['exclude'] ) ) : '';

		$post_type    = explode( ',', $post_type_raw );
		$query   = $query_raw;
		$exclude = ! empty( $exclude_raw ) ? array_map( 'absint', explode( ',', $exclude_raw ) ) : false;

		wp_send_json( array(
			'results' => $this->search_posts( array(
				'post_type' => $post_type,
				'exclude'   => $exclude,
				'q'         => $query,
			) ),
		) );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Force query to look in post title while searching
	 *
	 * @return [type] [description]
	 */
	public function force_search_by_title( $where, $query ) {

		$args = $query->query;

		if ( ! isset( $args['s_title'] ) ) {
			return $where;
		} else {
			global $wpdb;

			$searh = esc_sql( $wpdb->esc_like( $args['s_title'] ) );
			$where .= " AND {$wpdb->posts}.post_title LIKE '%$searh%'";

		}

		return $where;

	}

}
