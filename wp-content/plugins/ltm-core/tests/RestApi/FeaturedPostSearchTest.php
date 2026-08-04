<?php
/**
 * Tests for the featured-post/search REST endpoint.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\RestApi;

use WP_REST_Request;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\RestApi\FeaturedPostSearch
 */
class FeaturedPostSearchTest extends WP_UnitTestCase {

	private const ROUTE = '/ltm-core/v1/featured-post/search';

	public function test_permission_callback_allows_edit_posts_capable_user() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'editor' ] ) );

		$response = rest_get_server()->dispatch( new WP_REST_Request( 'GET', self::ROUTE ) );

		$this->assertSame( 200, $response->get_status() );
	}

	public function test_permission_callback_rejects_users_who_cannot_edit_posts() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'subscriber' ] ) );

		$response = rest_get_server()->dispatch( new WP_REST_Request( 'GET', self::ROUTE ) );

		$this->assertNotSame( 200, $response->get_status() );
	}

	public function test_permission_callback_rejects_unauthenticated_requests() {
		wp_set_current_user( 0 );

		$response = rest_get_server()->dispatch( new WP_REST_Request( 'GET', self::ROUTE ) );

		$this->assertNotSame( 200, $response->get_status() );
	}

	public function test_default_query_only_returns_published_posts() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'editor' ] ) );

		$published = self::factory()->post->create( [ 'post_status' => 'publish', 'post_title' => 'Published Post' ] );
		self::factory()->post->create( [ 'post_status' => 'draft', 'post_title' => 'Draft Post' ] );

		$data = $this->search();

		$ids = wp_list_pluck( $data['items'], 'id' );
		$this->assertContains( $published, $ids );
		$this->assertCount( 1, $data['items'] );
	}

	public function test_default_query_caps_at_twenty_results() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'editor' ] ) );

		self::factory()->post->create_many( 25, [ 'post_status' => 'publish' ] );

		$data = $this->search();

		$this->assertCount( 20, $data['items'] );
	}

	public function test_sponsored_with_no_sponsor_on_page_short_circuits_to_empty_results() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'editor' ] ) );

		self::factory()->post->create( [ 'post_status' => 'publish' ] );
		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages', 'post_status' => 'publish' ] );

		$data = $this->search( [ 'sponsored' => 1, 'page_id' => $page_id ] );

		$this->assertSame( [ 'hasSponsor' => false, 'items' => [] ], $data );
	}

	public function test_sponsored_scopes_results_to_the_pages_sponsor() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'editor' ] ) );

		$sponsor_id = self::factory()->post->create( [ 'post_type' => 'sponsors', 'post_title' => 'Acme Corp' ] );
		$term_id    = $this->get_synced_term_id( $sponsor_id );

		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages', 'post_status' => 'publish' ] );
		wp_set_object_terms( $page_id, [ $term_id ], 'sponsor' );

		$sponsored_post = self::factory()->post->create( [ 'post_status' => 'publish', 'post_title' => 'Sponsored Post' ] );
		wp_set_object_terms( $sponsored_post, [ $term_id ], 'sponsor' );

		self::factory()->post->create( [ 'post_status' => 'publish', 'post_title' => 'Unsponsored Post' ] );

		$data = $this->search( [ 'sponsored' => 1, 'page_id' => $page_id ] );

		$this->assertTrue( $data['hasSponsor'] );
		$this->assertSame( [ $sponsored_post ], wp_list_pluck( $data['items'], 'id' ) );
	}

	public function test_item_shape_falls_back_to_no_title_placeholder() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'editor' ] ) );

		$post_id = self::factory()->post->create( [ 'post_status' => 'publish', 'post_title' => '' ] );

		$data = $this->search();

		$item = current( array_filter( $data['items'], fn( $item ) => $item['id'] === $post_id ) );
		$this->assertSame( '(no title)', $item['title'] );
	}

	private function search( array $params = [] ): array {
		$request = new WP_REST_Request( 'GET', self::ROUTE );
		foreach ( $params as $key => $value ) {
			$request->set_param( $key, $value );
		}

		return rest_get_server()->dispatch( $request )->get_data();
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
}
