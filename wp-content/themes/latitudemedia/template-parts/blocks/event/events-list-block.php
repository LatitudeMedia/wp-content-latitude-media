<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Events list block', 'ltm') . '</h3>';
}
// Set defaults Events list block.
$options = wp_parse_args(
    $args,
    [
        'title'     => 'Upcoming events',
        'type'      => [],
        'events'    => [],
        'page'      => 1,
        'display'   => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}


$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block three-events-section',
          "id" => $blockAttributes['anchor'] ?: '',
      ]
  )
);

$exclude = \LatitudeMedia\Page_Data()->getItems();
switch ($type) {
    case 'upcoming':
        $eventsList = get_events_list('upcoming', ['post__not_in' => $exclude]);
        break;
    case 'past':
        $eventsList = get_events_list('past', ['post__not_in' => $exclude]);
        break;
    default:
        $eventsList = get_events_list('', ['post__not_in' => $exclude], $events);
        break;
}

$postItemTemplate = get_wrap_rows_from_template('
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
');

$listID = uniqid();

if( $type !== 'upcoming' && !$eventsList->have_posts() ) {
    return;
}
?>


<?php if( $type === 'upcoming' && !$eventsList->have_posts() ) : ?>
    <div <?php echo $blockAttrs; ?> data-list-id="<?php echo $listID;?>"
    >
        <div class="container">
            <div class="bordered-title green"><?php _e($title); ?></div>
            <div class="three-events-section-wrapper">
                <div class="upcoming-events-empty">More events coming soon. To get notified of future events, <a href="/newsletter">subscribe to Latitude's newsletter.</a></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div <?php echo $blockAttrs; ?> data-list-id="<?php echo $listID;?>"
    >
        <div class="container">
            <div class="bordered-title green"><?php _e($title); ?></div>
            <div class="three-events-section-wrapper">
                <ul>
                    <?php
                    while( $eventsList->have_posts() ) {
                        $eventsList->the_post();
                        $rows = $postItemTemplate['rows'];
                        if($eventsList->current_post > 2) {
                            $wrap = preg_replace("/\{hiddenClass\}/", "hidden", $postItemTemplate['wrap']);
                        }
                        else {
                            $wrap = preg_replace("/\{hiddenClass\}/", "", $postItemTemplate['wrap']);
                        }
                        get_template_part(
                            'template-parts/components/post',
                            'item',
                            array(
                                'post_id'  => get_the_ID(),
                                'settings' => array(
                                    'thumb'   => array(
                                        'size'       => 'list-three-events',
                                        'link'       => true,
                                        'link_class' => '',
                                        'alt_image'  => false,
                                    ),
                                ),
                                'rows'          => $rows,
                                'wrap'          => $wrap,
                            )
                        );

                    }
                    ?>
                </ul>
                <?php
                    if($eventsList->found_posts > 3) {
                        printf('<a href="#" class="cta-button green load-more-events" data-page="%s" data-list-id="%s">%s</a>', $page, $listID, __('load more'));
                    }
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>