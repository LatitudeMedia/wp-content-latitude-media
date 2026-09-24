<?php
/**
 * Tests for the acf/styled-button-block migration from the theme.
 *
 * Deliberately makes NO render_block() call -- ACF Pro's block-meta handling
 * only resolves reliably for the FIRST ACF block render in a PHPUnit process
 * (see EventPreviewBlockRenderTest's docblock), and that slot is taken. Render
 * coverage lives in specs/frontend/styled-button-block.spec.js.
 *
 * The field-key assertions are the point: ACF resolves a block's values by
 * field key, so a mistyped key orphans every existing instance silently. This
 * block is also named directly by the theme's still-unmigrated info-cta-block,
 * so the block name matters just as much as the keys.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\StyledButton
 */
class StyledButtonTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		// Not a typo: the theme exported this group under a `field_`-prefixed
		// key rather than the usual `group_` one, and ACF keys groups by
		// whatever string it is given. Changing it orphans existing content.
		$group = acf_get_field_group( 'field_673b3369c9f5e' );

		$this->assertIsArray( $group, 'Field group field_673b3369c9f5e is not registered.' );
		$this->assertSame( 'Styled button block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/styled-button-block',
			],
			$group['location'][0][0]
		);

		$fields = array_map(
			static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
			acf_get_fields( $group )
		);

		$this->assertSame(
			[
				[ 'field_673b3369c9f5f', '', 'message' ],
				[ 'field_673b3369c9f60', 'button', 'link' ],
			],
			$fields
		);
	}

	/**
	 * The link field must keep returning an array -- render.php reads
	 * $button['url'], ['title'] and ['target'] off it.
	 */
	public function test_button_field_returns_an_array() {
		$field = acf_get_field( 'field_673b3369c9f60' );

		$this->assertIsArray( $field );
		$this->assertSame( 'array', $field['return_format'] );
	}

	public function test_block_is_registered_under_its_original_name() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/styled-button-block' );

		$this->assertNotNull( $block, 'acf/styled-button-block is not registered.' );

		// No `parent` restriction: the theme imposed none, and the block is
		// used both standalone and nested (info-cta-block, image-and-text).
		$this->assertNull( $block->parent );
	}

	/**
	 * The theme's info-cta-block (template-parts/blocks/common/info-cta-block.php)
	 * and the migrated image-and-text block both name this block in their
	 * InnerBlocks templates. Block names resolve globally, so the move works
	 * -- but only as long as the name is unchanged.
	 */
	public function test_existing_content_still_parses_as_this_block() {
		$content = '<!-- wp:acf/styled-button-block {"name":"acf/styled-button-block","data":{"button":{"title":"Learn more","url":"https://example.com","target":""},"_button":"field_673b3369c9f60"},"mode":"preview"} /-->';

		$blocks = array_values( array_filter( parse_blocks( $content ), static fn( $b ) => null !== $b['blockName'] ) );

		$this->assertCount( 1, $blocks );
		$this->assertSame( 'acf/styled-button-block', $blocks[0]['blockName'] );

		$field = acf_get_field( $blocks[0]['attrs']['data']['_button'] );

		$this->assertIsArray( $field, 'The stored field key no longer resolves to a field.' );
		$this->assertSame( 'button', $field['name'] );
	}

	/**
	 * Image and text's text slot is a curated inserter, and the styled button
	 * is on the list. Asserted against render.php's source rather than a
	 * render, for the reason in this file's docblock.
	 */
	public function test_image_and_text_allows_the_styled_button_as_an_inner_block() {
		$render = file_get_contents( LTM_CORE_DIR . '/src/image-and-text/render.php' );

		$this->assertMatchesRegularExpression(
			'/allowedBlocks=/',
			$render,
			'Image and text no longer restricts its inner blocks.'
		);
		$this->assertStringContainsString(
			"[ 'core/heading', 'core/paragraph', 'core/list', 'acf/styled-button-block' ]",
			$render
		);
	}
}
