<?php
/**
 * Tests for the acf/event-preview-block server-side render.
 *
 * Field values are supplied via the block's own embedded attribute data
 * (attrs['data'], keyed by field name, with parallel "_fieldname" => field
 * key entries) rather than post meta -- this is how ACF blocks actually
 * store values (even block.json/v3 ones with no `usePostMeta`), and matches
 * the format already saved in existing Event posts' content. Renders
 * against an `events` post set as the current global post, since render.php
 * resolves $post_id via get_the_ID() (see ACF's acf_render_block_callback()).
 *
 * Deliberately a single test method making a single render_block() call:
 * ACF's internal block-meta handling does not tear down cleanly between
 * successive render_block() calls to an ACF block outside a real
 * page-request lifecycle (confirmed empirically -- a second call in the
 * same process reads back wrong/empty field values regardless of its own
 * data, even after manually resetting every ACF store this plugin has
 * access to), so a second block-render test in this same PHPUnit process
 * would be flaky rather than a reliable regression check. This is almost
 * certainly why the sibling EventAgendaV2 block (also ACF, also block.json)
 * has no render tests either. One render, with several fields set at once
 * and several assertions against its single output, still exercises the
 * thing this test exists to catch: that the migrated field group correctly
 * resolves the legacy block-attribute-JSON storage format existing Event
 * posts already use, rather than a happy-path-only smoke test.
 *
 * thumbnail_formatting() (used for co-hosted logos) and the
 * print_event_date_range/print_event_location/button_unit action hooks are
 * theme globals not migrated as part of this block (same "tolerated theme
 * dependency" precedent as event-agenda-v2-block/render.php's calls to
 * get_published_posts_by_ids()/date_to_format()) -- they aren't loaded in
 * this plugin-only test environment, so the co_hosted_logo branch (which
 * calls thumbnail_formatting() directly, not via do_action()) is not
 * exercised here either. Full rendering with the theme active -- including
 * display=off, co_hosted_logo, and main_logo -- is covered by manual
 * verification against real Event posts instead.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\EventPreview
 */
class EventPreviewBlockRenderTest extends WP_UnitTestCase {

	public function test_render_resolves_block_attribute_data_for_multiple_fields() {
		$field_keys = [
			'display'                 => 'field_67447f65a31a7',
			'rows'                    => 'field_67473ac9a1f7c',
			'subtitle'                => 'field_67473b75a1f7d',
			'show_event_collaborator' => 'field_6a2f8c4d1e905',
			'event_collaborator_text' => 'field_6a2f8c4d1e903',
		];

		$data = [
			'display'                 => '1',
			'rows'                    => [ 'title' ],
			'subtitle'                => 'Save the date',
			'show_event_collaborator' => '1',
			'event_collaborator_text' => '<script>alert(1)</script>',
		];
		foreach ( $field_keys as $field_name => $field_key ) {
			$data[ "_{$field_name}" ] = $field_key;
		}

		$event_id = self::factory()->post->create( [ 'post_type' => 'events' ] );

		global $post;
		$post = get_post( $event_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		setup_postdata( $post );

		$output = render_block(
			[
				'blockName'    => 'acf/event-preview-block',
				'attrs'        => [
					'mode' => 'preview',
					'data' => $data,
				],
				'innerBlocks'  => [],
				'innerHTML'    => '',
				'innerContent' => [],
			]
		);

		wp_reset_postdata();

		// Wrapper and title (rows => ['title']) render, proving the block's
		// own field group resolved 'display' and 'rows' from block-attribute
		// data rather than (empty) post meta.
		$this->assertStringContainsString( 'single-event-hero-section', $output );
		$this->assertStringContainsString( '<h1>', $output );

		// Subtitle renders as-is (this field is not escaped in the original
		// template -- a pre-existing behavior carried over unchanged).
		$this->assertStringContainsString( '<div class="subtitle">Save the date</div>', $output );

		// Collaborator markup renders, and its text is escaped.
		$this->assertStringContainsString( 'collaborator-wrapper', $output );
		$this->assertStringNotContainsString( '<script>alert(1)</script>', $output );
		$this->assertStringContainsString( '&lt;script&gt;', $output );
	}
}
