<?php
/**
 * Tests for the acf/recap-video-block migration from the theme.
 *
 * Deliberately makes NO render_block() call. ACF Pro's block-meta handling
 * does not tear down cleanly between successive ACF block renders in one
 * PHPUnit process (see EventPreviewBlockRenderTest's docblock), so only the
 * first such render in the suite is trustworthy -- that slot is already taken.
 * Render coverage lives in specs/frontend/recap-video-block.spec.js.
 *
 * The field-key assertions are the point of this file: ACF resolves a block's
 * values by field key, so a mistyped key orphans every existing instance
 * silently -- no error, just empty fields.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\RecapVideo
 */
class RecapVideoTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_6759d1f713a46' );

		$this->assertIsArray( $group, 'Field group group_6759d1f713a46 is not registered.' );
		$this->assertSame( 'Recap video block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/recap-video-block',
			],
			$group['location'][0][0]
		);

		$this->assertSame(
			[
				[ 'field_6759d0706f204', '', 'message' ],
				[ 'field_6759d1f983e50', 'title', 'text' ],
				[ 'field_6759d1fe83e51', 'video', 'oembed' ],
				[ 'field_6759d0706f205', 'display', 'true_false' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				acf_get_fields( $group )
			)
		);
	}

	/**
	 * Registration comes from the committed build/blocks-manifest.php, so this
	 * also catches a build that was never regenerated after block.json changed.
	 * Padding support must stay on: 12 live instances carry a
	 * style.spacing.padding attribute that the wrapper renders inline.
	 */
	public function test_block_registers_with_padding_support() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/recap-video-block' );

		$this->assertNotNull( $block, 'acf/recap-video-block is not registered.' );
		$this->assertTrue( $block->supports['spacing']['padding'] );
	}
}
