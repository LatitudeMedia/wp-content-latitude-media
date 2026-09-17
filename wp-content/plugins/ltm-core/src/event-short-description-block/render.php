<?php
/**
 * Server-side render for acf/event-short-description-block.
 *
 * Fields are attached to the block itself (see
 * includes/Blocks/EventShortDescription.php) and stored in the block's own
 * embedded data, so bare get_field() calls resolve via ACF's active
 * block-meta context rather than a post ID.
 *
 * Styling is its own `style.scss`, declared via block.json's "style" key --
 * WordPress core enqueues that in both the block editor and the front end
 * automatically, so no manual wp_enqueue_style() call is needed here.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$display = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

$blockAttrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => 'content-block event-text-section',
			'id'    => $block['anchor'] ?? '',
		]
	)
);

?>

<div <?php echo $blockAttrs; ?>>
	<div class="event-text-section">
		<div class="container-narrow">
			<div class="event-text-section-wrapper">
					<InnerBlocks
						template="<?php echo esc_attr( wp_json_encode( array( array( 'core/post-excerpt', array( ) ) ) ) ); ?>"
						templateLock="all"
						allowedBlocks="<?php echo esc_attr( wp_json_encode( array( 'core/post-excerpt' ) ) ); ?>"
					/>
				
			</div>
		</div>
	</div>
</div>
