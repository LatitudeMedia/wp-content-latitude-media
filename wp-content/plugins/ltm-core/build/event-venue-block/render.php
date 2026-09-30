<?php
/**
 * Server-side render for acf/event-venue-block.
 *
 * Fields are attached to the block itself (see includes/Blocks/EventVenue.php)
 * and stored in the block's own embedded data, so bare get_field() calls resolve
 * via ACF's active block-meta context rather than a post ID. The date and the
 * place come from the event post instead, through the theme's
 * print_event_date_range / print_event_location template tags -- same coupling
 * as event-preview-block/render.php.
 *
 * The `green` wrapper class is hardcoded, as it was in the theme: it colours the
 * Date/Location column borders, which therefore stay green under every block
 * style. The Pink/Blue styles only override the title border and link colour
 * from style.scss.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$additional_info  = get_field( 'additional_info' );
$embed_code       = get_field( 'embed_code' );
$location_details = get_field( 'location_details' );
$display          = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

// className is passed through explicitly because ACF's AJAX preview re-render
// (acf/ajax/fetch-block) calls this template with no WP_Block, so
// get_block_wrapper_attributes() would drop the `is-style-*` class carrying the
// Pink/Blue theme. Same as event-contact-us-block/render.php.
$block_classes = array_filter(
	[
		'content-block',
		'venue-section',
		'green',
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

$post_id = get_the_ID();
?>

<div <?php echo $block_attrs; ?>>
    <div class="container-narrow">
        <div class="venue-section-wrapper">
            <div class="bordered-title"><?php esc_html_e( 'Venue', 'ltm' ); ?></div>
            <div class="additional-info">
                <?php
                // The WYSIWYG fields and the embed code (a map <iframe>) are echoed
                // unfiltered, as the theme did. They are sanitized on save according
                // to the author's capabilities, and wp_kses_post() would strip the
                // iframe outright.
                if ( ! empty( $additional_info ) ) {
                	echo $additional_info;
                }
                ?>
            </div>
            <?php
            if ( ! empty( $embed_code ) ) {
            	echo $embed_code;
            }
            ?>
            <div class="venue-info">
                <div class="date">
                    <h2><?php esc_html_e( 'Date', 'ltm' ); ?></h2>
                    <?php do_action( 'print_event_date_range', $post_id ); ?>
                </div>
                <div class="location">
                    <h2><?php esc_html_e( 'Location', 'ltm' ); ?></h2>
                    <address>
                        <?php
                        do_action( 'print_event_location', $post_id );
                        echo $location_details;
                        ?>
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
