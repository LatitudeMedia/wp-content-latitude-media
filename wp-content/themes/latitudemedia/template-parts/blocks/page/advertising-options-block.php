<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Advertising options block', 'ltm') . '</h3>';
}
// Set defaults Advertising options block.
$options = wp_parse_args(
    array_merge($args),
    [
        'title'     => 'Advertising Options',
        'options'   => [],
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
          "class" => 'content-block adv-options-section',
          "id" => 'advertising-options-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);

if ( empty($options) ) {
    return;
}

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="bordered-title"><?php _e($title); ?></div>
        <div class="adv-options-section-wrapper">
            <ul>
                <?php
                    foreach ($options as $option) {
                        $linkHtml = !empty($option['link']) ? sprintf('<a href="%s" class="listen-link">Listen to sample</a>', $option['link']) : '';
                        printf('<li><div class="title">%s</div><p>%s</p>%s</li>', $option['title'], $option['copy'], $linkHtml);
                    }
                ?>
            </ul>
        </div>
    </div>
</div>