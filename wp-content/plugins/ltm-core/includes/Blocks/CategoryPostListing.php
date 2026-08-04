<?php
namespace LTMCore\Blocks;

use LTMCore\PostTypes\ThematicPages;
use LTMCore\Taxonomies\ThematicPageTypes;

/**
 * Query logic for the Category Post Listing block.
 *
 * @package LTMCore
 */
class CategoryPostListing {

	/**
	 * Builds the posts query for the block from its attributes.
	 *
	 * @param array $attributes Block attributes (categories, onlyThematicPageTagged, layout).
	 * @return \WP_Query
	 */
	public static function get_query( array $attributes ): \WP_Query {
		$categories           = $attributes['categories'] ?? [];
		$only_thematic_tagged = $attributes['onlyThematicPageTagged'] ?? true;
		$layout               = $attributes['layout'] ?? 'five-post-feature';

		$args = [
			'post_type'   => 'post',
			'post_status' => 'publish',
			'orderby'     => 'date',
			'order'       => 'DESC',
		];

		$tax_query = [];

		if ( ! empty( $categories ) ) {
			$tax_query[] = [
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $categories,
			];
		}

		if ( $only_thematic_tagged ) {
			$current_post_id = get_the_ID();

			if ( $current_post_id && get_post_type( $current_post_id ) === 'thematic-pages' ) {
				$term_id = ( new ThematicPages() )->get_synced_term_id( $current_post_id, ( new ThematicPageTypes() )->name );

				if ( $term_id ) {
					$tax_query[] = [
						'taxonomy' => 'thematic-page-types',
						'field'    => 'term_id',
						'terms'    => [ $term_id ],
					];
				}
			}
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		if ( $layout === 'paginated-list' ) {
			$args['posts_per_page'] = 8;
			$args['paged']          = max( 1, absint( $_GET['cpl-page'] ?? 1 ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} else {
			$args['posts_per_page'] = 5;
		}

		return new \WP_Query( $args );
	}
}
