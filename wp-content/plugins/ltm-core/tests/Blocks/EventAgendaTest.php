<?php
/**
 * Tests for the acf/event-agenda-block migration from the theme.
 *
 * Deliberately makes NO render_block() call. ACF Pro's block-meta handling
 * does not tear down cleanly between successive ACF block renders in one
 * PHPUnit process (see EventPreviewBlockRenderTest's docblock), so only the
 * first such render in the suite is trustworthy -- that slot is already taken.
 * Everything here is assertable without rendering: that the field group
 * survived the move intact, and that the block registered from the committed
 * build/ manifest.
 *
 * The field-key assertions are the point of this file: ACF resolves a block's
 * values by field key, so a mistyped key orphans every existing instance
 * silently -- no error, just empty fields. Only four published events still use
 * this block (Transition-AI 2024 / 2025, Transition-AI: Boston / New York) and
 * all four are past events nobody is editing, so a silent break would go
 * unnoticed until someone visited the page.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\EventAgenda
 */
class EventAgendaTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_6745d58b04065' );

		$this->assertIsArray( $group, 'Field group group_6745d58b04065 is not registered.' );
		$this->assertSame( 'Event agenda block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/event-agenda-block',
			],
			$group['location'][0][0]
		);

		$fields = acf_get_fields( $group );

		$this->assertSame(
			[
				[ 'field_6744a1d23baa4', '', 'message' ],
				[ 'field_6745d5914809b', 'title', 'text' ],
				[ 'field_6745d9e1987af', 'schedule', 'repeater' ],
				[ 'field_6744a1d23baa5', 'display', 'true_false' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				$fields
			)
		);
	}

	/**
	 * The repeater's sub-fields carry their own keys, and the stored data keys
	 * them as `schedule_{row}_{name}` -- so both the keys and the names have to
	 * match the theme's group exactly. The accordion and the two tabs are
	 * nameless layout fields but still occupy positions in the list.
	 */
	public function test_schedule_sub_field_keys_survived_the_move() {
		$fields = acf_get_fields( acf_get_field_group( 'group_6745d58b04065' ) );

		$repeater = current(
			array_filter( $fields, static fn( $field ) => 'schedule' === $field['name'] )
		);

		$this->assertIsArray( $repeater, 'The schedule repeater is missing.' );
		$this->assertSame( 'row', $repeater['layout'] );

		$this->assertSame(
			[
				[ 'field_6745e4dd03a8e', '', 'accordion' ],
				[ 'field_6745e334c6af8', '', 'tab' ],
				[ 'field_6745de12af52d', 'time', 'text' ],
				[ 'field_6745de0caf52c', 'title', 'text' ],
				[ 'field_6745e13b47d00', '', 'tab' ],
				[ 'field_6745de97eafa4', 'description', 'textarea' ],
				[ 'field_6745ded1af1a3', 'speakers', 'post_object' ],
				[ 'field_6745def4af1a4', 'image', 'image' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				$repeater['sub_fields']
			)
		);

		$by_name = array_column( $repeater['sub_fields'], null, 'name' );

		// render.php passes $item['speakers'] straight into WP_Query's post__in and
		// $item['image'] into wp_get_attachment_image(), so both must stay IDs.
		$this->assertSame( 'id', $by_name['speakers']['return_format'] );
		$this->assertSame( [ 'speakers' ], array_values( $by_name['speakers']['post_type'] ) );
		$this->assertSame( 1, $by_name['speakers']['multiple'] );
		$this->assertSame( 'id', $by_name['image']['return_format'] );
	}

	/**
	 * Registration comes from the committed build/blocks-manifest.php via
	 * LTMCore\Blocks\Title::register_blocks(), so this also catches a build that
	 * was never regenerated after block.json changed.
	 */
	public function test_block_registers_without_styles() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/event-agenda-block' );

		$this->assertNotNull( $block, 'acf/event-agenda-block is not registered.' );

		// Unlike the other migrated event blocks this one registers no block
		// styles: the four live instances all render the `green` class hardcoded in
		// render.php, and the pages must keep looking exactly as they do.
		$this->assertEmpty( (array) $block->styles );
	}

	/**
	 * The agenda thumbnail size came along with the block (theme inc/media.php ->
	 * LTMCore\Blocks\EventAgenda). Renaming or resizing it would orphan every
	 * already-generated thumbnail file, which is recorded against the size name.
	 */
	public function test_agenda_image_size_is_registered() {
		$sizes = wp_get_additional_image_sizes();

		$this->assertArrayHasKey( 'event-agenda', $sizes );
		$this->assertSame( 552, $sizes['event-agenda']['width'] );
		$this->assertFalse( $sizes['event-agenda']['crop'] );
	}
}
