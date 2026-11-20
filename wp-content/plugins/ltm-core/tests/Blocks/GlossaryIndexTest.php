<?php
/**
 * Tests for the Glossary Index block's data helpers and render.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use LTMCore\Blocks\GlossaryIndex;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\GlossaryIndex
 */
class GlossaryIndexTest extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
		GlossaryIndex::reset_cache();
	}

	private function create_term( $title, $args = [] ) {
		return self::factory()->post->create(
			array_merge( [ 'post_type' => 'glossary', 'post_title' => $title, 'post_status' => 'publish' ], $args )
		);
	}

	public function test_groups_terms_alphabetically_with_non_letters_first() {
		$this->create_term( 'Battery' );
		$this->create_term( 'antenna' );
		$this->create_term( 'Ant' );
		$this->create_term( '3-phase power' );
		$this->create_term( 'Éolienne' );
		$this->create_term( 'Draft term', [ 'post_status' => 'draft' ] );

		$groups = GlossaryIndex::get_grouped_terms();

		$this->assertSame( [ '#', 'A', 'B', 'E' ], array_keys( $groups ) );
		$this->assertSame( [ 'Ant', 'antenna' ], wp_list_pluck( $groups['A'], 'post_title' ) );
		$this->assertSame( [ '3-phase power' ], wp_list_pluck( $groups['#'], 'post_title' ) );
	}

	public function test_get_index_page_id_finds_the_published_page_with_the_block() {
		self::factory()->post->create( [ 'post_type' => 'page', 'post_status' => 'draft', 'post_content' => '<!-- wp:latitudemedia/glossary-index /-->' ] );
		self::factory()->post->create( [ 'post_type' => 'page', 'post_content' => '<!-- wp:paragraph --><p>Glossary</p><!-- /wp:paragraph -->' ] );
		$page = self::factory()->post->create( [ 'post_type' => 'page', 'post_content' => '<!-- wp:latitudemedia/glossary-index /-->' ] );

		$this->assertSame( $page, GlossaryIndex::get_index_page_id() );
	}

	public function test_get_index_page_id_is_zero_without_a_page() {
		$this->assertSame( 0, GlossaryIndex::get_index_page_id() );
	}

	public function test_render_outputs_search_nav_and_terms() {
		$ant = $this->create_term( 'Ant', [ 'post_excerpt' => 'A small insect.' ] );
		$this->create_term( 'Zinc' );

		$html = do_blocks( '<!-- wp:latitudemedia/glossary-index /-->' );

		$this->assertStringContainsString( 'class="ltm-glossary-index__search" hidden', $html );
		$this->assertStringContainsString( 'type="search"', $html );
		$this->assertStringContainsString( 'data-text-empty=', $html );
		$this->assertStringContainsString( '<a href="#glossary-a" data-letter="A">A</a>', $html );
		$this->assertStringContainsString( '<span aria-disabled="true">B</span>', $html );
		$this->assertStringContainsString( 'id="glossary-z"', $html );
		$this->assertStringContainsString( 'data-term="ant"', $html );
		$this->assertStringContainsString( 'href="' . esc_url( get_permalink( $ant ) ) . '"', $html );
		$this->assertStringContainsString( 'A small insect.', $html );
	}

	public function test_render_outputs_abbreviation_and_synonyms_for_search() {
		$term = $this->create_term( 'Battery energy storage system' );
		update_field( 'field_ltm_glossary_abbreviation', 'BESS', $term );
		update_field(
			'field_ltm_glossary_synonyms',
			[
				[ 'field_ltm_glossary_synonym' => 'Cell bank' ],
				[ 'field_ltm_glossary_synonym' => 'Grid battery' ],
			],
			$term
		);

		$html = do_blocks( '<!-- wp:latitudemedia/glossary-index /-->' );

		$this->assertStringContainsString( 'data-abbr="bess"', $html );
		$this->assertStringContainsString( "data-synonyms=\"cell bank\ngrid battery\"", $html );
		$this->assertStringContainsString( '<span class="ltm-glossary-index__abbr">(BESS)</span>', $html );
		$this->assertMatchesRegularExpression( '/<p class="ltm-glossary-index__aka" hidden>\s*Also known as: Cell bank, Grid battery/', $html );
	}

	public function test_render_outputs_nothing_without_terms() {
		$this->assertSame( '', trim( do_blocks( '<!-- wp:latitudemedia/glossary-index /-->' ) ) );
	}
}
