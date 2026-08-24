<?php
/**
 * Tests for the plugin's taxonomies.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Taxonomies;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\Taxonomies\ThematicPageTypes
 * @covers \LTMCore\Taxonomies\PostSponsor
 */
class TaxonomiesTest extends WP_UnitTestCase {

	public function test_taxonomies_are_registered() {
		$this->assertTrue( taxonomy_exists( 'thematic-page-types' ) );
		$this->assertTrue( taxonomy_exists( 'sponsor' ) );
	}

	/**
	 * Both taxonomies attach to posts and thematic pages.
	 */
	public function test_taxonomies_are_attached_to_expected_post_types() {
		$this->assertTrue( is_object_in_taxonomy( 'post', 'thematic-page-types' ) );
		$this->assertTrue( is_object_in_taxonomy( 'thematic-pages', 'thematic-page-types' ) );

		$this->assertTrue( is_object_in_taxonomy( 'post', 'sponsor' ) );
		$this->assertTrue( is_object_in_taxonomy( 'thematic-pages', 'sponsor' ) );
	}

	public function test_thematic_page_types_is_hierarchical_and_in_rest() {
		$taxonomy = get_taxonomy( 'thematic-page-types' );

		$this->assertTrue( $taxonomy->hierarchical );
		$this->assertTrue( $taxonomy->show_in_rest );
	}

	/**
	 * The sponsor taxonomy is an internal relationship store, so it stays out
	 * of the admin UI and the REST API.
	 */
	public function test_sponsor_taxonomy_is_private() {
		$taxonomy = get_taxonomy( 'sponsor' );

		$this->assertFalse( $taxonomy->public );
		$this->assertFalse( $taxonomy->show_ui );
		$this->assertFalse( $taxonomy->show_in_rest );
		$this->assertFalse( $taxonomy->hierarchical );
	}

	/**
	 * Terms are managed by the sync code, never by hand — so term management
	 * caps are deliberately mapped to `do_not_allow` while assigning terms
	 * only needs `edit_posts`.
	 */
	public function test_term_management_capabilities_are_locked_down() {
		foreach ( [ 'thematic-page-types', 'sponsor' ] as $taxonomy_name ) {
			$capabilities = get_taxonomy( $taxonomy_name )->cap;

			$this->assertSame( 'do_not_allow', $capabilities->manage_terms, $taxonomy_name );
			$this->assertSame( 'do_not_allow', $capabilities->edit_terms, $taxonomy_name );
			$this->assertSame( 'do_not_allow', $capabilities->delete_terms, $taxonomy_name );
			$this->assertSame( 'edit_posts', $capabilities->assign_terms, $taxonomy_name );
		}
	}

	/**
	 * Even an administrator cannot manage these terms directly, because the
	 * taxonomy maps term management onto `do_not_allow`. Capabilities must be
	 * checked through the taxonomy's mapped cap strings — passing a taxonomy
	 * name as a second argument to current_user_can() is not a real meta-cap
	 * mapping and would silently always be false.
	 */
	public function test_administrator_cannot_manage_locked_terms() {
		$administrator = self::factory()->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $administrator );

		$capabilities = get_taxonomy( 'thematic-page-types' )->cap;

		$this->assertFalse( current_user_can( $capabilities->manage_terms ) );
		$this->assertFalse( current_user_can( $capabilities->edit_terms ) );
		$this->assertFalse( current_user_can( $capabilities->delete_terms ) );

		// Assigning terms is still allowed for editors of content.
		$this->assertTrue( current_user_can( $capabilities->assign_terms ) );
	}
}
