<?php
/**
 * Tests for the sponsor -> taxonomy term sync behaviour, and the one-time
 * backfill that syncs sponsors created before this relationship existed.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\PostTypes\Sponsors::sync_taxonomy_term
 * @covers \LTMCore\PostTypes\Sponsors::delete_taxonomy_term
 * @covers \LTMCore\PostTypes\Sponsors::backfill_sponsor_terms
 */
class SponsorsSyncTest extends WP_UnitTestCase {

	private const TAXONOMY = 'sponsor';

	/**
	 * Finds the term linked to a sponsor post through `_sponsor_post_id`.
	 */
	private function get_synced_term( int $post_id ) {
		$terms = get_terms(
			[
				'taxonomy'   => self::TAXONOMY,
				'hide_empty' => false,
				'meta_key'   => '_sponsor_post_id',
				'meta_value' => $post_id,
			]
		);

		return ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0] : null;
	}

	private function create_sponsor( array $args = [] ): int {
		return self::factory()->post->create(
			array_merge(
				[
					'post_type'   => 'sponsors',
					'post_title'  => 'Acme Corp',
					'post_status' => 'publish',
				],
				$args
			)
		);
	}

	/**
	 * Publishing a sponsor creates a matching term tagged with the source
	 * post ID.
	 */
	public function test_publishing_creates_synced_term() {
		$post_id = $this->create_sponsor();
		$term    = $this->get_synced_term( $post_id );

		$this->assertNotNull( $term, 'Expected a synced term for the new sponsor.' );
		$this->assertSame( 'Acme Corp', $term->name );
		$this->assertSame( (string) $post_id, get_term_meta( $term->term_id, '_sponsor_post_id', true ) );
	}

	/**
	 * Renaming the sponsor updates the existing term instead of creating a
	 * duplicate one.
	 */
	public function test_renaming_updates_existing_term_without_duplicating() {
		$post_id        = $this->create_sponsor();
		$original_term  = $this->get_synced_term( $post_id );

		wp_update_post(
			[
				'ID'         => $post_id,
				'post_title' => 'Acme Corporation',
			]
		);

		$updated_term = $this->get_synced_term( $post_id );

		$this->assertNotNull( $updated_term );
		$this->assertSame( $original_term->term_id, $updated_term->term_id, 'The term should be reused, not duplicated.' );
		$this->assertSame( 'Acme Corporation', $updated_term->name );
	}

	/**
	 * An auto-draft should not produce a term.
	 */
	public function test_auto_draft_does_not_create_term() {
		$post_id = $this->create_sponsor( [ 'post_status' => 'auto-draft' ] );

		$this->assertNull( $this->get_synced_term( $post_id ) );
	}

	/**
	 * Permanently deleting the sponsor removes its synced term.
	 */
	public function test_deleting_sponsor_removes_synced_term() {
		$post_id = $this->create_sponsor();
		$term    = $this->get_synced_term( $post_id );
		$this->assertNotNull( $term );

		wp_delete_post( $post_id, true );

		$this->assertNull( $this->get_synced_term( $post_id ) );
		$this->assertNull( get_term( $term->term_id, self::TAXONOMY ) );
	}

	/**
	 * Deleting an unrelated post type must not touch sponsor terms.
	 */
	public function test_deleting_regular_post_leaves_terms_intact() {
		$sponsor_id = $this->create_sponsor();
		$term       = $this->get_synced_term( $sponsor_id );

		$unrelated = self::factory()->post->create( [ 'post_type' => 'post' ] );
		wp_delete_post( $unrelated, true );

		$this->assertNotNull( $this->get_synced_term( $sponsor_id ) );
		$this->assertNotNull( get_term( $term->term_id, self::TAXONOMY ) );
	}

	/**
	 * backfill_sponsor_terms() syncs every existing sponsor, across mixed
	 * statuses, in one pass, and records that it has run.
	 */
	public function test_backfill_syncs_every_existing_sponsor_once() {
		delete_option( 'ltm_sponsor_terms_synced' );

		// Bypass the CPT's own save_post sync hook (restored automatically
		// after this test by WP_UnitTestCase) so these sponsors start out
		// genuinely un-synced, as if created before the relationship existed.
		remove_all_actions( 'save_post_sponsors' );

		$statuses    = [ 'publish', 'draft', 'pending', 'private', 'future' ];
		$sponsor_ids = [];
		foreach ( $statuses as $i => $status ) {
			$args = [ 'post_status' => $status ];
			if ( 'future' === $status ) {
				$args['post_date'] = gmdate( 'Y-m-d H:i:s', time() + DAY_IN_SECONDS );
			}
			$sponsor_ids[] = $this->create_sponsor( array_merge( $args, [ 'post_title' => "Sponsor {$i}" ] ) );
		}

		foreach ( $sponsor_ids as $sponsor_id ) {
			$this->assertNull( $this->get_synced_term( $sponsor_id ), 'Sponsor should start out un-synced for this test.' );
		}

		( new \LTMCore\PostTypes\Sponsors() )->backfill_sponsor_terms();

		foreach ( $sponsor_ids as $sponsor_id ) {
			$this->assertNotNull( $this->get_synced_term( $sponsor_id ) );
		}
		$this->assertSame( 1, (int) get_option( 'ltm_sponsor_terms_synced' ) );
	}

	/**
	 * A second call is a true no-op: a sponsor added after the backfill ran
	 * gets no term from running it again.
	 */
	public function test_backfill_is_a_one_time_no_op() {
		update_option( 'ltm_sponsor_terms_synced', 1 );

		// Bypass the CPT's own save_post sync hook (restored automatically
		// after this test by WP_UnitTestCase) so this sponsor starts out
		// genuinely un-synced — otherwise the live hook syncs it on creation
		// regardless of what the backfill does, and the test would prove
		// nothing about the backfill itself.
		remove_all_actions( 'save_post_sponsors' );
		$post_id = $this->create_sponsor( [ 'post_title' => 'Late Sponsor' ] );

		( new \LTMCore\PostTypes\Sponsors() )->backfill_sponsor_terms();

		$this->assertNull( $this->get_synced_term( $post_id ), 'The backfill should not run again once the option is already set.' );
	}
}
