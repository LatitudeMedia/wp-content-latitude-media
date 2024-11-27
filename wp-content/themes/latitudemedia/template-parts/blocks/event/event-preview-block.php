<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event preview block', 'ltm') . '</h3>';
}
// Set defaults Event preview block.

$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'post_id' => null,
        'display' => false,
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
          "id" => 'event-preview-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
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
                <div class="date"><?php _e($date); ?> ET</div>
                <div class="location"><?php _e(ucfirst($eventData['event_type']) ?? ''); ?></div>
            </div>
            <?php
                if( !empty($eventData['event_series']['label']) ) {
                    printf('<div class="upper-heading">%s</div>', $eventData['event_series']['label']);
                }
            ?>
            <h1><?php echo get_the_title($post_id);?></h1>
            <?php
                do_action('print_event_location', $post_id);

                if( !empty($eventData['link']) ) {
                    do_action('button_unit', ['title' => 'Register', 'url' => $eventData['link']], null, 'reg-button green');
                }
            ?>
        </div>
    </div>
</div>