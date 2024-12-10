<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Research banner block', 'ltm') . '</h3>';
}
// Set defaults Research banner block.

$options = wp_parse_args(
    $args,
    [
        'post_id'           => false,
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

$blockAttrs =  wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block single-research-featured-section',
          "id" => $blockAttributes['anchor'] ?: '',
      ]
  )
);

if( get_post_type($post_id) === 'order-reports') {
    $researchData = get_order_report_data($post_id);
} else {
    $researchData = get_research_data($post_id);
}
if( empty($researchData['banner']['ID']) ) {
    return;
}
?>

<div <?php echo $blockAttrs; ?>>
    <?php do_action('thumbnail_formatting', null, ['link' => false, 'image_id' => $researchData['banner']['ID']]); ?>
</div>