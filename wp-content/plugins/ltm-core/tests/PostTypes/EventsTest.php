<?php
/**
 * Tests for the Events post type's Cloudflare purge hook.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

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
}
