<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event description block DEFAULT', 'ltm') . '</h3>';
}
// Set defaults Event description block.
$options = wp_parse_args(
    $args,
    [
        'title'     => 'About',
        'display'   => false,
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
            "class" => 'content-block event-text-section',
            "id" => 'event-description-block' . ($blockAttributes['anchor'] ? ' ' . $blockAttributes['anchor'] : ''),
        ]
    )
);

$my_block_template = array(
    array(
        'core/paragraph',
    ),
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="event-text-section-wrapper">
            <div class="bordered-title green"><?php _e($title); ?></div>
            <article>
                <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
            </article>
        </div>
    </div>
</div>