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
