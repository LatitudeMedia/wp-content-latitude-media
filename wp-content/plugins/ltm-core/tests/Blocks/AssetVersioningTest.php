<?php
/**
 * Tests for block stylesheet cache-busting.
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
 * @covers \LTMCore\Blocks\AssetVersioning
 */
class AssetVersioningTest extends WP_UnitTestCase {

	public function test_block_style_version_follows_built_css_mtime() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/event-agenda-v2-block' );
		$style = wp_styles()->registered[ $block->style_handles[0] ];

		$this->assertSame( (string) filemtime( LTM_CORE_DIR . '/build/event-agenda-v2-block/style-index.css' ), $style->ver );
	}

	public function test_version_untouched_for_blocks_outside_this_plugin() {
		$versioning = new \LTMCore\Blocks\AssetVersioning();
		$metadata   = [ 'file' => ABSPATH . 'wp-content/plugins/other/build/x/block.json', 'version' => '1.2.3' ];

		$this->assertSame( '1.2.3', $versioning->version_from_build_files( $metadata )['version'] );
	}
}
