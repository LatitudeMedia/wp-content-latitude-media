<?php
/**
 * Tests for the latitudemedia/category-post-listing block registration.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\Title
 */
class CategoryPostListingTest extends WP_UnitTestCase {

	public function test_block_is_registered() {
		$registry = WP_Block_Type_Registry::get_instance();

		$this->assertTrue( $registry->is_registered( 'latitudemedia/category-post-listing' ) );
	}
}
