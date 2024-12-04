<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Popup modal block', 'ltm') . '</h3>';
}
// Set defaults Popup modal block.
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

$classes = !is_admin() ? 'modal-content' : '';
$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block pink ' . $classes,
          "id" => ($options['blockAttributes']['anchor'] ?: ''),
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
    <div class="modal-folder">
        <a href="#" class="cross js-modal-close"></a>
        <div class="form">
            <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
        </div>
    </div>
</div>