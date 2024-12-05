<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event preview block', 'ltm') . '</h3>';
}
// Set defaults Event preview block.

$options = wp_parse_args(
    $args,
    [
        'post_id'   => null,
        'rows'      => [
                'date',
                'type',
                'series',
                'location',
                'button',
        ],
        'subtitle'      => '',
        'co_hosted_logo'=> null,
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
          "class" => 'content-block single-event-hero-section',
          "id" => 'event-preview-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);

$date       = get_event_start_date($post_id, 'F d, Y H:i');
$eventData  = get_fields($post_id);
$registerButtonTitle = 'Register';
if( $eventData['event_type'] === 'virtual') {
    $registerButtonTitle = 'Watch Recording';
}
?>

<div <?php echo $blockAttrs; ?>
>
    <?php
        if( !empty($eventData['background_image']) ) {
            printf('<img alt="img" class="bg-img" src="%s">', $eventData['background_image']['url']);
        }
    ?>
    <div class="container">
        <div class="single-event-hero-section-wrapper">
            <div class="data-row">
                <?php
                    if( in_array('date', $rows) ) {
                        printf('<div class="date">%s ET</div>', $date);
                    }

                    if( in_array('type', $rows) ) {
                        printf('<div class="location">%s</div>', ucfirst($eventData['event_type']) ?? '');
                    }
                ?>
            </div>
            <?php
                if( in_array('series', $rows) && !empty($eventData['event_series']['label']) ) {
                    printf('<div class="upper-heading">%s</div>', $eventData['event_series']['label']);
                }
            ?>
            <?php
                printf('<h1>%s</h1>', get_the_title($post_id));

                if( in_array('location', $rows) ) {
                    do_action('print_event_location', $post_id);
                }

                if( in_array('button', $rows) && !empty($eventData['link']) ) {
                    do_action('button_unit', ['title' => $registerButtonTitle, 'url' => $eventData['link']], null, 'strict-button green');
                }

                if( !empty($subtitle) ) {
                    printf('<div class="subtitle">%s</div>', $subtitle);
                }
                if ( !empty($co_hosted_logo) ) {
                    $imageHtml = thumbnail_formatting(null, ['image_id' => $co_hosted_logo, 'size' => 'event-preview-hosted', 'link' => false], false);
                    printf('<div class="logo-wrapper"><div class="label">Co-hosted with:</div>%s</div>', $imageHtml);
                }
            ?>
        </div>
    </div>
</div>