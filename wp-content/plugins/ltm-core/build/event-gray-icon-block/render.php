<?php
/**
 * Server-side render for acf/event-gray-icon-block.
 *
 * Fields are attached to the block itself (see
 * includes/Blocks/EventGrayIcon.php) and stored in the block's own embedded
 * data, so bare get_field() calls resolve via ACF's active block-meta context
 * rather than a post ID.
 *
 * The "type" select field picks between the one-column and two-column
 * layouts, replacing the theme's template-part dispatcher
 * (template-parts/blocks/event/event-gray-icon-block.php) and its two
 * component partials. The icon goes through the theme's `thumbnail_formatting`
 * action exactly as before — the same tolerated theme dependency documented in
 * event-preview-block/render.php.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$display = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

$is_type2 = get_field( 'type' ) === 'type2';

$blockAttrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => 'content-block ' . ( $is_type2 ? 'half-wide-section' : 'grey-icon-block single-grey-icon-block' ),
			'id'    => $block['anchor'] ?? '',
		]
	)
);

// One column's worth of markup; type2 renders it twice with the *_2 fields.
$column = static function ( $logo, $content ) {
	echo '<div class="grey-icon-block-wrapper">';
	if ( ! empty( $logo ) ) {
		do_action( 'thumbnail_formatting', null, [ 'image_id' => $logo, 'link' => false, 'img_attr' => [ 'class' => 'icon' ] ] );
	}
	echo $content;
	echo '</div>';
};
?>

<div <?php echo $blockAttrs; ?>>
	<div class="container-narrow">
		<?php if ( $is_type2 ) : ?>
			<div class="half-wide-section-wrapper">
				<div class="half left">
					<div class="grey-icon-block">
						<?php $column( get_field( 'logo' ), get_field( 'content' ) ); ?>
					</div>
				</div>
				<div class="half right">
					<div class="grey-icon-block">
						<?php $column( get_field( 'logo_2' ), get_field( 'content_2' ) ); ?>
					</div>
				</div>
			</div>
		<?php else : ?>
			<?php $column( get_field( 'logo' ), get_field( 'content' ) ); ?>
		<?php endif; ?>
	</div>
</div>
