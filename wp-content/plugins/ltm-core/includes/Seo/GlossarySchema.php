<?php
namespace LTMCore\Seo;

use LTMCore\Blocks\GlossaryIndex;
use LTMCore\PostTypes\Glossary;

/**
 * schema.org markup for the glossary, added to Yoast SEO's JSON-LD graph:
 *
 * - each glossary term page gets a DefinedTerm, and its WebPage points at it
 *   (`mainEntity`) and at the related terms (`relatedLink`);
 * - the page holding the glossary index block gets a DefinedTermSet listing
 *   every term, and its WebPage points at that.
 *
 * Terms reference the set by `@id` (`inDefinedTermSet`) and vice versa.
 *
 * @see https://schema.org/DefinedTerm
 * @see https://schema.org/DefinedTermSet
 * @package LTMCore
 */
class GlossarySchema {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'wpseo_schema_graph', array( $this, 'add_graph_nodes' ) );
		add_filter( 'wpseo_schema_webpage', array( $this, 'extend_webpage' ) );
	}

	/**
	 * Appends the DefinedTerm or DefinedTermSet node for the current page.
	 *
	 * @param array $graph Yoast's schema graph nodes.
	 * @return array
	 */
	public function add_graph_nodes( $graph ) {
		if ( is_singular( 'glossary' ) ) {
			$graph[] = $this->build_defined_term( get_queried_object_id() );
		} elseif ( $this->is_index_page() ) {
			$graph[] = $this->build_defined_term_set( get_queried_object_id() );
		}

		return $graph;
	}

	/**
	 * Points the WebPage node at the term or set, and at related terms.
	 *
	 * @param array $data Yoast's WebPage node.
	 * @return array
	 */
	public function extend_webpage( $data ) {
		if ( is_singular( 'glossary' ) ) {
			$post_id            = get_queried_object_id();
			$data['mainEntity'] = [ '@id' => self::term_id( $post_id ) ];

			$related = array_map( 'get_permalink', Glossary::get_related_term_ids( $post_id ) );
			if ( $related ) {
				$data['relatedLink'] = $related;
			}
		} elseif ( $this->is_index_page() ) {
			$data['mainEntity'] = [ '@id' => self::set_id( get_queried_object_id() ) ];
		}

		return $data;
	}

	/**
	 * DefinedTerm node for one glossary term.
	 *
	 * @param int $post_id Glossary term post ID.
	 * @return array
	 */
	public function build_defined_term( $post_id ) {
		$node = [
			'@type'       => 'DefinedTerm',
			'@id'         => self::term_id( $post_id ),
			'name'        => wp_strip_all_tags( get_the_title( $post_id ) ),
			'description' => self::get_description( $post_id ),
			'url'         => get_permalink( $post_id ),
		];

		$synonyms = Glossary::get_synonyms( $post_id );
		if ( $synonyms ) {
			$node['alternateName'] = $synonyms;
		}

		$index_page_id = GlossaryIndex::get_index_page_id();
		if ( $index_page_id ) {
			$node['inDefinedTermSet'] = [ '@id' => self::set_id( $index_page_id ) ];
		}

		return array_filter( $node );
	}

	/**
	 * DefinedTermSet node for the glossary index page, listing every term.
	 *
	 * @param int $page_id ID of the page holding the glossary index block.
	 * @return array
	 */
	public function build_defined_term_set( $page_id ) {
		$terms = [];
		foreach ( GlossaryIndex::get_grouped_terms() as $group ) {
			foreach ( $group as $term ) {
				$terms[] = array_filter(
					[
						'@type'       => 'DefinedTerm',
						'@id'         => self::term_id( $term->ID ),
						'name'        => wp_strip_all_tags( get_the_title( $term ) ),
						'description' => self::get_description( $term->ID ),
						'url'         => get_permalink( $term ),
					]
				);
			}
		}

		return [
			'@type'          => 'DefinedTermSet',
			'@id'            => self::set_id( $page_id ),
			'name'           => wp_strip_all_tags( get_the_title( $page_id ) ),
			'url'            => get_permalink( $page_id ),
			'hasDefinedTerm' => $terms,
		];
	}

	/**
	 * Whether the current request is the glossary index page.
	 *
	 * @return bool
	 */
	private function is_index_page() {
		$index_page_id = GlossaryIndex::get_index_page_id();

		return $index_page_id && is_page( $index_page_id );
	}

	/**
	 * The term's short summary (excerpt), or failing that the start of its
	 * definition.
	 *
	 * @param int $post_id Glossary term post ID.
	 * @return string
	 */
	private static function get_description( $post_id ) {
		$excerpt = get_post_field( 'post_excerpt', $post_id );
		if ( '' !== trim( $excerpt ) ) {
			return wp_strip_all_tags( $excerpt );
		}

		$definition = get_post_meta( $post_id, 'definition', true );

		return wp_trim_words( wp_strip_all_tags( (string) $definition ), 55 );
	}

	/**
	 * `@id` of a term's DefinedTerm node.
	 *
	 * @param int $post_id Glossary term post ID.
	 * @return string
	 */
	private static function term_id( $post_id ) {
		return get_permalink( $post_id ) . '#definedterm';
	}

	/**
	 * `@id` of the DefinedTermSet node.
	 *
	 * @param int $page_id Glossary index page ID.
	 * @return string
	 */
	private static function set_id( $page_id ) {
		return get_permalink( $page_id ) . '#definedtermset';
	}
}
