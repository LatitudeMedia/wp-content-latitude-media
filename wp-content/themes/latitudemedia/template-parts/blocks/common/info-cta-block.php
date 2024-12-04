<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Info cta block', 'ltm') . '</h3>';
}
// Set defaults Info cta block.
$options = wp_parse_args(
    array_merge($args),
    [
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

$my_block_template = array(
    array(
        'core/paragraph',
    ),
    array(
        'acf/styled-button-block',
    ),
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="grey-cta-block-wrapper">
            <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
        </div>
    </div>
</div>