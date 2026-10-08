<?php
/**
 * Server-side render for acf/recap-video-block.
 *
 * Fields are attached to the block itself (see includes/Blocks/RecapVideo.php)
 * and stored in the block's own embedded data, so bare get_field() calls resolve
 * via ACF's active block-meta context rather than a post ID.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$title   = get_field( 'title' );
$video   = get_field( 'video' );
$display = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

// className is passed through explicitly because ACF's AJAX preview re-render
// (acf/ajax/fetch-block) calls this template with no WP_Block, so
// get_block_wrapper_attributes() would drop any editor-entered class. Same as
// event-contact-us-block/render.php.
$block_classes = array_filter(
	[
		'content-block',
		'recap-video-section',
		$block['className'] ?? '',
	]
);

$block_attrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => implode( ' ', $block_classes ),
			// `??` rather than the theme's `?:`: blocks with no anchor set have no
			// `anchor` key at all, which `?:` warns on.
			'id'    => $block['anchor'] ?? '',
		]
	)
);
?>

<div <?php echo $block_attrs; ?>>
    <div class="container-narrow">
        <div class="recap-video-section-wrapper">
            <h2><?php echo esc_html( $title ); ?></h2>
            <div class="recap-iframe-folder">
                <?php
                // The oembed field returns provider embed HTML (an <iframe>), echoed
                // unfiltered as the theme did -- wp_kses_post() would strip it.
                if ( ! empty( $video ) ) {
                	echo $video;
                }
                ?>
            </div>
        </div>
    </div>
</div>
