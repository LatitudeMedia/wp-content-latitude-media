<?php
/**
 * Tests for the Thematic Pages post type.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

use LTMCore\PostTypes\ThematicPages;
use WP_Scripts;
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

	/**
	 * Resets the global script registry and current screen so inline-script
	 * assertions below aren't polluted by state left over from other tests.
	 */
	private function reset_wp_scripts() {
		global $wp_scripts;
		$wp_scripts = new WP_Scripts(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		wp_register_script( 'wp-edit-post', '', array(), false, true );
	}

	/**
	 * The "Display in Thematic Page" taxonomy panel only makes sense on
	 * regular posts (it says which thematic page a post belongs to), so it
	 * must be removed from the Thematic Page's own editor.
	 */
	public function test_hide_taxonomy_panel_removes_panel_on_thematic_pages_screen() {
		$this->reset_wp_scripts();

		global $current_screen;
		$original_screen = $current_screen;
		set_current_screen( 'thematic-pages' );

		( new ThematicPages() )->hide_taxonomy_panel();

		$current_screen = $original_screen;

		$inline = implode( '', (array) wp_scripts()->get_data( 'wp-edit-post', 'after' ) );

		$this->assertStringContainsString(
			"removeEditorPanel( 'taxonomy-panel-thematic-page-types' )",
			$inline
		);
	}

	/**
	 * The panel must stay untouched on the regular Post editor, where it's
	 * how editors tag a post with the thematic page it displays in.
	 */
	public function test_hide_taxonomy_panel_leaves_post_screen_alone() {
		$this->reset_wp_scripts();

		global $current_screen;
		$original_screen = $current_screen;
		set_current_screen( 'post' );

		( new ThematicPages() )->hide_taxonomy_panel();

		$current_screen = $original_screen;

		$this->assertEmpty( wp_scripts()->get_data( 'wp-edit-post', 'after' ) );
	}
}
