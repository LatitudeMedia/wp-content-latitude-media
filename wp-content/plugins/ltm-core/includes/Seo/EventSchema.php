<?php
namespace LTMCore\Seo;

/**
 * schema.org Event node for event pages, added to Yoast SEO's JSON-LD graph.
 * Yoast has no Event type of its own (its "Article type" setting only offers
 * Article subtypes), so this adds one, `@id` `{canonical}#event`, and points
 * the page's WebPage at it as `mainEntity`.
 *
 * Only events with a start date get one: startDate is required for an Event.
 * Undated events keep Yoast's plain WebPage.
 *
 * Conferences also get the properties Google's Event rich result uses
 * (venue address, offers, status, images) from their "Google event details"
 * fields. Other event types keep the base node.
 *
 * @see https://schema.org/Event
 * @package LTMCore
 */
class EventSchema {

	/**
	 * Event types (the `event_type` field) held online. The rest are in
	 * person.
	 *
	 * @var string[]
	 */
	const ONLINE_EVENT_TYPES = [ 'virtual', 'webinar', 'dispatch', 'frontier-forum' ];

	/**
	 * Values of the free-text `timezone` field mapped to timezones. Anything
	 * else (including blank) falls back to the site's timezone.
	 *
	 * @var array<string, string>
	 */
	const TIMEZONES = [
		'ET' => 'America/New_York',
		'CT' => 'America/Chicago',
		'MT' => 'America/Denver',
		'PT' => 'America/Los_Angeles',
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Ahead of GlossarySchema (10), which adds `about` / `mentions` to this node.
		add_filter( 'wpseo_schema_graph', array( $this, 'add_graph_node' ), 5, 2 );
	}

	/**
	 * Appends the Event node on a dated event's page, and makes it the
	 * WebPage's `mainEntity`.
	 *
	 * @param array  $graph   Yoast's schema graph nodes.
	 * @param object $context Yoast's Meta_Tags_Context.
	 * @return array
	 */
	public function add_graph_node( $graph, $context ) {
		if ( ! is_singular( 'events' ) ) {
			return $graph;
		}

		$event = $this->build_event( get_queried_object_id(), $context );
		if ( ! $event ) {
			return $graph;
		}

		foreach ( $graph as $index => $node ) {
			if ( is_array( $node ) && in_array( 'WebPage', (array) ( $node['@type'] ?? [] ), true ) ) {
				$graph[ $index ]['mainEntity'] = [ '@id' => $event['@id'] ];
				break;
			}
		}

		$graph[] = $event;

		return $graph;
	}

	/**
	 * The Event node for an event, or [] when it has no start date.
	 *
	 * @param int    $post_id Event post ID.
	 * @param object $context Yoast's Meta_Tags_Context (canonical,
	 *                        main_schema_id, description, has_image,
	 *                        site_represents_reference).
	 * @return array
	 */
	public function build_event( $post_id, $context ) {
		$start = self::get_date( $post_id, 'start_date' );
		if ( '' === $start ) {
			return [];
		}

		$online = in_array( get_post_meta( $post_id, 'event_type', true ), self::ONLINE_EVENT_TYPES, true );
		$place  = trim( (string) get_post_meta( $post_id, 'location', true ) );

		if ( $online ) {
			$location = [ '@type' => 'VirtualLocation', 'url' => $context->canonical ];
		} elseif ( '' !== $place ) {
			$location = [ '@type' => 'Place', 'name' => $place, 'address' => $place ];
		} else {
			$location = null;
		}

		$event = [
			'@type'               => 'Event',
			'@id'                 => $context->canonical . '#event',
			'name'                => wp_strip_all_tags( get_the_title( $post_id ) ),
			'description'         => $context->description,
			'url'                 => $context->canonical,
			'startDate'           => $start,
			'endDate'             => self::get_date( $post_id, 'end_date' ),
			'eventAttendanceMode' => 'https://schema.org/' . ( $online ? 'Online' : 'Offline' ) . 'EventAttendanceMode',
			'location'            => $location,
			'image'               => $context->has_image ? [ '@id' => $context->canonical . '#primaryimage' ] : null,
			'organizer'           => $context->site_represents_reference,
			'mainEntityOfPage'    => [ '@id' => $context->main_schema_id ],
		];

		if ( 'conference' === get_post_meta( $post_id, 'event_type', true ) ) {
			$event = array_merge( $event, $this->conference_properties( $post_id, $context ) );
		}

		return array_filter( $event );
	}

	/**
	 * Google Event rich result properties for a conference, from the "Google
	 * event details" fields (see Events::register_google_event_field_group()).
	 * Merged over the base node, so a null leaves that property out.
	 *
	 * @see https://developers.google.com/search/docs/appearance/structured-data/event
	 *
	 * @param int    $post_id Conference event post ID.
	 * @param object $context Yoast's Meta_Tags_Context.
	 * @return array
	 */
	private function conference_properties( $post_id, $context ) {
		$meta = static function ( $key ) use ( $post_id ) {
			return trim( (string) get_post_meta( $post_id, $key, true ) );
		};

		$properties = [];

		$address = array_filter(
			[
				'@type'           => 'PostalAddress',
				'streetAddress'   => $meta( 'seo_street_address' ),
				'addressLocality' => $meta( 'seo_city' ) ?: $meta( 'location' ),
				'addressRegion'   => $meta( 'seo_region' ),
				'postalCode'      => $meta( 'seo_postal_code' ),
				'addressCountry'  => $meta( 'seo_country' ),
			]
		);
		// Country defaults to US, so it alone isn't an address.
		if ( isset( $address['streetAddress'] ) || isset( $address['addressLocality'] ) ) {
			$properties['location'] = array_filter(
				[
					'@type'   => 'Place',
					'name'    => $meta( 'seo_venue_name' ),
					'address' => $address,
				]
			);
		}

		$status                    = $meta( 'seo_event_status' ) ?: 'EventScheduled';
		$properties['eventStatus'] = 'https://schema.org/' . $status;
		if ( 'EventRescheduled' === $status ) {
			$properties['previousStartDate'] = self::get_date( $post_id, 'seo_previous_start_date' );
		}

		$price = $meta( 'seo_ticket_price' );
		if ( is_numeric( $price ) ) {
			$availability         = $meta( 'seo_ticket_availability' );
			$properties['offers'] = array_filter(
				[
					'@type'         => 'Offer',
					'url'           => esc_url_raw( $meta( 'seo_ticket_url' ) ) ?: $context->canonical,
					'price'         => $price,
					'priceCurrency' => strtoupper( $meta( 'seo_ticket_currency' ) ) ?: 'USD',
					'availability'  => $availability ? 'https://schema.org/' . $availability : '',
					'validFrom'     => self::get_date( $post_id, 'seo_tickets_on_sale' ),
				],
				'strlen'
			);
		}

		$images = [];
		foreach ( [ 'seo_image_16x9', 'seo_image_4x3', 'seo_image_1x1' ] as $key ) {
			$url = wp_get_attachment_image_url( (int) $meta( $key ), 'full' );
			if ( $url ) {
				$images[] = $url;
			}
		}
		if ( $images ) {
			$properties['image'] = $images;
		}

		return $properties;
	}

	/**
	 * Whether an event's page has an Event node (i.e. it has a start date).
	 *
	 * @param int $post_id Event post ID.
	 * @return bool
	 */
	public static function has_event( $post_id ) {
		return '' !== self::get_date( $post_id, 'start_date' );
	}

	/**
	 * An event date field as ISO 8601 with its UTC offset, or ''. ACF stores
	 * it as Y-m-d H:i:s, read as local time in the event's `timezone`.
	 *
	 * @param int    $post_id    Event post ID.
	 * @param string $field_name `start_date` or `end_date`.
	 * @return string
	 */
	private static function get_date( $post_id, $field_name ) {
		$raw = (string) get_post_meta( $post_id, $field_name, true );
		if ( '' === $raw ) {
			return '';
		}

		$abbr     = strtoupper( trim( (string) get_post_meta( $post_id, 'timezone', true ) ) );
		$timezone = isset( self::TIMEZONES[ $abbr ] ) ? new \DateTimeZone( self::TIMEZONES[ $abbr ] ) : wp_timezone();
		$date     = \DateTime::createFromFormat( 'Y-m-d H:i:s', $raw, $timezone );

		return $date ? $date->format( 'c' ) : '';
	}
}
