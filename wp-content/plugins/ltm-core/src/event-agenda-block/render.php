<?php
/**
 * Server-side render for acf/event-agenda-block.
 *
 * Fields are attached to the block itself (see includes/Blocks/EventAgenda.php)
 * and stored in the block's own embedded data, so bare get_field() calls resolve
 * via ACF's active block-meta context rather than a post ID.
 *
 * This is the original agenda block, distinct from acf/event-agenda-v2-block: it
 * is a two-column list of schedule rows with no day tabs, and the events still
 * using it (Transition-AI 2024 / 2025, Transition-AI: Boston / New York) are
 * past events whose pages must keep looking exactly as they do. So unlike the
 * other blocks migrated out of the theme, this one registers no block `styles`:
 * the `green` classes below stay hardcoded as the theme template had them, and
 * no `className` is threaded through. style.scss still carries the `&.pink` /
 * `&.blue` / `&.orange` modifiers, so styles can be added later without touching
 * this file.
 *
 * For the same reason the field values below are echoed unescaped, as the theme
 * template did. Two things break if they are run through esc_html():
 *
 *  - `wptexturize` is filtered onto `the_content` at priority 10 and `do_blocks`
 *    at 9, so it runs over this block's *output*. Escaping an apostrophe here
 *    turns it into `&#039;`, which wptexturize then leaves alone, where the raw
 *    `'` would have become a curly `&#8217;` -- visibly different typography on
 *    every agenda title. (`get_the_title()` below is safe to escape: it has
 *    already been through the `the_title` filters, and esc_html() does not
 *    double-encode the entities they produced.)
 *  - `description` is nominally a plain textarea, but editors have hand-written
 *    markup into it: Transition-AI: Boston (post 2134) has a row whose
 *    description is an entire `<h5>Sponsored by <a href="...">Mintz</a></h5>`
 *    heading, which escaping would render as visible tag soup.
 *
 * These fields carry the same trust level as post content -- only users who can
 * already edit the event can set them -- and are sanitized on save according to
 * the author's capabilities.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$title    = get_field( 'title' );
$schedule = get_field( 'schedule' ) ?: [];
$display  = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

if ( empty( $schedule ) ) {
	return;
}

$block_attrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => 'content-block agenda-section green',
			// `??` rather than `?:`: blocks with no anchor set have no `anchor` key
			// at all, which `?:` warns on. Same as event-contact-us-block.
			'id'    => $block['anchor'] ?? '',
		]
	)
);
?>

<div <?php echo $block_attrs; ?> >
    <div class="container-narrow">
        <div class="bordered-title green"><?php echo $title; ?></div>
        <div class="agenda-section-wrapper">
            <ul>
                <?php
                    foreach ( $schedule as $item ) {
                    	$data_html          = '';
                    	$image_html         = '';
                    	$speakers_list_html = '';

                    	if ( ! empty( $item['image'] ) ) {
                    		// The theme called thumbnail_formatting(), which for an image_id
                    		// with `link` false and no mobile_size is exactly this core call.
                    		// Same substitution as event-sponsors-block/render.php.
                    		$image_html = wp_get_attachment_image(
                    			$item['image'],
                    			'event-agenda',
                    			false,
                    			[ 'class' => 'agenda-thumbnail' ]
                    		);
                    	}

                    	// Guarded with `! empty()` rather than read bare: a repeater row left
                    	// partly blank has no key for the empty sub-field at all.
                    	if ( ! empty( $item['time'] ) ) {
                    		$data_html = sprintf( '<div class="time">%s</div>', $item['time'] );
                    	}

                    	if ( ! empty( $item['title'] ) ) {
                    		$data_html .= sprintf( '<h5>%s</h5>', $item['title'] );
                    	}

                    	if ( ! empty( $item['description'] ) ) {
                    		$data_html .= sprintf( '<p>%s</p>', $item['description'] );
                    	}

                    	if ( ! empty( $item['speakers'] ) ) {
                    		// Mirrors the theme's get_published_posts_by_ids() / curated_query()
                    		// behaviour: published only, ordered by the editor's chosen order, no
                    		// pagination. Same as event-sponsors-block/render.php.
                    		$speakers_posts = new WP_Query(
                    			[
                    				'post_type'           => 'speakers',
                    				'post__in'            => $item['speakers'],
                    				'orderby'             => 'post__in',
                    				'post_status'         => 'publish',
                    				'posts_per_page'      => -1,
                    				'ignore_sticky_posts' => 1,
                    			]
                    		);

                    		while ( $speakers_posts->have_posts() ) {
                    			$speakers_posts->the_post();
                    			$speakers_list_html .= sprintf( '<li><span class="name">%s</span></li>', esc_html( get_the_title() ) );
                    		}
                    		// The theme version never reset, so the last speaker stayed the
                    		// global $post for every block rendered after the agenda.
                    		wp_reset_postdata();
                    	}

                    	if ( $speakers_list_html ) {
                    		$data_html .= sprintf( '<ul class="speakers">%s</ul>', $speakers_list_html );
                    	}

                    	printf( '<li>%s<div class="agenda-data">%s</div></li>', $image_html, $data_html );
                    }
                ?>
            </ul>
        </div>
    </div>
</div>