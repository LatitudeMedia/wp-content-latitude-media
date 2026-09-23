<?php
/**
 * Server-side render for acf/event-speakers-block.
 *
 * Fields are attached to the block itself (see includes/Blocks/EventSpeakers.php)
 * and stored in the block's own embedded data, so bare get_field() calls resolve
 * via ACF's active block-meta context rather than a post ID.
 *
 * The speakers themselves are a theme-registered post type; their per-speaker
 * fields (job_title, company, linkedin_link) come from the theme's "Speaker
 * options" group and are read here by speaker ID.
 *
 * Modal open/close is handled by the theme's global jQuery handler in
 * src/assets/js/custom.js, which binds the .js-modal-open / .js-modal-close
 * classes emitted below. That handler is shared with several blocks still in
 * the theme, so this block intentionally ships no JS of its own.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$title                 = get_field( 'title' ) ?: 'Featured speakers';
$speakers              = get_field( 'speakers' ) ?: [];
$display               = get_field( 'display' );
$show_read_more_button = get_field( 'show_read_more_button' );

if ( ! $display && ! is_admin() ) {
	return;
}

if ( empty( $speakers ) ) {
	return;
}

// Mirrors the theme's get_published_posts_by_ids() / curated_query() behaviour:
// published only, ordered by the editor's chosen order, no pagination.
$speakers_posts = new WP_Query(
	[
		'post_type'           => 'speakers',
		'post__in'            => $speakers,
		'orderby'             => 'post__in',
		'post_status'         => 'publish',
		'posts_per_page'      => -1,
		'ignore_sticky_posts' => 1,
	]
);

if ( ! $speakers_posts->have_posts() ) {
	return;
}

$block_attrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => 'content-block our-team-section',
			'id'    => $block['anchor'] ?: '',
		]
	)
);

/**
 * LinkedIn glyph, ported from the theme's print_speaker_socials() template tag.
 * That tag stays in the theme (other blocks still use it), so the markup is
 * duplicated here rather than called across the boundary.
 */
$linkedin_icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M5 1.25C3.48122 1.25 2.25 2.48122 2.25 4C2.25 5.51878 3.48122 6.75 5 6.75C6.51878 6.75 7.75 5.51878 7.75 4C7.75 2.48122 6.51878 1.25 5 1.25ZM3.75 4C3.75 3.30964 4.30964 2.75 5 2.75C5.69036 2.75 6.25 3.30964 6.25 4C6.25 4.69036 5.69036 5.25 5 5.25C4.30964 5.25 3.75 4.69036 3.75 4Z" fill="currentColor"></path>
<path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 8C2.25 7.58579 2.58579 7.25 3 7.25H7C7.41421 7.25 7.75 7.58579 7.75 8V21C7.75 21.4142 7.41421 21.75 7 21.75H3C2.58579 21.75 2.25 21.4142 2.25 21V8ZM3.75 8.75V20.25H6.25V8.75H3.75Z" fill="currentColor"></path>
<path fill-rule="evenodd" clip-rule="evenodd" d="M9.25 8C9.25 7.58579 9.58579 7.25 10 7.25H14C14.4142 7.25 14.75 7.58579 14.75 8V8.43402L15.1853 8.24748C15.9336 7.92676 16.7339 7.72565 17.5433 7.65207C20.3182 7.3998 22.75 9.58038 22.75 12.3802V21C22.75 21.4142 22.4142 21.75 22 21.75H18C17.5858 21.75 17.25 21.4142 17.25 21V14C17.25 13.6685 17.1183 13.3505 16.8839 13.1161C16.6495 12.8817 16.3315 12.75 16 12.75C15.6685 12.75 15.3505 12.8817 15.1161 13.1161C14.8817 13.3505 14.75 13.6685 14.75 14V21C14.75 21.4142 14.4142 21.75 14 21.75H10C9.58579 21.75 9.25 21.4142 9.25 21V8ZM10.75 8.75V20.25H13.25V14C13.25 13.2707 13.5397 12.5712 14.0555 12.0555C14.5712 11.5397 15.2707 11.25 16 11.25C16.7293 11.25 17.4288 11.5397 17.9445 12.0555C18.4603 12.5712 18.75 13.2707 18.75 14V20.25H21.25V12.3802C21.25 10.4759 19.589 8.97227 17.6791 9.14591C17.025 9.20536 16.3784 9.36807 15.7762 9.6262L14.2954 10.2608C14.0637 10.3601 13.7976 10.3363 13.5871 10.1976C13.3767 10.0588 13.25 9.82354 13.25 9.57143V8.75H10.75Z" fill="currentColor"></path>
</svg>';
?>

<div <?php echo $block_attrs; ?>>
    <div class="container-narrow">
        <div class="our-team-section-wrapper">
            <div class="bordered-title green"><?php echo esc_html( $title ); ?></div>
            <ul class="team">
                <?php
                while ( $speakers_posts->have_posts() ) :
                	$speakers_posts->the_post();
                	$speaker_id = get_the_ID();
                	$modal_id   = uniqid();
                	$job_title  = get_field( 'job_title', $speaker_id );
                	$company    = get_field( 'company', $speaker_id );
                	$linkedin   = get_field( 'linkedin_link', $speaker_id );
                	?>
                    <li>
                        <div class="image-folder green"><a href="#<?php echo esc_attr( $modal_id ); ?>" class="js-modal-open"><?php echo get_the_post_thumbnail( $speaker_id, 'event-speakers-list' ); ?></a></div>
                        <div class="content-folder">
                            <a href="#<?php echo esc_attr( $modal_id ); ?>" class="name green js-modal-open"><?php echo esc_html( get_the_title( $speaker_id ) ); ?></a>
                            <?php if ( $company ) : ?>
                                <p class="occupation"><?php echo esc_html( $company ); ?></p>
                            <?php endif; ?>
                            <?php if ( $show_read_more_button ) : ?>
                                <a class="more-link js-modal-open" href="#<?php echo esc_attr( $modal_id ); ?>">Read more</a>
                            <?php endif; ?>
                        </div>

                        <div id="<?php echo esc_attr( $modal_id ); ?>" class="modal-content green">
                            <div class="modal-folder">
                                <a class="cross js-modal-close"></a>
                                <div class="bio">
                                    <?php echo get_the_post_thumbnail( $speaker_id, 'event-speakers-modal', [ 'class' => 'avatar' ] ); ?>
                                    <div class="info">
                                        <h3><?php echo esc_html( get_the_title( $speaker_id ) ); ?></h3>
                                        <?php if ( $job_title ) : ?>
                                            <p class="occupation"><?php echo esc_html( $job_title ); ?></p>
                                        <?php endif; ?>
                                        <?php if ( $company ) : ?>
                                            <p class="company"><?php echo esc_html( $company ); ?></p>
                                        <?php endif; ?>
                                        <p class="connect">
                                            <span class="label">Connect via:</span>
                                            <?php if ( $linkedin ) : ?>
                                                <a href="<?php echo esc_url( $linkedin ); ?>" class="icon"><?php echo $linkedin_icon; ?></a>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="description">
                                    <?php
                                    // Speaker bio is post content, already sanitized on save according to
                                    // the author's capabilities. Echoed unfiltered, as the theme did and as
                                    // core does for post content — running it through wp_kses_post() here
                                    // would strip legitimate embeds out of a bio.
                                    echo get_the_content( null, false, $speaker_id );
                                    ?>
                                </div>
                                <a class="strict-button green js-modal-close">Close</a>
                            </div>
                        </div>
                    </li>
                	<?php
                endwhile;
                wp_reset_postdata();
                ?>
            </ul>
        </div>
    </div>
</div>
