<?php
/**
 * Server-side render for acf/event-sponsors-block.
 *
 * Fields are attached to the block itself (see includes/Blocks/EventSponsors.php)
 * and stored in the block's own embedded data, so bare get_field() calls resolve
 * via ACF's active block-meta context rather than a post ID.
 *
 * The sponsors themselves are a plugin-registered post type (see
 * LTMCore\PostTypes\Sponsors), but their per-sponsor `website_link` field comes
 * from the theme's "Sponsor options" group and is read here by sponsor ID.
 *
 * The `title` field doubles as the section label -- events use a second instance
 * of this block titled "Partners" for their partner logos, which is why the
 * retired acf/event-partners-block was folded into this one rather than migrated
 * (see LTMCore\Blocks\EventSponsors::migrate_partners_blocks()).
 *
 * Modal open/close is handled by the theme's global jQuery handler in
 * src/assets/js/custom.js, which binds the .js-modal-open / .js-modal-close
 * classes emitted below. That handler is shared with several blocks still in
 * the theme, so this block intentionally ships no JS of its own.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$title                 = get_field( 'title' ) ?: 'Sponsors';
$sponsors_category     = get_field( 'sponsors_category' ) ?: [];
$display               = get_field( 'display' );
$different_sizes       = get_field( 'different_sizes' );
$show_read_more_button = get_field( 'show_read_more_button' );

if ( ! $display && ! is_admin() ) {
	return;
}

if ( empty( $sponsors_category ) ) {
	return;
}

// className is passed through explicitly because ACF's AJAX preview re-render
// (acf/ajax/fetch-block) calls this template with no WP_Block, so
// WP_Block_Supports::$block_to_render is unset and
// get_block_wrapper_attributes() emits none of the supports-derived classes --
// including the `is-style-*` one carrying the Pink/Blue theme. Without this,
// picking a style and then clicking another block (which is when ACF re-renders
// the preview) dropped the theme and the sponsors snapped back to green. Core
// array_unique()s the merged class list, so the front-end path, where the
// custom-class-name support does add it, is unchanged. Same as
// event-speakers-block/render.php.
$block_classes = array_filter(
	[
		'content-block',
		'event-sponsors-section',
		'green',
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
        <div class="bordered-title"><?php echo esc_html( $title ); ?></div>
        <div class="event-sponsors-section-wrapper">

            <?php
            foreach ( $sponsors_category as $category ) {
            	if ( empty( $category['sponsors'] ) ) {
            		continue;
            	}

            	$size = $different_sizes ? ( $category['size'] ?: 'xlarge' ) : 'xlarge';

            	// Mirrors the theme's get_published_posts_by_ids() / curated_query()
            	// behaviour: published only, ordered by the editor's chosen order, no
            	// pagination. Same as event-speakers-block/render.php.
            	$sponsors_posts = new WP_Query(
            		[
            			'post_type'           => 'sponsors',
            			'post__in'            => $category['sponsors'],
            			'orderby'             => 'post__in',
            			'post_status'         => 'publish',
            			'posts_per_page'      => -1,
            			'ignore_sticky_posts' => 1,
            		]
            	);

            	$list_html = '';
            	while ( $sponsors_posts->have_posts() ) {
            		$sponsors_posts->the_post();
            		$sponsor_id = get_the_ID();
            		$modal_id   = uniqid();

            		// Trimmed before esc_url(): several sponsors have a stray leading or
            		// trailing space in website_link, and esc_url() percent-encodes a
            		// trailing one into the URL rather than dropping it.
            		$sponsor_url = trim( (string) get_field( 'website_link', $sponsor_id ) ) ?: '#';
            		$img         = get_the_post_thumbnail( $sponsor_id, 'event-sponsors-list' );

            		$read_more_link = $show_read_more_button
            			? sprintf( '<a class="more-link js-modal-open" href="#%s">Read more</a>', esc_attr( $modal_id ) )
            			: '';

            		$sponsor_html = sprintf(
            			'<div class="image-folder green"><a href="#%1$s" class="js-modal-open">%2$s</a></div>
                        <div class="content-folder">
                            %3$s
                        </div>',
            			esc_attr( $modal_id ),
            			$img,
            			$read_more_link
            		);

            		// Sponsor bio is post content, already sanitized on save according to
            		// the author's capabilities. Echoed unfiltered, as the theme did and as
            		// core does for post content -- running it through wp_kses_post() here
            		// would strip legitimate embeds out of a bio.
            		$sponsor_modal_html = sprintf(
            			'<div id="%1$s" class="modal-content green">
                        <div class="modal-folder">
                            <a class="cross js-modal-close"></a>
                            <div class="bio">
                                %2$s

                                <div class="info">
                                    <h3>%3$s</h3>
                                </div>
                            </div>
                            <div class="description">
                                %4$s
                            </div>
                            <div class="control-buttons">
                                <a href="%5$s" class="strict-button green" target="_blank">Visit Website</a>
                                <a class="strict-button green js-modal-close">Close</a>
                            </div>
                        </div>
                    </div>',
            			esc_attr( $modal_id ),
            			$img,
            			esc_html( get_the_title( $sponsor_id ) ),
            			get_the_content( null, false, $sponsor_id ),
            			esc_url( $sponsor_url )
            		);

            		$list_html .= sprintf( '<li>%1$s %2$s</li>', $sponsor_html, $sponsor_modal_html );
            	}
            	wp_reset_postdata();

            	// Scoped to this iteration: the theme version declared $titleHtml
            	// outside the loop and only ever assigned it, so a category with no
            	// title inherited the previous category's heading.
            	$title_html = ! empty( $category['title'] )
            		? sprintf( '<h5>%s</h5>', esc_html( $category['title'] ) )
            		: '';

            	if ( $list_html ) {
            		printf( '<div class="sponsors-row %s">%s<ul>%s</ul></div>', esc_attr( $size ), $title_html, $list_html );
            	}
            }
            ?>
        </div>
    </div>
</div>
