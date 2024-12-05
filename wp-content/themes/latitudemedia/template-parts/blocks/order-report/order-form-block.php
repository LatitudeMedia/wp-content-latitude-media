<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Order form block', 'ltm') . '</h3>';
}
// Set defaults Order form block.
$options = wp_parse_args(
    $args,
    [
        'post_id' => null,
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
          "class" => 'content-block order-form-section',
          "id" => 'order-form-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
      ]
  )
);

$researchData = get_order_report_data($post_id);

if( empty($researchData['purchase_form_code']) ) {
    return;
}
?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="order-form-section-wrapper">
            <?php echo $researchData['purchase_form_code']; ?>
        </div>
    </div>
</div>