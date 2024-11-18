<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Logo and text DEFAULT', 'ltm') . '</h3>';
}
// Set defaults Logo and text.

$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'title'         => '',
        'logo'          => null,
        'description'   => '',
        'base_color'    => '#c6168d',
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
          "style" => '--custom-block-base-color: ' . $base_color,
          "class" => 'content-block logo-description-block',
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
        <div class="logo-description-block-wrapper">
            <?php if( !empty($logo) ) : ?>
                <div class="logo-image">
                    <?php do_action('thumbnail_formatting', null, ['link' => false, 'image_id' => $logo['ID']]); ?>
                </div>
            <?php endif; ?>
            <div class="description">
                <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
            </div>
        </div>
    </div>
</div>