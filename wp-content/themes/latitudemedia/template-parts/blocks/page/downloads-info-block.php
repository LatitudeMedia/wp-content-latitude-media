<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Downloads info block', 'ltm') . '</h3>';
}
// Set defaults Downloads info block.
$options = wp_parse_args(
    $args,
    [
        'description'       => '',
        'number'            => '',
        'caption'           => '',
        'number_2'          => '',
        'caption_2'         => '',
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}


$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block downloads-counter-section',
          "id" => 'downloads-info-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="downloads-counter-section-wrapper">
            <div class="description">
                <?php _e($description); ?>
            </div>
            <div class="digits">
                <div class="left">
                    <div class="digit"><?php _e($number); ?></div>
                    <div class="caption"><?php _e($caption); ?></div>
                </div>
                <div class="right">
                    <div class="digit"><?php _e($number_2); ?></div>
                    <div class="caption"><?php _e($caption_2); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>