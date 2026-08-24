<?php
/**
 * Tests for the PostSponsor taxonomy's meta box, save handler, and REST
 * field — everything TaxonomiesTest.php doesn't already cover.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Taxonomies;

use WP_REST_Request;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Taxonomies\PostSponsor
 */
class PostSponsorTest extends WP_UnitTestCase {

	private function create_sponsor( string $title = 'Acme Corp' ): int {
		return self::factory()->post->create(
			[
				'post_type'   => 'sponsors',
				'post_title'  => $title,
				'post_status' => 'publish',
			]
		);
	}

	private function get_synced_term_id( int $sponsor_post_id ): int {
		$terms = get_terms(
			[
				'taxonomy'   => 'sponsor',
				'hide_empty' => false,
				'meta_key'   => '_sponsor_post_id',
				'meta_value' => $sponsor_post_id,
				'fields'     => 'ids',
			]
		);

		return ! empty( $terms ) && ! is_wp_error( $terms ) ? (int) $terms[0] : 0;
	}

	public function test_sponsor_term_map_is_keyed_by_sponsor_post_id_and_ordered_by_name() {
		$this->create_sponsor( 'Zeta Sponsor' );
		$this->create_sponsor( 'Acme Corp' );

		$map = ( new \LTMCore\Taxonomies\PostSponsor() )->get_sponsor_term_map();

		$this->assertSame( [ 'Acme Corp', 'Zeta Sponsor' ], array_map( fn( $term ) => $term->name, array_values( $map ) ) );
		foreach ( $map as $sponsor_post_id => $term ) {
			$this->assertSame( get_the_title( $sponsor_post_id ), $term->name );
		}
	}

	public function test_meta_box_renders_not_sponsored_and_one_option_per_sponsor() {
		$sponsor_id = $this->create_sponsor();
		$term_id    = $this->get_synced_term_id( $sponsor_id );

		$post_id = self::factory()->post->create();
		wp_set_object_terms( $post_id, [ $term_id ], 'sponsor' );

		$post_sponsor = new \LTMCore\Taxonomies\PostSponsor();

		ob_start();
		$post_sponsor->render_meta_box( get_post( $post_id ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( '<option value="0">Not Sponsored</option>', $output );
		$this->assertMatchesRegularExpression( '/<option value="' . $term_id . '" selected[^>]*>Acme Corp<\/option>/', $output );
	}

	private function post_with_nonce( int $post_id, $sponsor_value ) {
		$_POST['ltm_sponsor_nonce'] = wp_create_nonce( 'ltm_sponsor_save' );
		$_POST['ltm_sponsor']       = $sponsor_value;

		( new \LTMCore\Taxonomies\PostSponsor() )->save_meta_box( $post_id, get_post( $post_id ) );

		unset( $_POST['ltm_sponsor_nonce'], $_POST['ltm_sponsor'] );
	}

	public function test_save_meta_box_assigns_the_selected_sponsor() {
		$sponsor_id = $this->create_sponsor();
		$term_id    = $this->get_synced_term_id( $sponsor_id );
		$post_id    = self::factory()->post->create();

		wp_set_current_user( self::factory()->user->create( [ 'role' => 'administrator' ] ) );
		$this->post_with_nonce( $post_id, $term_id );

		$terms = wp_get_object_terms( $post_id, 'sponsor', [ 'fields' => 'ids' ] );
		$this->assertSame( [ $term_id ], $terms );
	}

	/**
	 * Saving replaces the prior sponsor rather than appending to it —
	 * wp_set_object_terms() is called with $append = false.
	 */
	public function test_save_meta_box_replaces_rather_than_appends() {
		$first_sponsor  = $this->get_synced_term_id( $this->create_sponsor( 'First Sponsor' ) );
		$second_sponsor = $this->get_synced_term_id( $this->create_sponsor( 'Second Sponsor' ) );
		$post_id        = self::factory()->post->create();

		wp_set_current_user( self::factory()->user->create( [ 'role' => 'administrator' ] ) );
		$this->post_with_nonce( $post_id, $first_sponsor );
		$this->post_with_nonce( $post_id, $second_sponsor );

		$terms = wp_get_object_terms( $post_id, 'sponsor', [ 'fields' => 'ids' ] );
		$this->assertSame( [ $second_sponsor ], $terms, 'Only the most recently saved sponsor should remain assigned.' );
	}

	public function test_save_meta_box_with_zero_clears_the_sponsor() {
		$term_id = $this->get_synced_term_id( $this->create_sponsor() );
		$post_id = self::factory()->post->create();
		wp_set_object_terms( $post_id, [ $term_id ], 'sponsor' );

		wp_set_current_user( self::factory()->user->create( [ 'role' => 'administrator' ] ) );
		$this->post_with_nonce( $post_id, 0 );

		$this->assertSame( [], wp_get_object_terms( $post_id, 'sponsor', [ 'fields' => 'ids' ] ) );
	}

	public function test_save_meta_box_ignores_missing_or_invalid_nonce() {
		$term_id = $this->get_synced_term_id( $this->create_sponsor() );
		$post_id = self::factory()->post->create();

		wp_set_current_user( self::factory()->user->create( [ 'role' => 'administrator' ] ) );

		$_POST['ltm_sponsor'] = $term_id;
		( new \LTMCore\Taxonomies\PostSponsor() )->save_meta_box( $post_id, get_post( $post_id ) );
		unset( $_POST['ltm_sponsor'] );

		$this->assertSame( [], wp_get_object_terms( $post_id, 'sponsor', [ 'fields' => 'ids' ] ) );
	}

	public function test_save_meta_box_requires_edit_post_capability() {
		$term_id = $this->get_synced_term_id( $this->create_sponsor() );
		$post_id = self::factory()->post->create();

		// Subscribers cannot edit posts.
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'subscriber' ] ) );
		$this->post_with_nonce( $post_id, $term_id );

		$this->assertSame( [], wp_get_object_terms( $post_id, 'sponsor', [ 'fields' => 'ids' ] ) );
	}

	public function test_rest_field_returns_null_when_no_sponsor_is_linked() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'editor' ] ) );
		$post_id = self::factory()->post->create();

		$request = new WP_REST_Request( 'GET', "/wp/v2/posts/{$post_id}" );
		$request->set_param( 'context', 'edit' );
		$response = rest_get_server()->dispatch( $request );

		$this->assertNull( $response->get_data()['ltm_sponsor'] );
	}

	public function test_rest_field_returns_sponsor_shape_when_linked() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'editor' ] ) );

		$sponsor_id = $this->create_sponsor();
		$term_id    = $this->get_synced_term_id( $sponsor_id );
		$post_id    = self::factory()->post->create();
		wp_set_object_terms( $post_id, [ $term_id ], 'sponsor' );

		$request = new WP_REST_Request( 'GET', "/wp/v2/posts/{$post_id}" );
		$request->set_param( 'context', 'edit' );
		$response = rest_get_server()->dispatch( $request );

		$this->assertSame(
			[
				'id'   => $sponsor_id,
				'name' => 'Acme Corp',
				'logo' => null,
			],
			$response->get_data()['ltm_sponsor']
		);
	}

	/**
	 * The field is registered with context => ['edit'] only, so it must not
	 * appear in the public view context.
	 */
	public function test_rest_field_is_absent_in_view_context() {
		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );

		$request  = new WP_REST_Request( 'GET', "/wp/v2/posts/{$post_id}" );
		$response = rest_get_server()->dispatch( $request );

		$this->assertArrayNotHasKey( 'ltm_sponsor', $response->get_data() );
	}
}
