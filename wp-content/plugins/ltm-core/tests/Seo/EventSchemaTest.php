<?php
/**
 * Tests for the schema.org Event node on event pages.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Seo;

use LTMCore\Seo\EventSchema;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Seo\EventSchema
 */
class EventSchemaTest extends WP_UnitTestCase {

	/**
	 * @var EventSchema
	 */
	private $schema;

	public function set_up() {
		parent::set_up();
		$this->schema = new EventSchema();
	}

	/**
	 * Stand-in for Yoast's Meta_Tags_Context.
	 */
	private function context( $url = 'https://example.com/events/e/' ) {
		return (object) [
			'canonical'                 => $url,
			'main_schema_id'            => $url,
			'description'               => 'An event.',
			'has_image'                 => false,
			'site_represents_reference' => [ '@id' => 'https://example.com/#organization' ],
		];
	}

	private function create_event( $meta ) {
		$event = self::factory()->post->create( [ 'post_type' => 'events', 'post_title' => 'Grid summit' ] );
		foreach ( $meta as $key => $value ) {
			update_post_meta( $event, $key, $value );
		}

		return $event;
	}

	public function test_in_person_event() {
		$event = $this->create_event(
			[
				'start_date' => '2026-03-04 09:00:00',
				'end_date'   => '2026-03-04 17:00:00',
				'timezone'   => 'CT',
				'event_type' => 'conference',
				'location'   => 'Austin, TX',
			]
		);

		$this->assertSame(
			[
				'@type'               => 'Event',
				'@id'                 => 'https://example.com/events/e/#event',
				'name'                => 'Grid summit',
				'description'         => 'An event.',
				'url'                 => 'https://example.com/events/e/',
				'startDate'           => '2026-03-04T09:00:00-06:00',
				'endDate'             => '2026-03-04T17:00:00-06:00',
				'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
				'location'            => [ '@type' => 'Place', 'name' => 'Austin, TX', 'address' => 'Austin, TX' ],
				'organizer'           => [ '@id' => 'https://example.com/#organization' ],
				'mainEntityOfPage'    => [ '@id' => 'https://example.com/events/e/' ],
			],
			$this->schema->build_event( $event, $this->context() )
		);
	}

	public function test_online_event_types_get_a_virtual_location() {
		foreach ( [ 'virtual', 'webinar', 'dispatch', 'frontier-forum' ] as $type ) {
			$event = $this->create_event( [ 'start_date' => '2026-03-04 09:00:00', 'event_type' => $type, 'location' => 'Ignored' ] );
			$node  = $this->schema->build_event( $event, $this->context() );

			$this->assertSame( 'https://schema.org/OnlineEventAttendanceMode', $node['eventAttendanceMode'], $type );
			$this->assertSame( [ '@type' => 'VirtualLocation', 'url' => 'https://example.com/events/e/' ], $node['location'], $type );
		}
	}

	public function test_blank_or_unknown_timezone_uses_the_site_timezone_and_empty_props_are_omitted() {
		update_option( 'timezone_string', 'America/New_York' );
		$event = $this->create_event( [ 'start_date' => '2026-07-01 12:00:00', 'timezone' => 'Local', 'event_type' => 'live' ] );

		$node = $this->schema->build_event( $event, $this->context() );

		$this->assertSame( '2026-07-01T12:00:00-04:00', $node['startDate'] );
		$this->assertArrayNotHasKey( 'endDate', $node );
		$this->assertArrayNotHasKey( 'location', $node, 'No Place without a location.' );
	}

	public function test_undated_event_has_no_node() {
		$event = $this->create_event( [ 'event_type' => 'live' ] );

		$this->assertSame( [], $this->schema->build_event( $event, $this->context() ) );
		$this->assertFalse( EventSchema::has_event( $event ) );
	}

	public function test_graph_filter_adds_the_node_and_points_the_webpage_at_it() {
		$event = $this->create_event( [ 'start_date' => '2026-03-04 09:00:00', 'event_type' => 'live' ] );
		$this->go_to( get_permalink( $event ) );

		$graph = $this->schema->add_graph_node( [ [ '@type' => 'WebPage', '@id' => 'https://example.com/events/e/' ] ], $this->context() );

		$this->assertSame( [ '@id' => 'https://example.com/events/e/#event' ], $graph[0]['mainEntity'] );
		$this->assertSame( 'Event', $graph[1]['@type'] );
	}

	public function test_graph_untouched_for_undated_events_and_other_pages() {
		$graph = [ [ '@type' => 'WebPage' ] ];

		$this->go_to( get_permalink( $this->create_event( [] ) ) );
		$this->assertSame( $graph, $this->schema->add_graph_node( $graph, $this->context() ) );

		$this->go_to( get_permalink( self::factory()->post->create() ) );
		$this->assertSame( $graph, $this->schema->add_graph_node( $graph, $this->context() ) );
	}
}
