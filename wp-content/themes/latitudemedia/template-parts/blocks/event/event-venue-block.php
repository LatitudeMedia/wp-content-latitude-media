<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event venue block', 'ltm') . '</h3>';
}
// Set defaults Event venue block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
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
          "class" => 'content-block venue-section green',
          "id" => 'event-venue-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="venue-section-wrapper">
            <div class="bordered-title">Venue</div>
            <img alt="map" src="client/assets/images/map.png">
            <div class="venue-info">
                <div class="date">
                    <h2>Date</h2>
                    <div class="venue-date">December 3, 2024</div>
                </div>
                <div class="location">
                    <h2>Location</h2>
                    <address>
                        <div class="place">Washington, DC</div>
                        <p><strong>Convene, Arlington Virginia</strong></p>
                        <p>1201 Wilson Boulevard Arlington, VA 22209</p>
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
