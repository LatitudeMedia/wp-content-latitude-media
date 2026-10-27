<?php
namespace LTMCore\RestApi;

/**
 * REST endpoint backing the Featured Single Item block's searchable post picker.
 *
 * Queries all public post types (except attachments and the people/org types
 * — speakers, sponsors, team — that don't fit the post card) directly, rather than
 * the core `/wp/v2/search`, so it can also filter by the `sponsor` taxonomy, which is
 * intentionally not exposed via `show_in_rest` (see the
 * LTMCore\Taxonomies\PostSponsor class).
 *
 * @package LTMCore
 */
class FeaturedSingleItemSearch {

	/**
	 * Namespace for REST API endpoints.
	 *
	 * @var string
	 */
	const REST_API_NAMESPACE = 'ltm-core/v1';

	/**
	 * Route for this endpoint.
	 *
	 * @var string
	 */
	const REST_API_ROUTE = 'featured-single-item/search';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Registers the REST route.
	 */
	public function register_routes() {
		register_rest_route(
			self::REST_API_NAMESPACE,
			self::REST_API_ROUTE,
			[
				'methods'             => 'GET',
				'callback'            => array( $this, 'search' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => [
					'search'    => [ 'type' => 'string', 'default' => '' ],
					'sponsored' => [ 'type' => 'boolean', 'default' => false ],
					'page_id'   => [ 'type' => 'integer', 'default' => 0 ],
				],
				'show_in_index'       => false,
			]
		);
	}

	/**
	 * Searches published items of any public post type, optionally scoped to the sponsor assigned
	 * to the given page.
	 *
	 * @param \WP_REST_Request $request
	 * @return array
	 */
	public function search( \WP_REST_Request $request ) {
		$search    = (string) $request->get_param( 'search' );
		$sponsored = (bool) $request->get_param( 'sponsored' );
		$page_id   = (int) $request->get_param( 'page_id' );

		$post_types = array_values( array_diff( get_post_types( [ 'public' => true ] ), [ 'attachment', 'speakers', 'sponsors', 'team' ] ) );

		$args = [
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			's'              => $search,
			'orderby'        => $search ? 'relevance' : 'date',
		];

		// The page being edited is now a searchable type; don't let it feature itself.
		if ( $page_id ) {
			$args['post__not_in'] = [ $page_id ];
		}

		if ( $sponsored ) {
			$sponsor_terms = $page_id ? wp_get_object_terms( $page_id, 'sponsor', [ 'fields' => 'ids' ] ) : [];

			if ( empty( $sponsor_terms ) || is_wp_error( $sponsor_terms ) ) {
				return [
					'hasSponsor' => false,
					'items'      => [],
				];
			}

			$args['tax_query'] = [
				[
					'taxonomy' => 'sponsor',
					'field'    => 'term_id',
					'terms'    => $sponsor_terms,
				],
			];
		}

		$query = new \WP_Query( $args );

		$items = array_map(
			function ( $post ) {
				return [
					'id'    => $post->ID,
					'title' => get_the_title( $post ) ?: __( '(no title)', 'ltm' ),
				];
			},
			$query->posts
		);

		return [
			'hasSponsor' => true,
			'items'      => $items,
		];
	}
}
