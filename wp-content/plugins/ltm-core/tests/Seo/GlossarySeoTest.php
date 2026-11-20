<?php
/**
 * Tests for the glossary's Yoast templates, breadcrumbs and sitemap entry.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Seo;

use LTMCore\Blocks\GlossaryIndex;
use LTMCore\Seo\GlossarySeo;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Seo\GlossarySeo
 */
class GlossarySeoTest extends WP_UnitTestCase {

	/**
	 * @var GlossarySeo
	 */
	private $seo;

	public function set_up() {
		parent::set_up();
		GlossaryIndex::reset_cache();
		$this->seo = new GlossarySeo();
	}

	public function tear_down() {
		GlossaryIndex::reset_cache();
		parent::tear_down();
	}

	private function create_index_page() {
		return self::factory()->post->create( [ 'post_type' => 'page', 'post_title' => 'Glossary', 'post_content' => '<!-- wp:latitudemedia/glossary-index /-->' ] );
	}

	public function test_title_settings_override_stored_values_and_keep_the_rest() {
		$titles = $this->seo->force_title_settings(
			[
				'title-glossary' => '%%title%% %%page%% %%sep%% %%sitename%%',
				'noindex-glossary' => true,
				'title-post'     => '%%title%%',
			]
		);

		$this->assertSame( '%%title%%%%glossary_abbreviation%%: Definition & Meaning %%sep%% %%sitename%%', $titles['title-glossary'] );
		$this->assertSame( '%%excerpt%%', $titles['metadesc-glossary'] );
		$this->assertSame( '%%title%%%%glossary_abbreviation%%: Definition & Meaning', $titles['social-title-glossary'] );
		$this->assertSame( '%%excerpt%%', $titles['social-description-glossary'] );
		$this->assertFalse( $titles['noindex-glossary'] );
		$this->assertSame( '%%title%%', $titles['title-post'] );
	}

	public function test_title_settings_filter_is_hooked() {
		$this->assertSame( 10, has_filter( 'option_wpseo_titles', [ $this->seo, 'force_title_settings' ] ) );
	}

	public function test_abbreviation_replacement() {
		$with    = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$without = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$post    = self::factory()->post->create();
		update_field( 'field_ltm_glossary_abbreviation', 'BESS', $with );
		update_post_meta( $post, 'abbreviation', 'NOPE' );

		$this->assertSame( ' (BESS)', $this->seo->replace_abbreviation( 'glossary_abbreviation', (object) [ 'ID' => $with ] ) );
		$this->assertSame( '', $this->seo->replace_abbreviation( 'glossary_abbreviation', (object) [ 'ID' => $without ] ) );
		$this->assertSame( '', $this->seo->replace_abbreviation( 'glossary_abbreviation', (object) [ 'ID' => $post ] ) );
		$this->assertSame( '', $this->seo->replace_abbreviation( 'glossary_abbreviation', (object) [] ) );
	}

	public function test_breadcrumbs_get_the_index_page_after_home_on_term_pages() {
		$page = $this->create_index_page();
		$term = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Ant' ] );
		$this->go_to( get_permalink( $term ) );

		$links = $this->seo->add_index_page_crumb(
			[
				[ 'url' => home_url( '/' ), 'text' => 'Home' ],
				[ 'url' => get_permalink( $term ), 'text' => 'Ant', 'id' => $term ],
			]
		);

		$this->assertSame( [ 'Home', 'Glossary', 'Ant' ], wp_list_pluck( $links, 'text' ) );
		$this->assertSame( get_permalink( $page ), $links[1]['url'] );
		$this->assertSame( $page, $links[1]['id'] );
	}

	public function test_breadcrumbs_untouched_elsewhere_or_without_index_page() {
		$links = [ [ 'url' => home_url( '/' ), 'text' => 'Home' ], [ 'text' => 'Ant' ] ];

		$term = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$this->go_to( get_permalink( $term ) );
		$this->assertSame( $links, $this->seo->add_index_page_crumb( $links ), 'No index page.' );

		$this->create_index_page();
		GlossaryIndex::reset_cache();
		$this->go_to( get_permalink( self::factory()->post->create() ) );
		$this->assertSame( $links, $this->seo->add_index_page_crumb( $links ), 'Not a glossary page.' );
	}

	public function test_index_page_lastmod_follows_newest_term() {
		$page = get_post( $this->create_index_page() );
		self::factory()->post->create( [ 'post_type' => 'glossary', 'post_modified_gmt' => '2030-01-01 00:00:00' ] );
		$newest = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		global $wpdb;
		$wpdb->update( $wpdb->posts, [ 'post_modified_gmt' => '2031-06-01 12:00:00', 'post_modified' => '2031-06-01 12:00:00' ], [ 'ID' => $newest ] );
		clean_post_cache( $newest );

		$url = $this->seo->index_page_lastmod( [ 'mod' => '2026-01-01 00:00:00' ], 'post', $page );
		$this->assertSame( '2031-06-01 12:00:00', $url['mod'] );

		$url = $this->seo->index_page_lastmod( [ 'mod' => '2040-01-01 00:00:00' ], 'post', $page );
		$this->assertSame( '2040-01-01 00:00:00', $url['mod'], 'Keeps the page date when it is newer.' );
	}

	public function test_lastmod_untouched_for_other_entries() {
		$this->create_index_page();
		$other = get_post( self::factory()->post->create( [ 'post_type' => 'page' ] ) );

		$this->assertSame( [ 'mod' => 'x' ], $this->seo->index_page_lastmod( [ 'mod' => 'x' ], 'post', $other ) );
		$this->assertSame( [ 'mod' => 'x' ], $this->seo->index_page_lastmod( [ 'mod' => 'x' ], 'term', (object) [] ) );
	}
}
