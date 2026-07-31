<?php
namespace LatitudeMedia\RestApi;

/**
 * REST endpoint backing the Featured Post block's searchable post picker.
 *
 * Queries the `post` post type directly (rather than the core `/wp/v2/posts`
 * search) so it can also filter by the `sponsor` taxonomy, which is
 * intentionally not exposed via `show_in_rest` (see
 * LatitudeMedia\Taxonomies\PostSponsor).
 *
 * @package LatitudeMedia
 */
class FeaturedPostSearch {

	/**
	 * Namespace for REST API endpoints.
	 *
	 * @var string
	 */
	const REST_API_NAMESPACE = 'wp/v2';

	/**
	 * Route for this endpoint.
	 *
	 * @var string
	 */
	const REST_API_ROUTE = 'featured-post/search';

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
	 * Searches published posts, optionally scoped to the sponsor assigned
	 * to the given page.
	 *
	 * @param \WP_REST_Request $request
	 * @return array
	 */
	public function search( \WP_REST_Request $request ) {
		$search    = (string) $request->get_param( 'search' );
		$sponsored = (bool) $request->get_param( 'sponsored' );
		$page_id   = (int) $request->get_param( 'page_id' );

		$args = [
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			's'              => $search,
			'orderby'        => $search ? 'relevance' : 'date',
		];

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
