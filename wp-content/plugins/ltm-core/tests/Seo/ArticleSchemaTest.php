<?php
/**
 * Tests for the industry news / resources Article schema settings and author.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Seo;

use LTMCore\Seo\ArticleSchema;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Seo\ArticleSchema
 */
class ArticleSchemaTest extends WP_UnitTestCase {

	/**
	 * @var ArticleSchema
	 */
	private $schema;

	public function set_up() {
		parent::set_up();
		$this->schema = new ArticleSchema();
	}

	/**
	 * Stand-in for Yoast's Meta_Tags_Context.
	 */
	private function context( $post_id ) {
		return (object) [
			'id'                        => $post_id,
			'site_represents_reference' => [ '@id' => 'https://example.com/#organization' ],
		];
	}

	private function article( $post_id ) {
		return $this->schema->set_author( [ '@type' => 'NewsArticle', 'author' => [ 'name' => 'admin', '@id' => 'https://example.com/#/schema/person/1' ] ], $this->context( $post_id ) );
	}

	public function test_forces_newsarticle_for_industry_news_and_resources_only() {
		$titles = $this->schema->force_article_types( [ 'schema-article-type-industry-news' => 'None', 'schema-article-type-post' => 'Article' ] );

		$this->assertSame( 'NewsArticle', $titles['schema-article-type-industry-news'] );
		$this->assertSame( 'NewsArticle', $titles['schema-article-type-resources'] );
		$this->assertSame( 'Article', $titles['schema-article-type-post'] );
		$this->assertFalse( $this->schema->force_article_types( false ) );
	}

	public function test_article_is_needed_on_these_post_types() {
		// Registered by the theme, which the test suite doesn't load.
		register_post_type( 'resources', [ 'public' => true ] );
		$this->go_to( get_permalink( self::factory()->post->create( [ 'post_type' => 'resources' ] ) ) );
		$this->assertTrue( $this->schema->needs_article( false ) );
		unregister_post_type( 'resources' );

		$this->go_to( get_permalink( self::factory()->post->create( [ 'post_type' => 'events' ] ) ) );
		$this->assertFalse( $this->schema->needs_article( false ) );
		$this->assertTrue( $this->schema->needs_article( true ), "Yoast's own decision stands." );
	}

	public function test_resource_author_is_its_sponsor() {
		$sponsor  = self::factory()->post->create( [ 'post_type' => 'sponsors', 'post_title' => 'Acme Energy' ] );
		$resource = self::factory()->post->create( [ 'post_type' => 'resources' ] );
		update_post_meta( $sponsor, 'website_link', 'https://acme.example/' );
		update_post_meta( $resource, 'sponsor', (string) $sponsor );

		$this->assertSame( [ '@type' => 'Organization', 'name' => 'Acme Energy', 'url' => 'https://acme.example/' ], $this->article( $resource )['author'] );
	}

	public function test_industry_news_author_is_its_company() {
		$news = self::factory()->post->create( [ 'post_type' => 'industry-news' ] );
		update_post_meta( $news, 'company_name', 'Vantage' );

		$this->assertSame( [ '@type' => 'Organization', 'name' => 'Vantage' ], $this->article( $news )['author'] );
	}

	public function test_falls_back_to_the_site_organization() {
		$draft_sponsor = self::factory()->post->create( [ 'post_type' => 'sponsors', 'post_status' => 'draft' ] );
		$resource      = self::factory()->post->create( [ 'post_type' => 'resources' ] );
		update_post_meta( $resource, 'sponsor', (string) $draft_sponsor );
		$news = self::factory()->post->create( [ 'post_type' => 'industry-news' ] );

		$this->assertSame( [ '@id' => 'https://example.com/#organization' ], $this->article( $resource )['author'] );
		$this->assertSame( [ '@id' => 'https://example.com/#organization' ], $this->article( $news )['author'] );
	}

	public function test_other_post_types_keep_their_author() {
		$post = self::factory()->post->create();

		$this->assertSame( 'admin', $this->article( $post )['author']['name'] );
	}
}
