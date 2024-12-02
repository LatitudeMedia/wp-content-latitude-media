<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Info cta block', 'ltm') . '</h3>';
}
// Set defaults Info cta block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'copy'          => '',
        'button'        => [],
        'base_color'    => '#00B48D',
        'shadow_color'  => '#CCF0E8',
        'display'       => false,
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
          "style" => "--custom-block-base-color: {$base_color}; --custom-block-shadow-color: {$shadow_color};",
          "class" => 'content-block grey-cta-block',
          "id" => 'info-cta-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="grey-cta-block-wrapper">
            <?php
                if( !empty($copy) ) {
                    echo $copy;
                }
                do_action('button_unit', $button, null, 'cta-button');
            ?>
        </div>
    </div>
</div>