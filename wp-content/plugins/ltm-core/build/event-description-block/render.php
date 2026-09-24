<?php
/**
 * Server-side render for acf/event-description-block.
 *
 * Fields are attached to the block itself (see
 * includes/Blocks/EventDescription.php) and stored in the block's own embedded
 * data, so bare get_field() calls resolve via ACF's active block-meta context
 * rather than a post ID.
 *
 * The "type2" style additionally reads form_text and
 * form_code__registration_cta from the separate "General event options" group
 * (location: post_type == events), which is still theme-registered and out of
 * scope for this migration — the same tolerated cross-boundary dependency
 * documented in includes/Blocks/EventPreview.php. Those are read via $post_id,
 * which ACF supplies and which refers to the underlying Event post.
 *
 * Style selection replaces the theme's template-part dispatcher
 * (template-parts/blocks/event/event-description-block.php) and its two
 * component partials; see EventDescription::is_type2() for why it does not
 * reuse the theme's ltm_get_block_style() helper.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$title   	= get_field( 'title' ) ?: 'About';
$display 	= get_field( 'display' );
$embed_code = get_field( 'form_code__registration_cta', $post_id );

// Unlike the subscriber-form block (a ServerSideRender block, whose editor
// preview arrives over REST), ACF renders block previews through admin-ajax and
// passes $is_preview into this template's scope -- same place $block and
// $post_id come from. See acf_block_render_template() in ACF Pro's
// pro/blocks.php; REST_REQUEST is never defined on that path.
$is_editor_preview = ! empty( $is_preview );

if ( ! $display && ! is_admin() ) {
	return;
}

$is_type2 = \LTMCore\Blocks\EventDescription::is_type2( $block['className'] ?? '' );

$blockAttrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => 'content-block ' . ( $is_type2 ? 'right-sidebar-layout' : 'event-text-section' ),
			'id'    => $block['anchor'] ?? '',
		]
	)
);

// Identical in both styles -- built once so the two cannot drift apart.
// allowedBlocks is the gray icon block (which is `parent`-restricted to this
// block) plus every core block live Event content already uses in here. It
// only gates the inserter; existing children of other types still load.
$inner = sprintf(
	'<div class="event-text-section-wrapper">
	<div class="bordered-title green">%1$s</div>
	<article>
		<InnerBlocks template="%2$s" allowedBlocks="%3$s" />
	</article>
</div>',
	esc_html( $title ),
	esc_attr( wp_json_encode( [ [ 'core/paragraph' ] ] ) ),
	esc_attr( wp_json_encode( [ 'acf/event-gray-icon-block', 'core/paragraph', 'core/heading', 'core/list', 'core/quote', 'core/columns' ] ) )
);
?>

<div <?php echo $blockAttrs; ?>>
	<div class="container-narrow">
		<?php if ( $is_type2 ) : ?>
			<div class="right-sidebar-layout-wrapper">
				<div class="main-column">
					<div class="event-text-section">
						<div class="container-narrow">
							<?php echo $inner; ?>
						</div>
					</div>
				</div>
				<div class="sidebar">
					<div class="form-block green">
						<div class="form-block-wrapper">
							<div class="form-title"><?php echo get_field( 'form_text', $post_id ); ?></div>

							<?php if ( $is_editor_preview ) : ?>
								<?php
								// Always emitted in the preview, hidden when there is no form
								// code yet: editor.js toggles [hidden] live as the meta box
								// field is typed into, which keeps the wording in one place
								// here rather than duplicating the string in JS.
								?>
								<p class="form-embed-placeholder"<?php echo $embed_code ? '' : ' hidden'; ?>>
									<?php echo esc_html__( '[your form will render here on the frontend]', 'ltm' ); ?>
								</p>
							<?php else : ?>
								<?php echo $embed_code; ?>
							<?php endif; ?>
							
						</div>
					</div>
				</div>
			</div>
		<?php else : ?>
			<?php echo $inner; ?>
		<?php endif; ?>
	</div>
</div>
