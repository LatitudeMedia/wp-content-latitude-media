<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Research banner block', 'ltm') . '</h3>';
}
// Set defaults Research banner block.

$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'display' => false,
        'banner' => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( !$banner ) {
    return;
}
$blockAttrs =  wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block single-research-featured-section',
          "id" => 'research-banner-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>>
    <?php do_action('thumbnail_formatting', null, ['link' => false, 'image_id' => $banner['ID']]); ?>
</div>