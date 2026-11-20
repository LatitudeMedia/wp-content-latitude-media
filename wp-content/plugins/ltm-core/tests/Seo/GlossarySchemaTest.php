<?php
/**
 * Tests for the glossary's schema.org DefinedTerm / DefinedTermSet nodes.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Seo;

use LTMCore\Blocks\GlossaryIndex;
use LTMCore\PostTypes\Glossary;
use LTMCore\Seo\GlossarySchema;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Seo\GlossarySchema
 */
class GlossarySchemaTest extends WP_UnitTestCase {

	/**
	 * @var GlossarySchema
	 */
	private $schema;

	public function set_up() {
		parent::set_up();
		GlossaryIndex::reset_cache();
		$this->schema = new GlossarySchema();
	}

	private function create_index_page() {
		return self::factory()->post->create( [ 'post_type' => 'page', 'post_title' => 'Glossary', 'post_content' => '<!-- wp:latitudemedia/glossary-index /-->' ] );
	}

	public function test_defined_term_node() {
		$page = $this->create_index_page();
		$term = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Battery', 'post_excerpt' => 'Stores energy.' ] );
		update_field( 'field_ltm_glossary_abbreviation', 'BESS', $term );
		update_field( 'field_ltm_glossary_synonyms', [ [ 'field_ltm_glossary_synonym' => 'Grid battery' ] ], $term );

		$this->assertSame(
			[
				'@type'            => 'DefinedTerm',
				'@id'              => get_permalink( $term ) . '#definedterm',
				'name'             => 'Battery',
				'description'      => 'Stores energy.',
				'url'              => get_permalink( $term ),
				'alternateName'    => [ 'BESS', 'Grid battery' ],
				'inDefinedTermSet' => [ '@id' => get_permalink( $page ) . '#definedtermset' ],
			],
			$this->schema->build_defined_term( $term )
		);
	}

	public function test_defined_term_falls_back_to_definition_and_omits_empty_props() {
		$term = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Carbon', 'post_excerpt' => '' ] );
		update_field( 'field_ltm_glossary_definition', '<p>Carbon is <strong>element 6</strong>.</p>', $term );

		$node = $this->schema->build_defined_term( $term );

		$this->assertSame( 'Carbon is element 6.', $node['description'] );
		$this->assertArrayNotHasKey( 'alternateName', $node );
		$this->assertArrayNotHasKey( 'inDefinedTermSet', $node );
	}

	public function test_defined_term_set_node_lists_every_published_term() {
		$page = $this->create_index_page();
		$ant  = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Ant', 'post_excerpt' => 'Insect.' ] );
		self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Zinc', 'post_excerpt' => 'Metal.' ] );
		self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Draft', 'post_status' => 'draft' ] );

		$node = $this->schema->build_defined_term_set( $page );

		$this->assertSame( 'DefinedTermSet', $node['@type'] );
		$this->assertSame( get_permalink( $page ) . '#definedtermset', $node['@id'] );
		$this->assertSame( 'Glossary', $node['name'] );
		$this->assertSame( [ 'Ant', 'Zinc' ], wp_list_pluck( $node['hasDefinedTerm'], 'name' ) );
		$this->assertSame( get_permalink( $ant ) . '#definedterm', $node['hasDefinedTerm'][0]['@id'] );
	}

	public function test_graph_and_webpage_on_a_term_page() {
		$term    = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Ant' ] );
		$related = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Antenna' ] );
		update_field( Glossary::RELATED_TERMS_FIELD, [ $related ], $term );

		$this->go_to( get_permalink( $term ) );

		$graph = $this->schema->add_graph_nodes( [ [ '@type' => 'WebPage' ] ] );
		$this->assertSame( 'DefinedTerm', end( $graph )['@type'] );

		$webpage = $this->schema->extend_webpage( [ '@type' => 'WebPage' ] );
		$this->assertSame( [ '@id' => get_permalink( $term ) . '#definedterm' ], $webpage['mainEntity'] );
		$this->assertSame( [ get_permalink( $related ) ], $webpage['relatedLink'] );
	}

	public function test_graph_and_webpage_on_the_index_page() {
		$page = $this->create_index_page();

		$this->go_to( get_permalink( $page ) );

		$graph = $this->schema->add_graph_nodes( [] );
		$this->assertSame( 'DefinedTermSet', $graph[0]['@type'] );

		$webpage = $this->schema->extend_webpage( [] );
		$this->assertSame( [ '@id' => get_permalink( $page ) . '#definedtermset' ], $webpage['mainEntity'] );
	}

	public function test_graph_untouched_elsewhere() {
		$this->go_to( get_permalink( self::factory()->post->create() ) );

		$this->assertSame( [ [ '@type' => 'WebPage' ] ], $this->schema->add_graph_nodes( [ [ '@type' => 'WebPage' ] ] ) );
		$this->assertSame( [ '@type' => 'WebPage' ], $this->schema->extend_webpage( [ '@type' => 'WebPage' ] ) );
	}
}
