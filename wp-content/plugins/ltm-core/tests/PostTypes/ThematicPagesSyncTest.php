<?php
/**
 * Tests for the thematic-page -> taxonomy term sync behaviour.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\PostTypes\ThematicPages::sync_taxonomy_term
 * @covers \LTMCore\PostTypes\ThematicPages::delete_taxonomy_term
 */
class ThematicPagesSyncTest extends WP_UnitTestCase {

	private const TAXONOMY = 'thematic-page-types';

	/**
	 * Finds the term linked to a thematic page through `_thematic_page_id`.
	 */
	private function get_synced_term( int $post_id ) {
		$terms = get_terms(
			[
				'taxonomy'   => self::TAXONOMY,
				'hide_empty' => false,
				'meta_key'   => '_thematic_page_id',
				'meta_value' => $post_id,
			]
		);

		return ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0] : null;
	}

	private function create_thematic_page( array $args = [] ): int {
		return self::factory()->post->create(
			array_merge(
				[
					'post_type'   => 'thematic-pages',
					'post_title'  => 'Grid Resilience',
					'post_status' => 'publish',
				],
				$args
			)
		);
	}

	/**
	 * Publishing a thematic page creates a matching term tagged with the
	 * source post ID.
	 */
	public function test_publishing_creates_synced_term() {
		$post_id = $this->create_thematic_page();
		$term    = $this->get_synced_term( $post_id );

		$this->assertNotNull( $term, 'Expected a synced term for the new thematic page.' );
		$this->assertSame( 'Grid Resilience', $term->name );
		$this->assertSame(
			(string) $post_id,
			get_term_meta( $term->term_id, '_thematic_page_id', true )
		);
	}

	/**
	 * Renaming the page updates the existing term instead of creating a
	 * duplicate one.
	 */
	public function test_renaming_updates_existing_term_without_duplicating() {
		$post_id      = $this->create_thematic_page();
		$original_term = $this->get_synced_term( $post_id );

		wp_update_post(
			[
				'ID'         => $post_id,
				'post_title' => 'Grid Resilience 2026',
			]
		);

		$updated_term = $this->get_synced_term( $post_id );

		$this->assertNotNull( $updated_term );
		$this->assertSame( $original_term->term_id, $updated_term->term_id, 'The term should be reused, not duplicated.' );
		$this->assertSame( 'Grid Resilience 2026', $updated_term->name );
	}

	/**
	 * An auto-draft should not produce a term.
	 */
	public function test_auto_draft_does_not_create_term() {
		$post_id = $this->create_thematic_page( [ 'post_status' => 'auto-draft' ] );

		$this->assertNull( $this->get_synced_term( $post_id ) );
	}

	/**
	 * Permanently deleting the page removes its synced term.
	 */
	public function test_deleting_page_removes_synced_term() {
		$post_id = $this->create_thematic_page();
		$term    = $this->get_synced_term( $post_id );
		$this->assertNotNull( $term );

		wp_delete_post( $post_id, true );

		$this->assertNull( $this->get_synced_term( $post_id ) );
		$this->assertNull( get_term( $term->term_id, self::TAXONOMY ) );
	}

	/**
	 * Deleting an unrelated post type must not touch thematic terms.
	 */
	public function test_deleting_regular_post_leaves_terms_intact() {
		$page_id = $this->create_thematic_page();
		$term    = $this->get_synced_term( $page_id );

		$unrelated = self::factory()->post->create( [ 'post_type' => 'post' ] );
		wp_delete_post( $unrelated, true );

		$this->assertNotNull( $this->get_synced_term( $page_id ) );
		$this->assertNotNull( get_term( $term->term_id, self::TAXONOMY ) );
	}
}
