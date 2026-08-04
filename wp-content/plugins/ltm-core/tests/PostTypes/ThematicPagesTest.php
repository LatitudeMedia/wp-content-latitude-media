<?php
/**
 * Tests for the Thematic Pages post type.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\PostTypes\ThematicPages
 */
class ThematicPagesTest extends WP_UnitTestCase {

	/**
	 * The post type is registered by the plugin's init hook.
	 */
	public function test_post_type_is_registered() {
		$this->assertTrue( post_type_exists( 'thematic-pages' ) );
	}

	/**
	 * The internal post type key stays `thematic-pages` while the public URL
	 * slug is `themes`. Renaming either independently is a regression.
	 */
	public function test_public_rewrite_slug_is_themes() {
		$post_type = get_post_type_object( 'thematic-pages' );

		$this->assertIsArray( $post_type->rewrite );
		$this->assertSame( 'themes', $post_type->rewrite['slug'] );
	}

	/**
	 * Editor-facing capabilities the blocks and REST endpoints depend on.
	 */
	public function test_post_type_supports_expected_features() {
		$this->assertTrue( post_type_supports( 'thematic-pages', 'title' ) );
		$this->assertTrue( post_type_supports( 'thematic-pages', 'editor' ) );
		$this->assertTrue( post_type_supports( 'thematic-pages', 'thumbnail' ) );
		$this->assertTrue( post_type_supports( 'thematic-pages', 'excerpt' ) );
		$this->assertTrue( post_type_supports( 'thematic-pages', 'revisions' ) );
	}

	/**
	 * The post type is exposed to the block editor / REST API but kept out of
	 * front-end search results.
	 */
	public function test_post_type_visibility_flags() {
		$post_type = get_post_type_object( 'thematic-pages' );

		$this->assertTrue( $post_type->public );
		$this->assertTrue( $post_type->show_in_rest );
		$this->assertTrue( $post_type->exclude_from_search );
		$this->assertFalse( $post_type->has_archive );
	}
}
