<?php
/**
 * Tests for the Events post type's Cloudflare purge hook.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

use LTMCore\PostTypes\Events;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\PostTypes\Events
 */
class EventsTest extends WP_UnitTestCase {

	public function test_saving_an_event_purges_it_and_the_events_page() {
		$event = self::factory()->post->create( [ 'post_type' => 'events' ] );

		$urls = apply_filters( 'cloudflare_purge_by_url', [ 'https://example.org/already-queued/' ], $event );

		$this->assertContains( 'https://example.org/already-queued/', $urls );
		$this->assertContains( get_permalink( $event ), $urls );
		$this->assertContains( home_url( '/events/' ), $urls );
	}

	public function test_saving_a_non_event_leaves_purge_list_alone() {
		$post = self::factory()->post->create();

		$this->assertSame( [ 'x' ], apply_filters( 'cloudflare_purge_by_url', [ 'x' ], $post ) );
	}

	/**
	 * Creates an event with the given raw meta (end_date as ACF stores it:
	 * Y-m-d H:i:s, site timezone).
	 */
	private function create_event( $meta = [] ) {
		return self::factory()->post->create( [ 'post_type' => 'events', 'meta_input' => $meta ] );
	}

	private function end_date( $modifier ) {
		return current_datetime()->modify( $modifier )->format( 'Y-m-d H:i:s' );
	}

	public function test_event_whose_end_date_has_passed_is_past() {
		$event = $this->create_event( [ 'end_date' => $this->end_date( '-1 hour' ) ] );

		$this->assertTrue( Events::is_past_event( $event ) );
	}

	public function test_event_whose_end_date_is_later_today_is_not_past() {
		$event = $this->create_event( [ 'end_date' => $this->end_date( '+1 hour' ) ] );

		$this->assertFalse( Events::is_past_event( $event ) );
	}

	public function test_past_event_flag_wins_over_a_future_end_date() {
		$event = $this->create_event( [
			'end_date'   => $this->end_date( '+1 week' ),
			'past_event' => '1',
		] );

		$this->assertTrue( Events::is_past_event( $event ) );
	}

	public function test_unchecked_past_event_flag_does_not_make_event_past() {
		$event = $this->create_event( [
			'end_date'   => $this->end_date( '+1 week' ),
			'past_event' => '0',
		] );

		$this->assertFalse( Events::is_past_event( $event ) );
	}

	public function test_event_with_no_end_date_and_no_flag_is_not_past() {
		$this->assertFalse( Events::is_past_event( $this->create_event() ) );
	}

	public function test_end_date_is_read_in_the_site_timezone() {
		// 30 minutes ago in site time (UTC+9). Read as UTC it would be 8h30m
		// in the future, so this only passes if the site timezone is applied.
		update_option( 'timezone_string', 'Asia/Tokyo' );
		$event = $this->create_event( [ 'end_date' => $this->end_date( '-30 minutes' ) ] );

		$this->assertTrue( Events::is_past_event( $event ) );
	}

	/**
	 * Runs Yoast's robots filter for a post, using a stand-in for its
	 * Indexable_Presentation (Yoast isn't active in the test environment).
	 */
	private function robots_for( $post_id ) {
		$presentation = (object) [ 'model' => (object) [ 'object_type' => 'post', 'object_id' => $post_id ] ];

		return apply_filters( 'wpseo_robots_array', [ 'index' => 'index', 'follow' => 'follow' ], $presentation );
	}

	public function test_gated_event_is_noindexed() {
		$event = $this->create_event( [ 'gated' => '1' ] );

		$this->assertSame( [ 'index' => 'noindex', 'follow' => 'follow' ], $this->robots_for( $event ) );
	}

	public function test_recording_event_is_noindexed() {
		$event = self::factory()->post->create( [ 'post_type' => 'events', 'post_title' => 'Powering AI, faster | Recording' ] );

		$this->assertSame( 'noindex', $this->robots_for( $event )['index'] );
	}

	public function test_ungated_non_recording_event_stays_indexable() {
		$event = $this->create_event( [ 'gated' => '0' ] );

		$this->assertSame( 'index', $this->robots_for( $event )['index'] );
	}

	public function test_non_event_with_recording_title_stays_indexable() {
		$post = self::factory()->post->create( [ 'post_title' => 'Something | Recording' ] );

		$this->assertSame( 'index', $this->robots_for( $post )['index'] );
	}

	public function test_hidden_events_are_excluded_from_sitemap() {
		$gated     = $this->create_event( [ 'gated' => '1' ] );
		$recording = self::factory()->post->create( [ 'post_type' => 'events', 'post_title' => 'Open Circuit Live | Recording' ] );
		$visible   = $this->create_event( [ 'gated' => '0' ] );
		$post      = self::factory()->post->create( [ 'post_title' => 'Something | Recording' ] );

		$excluded = apply_filters( 'wpseo_exclude_from_sitemap_by_post_ids', [ 99 ] );

		$this->assertContains( 99, $excluded );
		$this->assertContains( $gated, $excluded );
		$this->assertContains( $recording, $excluded );
		$this->assertNotContains( $visible, $excluded );
		$this->assertNotContains( $post, $excluded );
	}

	/**
	 * "General event options" came over from the theme's acf-export.php with
	 * its group key and all field keys intact.
	 */
	public function test_general_event_options_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_6744b701b9197' );

		$this->assertIsArray( $group, 'Field group group_6744b701b9197 is not registered.' );
		$this->assertSame( 'General event options', $group['title'] );
		$this->assertSame( 'side', $group['position'] );
		$this->assertSame(
			[
				'param'    => 'post_type',
				'operator' => '==',
				'value'    => 'events',
			],
			$group['location'][0][0]
		);

		$this->assertSame(
			[
				[ 'field_6713ad31e1567', 'start_date', 'date_time_picker' ],
				[ 'field_6713ad5ee1568', 'end_date', 'date_time_picker' ],
				[ 'field_6713b050e1583', 'timezone', 'text' ],
				[ 'field_6713ade8e156a', 'event_type', 'select' ],
				[ 'field_6713ae1fe156b', 'location', 'text' ],
				[ 'field_6713ae62e156d', 'link', 'text' ],
				[ 'field_6713ae6fe156e', 'cover_image', 'image' ],
				[ 'field_6713ae7ae156f', 'background_image', 'image' ],
				[ 'field_6713ae8de1570', 'form_text', 'text' ],
				[ 'field_6713aea8e1571', 'form_code__registration_cta', 'textarea' ],
				[ 'field_6713ad28e1566', 'past_event', 'true_false' ],
				[ 'field_6713b049e1582', 'gated', 'true_false' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				acf_get_fields( $group )
			)
		);
	}

	public function test_type_does_not_affect_the_result() {
		$event = $this->create_event( [
			'end_date'   => $this->end_date( '+1 week' ),
			'event_type' => 'webinar',
		] );

		$this->assertFalse( Events::is_past_event( $event ) );
	}
}
