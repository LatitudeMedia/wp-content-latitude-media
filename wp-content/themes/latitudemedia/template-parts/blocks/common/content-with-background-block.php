<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Content with background block', 'ltm') . '</h3>';
}
// Set defaults Content with background block. 

$options = wp_parse_args(
    $args, get_fields() ?: [],
    [
        'background_color'  => [],
        'blockAttributes'   => [],
    ]
);

extract($options);

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "style" => "--custom-block-base-color: {$background_color};",
          "class" => 'content-block about-page-hero',
          "id" => 'content-with-background-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

$my_block_template = array(
    array(
        'core/paragraph',
    ),
);

?>

<div <?php echo $blockAttrs; ?> >
    <div class="container-narrow">
        <div class="about-page-hero-wrapper">
            <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
        </div>
    </div>
</div>
