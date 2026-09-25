<?php
/**
 * Tests for the acf/event-description-block migration from the theme.
 *
 * Deliberately makes NO render_block() call. ACF Pro's block-meta handling
 * does not tear down cleanly between successive ACF block renders in one
 * PHPUnit process (see EventPreviewBlockRenderTest's docblock), so only the
 * first such render in the suite is trustworthy -- that slot is already
 * taken. Everything here is assertable without rendering: that the field
 * group survived the move intact, that the block registered from the
 * committed build/ manifest, and that the one piece of genuinely new logic
 * (layout selection) behaves. Real render coverage for both layouts lives in
 * specs/frontend/event-description-block.spec.js, where each page load is its
 * own PHP process and the ACF constraint does not apply.
 *
 * The field-key assertions are the point of this file: ACF resolves a block's
 * values by field key, so a mistyped key orphans every existing instance
 * silently -- no error, just empty fields.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use LTMCore\Blocks\EventDescription;
use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\EventDescription
 */
class EventDescriptionTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_674595857108f' );

		$this->assertIsArray( $group, 'Field group group_674595857108f is not registered.' );
		$this->assertSame( 'Event description block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/event-description-block',
			],
			$group['location'][0][0]
		);

		$fields = array_map(
			static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
			acf_get_fields( $group )
		);

		$this->assertSame(
			[
				[ 'field_674481518f9b4', '', 'message' ],
				[ 'field_674595b6064e9', 'title', 'text' ],
				[ 'field_674481518f9b5', 'display', 'true_false' ],
			],
			$fields
		);
	}

	/**
	 * The form layout is a toggle, not a block style, so the block must
	 * register the showForm attribute; the one mutually-exclusive is-style-*
	 * slot is spent on the colour themes instead.
	 *
	 * showForm having no default is load-bearing rather than an oversight: it
	 * is what makes an absent attribute mean "saved before the toggle existed"
	 * and so lets legacy is-style-type2 blocks keep their form with no data
	 * migration. Asserted explicitly so a well-meaning `"default": false` in
	 * block.json fails here instead of silently on live content.
	 */
	public function test_block_registers_the_show_form_attribute_and_theme_styles() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/event-description-block' );

		$this->assertNotNull( $block, 'acf/event-description-block is not registered.' );

		$this->assertSame(
			[ 'default', 'pink-theme', 'blue-theme' ],
			array_column( (array) $block->styles, 'name' )
		);

		$this->assertArrayHasKey( 'showForm', (array) $block->attributes );
		$this->assertSame( 'boolean', $block->attributes['showForm']['type'] );
		$this->assertArrayNotHasKey(
			'default',
			$block->attributes['showForm'],
			'showForm must not have a default; see EventDescription::shows_form().'
		);
	}

	/**
	 * @dataProvider provide_class_names
	 */
	public function test_theme_class( string $class_name, string $expected ) {
		$this->assertSame( $expected, EventDescription::theme_class( $class_name ) );
	}

	/**
	 * The bare tokens are what live content carries (Additional CSS classes)
	 * and what style.scss targets, so they must pass through untranslated --
	 * only the editor's is-style-* form gets mapped onto them.
	 */
	public static function provide_class_names(): array {
		return [
			'style: pink'            => [ 'is-style-pink-theme', 'pink-theme' ],
			'style: blue'            => [ 'is-style-blue-theme', 'blue-theme' ],
			'style: default'         => [ 'is-style-default', '' ],
			'style not first'        => [ 'is-style-type2 is-style-blue-theme', 'blue-theme' ],
			'extra whitespace'       => [ '  is-style-pink-theme  ', 'pink-theme' ],
			'live bare class'        => [ 'pink-theme', '' ],
			'no class'               => [ '', '' ],
			'unrelated class'        => [ 'pink-separator', '' ],
			'longer style name'      => [ 'is-style-pink-theme-compact', '' ],
			'substring is no match'  => [ 'not-is-style-pink-theme', '' ],
		];
	}

	/**
	 * @dataProvider provide_blocks
	 */
	public function test_shows_form( array $block, bool $expected ) {
		$this->assertSame( $expected, EventDescription::shows_form( $block ) );
	}

	/**
	 * The className rows are every string that actually occurs in live Event
	 * content, plus guards for the ways the theme's ltm_get_block_style()
	 * would have got it wrong; the showForm rows cover the tri-state.
	 */
	public static function provide_blocks(): array {
		return [
			// The toggle, once an editor has touched it.
			'showForm true'               => [ [ 'showForm' => true ], true ],
			'showForm false'              => [ [ 'showForm' => false ], false ],
			'showForm false beats legacy'  => [ [ 'showForm' => false, 'className' => 'is-style-type2' ], false ],
			'showForm true, no class'     => [ [ 'showForm' => true, 'className' => '' ], true ],
			// Never touched: fall back to the retired block style class.
			'showForm null is untouched'  => [ [ 'showForm' => null, 'className' => 'is-style-type2' ], true ],
			'no attributes at all'        => [ [], false ],
			'live: type2'                 => [ [ 'className' => 'is-style-type2' ], true ],
			'live: none'                  => [ [ 'className' => '' ], false ],
			'live: blue-theme'            => [ [ 'className' => 'blue-theme' ], false ],
			'live: pink-theme'            => [ [ 'className' => 'pink-theme' ], false ],
			'live: pink-separator'        => [ [ 'className' => 'pink-separator' ], false ],
			'live: two theme classes'     => [ [ 'className' => 'pink-separator pink-theme' ], false ],
			'style class not first'       => [ [ 'className' => 'blue-theme is-style-type2' ], true ],
			'extra whitespace'            => [ [ 'className' => '  is-style-type2   blue-theme ' ], true ],
			'longer style name'           => [ [ 'className' => 'is-style-type2-compact' ], false ],
			'different style'             => [ [ 'className' => 'is-style-default' ], false ],
			'substring is not a match'    => [ [ 'className' => 'not-is-style-type2' ], false ],
		];
	}
}
