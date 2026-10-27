<?php
/**
 * Tests for the latitudemedia/title-block server-side render.
 *
 * Renders the registered block directly (reading from the committed build/
 * output) against a `thematic-pages` post set as the current global post,
 * since render.php resolves the sponsor via get_the_ID().
 *
 * featured-single-item-block/render.php is deliberately not covered here — it pulls
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

	public function test_layout_defaults_to_sponsored_content() {
		$output = $this->render();

		$this->assertStringContainsString( 'ltm-title-block--sponsored-content', $output );
	}

	public function test_unsponsored_layout_adds_modifier_class() {
		$output = $this->render( [ 'layout' => 'unsponsored' ] );

		$this->assertStringContainsString( 'ltm-title-block--unsponsored', $output );
		$this->assertStringNotContainsString( 'ltm-title-block--sponsored-content', $output );
	}

	public function test_layout_value_outside_enum_falls_back_to_default() {
		$output = $this->render( [ 'layout' => 'bogus' ] );

		$this->assertStringContainsString( 'ltm-title-block--sponsored-content', $output );
		$this->assertStringNotContainsString( 'ltm-title-block--bogus', $output );
	}

	public function test_unsponsored_layout_hides_sponsor_on_sponsored_page() {
		$sponsor_id    = self::factory()->post->create( [ 'post_type' => 'sponsors' ] );
		$attachment_id = self::factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg', $sponsor_id );
		set_post_thumbnail( $sponsor_id, $attachment_id );

		$output = $this->render_for_sponsored_page( [ 'layout' => 'unsponsored' ], $sponsor_id );

		$this->assertStringNotContainsString( 'ltm-title-block__sponsor', $output );
		$this->assertStringNotContainsString( 'Presented By', $output );
	}

	public function test_unsponsored_layout_hides_kicker() {
		$output = $this->render(
			[
				'layout' => 'unsponsored',
				'kicker' => 'Special Report',
			]
		);

		$this->assertStringNotContainsString( 'ltm-title-block__kicker', $output );
		$this->assertStringNotContainsString( 'Special Report', $output );
	}

	/**
	 * Sets the sponsor the way an editor does — through the Thematic Page's
	 * "Is Sponsored By" meta box save handler — rather than assigning the term directly.
	 */
	public function test_sponsor_set_via_thematic_page_meta_box_displays_in_sponsored_content_layout() {
		$sponsor_id    = self::factory()->post->create( [ 'post_type' => 'sponsors' ] );
		$attachment_id = self::factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg', $sponsor_id );
		set_post_thumbnail( $sponsor_id, $attachment_id );

		$term_ids = get_terms(
			[
				'taxonomy'   => 'sponsor',
				'hide_empty' => false,
				'meta_key'   => '_sponsor_post_id',
				'meta_value' => $sponsor_id,
				'fields'     => 'ids',
			]
		);

		$page_id = self::factory()->post->create( [ 'post_type' => 'thematic-pages' ] );

		wp_set_current_user( self::factory()->user->create( [ 'role' => 'administrator' ] ) );
		$_POST['ltm_sponsor_nonce'] = wp_create_nonce( 'ltm_sponsor_save' );
		$_POST['ltm_sponsor']       = $term_ids[0];
		( new \LTMCore\Taxonomies\PostSponsor() )->save_meta_box( $page_id, get_post( $page_id ) );
		unset( $_POST['ltm_sponsor_nonce'], $_POST['ltm_sponsor'] );

		global $post;
		$post = get_post( $page_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		setup_postdata( $post );

		$output = render_block(
			[
				'blockName'    => 'latitudemedia/title-block',
				'attrs'        => [ 'layout' => 'sponsored-content' ],
				'innerBlocks'  => [],
				'innerHTML'    => '',
				'innerContent' => [],
			]
		);

		wp_reset_postdata();

		$this->assertStringContainsString( 'ltm-title-block--sponsored-content', $output );
		$this->assertStringContainsString( 'ltm-title-block__sponsor', $output );
		$this->assertStringContainsString( 'Presented By', $output );
		$this->assertStringContainsString( get_the_post_thumbnail_url( $sponsor_id, 'medium' ), $output );
	}
}
