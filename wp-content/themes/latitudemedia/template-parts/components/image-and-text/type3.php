<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Image and text TYPE 3', 'ltm') . '</h3>';
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
          "class" => 'content-block icon-text-block',
          "id" => 'image-and-text' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
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
        <div class="icon-text-block-wrapper">
            <?php if( !empty($logo) ) : ?>
                <div class="icon-folder">
                    <?php do_action('thumbnail_formatting', null, ['link' => false, 'image_id' => $logo['ID']]); ?>
                </div>
            <?php endif; ?>

            <div class="content-folder">
                <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
            </div>
        </div>
    </div>
</div>