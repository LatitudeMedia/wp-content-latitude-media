<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Research overview block', 'ltm') . '</h3>';
}
// Set defaults Research overview block.
$options = wp_parse_args(
    $args,
    [
        'content' => '',
        'display' => false,
        'post_id' => null,
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
          "class" => 'content-block right-sidebar-layout',
          "id" => $blockAttributes['anchor'] ?: '',
      ]
  )
);

$researchData = get_research_data($post_id);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="right-sidebar-layout-wrapper">
            <div class="main-column">
                <div class="bordered-title blue"><?php _e('Report Overview', 'ltm'); ?></div>
                <article>
                    <?php echo $content; ?>
                </article>
            </div>
            <div class="sidebar">
                <div class="form-block purchase blue">
                    <div class="form-block-wrapper">
                        <?php
                            if( !empty($researchData['full_price']) ) {
                                printf('<div class="form-title">Purchase the full report: %s</div>', $researchData['full_price']);
                            }
                            if( !empty($researchData['hubspot_payment_link']) ) {
                                printf('<a href="%s" class="button" target="_blank">Order Online</a>', $researchData['hubspot_payment_link']);
                            }
                            if( !empty($researchData['order_page']) ) {
                                printf('<a href="%s" class="center-link" target="_blank">Pay via invoice</a>', get_the_permalink($researchData['order_page']));
                            }
                        ?>
                    </div>
                </div>
                <div class="form-block blue">
                    <div class="form-block-wrapper">
                        <div class="form-title"><?php _e('Download executive summary', 'ltm'); ?></div>
                        <?php echo $researchData['download_form_code']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>