<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event short description block', 'ltm') . '</h3>';
}
// Set defaults Event short description block. 
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
          "class" => 'content-block event-text-section',
          "id" => 'event-short-description-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="event-text-section">
        <div class="container-narrow">
            <div class="event-text-section-wrapper">
                <h2><?php echo get_the_excerpt(); ?></h2>
            </div>
        </div>
    </div>
</div>

