<?php
/**
 * Tests for block registration and the title-block inserter restriction.
 *
 * Reads from the committed build/ output (build/blocks-manifest.php), not
 * src/ — if src/ changed without a matching `npm run build`, these
 * assertions test stale markup/attributes rather than catching that.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\Title
 */
class TitleTest extends WP_UnitTestCase {

	public function test_both_blocks_are_registered() {
		$registry = WP_Block_Type_Registry::get_instance();

		$this->assertTrue( $registry->is_registered( 'latitudemedia/title-block' ) );
		$this->assertTrue( $registry->is_registered( 'latitudemedia/featured-post-block' ) );
	}

	public function test_allowed_block_types_unmodified_when_no_post_context() {
		$title = new \LTMCore\Blocks\Title();

		$context = (object) [ 'post' => null ];
		$result  = $title->restrict_to_thematic_pages( [ 'core/paragraph', 'latitudemedia/title-block' ], $context );

		$this->assertSame( [ 'core/paragraph', 'latitudemedia/title-block' ], $result );
	}

	public function test_allowed_block_types_unmodified_on_thematic_pages() {
		$title = new \LTMCore\Blocks\Title();

		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages' ] );
		$context = (object) [ 'post' => get_post( $page_id ) ];

		$result = $title->restrict_to_thematic_pages( [ 'core/paragraph', 'latitudemedia/title-block' ], $context );

		$this->assertSame( [ 'core/paragraph', 'latitudemedia/title-block' ], $result );
	}

	public function test_title_block_removed_from_explicit_allowed_list_on_other_post_types() {
		$title = new \LTMCore\Blocks\Title();

		$post_id = self::factory()->post->create( [ 'post_type' => 'post' ] );
		$context = (object) [ 'post' => get_post( $post_id ) ];

		$result = $title->restrict_to_thematic_pages( [ 'core/paragraph', 'latitudemedia/title-block' ], $context );

		$this->assertSame( [ 'core/paragraph' ], array_values( $result ) );
	}

	public function test_title_block_excluded_when_allowed_list_expands_from_true() {
		$title = new \LTMCore\Blocks\Title();

		$post_id = self::factory()->post->create( [ 'post_type' => 'post' ] );
		$context = (object) [ 'post' => get_post( $post_id ) ];

		$result = $title->restrict_to_thematic_pages( true, $context );

		$this->assertIsArray( $result );
		$this->assertNotContains( 'latitudemedia/title-block', $result );
		$this->assertContains( 'latitudemedia/featured-post-block', $result );
	}
}
