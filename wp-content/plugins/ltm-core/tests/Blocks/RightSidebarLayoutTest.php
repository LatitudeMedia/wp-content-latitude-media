<?php
/**
 * Tests for the "Right Sidebar Layout" core/columns variation's PHP enqueue.
 *
 * This variation has no block.json/block type of its own — the JS-side
 * registerBlockVariation() call is out of PHPUnit's reach — so the only real
 * PHP-testable surface is that enqueue_editor_assets() enqueues the compiled
 * script. Reads from committed build/ output
 * (build/right-sidebar-layout/index.asset.php); if src/ changed without a
 * matching `npm run build`, this no-ops rather than catching that.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\RightSidebarLayout
 */
class RightSidebarLayoutTest extends WP_UnitTestCase {

	public function test_editor_script_is_enqueued() {
		( new \LTMCore\Blocks\RightSidebarLayout() )->enqueue_editor_assets();

		$this->assertTrue( wp_script_is( 'ltm-right-sidebar-layout', 'enqueued' ) );
	}
}
