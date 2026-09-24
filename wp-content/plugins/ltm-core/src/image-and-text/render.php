<?php
/**
 * Server-side render for acf/image-and-text.
 *
 * Fields are attached to the block itself (see
 * includes/Blocks/ImageAndText.php) and stored in the block's own embedded
 * data, so bare get_field() calls resolve via ACF's active block-meta context
 * rather than a post ID.
 *
 * This single template replaces the theme's dispatcher
 * (template-parts/blocks/common/image-and-text.php) and all eight of its
 * component partials (template-parts/components/image-and-text/*.php), which
 * were ~95% identical. Everything that varied per style now lives in
 * ImageAndText::STYLES; see that map's docblock for the per-field meaning and
 * for the two quirks deliberately preserved from the theme (`default` ignoring
 * image_link, and type6/type8 rendering an unguarded image slot).
 *
 * Two theme template tags are inlined here rather than called, so ltm-core
 * carries no hard dependency on theme PHP:
 *  - section_title()               -> the bordered-title div below.
 *  - print_image_and_text_image()  -> ltm_image_and_text_image() below, which
 *    in turn inlines the one branch of thumbnail_formatting() this block ever
 *    reached (image_id set, link false, no img_attr, no mobile_size).
 *
 * The registered image sizes it names are still theme-owned -- see the
 * ImageAndText class docblock for why.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$title        = get_field( 'title' );
$logo         = get_field( 'logo' );
$image_link   = get_field( 'image_link' );
$base_color   = get_field( 'base_color' ) ?: '#c6168d';
$shadow_color = get_field( 'shadow_color' ) ?: '#F9E8F4';
$display      = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

$cfg = \LTMCore\Blocks\ImageAndText::style_config( $block['className'] ?? '' );

$blockAttrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'style' => "--custom-block-base-color: {$base_color}; --custom-block-shadow-color: {$shadow_color};",
			'class' => 'content-block ' . $cfg['classes'],
			'id'    => $block['anchor'] ?? '',
		]
	)
);

if ( ! function_exists( 'ltm_image_and_text_image' ) ) {
	/**
	 * Renders the block's image.
	 *
	 * Mirrors the theme's print_image_and_text_image(): an empty logo emits
	 * nothing at all (so an unguarded image slot yields an empty div, not a
	 * broken image), and the image is wrapped in <a> when image_link is set,
	 * <span> otherwise. When $link_image is false the bare <img> is emitted
	 * with no wrapper and image_link is ignored, which is what the `default`
	 * style did by calling thumbnail_formatting() directly.
	 *
	 * @param array|null  $logo       ACF image field value (return_format array).
	 * @param string      $size       Registered image size name.
	 * @param string|null $image_link Optional URL to wrap the image in.
	 * @param bool        $link_image Whether this style honours $image_link.
	 */
	function ltm_image_and_text_image( $logo, string $size, $image_link, bool $link_image ): void {
		if ( empty( $logo['ID'] ) ) {
			return;
		}

		$image = wp_get_attachment_image( $logo['ID'], $size );

		if ( ! $link_image ) {
			echo $image;
			return;
		}

		if ( ! empty( $image_link ) ) {
			printf( '<a href="%1$s">%2$s</a>', esc_url( $image_link ), $image );
		} else {
			printf( '<span>%1$s</span>', $image );
		}
	}
}

$image_slot = sprintf(
	'<div class="%s">%s</div>',
	esc_attr( $cfg['image_slot'] ),
	// Buffered so the slot markup and its contents stay one string, keeping
	// the two slots orderable below without duplicating either.
	( static function () use ( $logo, $cfg, $image_link ) {
		ob_start();
		ltm_image_and_text_image( $logo, $cfg['size'], $image_link, $cfg['link_image'] );
		return ob_get_clean();
	} )()
);

if ( $cfg['guard_image'] && empty( $logo ) ) {
	$image_slot = '';
}

// The text slot is body copy with an optional call to action, so the inserter
// is curated rather than open: headings, paragraphs and lists for the copy,
// plus acf/styled-button-block for the CTA. Widening this list should be a
// deliberate decision, not a reflex. The template still seeds only a
// paragraph -- a button is offered, not auto-inserted.
$text_slot = sprintf(
	'<div class="%1$s"><InnerBlocks allowedBlocks="%2$s" template="%3$s" /></div>',
	esc_attr( $cfg['text_slot'] ),
	esc_attr( wp_json_encode( [ 'core/heading', 'core/paragraph', 'core/list', 'acf/styled-button-block' ] ) ),
	esc_attr( wp_json_encode( [ [ 'core/paragraph' ] ] ) )
);

$slots = $cfg['text_first'] ? $text_slot . $image_slot : $image_slot . $text_slot;
?>

<div <?php echo $blockAttrs; ?>>
	<div class="<?php echo esc_attr( $cfg['container'] ); ?>">
		<?php if ( ! empty( $title ) ) : ?>
			<div class="bordered-title"><?php echo esc_html( $title ); ?></div>
		<?php endif; ?>
		<div class="<?php echo esc_attr( $cfg['wrapper'] ); ?>">
			<?php echo $slots; ?>
		</div>
	</div>
</div>
