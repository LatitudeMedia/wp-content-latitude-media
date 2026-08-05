<?php
/**
 * Tests for the News Type Preview block's query-building logic.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use LTMCore\Blocks\NewsTypePreview;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\NewsTypePreview
 */
class NewsTypePreviewQueryTest extends WP_UnitTestCase {

	public function test_filters_by_news_type() {
		$analysis_post = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		update_post_meta( $analysis_post, 'news_type', 'analysis' );

		$news_post = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		update_post_meta( $news_post, 'news_type', 'news' );

		$query = NewsTypePreview::get_query(
			[
				'newsType'      => 'analysis',
				'podcastId'     => 0,
				'numberOfPosts' => 5,
			]
		);

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertSame( [ $analysis_post ], $ids );
	}

	public function test_podcast_type_without_podcast_id_returns_all_podcast_posts() {
		$podcast_a = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		update_post_meta( $podcast_a, 'news_type', 'podcast' );
		update_post_meta( $podcast_a, 'podcast', 111 );

		$podcast_b = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		update_post_meta( $podcast_b, 'news_type', 'podcast' );
		update_post_meta( $podcast_b, 'podcast', 222 );

		$non_podcast = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		update_post_meta( $non_podcast, 'news_type', 'news' );

		$query = NewsTypePreview::get_query(
			[
				'newsType'      => 'podcast',
				'podcastId'     => 0,
				'numberOfPosts' => 5,
			]
		);

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertContains( $podcast_a, $ids );
		$this->assertContains( $podcast_b, $ids );
		$this->assertNotContains( $non_podcast, $ids );
	}

	public function test_podcast_id_narrows_to_that_shows_episodes() {
		$show_a_episode = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		update_post_meta( $show_a_episode, 'news_type', 'podcast' );
		update_post_meta( $show_a_episode, 'podcast', 111 );

		$show_b_episode = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		update_post_meta( $show_b_episode, 'news_type', 'podcast' );
		update_post_meta( $show_b_episode, 'podcast', 222 );

		$query = NewsTypePreview::get_query(
			[
				'newsType'      => 'podcast',
				'podcastId'     => 111,
				'numberOfPosts' => 5,
			]
		);

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertSame( [ $show_a_episode ], $ids );
	}

	public function test_number_of_posts_caps_posts_per_page() {
		$posts = self::factory()->post->create_many( 7, [ 'post_status' => 'publish' ] );
		foreach ( $posts as $post_id ) {
			update_post_meta( $post_id, 'news_type', 'news' );
		}

		$query = NewsTypePreview::get_query(
			[
				'newsType'      => 'news',
				'podcastId'     => 0,
				'numberOfPosts' => 3,
			]
		);

		$this->assertCount( 3, $query->posts );
	}

	public function test_orders_by_publish_date_descending_and_only_published() {
		$older = self::factory()->post->create(
			[
				'post_status' => 'publish',
				'post_date'   => '2020-01-01 00:00:00',
			]
		);
		update_post_meta( $older, 'news_type', 'news' );

		$newer = self::factory()->post->create(
			[
				'post_status' => 'publish',
				'post_date'   => '2024-01-01 00:00:00',
			]
		);
		update_post_meta( $newer, 'news_type', 'news' );

		$draft = self::factory()->post->create( [ 'post_status' => 'draft' ] );
		update_post_meta( $draft, 'news_type', 'news' );

		$query = NewsTypePreview::get_query(
			[
				'newsType'      => 'news',
				'podcastId'     => 0,
				'numberOfPosts' => 5,
			]
		);

		$ids = wp_list_pluck( $query->posts, 'ID' );
		$this->assertSame( [ $newer, $older ], $ids );
	}
}
