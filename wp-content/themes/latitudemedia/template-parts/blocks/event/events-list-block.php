<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Events list block', 'ltm') . '</h3>';
}
// Set defaults Events list block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
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
          "id" => 'events-list-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

switch ($type) {
    case 'upcoming':
        $eventsList = get_events_list('upcoming', ['paged' => $page]);
        break;
    case 'past':
        $eventsList = get_events_list('past', ['paged' => $page]);
        break;
    default:
        $eventsList = get_events_list('', ['paged' => $page], $events);
        break;
}

if( !$eventsList->have_posts() ) {
    return;
}

$postItemTemplate = get_wrap_rows_from_template('
<li>
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
?>

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
                    $wrap = $postItemTemplate['wrap'];
                    get_template_part(
                        'template-parts/components/post',
                        'item',
                        array(
                            'post_id'  => get_the_ID(),
                            'settings' => array(
                                'thumb'   => array(
                                    'size'       => 'news-with-hero',
                                    'link'       => true,
                                    'link_class' => '',
                                    'alt_image'  => false,
                                    'type'       => true,
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
                if($eventsList->max_num_pages > 1 && $eventsList->max_num_pages > $page) {
                    $page++;
                    printf('<a href="#" class="cta-button green load-more-events" data-page="%s" data-type="%s" data-events="%s" data-list-id="%s">%s</a>', $page, $type, implode(',', $events), $listID, __('load more'));
                }
            ?>
        </div>
    </div>
</div>
