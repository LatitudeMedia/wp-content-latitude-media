<?php
/**
 * Server-side render for acf/styled-button-block.
 *
 * Fields are attached to the block itself (see
 * includes/Blocks/StyledButton.php) and stored in the block's own embedded
 * data, so the bare get_field() call resolves via ACF's active block-meta
 * context rather than a post ID.
 *
 * Replaces the theme's template-parts/blocks/common/styled-button-block.php.
 *
 * Three deliberate differences from that template:
 *
 *  - The theme's button_unit() (inc/template-tags.php) is inlined here rather
 *    than called through its `button_unit` action, so ltm-core carries no hard
 *    dependency on theme PHP -- same reasoning as ltm_image_and_text_image()
 *    in src/image-and-text/render.php. Only the no-$wrap branch was ever
 *    reached from this block, so only that branch is ported, and the markup it
 *    emits is unchanged. The values are escaped here; button_unit() printf'd
 *    them raw.
 *  - get_block_wrapper_attributes() is actually applied. The theme template
 *    declared anchor/color/spacing support but emitted no wrapper at all,
 *    concatenating className raw into 'cta-button ' . $className, so none of
 *    those supports did anything. The `cta-button` class -- which is what the
 *    theme's .cta-button rule in src/assets/scss/base/_global.scss styles, on
 *    the front end and in the editor canvas alike -- is preserved.
 *  - An unconfigured button renders nothing on the front end. The theme
 *    template printed a live "Select link" -> "/" anchor to real visitors.
 *    The placeholder is kept for the editor preview only, so the block stays
 *    visible and selectable there.
 *
 * Unlike the other migrated ACF blocks there is no `display` true_false field
 * and so no `if ( ! $display && ! is_admin() ) return;` guard -- this block
 * never had one in the theme either.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$button = get_field( 'button' );

if ( empty( $button['url'] ) || empty( $button['title'] ) ) {
	if ( ! is_admin() ) {
		return;
	}

	$button = array(
		'title' => __( 'Select link', 'ltm' ),
		'url'   => '/',
	);
}

$blockAttrs = wp_kses_data(
	get_block_wrapper_attributes(
		array(
			'class' => 'cta-button',
			'id'    => $block['anchor'] ?? '',
		)
	)
);
?>

<a <?php echo $blockAttrs; ?> href="<?php echo esc_url( $button['url'] ); ?>" target="<?php echo esc_attr( $button['target'] ?? '' ); ?>"><?php echo esc_html( $button['title'] ); ?></a>
