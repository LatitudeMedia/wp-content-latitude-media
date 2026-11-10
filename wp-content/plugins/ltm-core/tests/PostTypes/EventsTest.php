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

	public function test_type_does_not_affect_the_result() {
		$event = $this->create_event( [
			'end_date'   => $this->end_date( '+1 week' ),
			'event_type' => 'webinar',
		] );

		$this->assertFalse( Events::is_past_event( $event ) );
	}
}
