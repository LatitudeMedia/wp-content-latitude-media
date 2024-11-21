<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Sidebar info block', 'ltm') . '</h3>';
}
// Set defaults Sidebar info block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'title'     => '',
        'button'    => [],
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
          "class" => 'sidebar-block info-block',
          "id" => 'sidebar-info-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="info-block-wrapper">
        <?php do_action('section_title', $title, '<h5>%1$s</h5>'); ?>
        <?php do_action('button_unit', $button, null, 'cta-button'); ?>
    </div>
</div>
