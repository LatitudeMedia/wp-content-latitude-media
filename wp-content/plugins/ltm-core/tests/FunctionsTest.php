<?php
/**
 * Tests for the plugin's global helper functions.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests;

use WP_UnitTestCase;

/**
 * @covers ::get_post_sponsor
 */
class FunctionsTest extends WP_UnitTestCase {

	public function test_returns_null_when_post_has_no_sponsor_term() {
		$post_id = self::factory()->post->create();

		$this->assertNull( get_post_sponsor( $post_id ) );
	}

	public function test_returns_null_when_term_has_no_synced_sponsor_meta() {
		$post_id = self::factory()->post->create();
		$term    = self::factory()->term->create_and_get( [ 'taxonomy' => 'sponsor' ] );
		wp_set_object_terms( $post_id, [ $term->term_id ], 'sponsor' );

		$this->assertNull( get_post_sponsor( $post_id ) );
	}

	public function test_resolves_the_linked_sponsor_post() {
		$sponsor_id = self::factory()->post->create( [ 'post_type' => 'sponsors', 'post_title' => 'Acme Corp' ] );

		$terms = get_terms(
			[
				'taxonomy'   => 'sponsor',
				'hide_empty' => false,
				'meta_key'   => '_sponsor_post_id',
				'meta_value' => $sponsor_id,
				'fields'     => 'ids',
			]
		);
		$term_id = $terms[0];

		$post_id = self::factory()->post->create();
		wp_set_object_terms( $post_id, [ $term_id ], 'sponsor' );

		$sponsor = get_post_sponsor( $post_id );

		$this->assertInstanceOf( \WP_Post::class, $sponsor );
		$this->assertSame( $sponsor_id, $sponsor->ID );
	}

	public function test_falls_back_to_the_current_global_post_when_no_id_given() {
		$post_id = self::factory()->post->create();

		global $post;
		$post = get_post( $post_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		setup_postdata( $post );

		$this->assertNull( get_post_sponsor() );

		wp_reset_postdata();
	}
}
