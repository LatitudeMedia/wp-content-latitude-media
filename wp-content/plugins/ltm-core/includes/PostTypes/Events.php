<?php
namespace LTMCore\PostTypes;

/**
 * Custom post type for Events
 *
 * Migrated from the theme's LatitudeMedia\PostTypes\Events (unchanged
 * behavior — same post type args).
 *
 * @package LTMCore
 */
class Events {

	/**
	 * Name of the custom post type.
	 *
	 * @var string
	 */
	public $name = 'events';

	/**
	 * Key of the "Google event details" field group (conference-only).
	 *
	 * @var string
	 */
	const GOOGLE_EVENT_GROUP = 'group_ltm_google_event';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Create the post type
		add_action( 'init', array( $this, 'create_post_type' ) );

		add_filter( 'cloudflare_purge_by_url', array( $this, 'purge_events_page' ), 10, 2 );

		add_filter( 'wpseo_robots_array', array( $this, 'noindex_hidden_events' ), 10, 2 );
		add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', array( $this, 'exclude_hidden_events_from_sitemap' ) );

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_acf_notice' ) );
			return;
		}

		add_action( 'acf/include_fields', array( $this, 'register_field_group' ) );
		add_action( 'acf/input/admin_enqueue_scripts', array( $this, 'toggle_google_event_group' ) );
		add_filter( 'acf/get_field_label', array( $this, 'mark_required_label' ), 10, 2 );
	}

	/**
	 * Warns in wp-admin that the event fields need ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the General event options fields require Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the "General event options" field group, shown as a side meta
	 * box on each event. Copied verbatim from the theme's acf-export.php — the
	 * group key and every field key are unchanged so existing event data keeps
	 * resolving.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_6744b701b9197',
			'title' => 'General event options',
			'fields' => array(
				array(
					'key' => 'field_6713ad31e1567',
					'label' => 'Start date',
					'name' => 'start_date',
					'type' => 'date_time_picker',
					'display_format' => 'm/d/Y g:i a',
					'return_format' => 'm/d/Y g:i a',
					'first_day' => 1,
				),
				array(
					'key' => 'field_6713ad5ee1568',
					'label' => 'End date',
					'name' => 'end_date',
					'type' => 'date_time_picker',
					'display_format' => 'm/d/Y g:i a',
					'return_format' => 'm/d/Y g:i a',
					'first_day' => 1,
				),
				array(
					'key' => 'field_6713b050e1583',
					'label' => 'Timezone',
					'name' => 'timezone',
					'type' => 'text',
				),
				array(
					'key' => 'field_6713ade8e156a',
					'label' => 'Event type',
					'name' => 'event_type',
					'type' => 'select',
					'choices' => array(
						'live' => 'Live',
						'live-podcast' => 'Live Podcast',
						'webinar' => 'Webinar',
						'virtual' => 'Virtual',
						'conference' => 'Conference',
						'dispatch' => 'Dispatch',
						'frontier-forum' => 'Frontier-forum',
					),
					'default_value' => false,
					'return_format' => 'value',
					'allow_null' => 0,
				),
				array(
					'key' => 'field_ltm_conference_google_notice',
					'label' => '',
					'name' => '',
					'type' => 'message',
					'message' => sprintf(
						'<div class="notice notice-info inline"><p>%s</p></div>',
						sprintf(
							/* translators: %s: link to the Google event details section. */
							esc_html__( 'To have this conference show up in Google as an event, fill in the %s section.', 'ltm' ),
							'<a href="#acf-' . self::GOOGLE_EVENT_GROUP . '">' . esc_html__( 'Google event details', 'ltm' ) . '</a>'
						)
					),
					'esc_html' => 0,
					'new_lines' => '',
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_6713ade8e156a',
								'operator' => '==',
								'value' => 'conference',
							),
						),
					),
				),
				array(
					'key' => 'field_6713ae1fe156b',
					'label' => 'Location',
					'name' => 'location',
					'type' => 'text',
				),
				array(
					'key' => 'field_6713ae62e156d',
					'label' => 'Link',
					'name' => 'link',
					'type' => 'text',
				),
				array(
					'key' => 'field_6713ae6fe156e',
					'label' => 'Cover image',
					'name' => 'cover_image',
					'type' => 'image',
					'return_format' => 'array',
					'library' => 'all',
					'preview_size' => 'medium',
				),
				array(
					'key' => 'field_6713ae7ae156f',
					'label' => 'Background image',
					'name' => 'background_image',
					'type' => 'image',
					'return_format' => 'array',
					'library' => 'all',
					'preview_size' => 'medium',
				),
				array(
					'key' => 'field_6713ae8de1570',
					'label' => 'Form text',
					'name' => 'form_text',
					'type' => 'text',
				),
				array(
					'key' => 'field_6713aea8e1571',
					'label' => 'Form code / Registration CTA',
					'name' => 'form_code__registration_cta',
					'type' => 'textarea',
					'rows' => '',
				),
				array(
					'key' => 'field_6713ad28e1566',
					'label' => 'Past event',
					'name' => 'past_event',
					'type' => 'true_false',
					'default_value' => 0,
				),
				array(
					'key' => 'field_6713b049e1582',
					'label' => 'Gated?',
					'name' => 'gated',
					'type' => 'true_false',
					'instructions' => 'This will cause the page not show throughout the site and will prevent search engines from crawling it.',
					'default_value' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'events',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'side',
			'style' => 'default',
			'active' => true,
		));

		$this->register_google_event_field_group();
	}

	/**
	 * Registers the "Google event details" group: the data Google's Event
	 * rich result needs that conferences don't otherwise store in a
	 * structured form (see LTMCore\Seo\EventSchema). Shown only while Event
	 * type is Conference (see toggle_google_event_group()).
	 *
	 * Fields Google needs for the event to qualify are flagged `ltm_required`,
	 * which only adds a red "(required)" to their label (see
	 * mark_required_label()), rather than ACF's `required`: the box is hidden
	 * with JS, not ACF conditional logic, so `required` would block saving
	 * every other event type (and draft conferences whose venue isn't known
	 * yet).
	 */
	private function register_google_event_field_group() {
		$datetime = array(
			'type' => 'date_time_picker',
			'display_format' => 'm/d/Y g:i a',
			'return_format' => 'm/d/Y g:i a',
			'first_day' => 1,
		);

		acf_add_local_field_group(array(
			'key' => self::GOOGLE_EVENT_GROUP,
			'title' => __( 'Google event details', 'ltm' ),
			'fields' => array(
				array(
					'key' => 'field_ltm_seo_intro',
					'label' => '',
					'name' => '',
					'type' => 'message',
					'message' => sprintf(
						'<p>%s</p>',
						sprintf(
							/* translators: 1: the red "(required)" marker, 2: link to Google's event structured data documentation. */
							esc_html__( 'These fields only affect the SEO metadata Google uses to show this conference as an event in search results. Fields marked %1$s (plus Start date above) must be filled in for the event to qualify; the rest improve the listing. %2$s', 'ltm' ),
							self::required_marker(),
							'<a href="https://developers.google.com/search/docs/appearance/structured-data/event" target="_blank" rel="noopener noreferrer">' . esc_html__( "Learn more about Google's event structured data", 'ltm' ) . '</a>'
						)
					),
					'esc_html' => 0,
					'new_lines' => '',
				),
				array(
					'key' => 'field_ltm_seo_venue_name',
					'label' => __( 'Venue name', 'ltm' ),
					'name' => 'seo_venue_name',
					'type' => 'text',
					'instructions' => __( 'e.g. "Hyatt Regency Embarcadero". Leave blank if the venue has no name.', 'ltm' ),
				),
				array(
					'key' => 'field_ltm_seo_street_address',
					'label' => __( 'Street address', 'ltm' ),
					'ltm_required' => 1,
					'name' => 'seo_street_address',
					'type' => 'text',
				),
				array(
					'key' => 'field_ltm_seo_city',
					'label' => __( 'City', 'ltm' ),
					'ltm_required' => 1,
					'name' => 'seo_city',
					'type' => 'text',
					'instructions' => __( 'Falls back to the Location field when blank.', 'ltm' ),
				),
				array(
					'key' => 'field_ltm_seo_region',
					'label' => __( 'State / region', 'ltm' ),
					'ltm_required' => 1,
					'name' => 'seo_region',
					'type' => 'text',
					'instructions' => __( 'e.g. "CA".', 'ltm' ),
				),
				array(
					'key' => 'field_ltm_seo_postal_code',
					'label' => __( 'Postal code', 'ltm' ),
					'ltm_required' => 1,
					'name' => 'seo_postal_code',
					'type' => 'text',
				),
				array(
					'key' => 'field_ltm_seo_country',
					'label' => __( 'Country', 'ltm' ),
					'ltm_required' => 1,
					'name' => 'seo_country',
					'type' => 'text',
					'instructions' => __( 'Two-letter country code.', 'ltm' ),
					'default_value' => 'US',
					'maxlength' => 2,
				),
				array(
					'key' => 'field_ltm_seo_ticket_url',
					'label' => __( 'Ticket page URL', 'ltm' ),
					'name' => 'seo_ticket_url',
					'type' => 'url',
					'instructions' => __( 'A page whose main purpose is buying tickets to this event. Leave blank to use this event page.', 'ltm' ),
				),
				array(
					'key' => 'field_ltm_seo_ticket_price',
					'label' => __( 'Lowest ticket price', 'ltm' ),
					'name' => 'seo_ticket_price',
					'type' => 'number',
					'instructions' => __( 'Including fees. 0 if free. Ticket details are only sent to Google when this is set.', 'ltm' ),
					'min' => 0,
					'step' => '0.01',
				),
				array(
					'key' => 'field_ltm_seo_ticket_currency',
					'label' => __( 'Currency', 'ltm' ),
					'name' => 'seo_ticket_currency',
					'type' => 'text',
					'instructions' => __( 'Three-letter currency code.', 'ltm' ),
					'default_value' => 'USD',
					'maxlength' => 3,
				),
				array(
					'key' => 'field_ltm_seo_ticket_availability',
					'label' => __( 'Ticket availability', 'ltm' ),
					'name' => 'seo_ticket_availability',
					'type' => 'select',
					'choices' => array(
						'InStock' => __( 'In stock', 'ltm' ),
						'SoldOut' => __( 'Sold out', 'ltm' ),
						'PreOrder' => __( 'Pre-order', 'ltm' ),
					),
					'allow_null' => 1,
					'return_format' => 'value',
				),
				array(
					'key' => 'field_ltm_seo_tickets_on_sale',
					'label' => __( 'Tickets on sale from', 'ltm' ),
					'name' => 'seo_tickets_on_sale',
					'instructions' => __( 'Only if ticket sales open later. Uses the event\'s timezone.', 'ltm' ),
				) + $datetime,
				array(
					'key' => 'field_ltm_seo_event_status',
					'label' => __( 'Event status', 'ltm' ),
					'name' => 'seo_event_status',
					'type' => 'select',
					'instructions' => __( 'Keep Start date as-is unless the event is rescheduled.', 'ltm' ),
					'choices' => array(
						'EventScheduled' => __( 'Scheduled', 'ltm' ),
						'EventCancelled' => __( 'Cancelled', 'ltm' ),
						'EventPostponed' => __( 'Postponed', 'ltm' ),
						'EventRescheduled' => __( 'Rescheduled', 'ltm' ),
					),
					'default_value' => 'EventScheduled',
					'allow_null' => 0,
					'return_format' => 'value',
				),
				array(
					'key' => 'field_ltm_seo_previous_start_date',
					'label' => __( 'Original start date', 'ltm' ),
					'name' => 'seo_previous_start_date',
					'instructions' => __( 'Set Start date to the new date and put the original one here.', 'ltm' ),
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_ltm_seo_event_status',
								'operator' => '==',
								'value' => 'EventRescheduled',
							),
						),
					),
				) + $datetime,
				array(
					'key' => 'field_ltm_seo_image_16x9',
					'label' => __( 'Image (16:9)', 'ltm' ),
					'name' => 'seo_image_16x9',
					'type' => 'image',
					'instructions' => __( 'At least 720px wide; 1920px recommended. Falls back to the featured image.', 'ltm' ),
					'return_format' => 'id',
					'library' => 'all',
					'preview_size' => 'medium',
				),
				array(
					'key' => 'field_ltm_seo_image_4x3',
					'label' => __( 'Image (4:3)', 'ltm' ),
					'name' => 'seo_image_4x3',
					'type' => 'image',
					'instructions' => __( 'At least 720px wide; 1920px recommended.', 'ltm' ),
					'return_format' => 'id',
					'library' => 'all',
					'preview_size' => 'medium',
				),
				array(
					'key' => 'field_ltm_seo_image_1x1',
					'label' => __( 'Image (1:1)', 'ltm' ),
					'name' => 'seo_image_1x1',
					'type' => 'image',
					'instructions' => __( 'At least 720px wide; 1920px recommended.', 'ltm' ),
					'return_format' => 'id',
					'library' => 'all',
					'preview_size' => 'medium',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'events',
					),
				),
			),
			'menu_order' => 1,
			'position' => 'side',
			'style' => 'default',
			'active' => true,
		));
	}

	/**
	 * The red "(required)" marker. `acf-required` is ACF's own class for its
	 * red required asterisk, so it's styled on every ACF edit screen.
	 *
	 * @return string
	 */
	private static function required_marker() {
		return '<span class="acf-required">' . esc_html__( '(required)', 'ltm' ) . '</span>';
	}

	/**
	 * ACF: appends the red "(required)" marker to labels of fields flagged
	 * `ltm_required`.
	 *
	 * @param string $label The label HTML.
	 * @param array  $field The field.
	 * @return string
	 */
	public function mark_required_label( $label, $field ) {
		if ( ! empty( $field['ltm_required'] ) ) {
			$label .= ' ' . self::required_marker();
		}

		return $label;
	}

	/**
	 * Shows the "Google event details" box only while Event type is
	 * Conference. ACF conditional logic only finds a controlling field within
	 * the same field group, so it can't hide another group's meta box.
	 */
	public function toggle_google_event_group() {
		$screen = get_current_screen();
		if ( ! $screen || $this->name !== $screen->post_type ) {
			return;
		}

		wp_add_inline_script(
			'acf-input',
			sprintf(
				"acf.addAction( 'ready', function () {
					var type = acf.getField( 'field_6713ade8e156a' );
					var box = document.getElementById( %s );
					if ( ! type || ! box ) {
						return;
					}
					var toggle = function () {
						box.style.display = 'conference' === type.val() ? '' : 'none';
					};
					toggle();
					type.on( 'change', toggle );
				} );",
				wp_json_encode( 'acf-' . self::GOOGLE_EVENT_GROUP )
			)
		);
	}

	/**
	 * Whether an event should be kept out of search engines: "Gated?" is
	 * checked, or its title contains "| Recording".
	 *
	 * @param int $post_id
	 * @return bool
	 */
	public static function is_hidden_from_search( $post_id ) {
		if ( 'events' !== get_post_type( $post_id ) ) {
			return false;
		}

		return (bool) get_post_meta( $post_id, 'gated', true )
			|| false !== stripos( get_post_field( 'post_title', $post_id ), '| Recording' );
	}

	/**
	 * Yoast: output noindex on hidden events, overriding the per-post setting.
	 *
	 * @param array  $robots       Robots directives, e.g. [ 'index' => 'index', 'follow' => 'follow' ].
	 * @param object $presentation Yoast's Indexable_Presentation.
	 * @return array
	 */
	public function noindex_hidden_events( $robots, $presentation ) {
		$model = $presentation->model ?? null;
		if ( $model && 'post' === $model->object_type && self::is_hidden_from_search( (int) $model->object_id ) ) {
			$robots['index'] = 'noindex';
		}

		return $robots;
	}

	/**
	 * Yoast: leave hidden events out of the XML sitemap.
	 *
	 * @param array $ids Post IDs already excluded.
	 * @return array
	 */
	public function exclude_hidden_events_from_sitemap( $ids ) {
		global $wpdb;

		$hidden = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT p.ID FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = 'gated'
				WHERE p.post_type = %s AND ( m.meta_value = '1' OR p.post_title LIKE %s )",
				$this->name,
				'%' . $wpdb->esc_like( '| Recording' ) . '%'
			)
		);

		return array_merge( (array) $ids, array_map( 'intval', $hidden ) );
	}

	/**
	 * When an event is saved, also purge the /events/ listing page from
	 * Cloudflare. The post type has no archive, so the Cloudflare plugin
	 * doesn't know /events/ is related and leaves it stale for logged-out
	 * visitors.
	 *
	 * @param array $urls    URLs the Cloudflare plugin is about to purge.
	 * @param int   $post_id The post being saved.
	 * @return array
	 */
	public function purge_events_page( $urls, $post_id ) {
		if ( $this->name !== get_post_type( $post_id ) ) {
			return $urls;
		}

		$urls[] = get_permalink( $post_id );
		$urls[] = home_url( '/events/' );

		return $urls;
	}

	/**
	 * Creates the post type.
	 */
	public function create_post_type() {
		register_post_type(
			$this->name,
			[
				'labels' => [
					'name'                  => __( 'Events', 'ltm' ),
					'singular_name'         => __( 'Event', 'ltm' ),
					'add_new'               => __( 'Add New Event', 'ltm' ),
					'add_new_item'          => __( 'Add New Event', 'ltm' ),
					'edit_item'             => __( 'Edit Event', 'ltm' ),
					'new_item'              => __( 'New Event', 'ltm' ),
					'view_item'             => __( 'View Event', 'ltm' ),
					'view_items'            => __( 'View Events', 'ltm' ),
					'search_items'          => __( 'Search Events', 'ltm' ),
					'not_found'             => __( 'No Events found', 'ltm' ),
					'not_found_in_trash'    => __( 'No Events found in Trash', 'ltm' ),
					'parent_item_colon'     => __( 'Parent Event:', 'ltm' ),
					'all_items'             => __( 'All Events', 'ltm' ),
					'archives'              => __( 'Event Archives', 'ltm' ),
					'attributes'            => __( 'Event Attributes', 'ltm' ),
					'insert_into_item'      => __( 'Insert into Event', 'ltm' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Event', 'ltm' ),
					'filter_items_list'     => __( 'Filter Events list', 'ltm' ),
					'items_list_navigation' => __( 'Events list navigation', 'ltm' ),
					'items_list'            => __( 'Events list', 'ltm' ),
					'menu_name'             => __( 'Events', 'ltm' ),
				],
				'menu_icon'           => 'dashicons-tickets-alt',
				'public'              => true,
				'map_meta_cap'        => true,
				'has_archive'         => false,
				'show_ui'             => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
			]
		);
	}

	/**
	 * Gets an event's start date.
	 *
	 * Migrated from the theme's global get_event_start_date().
	 *
	 * @param int    $event_id
	 * @param string $format
	 * @return string
	 */
	public static function get_event_start_date( $event_id, $format = 'F j Y' ) {
		if ( ! $event_id ) {
			$event_id = get_the_ID();
		}

		$start_date = get_field( 'start_date', $event_id );
		if ( empty( $start_date ) ) {
			return '';
		}

		return date_to_format( $start_date, 'm/d/Y g:i a', $format );
	}

	/**
	 * Gets an event's end date.
	 *
	 * Migrated from the theme's global get_event__end_date() (double
	 * underscore preserved as-is from the original).
	 *
	 * @param int    $event_id
	 * @param string $format
	 * @return string
	 */
	public static function get_event__end_date( $event_id, $format = 'F j Y' ) {
		if ( ! $event_id ) {
			$event_id = get_the_ID();
		}

		$start_date = get_field( 'end_date', $event_id );
		if ( empty( $start_date ) ) {
			return '';
		}

		return date_to_format( $start_date, 'm/d/Y g:i a', $format );
	}

	/**
	 * Whether an event is over: "Past event" is checked, or its end date has
	 * passed (same rule as get_events_list( 'past' )). Reads raw meta, which
	 * ACF stores as Y-m-d H:i:s in the site's timezone.
	 *
	 * @param int $event_id
	 * @return bool
	 */
	public static function is_past_event( $event_id ) {
		if ( get_post_meta( $event_id, 'past_event', true ) ) {
			return true;
		}

		$end_date = get_post_meta( $event_id, 'end_date', true );
		if ( empty( $end_date ) ) {
			return false;
		}

		$end = \DateTime::createFromFormat( 'Y-m-d H:i:s', $end_date, wp_timezone() );

		return $end && $end < current_datetime();
	}

	/**
	 * Gets an event's timezone.
	 *
	 * Migrated from the theme's global get_event_timezone().
	 *
	 * @param int $event_id
	 * @return string
	 */
	public static function get_event_timezone( $event_id ) {
		if ( ! $event_id ) {
			$event_id = get_the_ID();
		}

		$timezone = get_field( 'timezone', $event_id );
		if ( empty( $timezone ) ) {
			return '';
		}

		return $timezone;
	}

	/**
	 * Builds the WP_Query for an events listing.
	 *
	 * Migrated from the theme's global get_events_list(). Lived on
	 * LTMCore\RestApi\LoadMoreEvents until that never-called REST endpoint
	 * was removed; acf/events-list-block is its caller.
	 *
	 * @param string $type
	 * @param array  $args
	 * @param array  $ids
	 * @return \WP_Query the query object
	 */
	public static function get_events_list( $type = '', $args = [], $ids = [] ) {
		$queryArgs = [
			'post_type'      => 'events',
			'meta_key'       => 'start_date',
			'orderby'        => 'meta_value',
			'meta_type'      => 'DATE',
			'order'          => 'DESC',
			'posts_per_page' => -1,
		];

		$queryArgs = wp_parse_args( $args, $queryArgs );

		switch ( $type ) {
			case 'upcoming':
				$queryArgs['order']      = 'ASC';
				$queryArgs['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'     => 'end_date',
						'value'   => get_date_from_gmt( date( 'Y-m-d' ) ),
						'compare' => '>=',
						'type'    => 'DATE',
					),
					array(
						'key'     => 'past_event',
						'value'   => true,
						'compare' => '!=',
					),
					array(
						'key'     => 'gated',
						'value'   => true,
						'compare' => '!=',
					),
				);
				break;
			case 'past':
				$queryArgs['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'     => 'gated',
						'value'   => true,
						'compare' => '!=',
					),
					array(
						'relation' => 'OR',
						array(
							'key'     => 'end_date',
							'value'   => get_date_from_gmt( date( 'Y-m-d' ) ),
							'compare' => '<',
							'type'    => 'DATE',
						),
						array(
							'key'     => 'past_event',
							'value'   => true,
							'compare' => '=',
						),
					),
				);
				break;
		}

		return \LatitudeMedia\Manage_Data()->curated_query( $queryArgs, $ids );
	}
}
