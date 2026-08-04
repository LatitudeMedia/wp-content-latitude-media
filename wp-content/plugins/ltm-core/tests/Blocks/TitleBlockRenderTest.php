<?php
/**
 * Tests for the latitudemedia/title-block server-side render.
 *
 * Renders the registered block directly (reading from the committed build/
 * output) against a `thematic-pages` post set as the current global post,
 * since render.php resolves the sponsor via get_the_ID().
 *
 * featured-post-block/render.php is deliberately not covered here — it pulls
 * in the theme's get_wrap_rows_from_template()/Page_Data()/post-item
 * template-part chain, a poor PHPUnit target. Full rendering is covered by
 * Playwright instead.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\Title
 */
class TitleBlockRenderTest extends WP_UnitTestCase {

	private function render( array $attrs = [] ): string {
		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages' ] );

		global $post;
		$post = get_post( $page_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		setup_postdata( $post );

		$output = render_block(
			[
				'blockName'    => 'latitudemedia/title-block',
				'attrs'        => $attrs,
				'innerBlocks'  => [],
				'innerHTML'    => '',
				'innerContent' => [],
			]
		);

		wp_reset_postdata();

		return $output;
	}

	private function render_for_sponsored_page( array $attrs, int $sponsor_id ): string {
		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages' ] );

		$terms = get_terms(
			[
				'taxonomy'   => 'sponsor',
				'hide_empty' => false,
				'meta_key'   => '_sponsor_post_id',
				'meta_value' => $sponsor_id,
				'fields'     => 'ids',
			]
		);
		wp_set_object_terms( $page_id, [ $terms[0] ], 'sponsor' );

		global $post;
		$post = get_post( $page_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		setup_postdata( $post );

		$output = render_block(
			[
				'blockName'    => 'latitudemedia/title-block',
				'attrs'        => $attrs,
				'innerBlocks'  => [],
				'innerHTML'    => '',
				'innerContent' => [],
			]
		);

		wp_reset_postdata();

		return $output;
	}

	public function test_no_sponsor_markup_when_page_has_no_sponsor() {
		$output = $this->render();

		$this->assertStringNotContainsString( 'ltm-title-block__sponsor', $output );
	}

	public function test_sponsor_with_own_thumbnail_renders_presented_by() {
		$sponsor_id    = self::factory()->post->create( [ 'post_type' => 'sponsors' ] );
		$attachment_id = self::factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg', $sponsor_id );
		set_post_thumbnail( $sponsor_id, $attachment_id );

		$output = $this->render_for_sponsored_page( [], $sponsor_id );

		$this->assertStringContainsString( 'ltm-title-block__sponsor', $output );
		$this->assertStringContainsString( 'Presented By', $output );
		$this->assertStringContainsString( 'ltm-title-block__sponsor-logo', $output );
	}

	public function test_logo_override_takes_precedence_over_sponsor_thumbnail() {
		$sponsor_id    = self::factory()->post->create( [ 'post_type' => 'sponsors' ] );
		$sponsor_image = self::factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg', $sponsor_id );
		set_post_thumbnail( $sponsor_id, $sponsor_image );

		$override_image = self::factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/2004-07-22-DSC_0007.jpg' );

		$output = $this->render_for_sponsored_page( [ 'sponsorLogoOverrideId' => $override_image ], $sponsor_id );

		$expected_url = wp_get_attachment_image_url( $override_image, 'medium' );
		$this->assertStringContainsString( $expected_url, $output );
	}

	public function test_kicker_renders_when_set() {
		$output = $this->render( [ 'kicker' => 'Special Report' ] );

		$this->assertStringContainsString( 'ltm-title-block__kicker', $output );
		$this->assertStringContainsString( 'Special Report', $output );
	}

	public function test_kicker_absent_when_unset() {
		$output = $this->render();

		$this->assertStringNotContainsString( 'ltm-title-block__kicker', $output );
	}

	public function test_background_image_style_present_when_set() {
		$background_image = self::factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg' );

		$output = $this->render( [ 'backgroundImageId' => $background_image ] );

		$this->assertStringContainsString( '--ltm-title-block-bg:', $output );
		$this->assertStringContainsString( wp_get_attachment_image_url( $background_image, 'full' ), $output );
	}

	public function test_no_background_style_when_unset() {
		$output = $this->render();

		$this->assertStringNotContainsString( '--ltm-title-block-bg:', $output );
	}
}
