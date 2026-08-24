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
	 * @param array $attributes Block attributes (newsType, podcastId, numberOfPosts).
	 * @return \WP_Query
	 */
	public static function get_query( array $attributes ): \WP_Query {
		$news_type      = $attributes['newsType'] ?? 'news';
		$podcast_id     = $attributes['podcastId'] ?? 0;
		$number_of_posts = $attributes['numberOfPosts'] ?? 5;

		$meta_query = array(
			array(
				'key'     => 'news_type',
				'value'   => $news_type,
				'compare' => '=',
			),
		);

		if ( 'podcast' === $news_type && $podcast_id ) {
			$meta_query['relation'] = 'AND';
			$meta_query[]           = array(
				'key'     => 'podcast',
				'value'   => $podcast_id,
				'compare' => '=',
			);
		}

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => $number_of_posts,
			'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		);

		return new \WP_Query( $args );
	}
}
