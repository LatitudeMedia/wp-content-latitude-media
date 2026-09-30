<?php
/**
 * Server-side render for acf/events-list-block.
 *
 * Fields are attached to the block itself (see includes/Blocks/EventsList.php)
 * and stored in the block's own embedded data, so bare get_field() calls resolve
 * via ACF's active block-meta context rather than a post ID.
 *
 * Every matching event is rendered up front; items after the third get
 * `hidden` and view.js reveals them three at a time. There is no server
 * round-trip -- the theme's `data-page` attribute and the REST endpoint it was
 * meant for were never wired up, and were dropped in the migration.
 *
 * Event cards still go through the theme's post-item component and
 * Page_Data exclusion list, as featured-post-block/render.php does.
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

$title   = get_field( 'title' );
$type    = get_field( 'type' );
$events  = get_field( 'events' ) ?: [];
$display = get_field( 'display' );

if ( ! $display && ! is_admin() ) {
	return;
}

$block_attrs = wp_kses_data(
	get_block_wrapper_attributes(
		[
			'class' => 'content-block three-events-section',
			// `??` rather than `?:`: blocks with no anchor set have no `anchor` key
			// at all, which `?:` warns on. Same as event-agenda-block.
			'id'    => $block['anchor'] ?? '',
		]
	)
);

$exclude = \LatitudeMedia\Page_Data()->getItems();
switch ( $type ) {
	case 'upcoming':
		$events_list = get_events_list( 'upcoming', [ 'post__not_in' => $exclude ] );
		break;
	case 'past':
		$events_list = get_events_list( 'past', [ 'post__not_in' => $exclude ] );
		break;
	default:
		$events_list = get_events_list( '', [ 'post__not_in' => $exclude ], $events );
		break;
}

if ( 'upcoming' !== $type && ! $events_list->have_posts() ) {
	return;
}

$post_item_template = get_wrap_rows_from_template(
	'
<li class="{hiddenClass}">
    <div class="image-folder green">
        [thumb]
    </div>
    <div class="content-folder">
        <div class="event-date green">
            [event-type]
            [event-start-date]
        </div>
        [title]
        [excerpt]
    </div>
</li>
'
);

$list_id = uniqid();
?>

<div <?php echo $block_attrs; ?> data-list-id="<?php echo esc_attr( $list_id ); ?>">
	<div class="container">
		<div class="bordered-title green"><?php echo esc_html( $title ); ?></div>
		<div class="three-events-section-wrapper">
			<?php if ( ! $events_list->have_posts() ) : ?>
				<div class="upcoming-events-empty">More events coming soon. To get notified of future events, <a href="/newsletter">subscribe to Latitude's newsletter.</a></div>
			<?php else : ?>
				<ul>
					<?php
					while ( $events_list->have_posts() ) {
						$events_list->the_post();
						get_template_part(
							'template-parts/components/post',
							'item',
							[
								'post_id'  => get_the_ID(),
								'settings' => [
									'thumb' => [
										'size'       => 'list-three-events',
										'link'       => true,
										'link_class' => '',
										'alt_image'  => false,
									],
								],
								'rows'     => $post_item_template['rows'],
								'wrap'     => str_replace(
									'{hiddenClass}',
									$events_list->current_post > 2 ? 'hidden' : '',
									$post_item_template['wrap']
								),
							]
						);
					}
					wp_reset_postdata();
					?>
				</ul>
				<?php if ( $events_list->found_posts > 3 ) : ?>
					<a href="#" class="cta-button green load-more-events" data-list-id="<?php echo esc_attr( $list_id ); ?>"><?php esc_html_e( 'load more', 'ltm' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
