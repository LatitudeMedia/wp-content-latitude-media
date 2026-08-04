<?php
/**
 * Tests for the Sponsors post type.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\PostTypes\Sponsors
 */
class SponsorsTest extends WP_UnitTestCase {

	/**
	 * The post type is registered by the plugin's init hook.
	 */
	public function test_post_type_is_registered() {
		$this->assertTrue( post_type_exists( 'sponsors' ) );
	}

	/**
	 * The post type is exposed to the block editor / REST API but kept out of
	 * front-end search results, same as thematic-pages.
	 */
	public function test_post_type_visibility_flags() {
		$post_type = get_post_type_object( 'sponsors' );

		$this->assertTrue( $post_type->public );
		$this->assertTrue( $post_type->show_in_rest );
		$this->assertTrue( $post_type->exclude_from_search );
		$this->assertFalse( $post_type->has_archive );
	}

	/**
	 * "No content linked yet." is shown when nothing is tagged with this
	 * sponsor's synced term.
	 */
	public function test_sponsored_content_meta_box_shows_empty_state_when_nothing_linked() {
		$sponsor_id = self::factory()->post->create( [ 'post_type' => 'sponsors', 'post_title' => 'Acme Corp' ] );

		$output = $this->render_meta_box( $sponsor_id );

		$this->assertStringContainsString( 'No content linked yet.', $output );
	}

	/**
	 * Posts and thematic pages tagged with the sponsor's term are listed by
	 * title, and neither post type is excluded from the list.
	 */
	public function test_sponsored_content_meta_box_lists_linked_content_by_title() {
		$sponsor_id = self::factory()->post->create( [ 'post_type' => 'sponsors', 'post_title' => 'Acme Corp' ] );
		$term_id    = $this->get_synced_term_id( $sponsor_id );

		$post_id = self::factory()->post->create( [ 'post_type' => 'post', 'post_title' => 'A Sponsored Post' ] );
		wp_set_object_terms( $post_id, [ $term_id ], 'sponsor' );

		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages', 'post_title' => 'A Sponsored Page' ] );
		wp_set_object_terms( $page_id, [ $term_id ], 'sponsor' );

		$output = $this->render_meta_box( $sponsor_id );

		$this->assertStringContainsString( 'A Sponsored Post', $output );
		$this->assertStringContainsString( 'A Sponsored Page', $output );
	}

	/**
	 * A real sponsor assignment wins over the legacy ACF toggle.
	 */
	public function test_sponsored_column_shows_sponsor_title_when_linked() {
		$sponsor_id = self::factory()->post->create( [ 'post_type' => 'sponsors', 'post_title' => 'Acme Corp' ] );
		$term_id    = $this->get_synced_term_id( $sponsor_id );

		$post_id = self::factory()->post->create( [ 'post_type' => 'post' ] );
		wp_set_object_terms( $post_id, [ $term_id ], 'sponsor' );

		$this->assertSame( 'Acme Corp', $this->render_column( $post_id ) );
	}

	/**
	 * With no real sponsor linked, the legacy ACF `sponsored` toggle falls
	 * back to a "Yes (legacy)" label. Guarded with function_exists() in the
	 * source, so this is safe to run whether or not ACF is active.
	 */
	public function test_sponsored_column_shows_legacy_label_when_only_acf_toggle_is_set() {
		if ( ! function_exists( 'update_field' ) ) {
			$this->markTestSkipped( 'ACF is not active in this environment.' );
		}

		$post_id = self::factory()->post->create( [ 'post_type' => 'post' ] );
		update_field( 'sponsored', true, $post_id );

		$this->assertSame( 'Yes (legacy)', $this->render_column( $post_id ) );
	}

	/**
	 * Neither a real sponsor nor the legacy toggle: "Not sponsored".
	 */
	public function test_sponsored_column_shows_not_sponsored_by_default() {
		$post_id = self::factory()->post->create( [ 'post_type' => 'post' ] );

		$this->assertSame( 'Not sponsored', $this->render_column( $post_id ) );
	}

	/**
	 * The column definition itself is added under the expected key/label.
	 */
	public function test_sponsored_column_is_added_to_posts_list_table() {
		$sponsors = new \LTMCore\PostTypes\Sponsors();

		$columns = $sponsors->add_sponsored_column( [ 'title' => 'Title' ] );

		$this->assertArrayHasKey( 'sponsored', $columns );
	}

	/**
	 * Finds the term linked to a sponsor post through `_sponsor_post_id`,
	 * mirroring Sponsors::get_synced_term_id() (private, so this is a local
	 * re-implementation for test setup rather than a call into the class).
	 */
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

	private function render_meta_box( int $sponsor_id ): string {
		$sponsors = new \LTMCore\PostTypes\Sponsors();

		ob_start();
		$sponsors->render_sponsored_content_meta_box( get_post( $sponsor_id ) );
		return ob_get_clean();
	}

	private function render_column( int $post_id ): string {
		$sponsors = new \LTMCore\PostTypes\Sponsors();

		ob_start();
		$sponsors->render_sponsored_column( 'sponsored', $post_id );
		return trim( wp_strip_all_tags( ob_get_clean() ) );
	}
}
