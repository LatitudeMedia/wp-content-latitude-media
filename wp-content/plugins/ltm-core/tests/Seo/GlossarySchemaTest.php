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

	public function test_post_terms_go_on_the_article_node_as_about_and_mentions() {
		$this->create_index_page();
		$subject = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Battery' ] );
		$mention = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Grid' ] );
		$post    = self::factory()->post->create();
		update_post_meta( $post, 'glossary_about', [ (string) $subject ] );
		update_post_meta( $post, 'glossary_mentions', [ (string) $mention ] );

		$graph = $this->schema->add_post_term_properties(
			[
				[ '@type' => 'WebPage', '@id' => 'https://example.com/a/' ],
				[ '@type' => 'NewsArticle', '@id' => 'https://example.com/a/#article' ],
			],
			$post
		);

		$this->assertArrayNotHasKey( 'about', $graph[0], 'The WebPage is left alone when there is an Article.' );
		$this->assertSame( [ $this->schema->build_term_reference( $subject ) ], $graph[1]['about'] );
		$this->assertSame( [ $this->schema->build_term_reference( $mention ) ], $graph[1]['mentions'] );
		$this->assertSame(
			[
				'@type'            => 'DefinedTerm',
				'@id'              => get_permalink( $subject ) . '#definedterm',
				'name'             => 'Battery',
				'url'              => get_permalink( $subject ),
				'inDefinedTermSet' => [ '@id' => get_permalink( GlossaryIndex::get_index_page_id() ) . '#definedtermset' ],
			],
			$graph[1]['about'][0]
		);
	}

	public function test_post_terms_fall_back_to_the_webpage_and_keep_existing_values() {
		$term  = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$event = self::factory()->post->create( [ 'post_type' => 'events' ] );
		update_post_meta( $event, 'glossary_about', [ (string) $term ] );

		$existing = [ '@id' => 'https://example.com/#organization' ];
		$graph    = $this->schema->add_post_term_properties(
			[ [ '@type' => [ 'WebPage', 'ItemPage' ], '@id' => 'https://example.com/e/', 'about' => $existing ] ],
			$event
		);

		$this->assertSame( [ $existing, $this->schema->build_term_reference( $term ) ], $graph[0]['about'] );
		$this->assertArrayNotHasKey( 'mentions', $graph[0] );
	}

	public function test_event_terms_go_on_the_event_node() {
		$term  = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$event = self::factory()->post->create( [ 'post_type' => 'events' ] );
		update_post_meta( $event, 'glossary_mentions', [ (string) $term ] );

		$graph = $this->schema->add_post_term_properties(
			[
				[ '@type' => 'WebPage', '@id' => 'https://example.com/e/' ],
				[ '@type' => 'Event', '@id' => 'https://example.com/e/#event' ],
			],
			$event
		);

		$this->assertArrayNotHasKey( 'mentions', $graph[0] );
		$this->assertSame( [ $this->schema->build_term_reference( $term ) ], $graph[1]['mentions'] );
	}

	public function test_post_without_terms_is_untouched() {
		$post  = self::factory()->post->create();
		$graph = [ [ '@type' => 'NewsArticle', '@id' => 'https://example.com/a/#article' ] ];

		$this->assertSame( $graph, $this->schema->add_post_term_properties( $graph, $post ) );
	}

	public function test_graph_filter_adds_post_terms_on_a_linked_post_page() {
		$term = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$post = self::factory()->post->create();
		update_post_meta( $post, 'glossary_mentions', [ (string) $term ] );
		$this->go_to( get_permalink( $post ) );

		$graph = $this->schema->add_graph_nodes( [ [ '@type' => 'WebPage', '@id' => get_permalink( $post ) ] ] );

		$this->assertSame( get_permalink( $term ) . '#definedterm', $graph[0]['mentions'][0]['@id'] );
	}

	public function test_defined_term_lists_the_pieces_it_is_the_subject_of() {
		update_option( 'wpseo_titles', [ 'schema-article-type-post' => 'NewsArticle', 'schema-article-type-events' => 'None' ] );
		$term    = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Battery' ] );
		$article = self::factory()->post->create( [ 'post_title' => 'Storage story', 'post_date' => '2026-02-01 00:00:00' ] );
		$event   = self::factory()->post->create( [ 'post_type' => 'events', 'post_title' => 'Storage summit', 'post_date' => '2026-05-01 00:00:00' ] );
		$mention = self::factory()->post->create();
		update_post_meta( $term, 'subject_of', [ (string) $article, (string) $event ] );
		update_post_meta( $term, 'mentioned_in', [ (string) $mention ] );

		$node = $this->schema->build_defined_term( $term );
		delete_option( 'wpseo_titles' );

		$this->assertSame(
			[
				[
					'@type' => 'WebPage',
					'@id'   => get_permalink( $event ),
					'name'  => 'Storage summit',
					'url'   => get_permalink( $event ),
				],
				[
					'@type' => 'NewsArticle',
					'@id'   => get_permalink( $article ) . '#article',
					'name'  => 'Storage story',
					'url'   => get_permalink( $article ),
				],
			],
			$node['subjectOf']
		);
	}

	public function test_subject_of_references_a_dated_events_event_node() {
		$event = self::factory()->post->create( [ 'post_type' => 'events', 'post_title' => 'Grid summit' ] );
		update_post_meta( $event, 'start_date', '2026-03-04 09:00:00' );

		$this->assertSame(
			[
				'@type' => 'Event',
				'@id'   => get_permalink( $event ) . '#event',
				'name'  => 'Grid summit',
				'url'   => get_permalink( $event ),
			],
			GlossarySchema::build_work_reference( $event )
		);
	}

	public function test_graph_untouched_elsewhere() {
		$this->go_to( get_permalink( self::factory()->post->create() ) );

		$this->assertSame( [ [ '@type' => 'WebPage' ] ], $this->schema->add_graph_nodes( [ [ '@type' => 'WebPage' ] ] ) );
		$this->assertSame( [ '@type' => 'WebPage' ], $this->schema->extend_webpage( [ '@type' => 'WebPage' ] ) );
	}
}
