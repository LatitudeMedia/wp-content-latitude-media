<?php
namespace LTMCore\Blocks;

/**
 * Query logic for the News Type Preview block.
 *
 * @package LTMCore
 */
class NewsTypePreview {

	/**
	 * Builds the posts query for the block from its attributes.
	 *
	 * @param array $attributes Block attributes (newsTypes or legacy newsType, podcastId, numberOfPosts).
	 * @return \WP_Query
	 */
	public static function get_query( array $attributes ): \WP_Query {
		// Blocks saved before multiselect only have the legacy `newsType` string.
		$types           = $attributes['newsTypes'] ?? array( $attributes['newsType'] ?? 'news' );
		$podcast_id      = (int) ( $attributes['podcastId'] ?? 0 );
		$number_of_posts = $attributes['numberOfPosts'] ?? 5;

		// "events" isn't a news_type value — it selects the events CPT.
		$include_events = in_array( 'events', $types, true );
		$news_types     = array_values( array_diff( $types, array( 'events' ) ) );

		$base_args = array(
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => $number_of_posts,
		);

		if ( ! $news_types && ! $include_events ) {
			return new \WP_Query( array_merge( $base_args, array( 'post__in' => array( 0 ) ) ) );
		}

		if ( ! $include_events ) {
			return new \WP_Query( array_merge( $base_args, self::get_post_args( $news_types, $podcast_id ) ) );
		}

		$events_args = array( 'post_type' => 'events' );

		if ( ! $news_types ) {
			return new \WP_Query( array_merge( $base_args, $events_args ) );
		}

		// A single WP_Query can't apply the news_type meta filter to posts only,
		// so fetch the top IDs of each kind and merge them by date.
		$id_args   = array_merge( $base_args, array( 'fields' => 'ids' ) );
		$post_ids  = ( new \WP_Query( array_merge( $id_args, self::get_post_args( $news_types, $podcast_id ) ) ) )->posts;
		$event_ids = ( new \WP_Query( array_merge( $id_args, $events_args ) ) )->posts;

		return new \WP_Query(
			array_merge(
				$base_args,
				array(
					'post_type' => array( 'post', 'events' ),
					'post__in'  => array_merge( $post_ids, $event_ids ) ?: array( 0 ),
				)
			)
		);
	}

	/**
	 * Query args for regular posts matching any of the given news types.
	 *
	 * @param string[] $news_types News type values (ACF `news_type` choices).
	 * @param int      $podcast_id When set and "podcast" is selected, limits podcasts to this show.
	 * @return array
	 */
	private static function get_post_args( array $news_types, int $podcast_id ): array {
		$meta_query = array(
			array(
				'key'     => 'news_type',
				'value'   => $news_types,
				'compare' => 'IN',
			),
		);

		if ( in_array( 'podcast', $news_types, true ) && $podcast_id ) {
			$other_types = array_values( array_diff( $news_types, array( 'podcast' ) ) );
			$meta_query  = array(
				'relation' => 'OR',
				array(
					'relation' => 'AND',
					array(
						'key'     => 'news_type',
						'value'   => 'podcast',
						'compare' => '=',
					),
					array(
						'key'     => 'podcast',
						'value'   => $podcast_id,
						'compare' => '=',
					),
				),
			);

			if ( $other_types ) {
				$meta_query[] = array(
					'key'     => 'news_type',
					'value'   => $other_types,
					'compare' => 'IN',
				);
			}
		}

		return array(
			'post_type'  => 'post',
			'meta_query' => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		);
	}
}
