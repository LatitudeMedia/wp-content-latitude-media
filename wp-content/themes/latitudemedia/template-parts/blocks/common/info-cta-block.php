<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Info cta block', 'ltm') . '</h3>';
}
// Set defaults Info cta block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'title'         => false,
        'background'    => false,
        'button'        => false,
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
            <?php do_action('section_title', $title, '<h2>%1$s</h2>'); ?>
            <?php do_action('button_unit', $button, null, 'cta-button orange'); ?>
        </div>
    </div>
</div>