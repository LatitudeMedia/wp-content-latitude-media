<?php
namespace LTMCore\Seo;

use LTMCore\Blocks\GlossaryIndex;
use LTMCore\PostTypes\Glossary;

/**
 * Yoast SEO settings for the glossary that live in code rather than the
 * database, so they deploy with the plugin:
 *
 * - title / meta description / social templates for glossary terms, plus a
 *   `%%glossary_abbreviation%%` replacement variable used in them;
 * - glossary terms kept indexable (and so in the XML sitemap);
 * - breadcrumbs of Home › Glossary › Term (visible trail and schema);
 * - the glossary index page's sitemap `lastmod` following its newest term.
 *
 * The schema.org DefinedTerm / DefinedTermSet nodes are in GlossarySchema.
 *
 * @package LTMCore
 */
class GlossarySeo {

	/**
	 * Yoast "Search appearance" values forced for the glossary post type. The
	 * Yoast settings screen can't change these; per-post SEO title and
	 * description overrides still win, since Yoast stores those per post.
	 *
	 * @var array<string, string|bool>
	 */
	const TITLE_SETTINGS = [
		'title-glossary'              => '%%title%%%%glossary_abbreviation%%: Definition & Meaning %%sep%% %%sitename%%',
		'metadesc-glossary'           => '%%excerpt%%',
		'social-title-glossary'       => '%%title%%%%glossary_abbreviation%%: Definition & Meaning',
		'social-description-glossary' => '%%excerpt%%',
		'noindex-glossary'            => false,
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'option_wpseo_titles', array( $this, 'force_title_settings' ) );
		add_action( 'wpseo_register_extra_replacements', array( $this, 'register_replacement_vars' ) );
		add_filter( 'wpseo_breadcrumb_links', array( $this, 'add_index_page_crumb' ) );
		add_filter( 'wpseo_sitemap_entry', array( $this, 'index_page_lastmod' ), 10, 3 );
	}

	/**
	 * Overlays TITLE_SETTINGS onto Yoast's `wpseo_titles` option.
	 *
	 * @param mixed $titles The stored option.
	 * @return mixed
	 */
	public function force_title_settings( $titles ) {
		return is_array( $titles ) ? array_merge( $titles, self::TITLE_SETTINGS ) : $titles;
	}

	/**
	 * Registers `%%glossary_abbreviation%%`.
	 */
	public function register_replacement_vars() {
		wpseo_register_var_replacement(
			'glossary_abbreviation',
			array( $this, 'replace_abbreviation' ),
			'advanced',
			__( 'The glossary term\'s abbreviation in brackets with a leading space, e.g. " (BESS)". Empty when the term has none.', 'ltm' )
		);
	}

	/**
	 * Value of `%%glossary_abbreviation%%`: " (BESS)", or '' when none.
	 *
	 * @param string $var  The variable name.
	 * @param object $args Yoast's replacement args (the post being rendered).
	 * @return string
	 */
	public function replace_abbreviation( $var, $args ) {
		$post_id = (int) ( $args->ID ?? 0 );
		if ( ! $post_id || 'glossary' !== get_post_type( $post_id ) ) {
			return '';
		}

		$abbreviation = Glossary::get_abbreviation( $post_id );

		return '' === $abbreviation ? '' : ' (' . $abbreviation . ')';
	}

	/**
	 * Inserts the glossary index page after Home on glossary term pages, so
	 * breadcrumbs read Home › Glossary › Term. The same links feed Yoast's
	 * BreadcrumbList schema.
	 *
	 * @param array $links Yoast's breadcrumb links.
	 * @return array
	 */
	public function add_index_page_crumb( $links ) {
		$index_page_id = GlossaryIndex::get_index_page_id();
		if ( ! $index_page_id || ! is_singular( 'glossary' ) || ! $links ) {
			return $links;
		}

		$crumb = [
			'url'  => get_permalink( $index_page_id ),
			'text' => get_the_title( $index_page_id ),
			'id'   => $index_page_id,
		];
		array_splice( $links, 1, 0, [ $crumb ] );

		return $links;
	}

	/**
	 * The index page lists every term, so its content changes whenever a term
	 * does. Reports the newest term's modified date as the page's `lastmod`
	 * when that's later than the page's own.
	 *
	 * @param array  $url  Sitemap entry; `mod` is a GMT date string.
	 * @param string $type Entry type ('post' for post-type sitemaps).
	 * @param object $post The post the entry is for.
	 * @return array
	 */
	public function index_page_lastmod( $url, $type, $post ) {
		if ( 'post' !== $type || empty( $post->ID ) || (int) $post->ID !== GlossaryIndex::get_index_page_id() ) {
			return $url;
		}

		$newest = get_posts(
			[
				'post_type'      => 'glossary',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'no_found_rows'  => true,
			]
		);

		if ( $newest && ( empty( $url['mod'] ) || strtotime( $newest[0]->post_modified_gmt ) > strtotime( $url['mod'] ) ) ) {
			$url['mod'] = $newest[0]->post_modified_gmt;
		}

		return $url;
	}
}
