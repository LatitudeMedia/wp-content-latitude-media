<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Order preview block', 'ltm') . '</h3>';
}
// Set defaults Order preview block. 
$options = wp_parse_args(
    $args,
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
          "class" => 'content-block image-text-section report-image-text-block blue',
          "id" => $blockAttributes['anchor'] ?: '',
      ]
  )
);
$researchData = get_order_report_data($post_id);

if( empty($researchData['research_id']) ) {
    return;
}
?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="image-text-section-wrapper">
            <div class="image-folder">
                <a href="#">
                    <?php
                    if( has_post_thumbnail($researchData['research_id']) ) {
                        do_action('thumbnail_formatting', $researchData['research_id'], ['link' => false, 'size' => 'image-text-type7']);
                    }
                    ?>
                </a>
            </div>
            <div class="content-folder">
                <?php
                printf('<h1>%s</h1>', __('Order Report', 'ltm') );
                printf('<p class="description">%s</p>', get_the_title($researchData['research_id']));

                if( !empty($researchData['full_price']) ) {
                    printf('<p class="date">PRICE: %s</p>', $researchData['full_price']);
                }
                ?>
            </div>
        </div>
    </div>
</div>