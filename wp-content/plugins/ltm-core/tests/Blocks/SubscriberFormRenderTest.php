<?php
/**
 * Tests for the latitudemedia/subscriber-form server-side render.
 *
 * render.php is attribute-driven only (no post/taxonomy lookups), so unlike
 * TitleBlockRenderTest there's no need to set up a current post.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_UnitTestCase;

class SubscriberFormRenderTest extends WP_UnitTestCase {

	private function render( array $attrs = [] ): string {
		return render_block(
			[
				'blockName'    => 'latitudemedia/subscriber-form',
				'attrs'        => $attrs,
				'innerBlocks'  => [],
				'innerHTML'    => '',
				'innerContent' => [],
			]
		);
	}

	public function test_wrapper_carries_form_block_and_default_layout_classes() {
		$output = $this->render();

		$this->assertStringContainsString( 'subscriber-form', $output );
		$this->assertStringContainsString( 'form-block', $output );
		$this->assertStringContainsString( 'layout-square', $output );
	}

	public function test_layout_value_outside_enum_falls_back_to_default() {
		// "square" is the only declared enum choice in block.json; render_block()
		// sanitizes attrs against the schema, so an invalid value is dropped to the
		// registered default rather than passed through as "layout-wide".
		$output = $this->render( [ 'layout' => 'wide' ] );

		$this->assertStringContainsString( 'layout-square', $output );
		$this->assertStringNotContainsString( 'layout-wide', $output );
	}

	public function test_title_renders_as_heading_when_set() {
		$output = $this->render( [ 'title' => 'Get Latitude Media in your inbox' ] );

		$this->assertStringContainsString( '<h2 class="form-title">Get Latitude Media in your inbox</h2>', $output );
	}

	public function test_title_absent_when_unset() {
		$output = $this->render();

		$this->assertStringNotContainsString( '<h2', $output );
	}

	public function test_title_is_escaped() {
		$output = $this->render( [ 'title' => '<script>alert(1)</script>' ] );

		$this->assertStringNotContainsString( '<script>alert(1)</script>', $output );
		$this->assertStringContainsString( '&lt;script&gt;', $output );
	}

	public function test_disclaimer_renders_as_paragraph_when_set() {
		$output = $this->render( [ 'disclaimer' => 'Latitude Media provides rigorous coverage.' ] );

		$this->assertStringContainsString( '<p style="color:#fff;text-align:center;">Latitude Media provides rigorous coverage.</p>', $output );
	}

	public function test_disclaimer_absent_when_unset() {
		$output = $this->render();

		$this->assertStringNotContainsString( '<p', $output );
	}

	public function test_disclaimer_is_escaped() {
		$output = $this->render( [ 'disclaimer' => '<script>alert(1)</script>' ] );

		$this->assertStringNotContainsString( '<script>alert(1)</script>', $output );
		$this->assertStringContainsString( '&lt;script&gt;', $output );
	}

	public function test_embed_code_renders_unescaped() {
		$embed  = '<script id="hbspt-script" data-src="//js.hsforms.net/forms/embed/v2.js"></script>';
		$output = $this->render( [ 'embedCode' => $embed ] );

		$this->assertStringContainsString( $embed, $output );
	}

	public function test_embed_code_absent_when_unset() {
		$output = $this->render();

		$this->assertStringNotContainsString( '<script', $output );
	}

	public function test_no_placeholders_on_the_frontend() {
		$output = $this->render();

		$this->assertStringNotContainsString( 'is-placeholder', $output );
	}

	/**
	 * The editor preview is detected via the REST_REQUEST constant, which
	 * can't be undefined again, so each editor-preview test runs in its own process.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_editor_preview_shows_placeholders_for_empty_title_and_disclaimer() {
		define( 'REST_REQUEST', true );

		$output = $this->render();

		$this->assertMatchesRegularExpression( '/<h2 class="form-title is-placeholder">[^<]+<\/h2>/', $output );
		$this->assertMatchesRegularExpression( '/<p class="is-placeholder"[^>]*>[^<]+<\/p>/', $output );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_editor_preview_has_no_placeholders_when_fields_are_set() {
		define( 'REST_REQUEST', true );

		$output = $this->render(
			[
				'title'      => 'Get Latitude Media in your inbox',
				'disclaimer' => 'Latitude Media provides rigorous coverage.',
				'embedCode'  => '<div>form</div>',
			]
		);

		$this->assertStringNotContainsString( 'is-placeholder', $output );
	}
}
