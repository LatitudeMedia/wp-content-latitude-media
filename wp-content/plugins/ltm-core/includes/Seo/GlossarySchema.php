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
 *   every term, and its WebPage points at that;
 * - posts, podcasts, industry news, resources and events linked to terms in
 *   their editor sidebar get `about` (terms they're the subject of) and
 *   `mentions` (terms mentioned in passing) on their Article node (or an
 *   event's Event node, from EventSchema), or on their WebPage when there's
 *   neither; the term's DefinedTerm lists the pieces it's the subject of as
 *   `subjectOf`.
 *
 * Terms reference the set by `@id` (`inDefinedTermSet`) and vice versa.
 *
 * @see https://schema.org/DefinedTerm
 * @see https://schema.org/DefinedTermSet
 * @see https://schema.org/Article
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
		} elseif ( is_singular( Glossary::LINKED_POST_TYPES ) ) {
			$graph = $this->add_post_term_properties( $graph, get_queried_object_id() );
		}

		return $graph;
	}

	/**
	 * Adds `about` / `mentions` DefinedTerm references for a post's linked
	 * glossary terms to its Article node (Yoast `@id` ending `#article`) or
	 * Event node (`#event`, from EventSchema), or to its WebPage node when the
	 * graph has neither.
	 *
	 * @param array $graph   Yoast's schema graph nodes.
	 * @param int   $post_id The post being viewed.
	 * @return array
	 */
	public function add_post_term_properties( $graph, $post_id ) {
		$terms = Glossary::get_post_terms( $post_id );
		if ( ! $terms['about'] && ! $terms['mentions'] ) {
			return $graph;
		}

		$target = self::find_node( $graph, static fn( $node ) => (bool) preg_match( '/#(article|event)$/', (string) ( $node['@id'] ?? '' ) ) )
			?? self::find_node( $graph, static fn( $node ) => in_array( 'WebPage', (array) ( $node['@type'] ?? [] ), true ) );
		if ( null === $target ) {
			return $graph;
		}

		foreach ( [ 'about', 'mentions' ] as $property ) {
			if ( ! $terms[ $property ] ) {
				continue;
			}

			$existing = $graph[ $target ][ $property ] ?? [];
			// A single node (an associative array) becomes the first list item.
			$existing = ( $existing && ! array_is_list( $existing ) ) ? [ $existing ] : (array) $existing;

			$graph[ $target ][ $property ] = array_merge( $existing, array_map( [ $this, 'build_term_reference' ], $terms[ $property ] ) );
		}

		return $graph;
	}

	/**
	 * A DefinedTerm reference for use on another page's graph: enough to stand
	 * alone, with the same `@id` as the term page's full DefinedTerm node.
	 *
	 * @param int $term_id Glossary term post ID.
	 * @return array
	 */
	public function build_term_reference( $term_id ) {
		$node = [
			'@type' => 'DefinedTerm',
			'@id'   => self::term_id( $term_id ),
			'name'  => wp_strip_all_tags( get_the_title( $term_id ) ),
			'url'   => get_permalink( $term_id ),
		];

		$index_page_id = GlossaryIndex::get_index_page_id();
		if ( $index_page_id ) {
			$node['inDefinedTermSet'] = [ '@id' => self::set_id( $index_page_id ) ];
		}

		return $node;
	}

	/**
	 * Index of the first graph node matching a test, or null.
	 *
	 * @param array    $graph Graph nodes.
	 * @param callable $test  Receives a node, returns bool.
	 * @return int|string|null
	 */
	private static function find_node( $graph, $test ) {
		foreach ( $graph as $index => $node ) {
			if ( is_array( $node ) && $test( $node ) ) {
				return $index;
			}
		}

		return null;
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

		$alternate_names = array_values( array_filter( array_merge( [ Glossary::get_abbreviation( $post_id ) ], Glossary::get_synonyms( $post_id ) ) ) );
		if ( $alternate_names ) {
			$node['alternateName'] = $alternate_names;
		}

		$index_page_id = GlossaryIndex::get_index_page_id();
		if ( $index_page_id ) {
			$node['inDefinedTermSet'] = [ '@id' => self::set_id( $index_page_id ) ];
		}

		$subject_of = array_map( [ self::class, 'build_work_reference' ], Glossary::get_subject_of_ids( $post_id ) );
		if ( $subject_of ) {
			$node['subjectOf'] = $subject_of;
		}

		return array_filter( $node );
	}

	/**
	 * A reference to a post the term is the subject of, matching the node
	 * output on that post's own page: a dated event's Event (`#event`, from
	 * EventSchema); otherwise its Article (`#article`) when Yoast's Schema
	 * settings (or ArticleSchema) give the post type an article type;
	 * otherwise its WebPage.
	 *
	 * @param int $post_id A post of one of Glossary::LINKED_POST_TYPES.
	 * @return array
	 */
	public static function build_work_reference( $post_id ) {
		$titles      = get_option( 'wpseo_titles' );
		$type        = is_array( $titles ) ? ( $titles[ 'schema-article-type-' . get_post_type( $post_id ) ] ?? 'None' ) : 'None';
		$hash        = ( $type && 'None' !== $type ) ? '#article' : '';
		$url         = get_permalink( $post_id );
		$schema_base = self::yoast_schema_base( $post_id ) ?: $url;

		if ( 'events' === get_post_type( $post_id ) && EventSchema::has_event( $post_id ) ) {
			$type = 'Event';
			$hash = '#event';
		}

		return [
			'@type' => $hash ? $type : 'WebPage',
			'@id'   => $schema_base . $hash,
			'name'  => wp_strip_all_tags( get_the_title( $post_id ) ),
			'url'   => $url,
		];
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
	 * The URL Yoast builds a post's schema `@id`s from (its main schema ID is
	 * the indexable's permalink; the Article is that plus `#article`), so
	 * references here match the nodes on the post's own page. Empty when
	 * Yoast isn't available.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private static function yoast_schema_base( $post_id ) {
		if ( ! function_exists( 'YoastSEO' ) ) {
			return '';
		}

		$meta = YoastSEO()->meta->for_post( $post_id );

		return $meta ? (string) $meta->permalink : '';
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
