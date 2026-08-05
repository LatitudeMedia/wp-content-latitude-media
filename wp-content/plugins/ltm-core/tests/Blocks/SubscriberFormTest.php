<?php
/**
 * Tests for the latitudemedia/subscriber-form block registration.
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

class SubscriberFormTest extends WP_UnitTestCase {

	public function test_block_is_registered() {
		$registry = WP_Block_Type_Registry::get_instance();

		$this->assertTrue( $registry->is_registered( 'latitudemedia/subscriber-form' ) );
	}

	public function test_block_is_dynamic() {
		$registry   = WP_Block_Type_Registry::get_instance();
		$block_type = $registry->get_registered( 'latitudemedia/subscriber-form' );

		$this->assertTrue( $block_type->is_dynamic() );
	}

	public function test_declared_attribute_defaults() {
		$registry   = WP_Block_Type_Registry::get_instance();
		$block_type = $registry->get_registered( 'latitudemedia/subscriber-form' );

		$this->assertSame( '', $block_type->attributes['title']['default'] );
		$this->assertSame( '', $block_type->attributes['disclaimer']['default'] );
		$this->assertSame( '', $block_type->attributes['embedCode']['default'] );
		$this->assertSame( 'square', $block_type->attributes['layout']['default'] );
	}
}
