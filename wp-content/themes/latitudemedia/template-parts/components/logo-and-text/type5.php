<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Logo and text TYPE 5', 'ltm') . '</h3>';
}
// Set defaults Logo and text.

$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'title'         => '',
        'logo'          => null,
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
          "class" => 'content-block podcasts-sponsorship-section',
          "id" => 'logo-and-text' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
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
        <div class="podcasts-sponsorship-section-wrapper">
            <?php if( !empty($logo) ) : ?>
                <div class="image-folder">
                    <?php do_action('thumbnail_formatting', null, ['size' => 'image-and-text-type4', 'link' => true, 'image_id' => $logo['ID']]); ?>
                </div>
            <?php endif; ?>

            <div class="content-folder">
                <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
            </div>
        </div>
    </div>
</div>