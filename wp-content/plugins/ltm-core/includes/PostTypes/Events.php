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
