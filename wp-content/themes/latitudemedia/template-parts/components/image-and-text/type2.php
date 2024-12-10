<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Image and text TYPE 2', 'ltm') . '</h3>';
}
// Set defaults Image and text.

$options = wp_parse_args(
    $args,
    [
        'title'         => '',
        'logo'          => null,
        'image_link'    => null,
        'description'   => '',
        'base_color'    => '#c6168d',
        'shadow_color'  => '#F9E8F4',
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

$blockType = ltm_get_block_style($blockAttributes['className'] ?? []);

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "style" => "--custom-block-base-color: {$base_color}; --custom-block-shadow-color: {$shadow_color};",
          "class" => 'content-block logo-description-block reverse logo-description-block-bordered',
          "id" => $blockAttributes['anchor'] ?: '',
      ]
  )
);

$my_block_template = array(
    array(
        'core/paragraph',
    ),
);

?>

<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
        <div class="logo-description-block-wrapper">
            <?php if( !empty($logo) ) : ?>
                <div class="logo-image">
                    <?php do_action('print_image_and_text_image', $logo, 'image-text-default', $image_link); ?>
                </div>
            <?php endif; ?>

            <div class="description">
                <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
            </div>
        </div>
    </div>
</div>