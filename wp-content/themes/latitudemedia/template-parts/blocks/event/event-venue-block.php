<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event venue block', 'ltm') . '</h3>';
}
// Set defaults Event venue block.
$options = wp_parse_args(
    $args,
    [
        'title'             => 'Venue',
        'embed_code'        => false,
        'location_details'  => false,
        'post_id'           => null,
        'display'           => false,
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
          "class" => 'content-block venue-section green',
          "id" => 'event-venue-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);
?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="venue-section-wrapper">
            <div class="bordered-title"><?php _e($title); ?></div>
            <?php
                if( !empty($embed_code) ) {
                    echo $embed_code;
                }
            ?>
            <div class="venue-info">
                <div class="date">
                    <h2>Date</h2>
                    <?php do_action('print_event_start_date', $post_id, ['wrap' => '<div class="venue-date">%1$s</div>', 'format' => 'F d, Y']); ?>
                </div>
                <div class="location">
                    <h2>Location</h2>
                    <address>
                        <?php
                            do_action('print_event_location', $post_id);
                            echo $location_details;
                        ?>
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
