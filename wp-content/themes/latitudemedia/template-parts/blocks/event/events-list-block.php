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
        $eventsList = get_upcoming_events();
        break;
    case 'past':
        $eventsList = get_past_events();
        break;
    default:
        $eventsList = \LatitudeMedia\Manage_Data()->curated_query(['post_type' => 'events', 'posts_per_page' => 3], $events);
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
?>

<div <?php echo $blockAttrs; ?>
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
            <a href="#" class="cta-button green">load more</a>
        </div>
    </div>
</div>

