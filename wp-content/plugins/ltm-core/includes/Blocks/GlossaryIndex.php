<?php
namespace LTMCore\Blocks;

/**
 * Data helpers for the Glossary index block (latitudemedia/glossary-index),
 * used by its render.php, the glossary term template in the theme, and
 * \LTMCore\Seo\GlossarySchema.
 *
 * Static-only: autoloads on first use, nothing to instantiate.
 *
 * @package LTMCore
 */
class GlossaryIndex {

	/**
	 * Block name, as stored in post content.
	 *
	 * @var string
	 */
	const BLOCK_NAME = 'latitudemedia/glossary-index';

	/**
	 * Per-request cache for get_index_page_id().
	 *
	 * @var int|null
	 */
	private static $index_page_id = null;

	/**
	 * All published glossary terms, alphabetical, grouped by the uppercase
	 * first letter of the title. Titles that don't start with A–Z go under '#',
	 * which sorts first.
	 *
	 * @return array<string, \WP_Post[]>
	 */
	public static function get_grouped_terms() {
		$posts = get_posts(
			[
				'post_type'        => 'glossary',
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'no_found_rows'    => true,
				'suppress_filters' => false,
			]
		);

		$groups = [];
		foreach ( $posts as $post ) {
			$letter = strtoupper( substr( remove_accents( $post->post_title ), 0, 1 ) );
			if ( ! preg_match( '/^[A-Z]$/', $letter ) ) {
				$letter = '#';
			}
			$groups[ $letter ][] = $post;
		}

		// '#' is ASCII 35, so it sorts ahead of 'A'.
		ksort( $groups, SORT_STRING );

		return $groups;
	}

	/**
	 * ID of the published page that holds the glossary index block, or 0 if
	 * there isn't one. That page is the glossary's DefinedTermSet.
	 *
	 * @return int
	 */
	public static function get_index_page_id() {
		if ( null === self::$index_page_id ) {
			global $wpdb;

			self::$index_page_id = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND post_content LIKE %s ORDER BY ID ASC LIMIT 1",
					'%' . $wpdb->esc_like( '<!-- wp:' . self::BLOCK_NAME ) . '%'
				)
			);
		}

		return self::$index_page_id;
	}

	/**
	 * Clears the get_index_page_id() cache. Only tests need this.
	 */
	public static function reset_cache() {
		self::$index_page_id = null;
	}
}
