<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Content wrapper', 'ltm') . '</h3>';
}
// Set defaults Content with background block.

$options = wp_parse_args($args,
    [
        'blockAttributes'   => [],
    ]
);

extract($options);
$blockAttrs = wp_kses_data(
    get_block_wrapper_attributes(
        [
            "class" => 'content-block editor-content-wrapper',
            "id" => 'editor-content-wrapper' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
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
        <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
    </div>
</div>