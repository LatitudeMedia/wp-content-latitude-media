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
				'event_type' => 'live',
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

	private function create_image() {
		return self::factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg' );
	}

	public function test_conference_with_google_event_details() {
		$wide   = $this->create_image();
		$square = $this->create_image();
		$event  = $this->create_event(
			[
				'start_date'              => '2026-10-14 08:00:00',
				'end_date'                => '2026-10-15 17:00:00',
				'timezone'                => 'CT',
				'event_type'              => 'conference',
				'location'                => 'Austin',
				'seo_venue_name'          => 'AT&T Hotel and Conference Center',
				'seo_street_address'      => '1900 University Ave',
				'seo_city'                => 'Austin',
				'seo_region'              => 'TX',
				'seo_postal_code'         => '78705',
				'seo_country'             => 'US',
				'seo_ticket_url'          => 'https://tickets.example.com/flex',
				'seo_ticket_price'        => '995',
				'seo_ticket_currency'     => 'usd',
				'seo_ticket_availability' => 'InStock',
				'seo_tickets_on_sale'     => '2026-05-01 09:00:00',
				'seo_event_status'        => 'EventScheduled',
				'seo_image_16x9'          => $wide,
				'seo_image_1x1'           => $square,
			]
		);

		$this->assertSame(
			[
				'@type'               => 'Event',
				'@id'                 => 'https://example.com/events/e/#event',
				'name'                => 'Grid summit',
				'description'         => 'An event.',
				'url'                 => 'https://example.com/events/e/',
				'startDate'           => '2026-10-14T08:00:00-05:00',
				'endDate'             => '2026-10-15T17:00:00-05:00',
				'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
				'location'            => [
					'@type'   => 'Place',
					'name'    => 'AT&T Hotel and Conference Center',
					'address' => [
						'@type'           => 'PostalAddress',
						'streetAddress'   => '1900 University Ave',
						'addressLocality' => 'Austin',
						'addressRegion'   => 'TX',
						'postalCode'      => '78705',
						'addressCountry'  => 'US',
					],
				],
				'image'               => [ wp_get_attachment_image_url( $wide, 'full' ), wp_get_attachment_image_url( $square, 'full' ) ],
				'organizer'           => [ '@id' => 'https://example.com/#organization' ],
				'mainEntityOfPage'    => [ '@id' => 'https://example.com/events/e/' ],
				'eventStatus'         => 'https://schema.org/EventScheduled',
				'offers'              => [
					'@type'         => 'Offer',
					'url'           => 'https://tickets.example.com/flex',
					'price'         => '995',
					'priceCurrency' => 'USD',
					'availability'  => 'https://schema.org/InStock',
					'validFrom'     => '2026-05-01T09:00:00-05:00',
				],
			],
			$this->schema->build_event( $event, $this->context() )
		);
	}

	public function test_conference_defaults_and_city_fallback() {
		$event = $this->create_event(
			[
				'start_date'       => '2026-04-13 09:00:00',
				'event_type'       => 'conference',
				'location'         => 'San Francisco',
				'seo_country'      => 'US',
				'seo_ticket_price' => '0',
			]
		);
		$context            = $this->context();
		$context->has_image = true;

		$node = $this->schema->build_event( $event, $context );

		$this->assertSame(
			[
				'@type'   => 'Place',
				'address' => [ '@type' => 'PostalAddress', 'addressLocality' => 'San Francisco', 'addressCountry' => 'US' ],
			],
			$node['location']
		);
		$this->assertSame( 'https://schema.org/EventScheduled', $node['eventStatus'] );
		$this->assertSame(
			[ '@type' => 'Offer', 'url' => 'https://example.com/events/e/', 'price' => '0', 'priceCurrency' => 'USD' ],
			$node['offers'],
			'A free ticket is still an offer, pointing at the event page.'
		);
		$this->assertSame( [ '@id' => 'https://example.com/events/e/#primaryimage' ], $node['image'], 'Falls back to the featured image.' );
		$this->assertArrayNotHasKey( 'previousStartDate', $node );
	}

	public function test_conference_without_price_or_address_has_no_offers_or_location() {
		$event = $this->create_event( [ 'start_date' => '2026-04-13 09:00:00', 'event_type' => 'conference', 'seo_country' => 'US' ] );

		$node = $this->schema->build_event( $event, $this->context() );

		$this->assertArrayNotHasKey( 'offers', $node );
		$this->assertArrayNotHasKey( 'location', $node, 'A country on its own is not an address.' );
	}

	public function test_only_rescheduled_conferences_get_a_previous_start_date() {
		$meta = [
			'start_date'              => '2026-06-10 09:00:00',
			'timezone'                => 'ET',
			'event_type'              => 'conference',
			'seo_previous_start_date' => '2026-05-10 09:00:00',
		];

		$node = $this->schema->build_event( $this->create_event( $meta + [ 'seo_event_status' => 'EventRescheduled' ] ), $this->context() );
		$this->assertSame( 'https://schema.org/EventRescheduled', $node['eventStatus'] );
		$this->assertSame( '2026-05-10T09:00:00-04:00', $node['previousStartDate'] );

		$node = $this->schema->build_event( $this->create_event( $meta + [ 'seo_event_status' => 'EventPostponed' ] ), $this->context() );
		$this->assertSame( 'https://schema.org/EventPostponed', $node['eventStatus'] );
		$this->assertArrayNotHasKey( 'previousStartDate', $node );
	}

	public function test_google_event_details_are_ignored_on_other_event_types() {
		$meta = [
			'start_date'         => '2026-03-04 09:00:00',
			'location'           => 'Austin, TX',
			'seo_street_address' => '1900 University Ave',
			'seo_ticket_price'   => '50',
			'seo_event_status'   => 'EventCancelled',
		];

		foreach ( [ 'live', 'webinar' ] as $type ) {
			$node = $this->schema->build_event( $this->create_event( $meta + [ 'event_type' => $type ] ), $this->context() );

			$this->assertArrayNotHasKey( 'offers', $node, $type );
			$this->assertArrayNotHasKey( 'eventStatus', $node, $type );
			$this->assertNotSame( 'PostalAddress', $node['location']['address']['@type'] ?? null, $type );
		}
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
