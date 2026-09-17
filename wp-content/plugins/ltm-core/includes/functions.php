<?php
/**
 * Global helper functions for the sponsor domain, kept callable unqualified
 * (no namespace) since they're used from theme code, template-part render
 * files, and block render.php files alike.
 *
 * @package LTMCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'get_post_sponsor' ) ) {
	/**
	 * Resolves the sponsor post linked to a given post via the `sponsor` taxonomy.
	 *
	 * @param int|null $post_id
	 * @return \WP_Post|null
	 */
	function get_post_sponsor( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$terms = wp_get_object_terms( $post_id, 'sponsor' );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return null;
		}

		$sponsor_post_id = get_term_meta( $terms[0]->term_id, '_sponsor_post_id', true );

		return $sponsor_post_id ? get_post( $sponsor_post_id ) : null;
	}
}

if ( ! function_exists( 'get_events_list' ) ) {
	/**
	 * Wrapper around LTMCore\RestApi\LoadMoreEvents::get_events_list(), kept
	 * unqualified so theme code can call it the way it always has.
	 *
	 * @param string $type
	 * @param array  $args
	 * @param array  $ids
	 * @return \WP_Query the query object
	 */
	function get_events_list( $type = '', $args = [], $ids = [] ) {
		return \LTMCore\RestApi\LoadMoreEvents::get_events_list( $type, $args, $ids );
	}
}

if ( ! function_exists( 'get_event_start_date' ) ) {
	/**
	 * Wrapper around LTMCore\PostTypes\Events::get_event_start_date(), kept
	 * unqualified so theme code can call it the way it always has.
	 *
	 * @param int    $event_id
	 * @param string $format
	 * @return string
	 */
	function get_event_start_date( $event_id, $format = 'F j Y' ) {
		return \LTMCore\PostTypes\Events::get_event_start_date( $event_id, $format );
	}
}

if ( ! function_exists( 'get_event__end_date' ) ) {
	/**
	 * Wrapper around LTMCore\PostTypes\Events::get_event__end_date(), kept
	 * unqualified (double underscore preserved as-is) so theme code can call
	 * it the way it always has.
	 *
	 * @param int    $event_id
	 * @param string $format
	 * @return string
	 */
	function get_event__end_date( $event_id, $format = 'F j Y' ) {
		return \LTMCore\PostTypes\Events::get_event__end_date( $event_id, $format );
	}
}

if ( ! function_exists( 'get_event_timezone' ) ) {
	/**
	 * Wrapper around LTMCore\PostTypes\Events::get_event_timezone(), kept
	 * unqualified so theme code can call it the way it always has.
	 *
	 * @param int $event_id
	 * @return string
	 */
	function get_event_timezone( $event_id ) {
		return \LTMCore\PostTypes\Events::get_event_timezone( $event_id );
	}
}

if ( ! function_exists( 'enable_jetpack_copy_to_all_post_types' ) ) {
	function enable_jetpack_copy_to_all_post_types( $post_types ) {
		$post_types = array_merge( $post_types, [
			'events',
			'guides',
			'industry-news',
			'in-house-ads',
			'order-reports',
			'podcasts',
			'research',
			'resources',
			'sections-landing',
			'speakers',
			'team',
			'sponsors',
			'thematic-pages',
		] );
		return $post_types;
	}
	add_filter( 'jetpack_copy_post_post_types', 'enable_jetpack_copy_to_all_post_types' );
}