<?php
/**
 * Tests for the acf/event-speakers-block migration from the theme.
 *
 * Deliberately makes NO render_block() call -- see EventDescriptionTest's
 * docblock for why only the first ACF block render per PHPUnit process is
 * trustworthy. Real render coverage lives in
 * specs/frontend/event-speakers-block.spec.js.
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
 * @covers \LTMCore\Blocks\EventSpeakers
 */
class EventSpeakersTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_674597e182742' );

		$this->assertIsArray( $group, 'Field group group_674597e182742 is not registered.' );
		$this->assertSame( 'Event speakers block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/event-speakers-block',
			],
			$group['location'][0][0]
		);

		$fields = acf_get_fields( $group );

		$this->assertSame(
			[
				[ 'field_67449c4ecdcd9', '', 'message' ],
				[ 'field_674597fdb07ad', 'title', 'text' ],
				[ 'field_67459804b07ae', 'speakers', 'relationship' ],
				[ 'field_67449c4ecdcda', 'display', 'true_false' ],
				[ 'field_6746402d35367', 'show_read_more_button', 'true_false' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				$fields
			)
		);
	}

	public function test_speakers_relationship_still_points_at_the_theme_post_type() {
		// The speakers CPT stays registered by the theme for now; the block
		// reads it across that boundary, so the relationship field must keep
		// targeting it and keep returning plain IDs (render.php feeds them
		// straight into WP_Query's post__in).
		$field = acf_get_field( 'field_67459804b07ae' );

		$this->assertIsArray( $field );
		$this->assertSame( [ 'speakers' ], $field['post_type'] );
		$this->assertSame( 'id', $field['return_format'] );
	}

	public function test_block_is_registered_without_its_own_scripts_or_styles() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/event-speakers-block' );

		$this->assertNotNull( $block, 'acf/event-speakers-block is not registered.' );

		// Modal behaviour stays in the theme's global custom.js (it is shared
		// with event-sponsors, event-partners, popup-modal and reviews-popup),
		// and the layout/modal CSS moved into the theme's global base stylesheet.
		// So this block ships neither -- if that changes, revisit both decisions.
		$this->assertEmpty( $block->view_script_handles );
		$this->assertEmpty( $block->script_handles );
		$this->assertEmpty( $block->style_handles );
	}
}
