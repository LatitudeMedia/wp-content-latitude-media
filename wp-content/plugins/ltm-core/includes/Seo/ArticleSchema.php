<?php
namespace LTMCore\Seo;

/**
 * Yoast SEO Article schema for industry news and resources.
 *
 * Their Yoast "Article type" is set here in code (like GlossarySeo's title
 * settings), so it deploys with the plugin and the Yoast settings screen
 * can't change it. Neither post type supports `author`, and Yoast skips the
 * Article for post types that don't, so the Article is forced on, and its
 * author (which would otherwise be the post's WordPress user) is set to an
 * Organization:
 *
 * - resources: the sponsor (Resource options → Sponsor), when set;
 * - industry news: the Company Name field, when set;
 * - otherwise: the site's own Organization (Latitude Media).
 *
 * @package LTMCore
 */
class ArticleSchema {

	/**
	 * Yoast article type per post type.
	 *
	 * @var array<string, string>
	 */
	const ARTICLE_TYPES = [
		'industry-news' => 'NewsArticle',
		'resources'     => 'NewsArticle',
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'option_wpseo_titles', array( $this, 'force_article_types' ) );
		add_filter( 'wpseo_schema_needs_article', array( $this, 'needs_article' ) );
		add_filter( 'wpseo_schema_article', array( $this, 'set_author' ), 10, 2 );
	}

	/**
	 * Overlays ARTICLE_TYPES onto Yoast's `wpseo_titles` option.
	 *
	 * @param mixed $titles The stored option.
	 * @return mixed
	 */
	public function force_article_types( $titles ) {
		if ( ! is_array( $titles ) ) {
			return $titles;
		}

		foreach ( self::ARTICLE_TYPES as $post_type => $article_type ) {
			$titles[ 'schema-article-type-' . $post_type ] = $article_type;
		}

		return $titles;
	}

	/**
	 * Outputs the Article on these post types despite their lack of `author`
	 * support.
	 *
	 * @param bool $is_needed Yoast's own decision.
	 * @return bool
	 */
	public function needs_article( $is_needed ) {
		return $is_needed || is_singular( array_keys( self::ARTICLE_TYPES ) );
	}

	/**
	 * Replaces the Article's author on these post types (see the class
	 * docblock).
	 *
	 * @param array  $data    Yoast's Article node.
	 * @param object $context Yoast's Meta_Tags_Context.
	 * @return array
	 */
	public function set_author( $data, $context ) {
		$post_id = (int) ( $context->id ?? 0 );
		if ( ! isset( self::ARTICLE_TYPES[ get_post_type( $post_id ) ] ) ) {
			return $data;
		}

		$author = self::get_author( $post_id ) ?: $context->site_represents_reference;
		if ( $author ) {
			$data['author'] = $author;
		} else {
			unset( $data['author'] );
		}

		return $data;
	}

	/**
	 * The resource's sponsor or the industry news item's company, as an
	 * Organization, or [] when it has none.
	 *
	 * @param int $post_id A resources or industry-news post.
	 * @return array
	 */
	public static function get_author( $post_id ) {
		if ( ! function_exists( 'get_field' ) ) {
			return [];
		}

		if ( 'resources' === get_post_type( $post_id ) ) {
			$sponsor_id = (int) get_field( 'sponsor', $post_id, false );
			if ( ! $sponsor_id || 'publish' !== get_post_status( $sponsor_id ) ) {
				return [];
			}

			$name = get_the_title( $sponsor_id );
			$url  = get_field( 'website_link', $sponsor_id );
		} else {
			$name = get_field( 'company_name', $post_id );
			$url  = get_field( 'company_link', $post_id );
		}

		$name = trim( wp_strip_all_tags( (string) $name ) );
		if ( '' === $name ) {
			return [];
		}

		return array_filter(
			[
				'@type' => 'Organization',
				'name'  => $name,
				'url'   => esc_url_raw( trim( (string) $url ) ),
			]
		);
	}
}
