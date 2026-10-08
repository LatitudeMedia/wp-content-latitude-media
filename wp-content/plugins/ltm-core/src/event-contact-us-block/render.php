<?php
/**
 * Server-side render for acf/event-contact-us-block.
 *
 * Fields are attached to the block itself (see includes/Blocks/EventContactUs.php)
 * and stored in the block's own embedded data, so bare get_field() calls resolve
 * via ACF's active block-meta context rather than a post ID.
 *
 * The `green` classes on the title and the buttons are hardcoded, as they were in
 * the theme: green is this block's default colour, and the Pink/Blue block styles
 * override it from style.scss with !important rather than by swapping the class.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$title         = get_field( 'title' ) ?: 'Contact us';
$contact_links = get_field( 'contact_links' ) ?: [];
$display       = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

if ( empty( $contact_links ) && empty( $title ) ) {
	return;
}

// className is passed through explicitly because ACF's AJAX preview re-render
// (acf/ajax/fetch-block) calls this template with no WP_Block, so
// WP_Block_Supports::$block_to_render is unset and
// get_block_wrapper_attributes() emits none of the supports-derived classes --
// including the `is-style-*` one carrying the Pink/Blue theme. Without this,
// picking a style and then clicking another block (which is when ACF re-renders
// the preview) dropped the theme and the cards snapped back to green. Core
// array_unique()s the merged class list, so the front-end path, where the
// custom-class-name support does add it, is unchanged. Same as
// event-sponsors-block/render.php.
$block_classes = array_filter(
	[
		'content-block',
		'contact-us-section',
		$block['className'] ?? '',
	]
);

$block_attrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => implode( ' ', $block_classes ),
			// `??` rather than `?:`: blocks with no anchor set have no `anchor` key
			// at all, which `?:` warns on. Same as event-description-block.
			'id'    => $block['anchor'] ?? '',
		]
	)
);
?>

<div <?php echo $block_attrs; ?>>
    <div class="container-narrow">
        <?php if ( $title ) : ?>
            <div class="contact-us-section-wrapper">
                <div class="bordered-title green"><?php echo esc_html( $title ); ?></div>
            </div>
        <?php endif; ?>
        <div class="contact-us-cards-wrapper">
            <?php
            foreach ( $contact_links as $card ) {
            	$card_title       = $card['title'] ?? '';
            	$card_description = $card['description'] ?? '';
            	$cta_link         = $card['cta_link'] ?? null;

            	// `?? ''` throughout: a repeater row left partly blank has no key for
            	// the empty sub-field at all, which the theme's bare access warned on.
            	echo '<div class="contact-us-card">';

            	if ( $card_title ) {
            		printf( '<div class="card-title"><h2>%s</h2></div>', esc_html( $card_title ) );
            	}

            	if ( $card_description ) {
            		// The description is a WYSIWYG value, already sanitized on save
            		// according to the author's capabilities. Echoed unfiltered, as the
            		// theme did -- running it through wp_kses_post() here would strip
            		// legitimate embeds out of a card.
            		printf( '<div class="card-content">%s</div>', $card_description );
            	}

            	if ( $cta_link ) {
            		printf(
            			'<div class="card-button"><a href="%1$s" class="cta-button green" target="%2$s">%3$s</a></div>',
            			esc_url( $cta_link['url'] ?? '' ),
            			esc_attr( $cta_link['target'] ?? '' ),
            			esc_html( $cta_link['title'] ?? '' )
            		);
            	}

            	echo '</div>';
            }
            ?>
        </div>
    </div>
</div>
