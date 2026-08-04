<?php
/**
 * Tests for the Category Post Listing block's query-building logic.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use LTMCore\Blocks\CategoryPostListing;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\CategoryPostListing
 */
class CategoryPostListingQueryTest extends WP_UnitTestCase {

	public function test_no_categories_selected_returns_all_published_posts() {
		$published = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		self::factory()->post->create( [ 'post_status' => 'draft' ] );

		$query = CategoryPostListing::get_query(
			[
				'categories'             => [],
				'onlyThematicPageTagged' => false,
				'layout'                 => 'paginated-list',
			]
		);

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertContains( $published, $ids );
		$this->assertCount( 1, $ids );
	}

	public function test_filters_by_selected_categories() {
		$category_a = self::factory()->category->create();
		$category_b = self::factory()->category->create();

		$post_in_a           = self::factory()->post->create( [ 'post_category' => [ $category_a ] ] );
		$post_in_b           = self::factory()->post->create( [ 'post_category' => [ $category_b ] ] );
		$post_uncategorized  = self::factory()->post->create();

		$query = CategoryPostListing::get_query(
			[
				'categories'             => [ $category_a, $category_b ],
				'onlyThematicPageTagged' => false,
				'layout'                 => 'paginated-list',
			]
		);

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertContains( $post_in_a, $ids );
		$this->assertContains( $post_in_b, $ids );
		$this->assertNotContains( $post_uncategorized, $ids );
	}

	public function test_orders_by_publish_date_descending() {
		$older = self::factory()->post->create( [ 'post_date' => '2020-01-01 00:00:00' ] );
		$newer = self::factory()->post->create( [ 'post_date' => '2024-01-01 00:00:00' ] );

		$query = CategoryPostListing::get_query(
			[
				'categories'             => [],
				'onlyThematicPageTagged' => false,
				'layout'                 => 'paginated-list',
			]
		);

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertSame( [ $newer, $older ], $ids );
	}

	public function test_five_post_feature_caps_at_five_posts() {
		self::factory()->post->create_many( 7 );

		$query = CategoryPostListing::get_query(
			[
				'categories'             => [],
				'onlyThematicPageTagged' => false,
				'layout'                 => 'five-post-feature',
			]
		);

		$this->assertCount( 5, $query->posts );
	}

	public function test_paginated_list_honors_page_param() {
		self::factory()->post->create_many( 10 );

		$_GET['cpl-page'] = '2';

		$query = CategoryPostListing::get_query(
			[
				'categories'             => [],
				'onlyThematicPageTagged' => false,
				'layout'                 => 'paginated-list',
			]
		);

		unset( $_GET['cpl-page'] );

		$this->assertCount( 2, $query->posts );
		$this->assertSame( 2, $query->get( 'paged' ) );
	}

	public function test_thematic_toggle_true_on_thematic_page_filters_to_tagged_posts() {
		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages' ] );
		$term_id = $this->get_synced_term_id( $page_id );

		$tagged_post   = self::factory()->post->create();
		$untagged_post = self::factory()->post->create();
		wp_set_object_terms( $tagged_post, [ $term_id ], 'thematic-page-types' );

		global $post;
		$post = get_post( $page_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		setup_postdata( $post );

		$query = CategoryPostListing::get_query(
			[
				'categories'             => [],
				'onlyThematicPageTagged' => true,
				'layout'                 => 'five-post-feature',
			]
		);

		wp_reset_postdata();

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertSame( [ $tagged_post ], $ids );
	}

	public function test_thematic_toggle_false_ignores_tag() {
		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages' ] );
		$term_id = $this->get_synced_term_id( $page_id );

		$tagged_post   = self::factory()->post->create();
		$untagged_post = self::factory()->post->create();
		wp_set_object_terms( $tagged_post, [ $term_id ], 'thematic-page-types' );

		global $post;
		$post = get_post( $page_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		setup_postdata( $post );

		$query = CategoryPostListing::get_query(
			[
				'categories'             => [],
				'onlyThematicPageTagged' => false,
				'layout'                 => 'five-post-feature',
			]
		);

		wp_reset_postdata();

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertContains( $tagged_post, $ids );
		$this->assertContains( $untagged_post, $ids );
	}

	public function test_thematic_toggle_ignored_when_current_post_is_not_a_thematic_page() {
		$other_post = self::factory()->post->create();

		global $post;
		$post = get_post( $other_post ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		setup_postdata( $post );

		$another_post = self::factory()->post->create();

		$query = CategoryPostListing::get_query(
			[
				'categories'             => [],
				'onlyThematicPageTagged' => true,
				'layout'                 => 'five-post-feature',
			]
		);

		wp_reset_postdata();

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertContains( $other_post, $ids );
		$this->assertContains( $another_post, $ids );
	}

	public function test_category_and_thematic_filters_combine_with_and() {
		$category = self::factory()->category->create();

		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages' ] );
		$term_id = $this->get_synced_term_id( $page_id );

		$matches_both  = self::factory()->post->create( [ 'post_category' => [ $category ] ] );
		$category_only = self::factory()->post->create( [ 'post_category' => [ $category ] ] );
		$tag_only      = self::factory()->post->create();

		wp_set_object_terms( $matches_both, [ $term_id ], 'thematic-page-types' );
		wp_set_object_terms( $tag_only, [ $term_id ], 'thematic-page-types' );

		global $post;
		$post = get_post( $page_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		setup_postdata( $post );

		$query = CategoryPostListing::get_query(
			[
				'categories'             => [ $category ],
				'onlyThematicPageTagged' => true,
				'layout'                 => 'five-post-feature',
			]
		);

		wp_reset_postdata();

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertSame( [ $matches_both ], $ids );
	}

	private function get_synced_term_id( int $thematic_page_id ): int {
		$terms = get_terms(
			[
				'taxonomy'   => 'thematic-page-types',
				'hide_empty' => false,
				'meta_key'   => '_thematic_page_id',
				'meta_value' => $thematic_page_id,
				'fields'     => 'ids',
			]
		);

		return ! empty( $terms ) && ! is_wp_error( $terms ) ? (int) $terms[0] : 0;
	}
}
