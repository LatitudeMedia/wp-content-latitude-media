<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event description block TYPE 2 (with form)', 'ltm') . '</h3>';
}
// Set defaults Event description block.
$options = wp_parse_args(
    $args,
    [
        'title'     => 'About',
        'post_id'   => null,
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
            "class" => 'content-block right-sidebar-layout',
            "id" => $blockAttributes['anchor'] ?: '',
        ]
    )
);

$my_block_template = array(
    array(
        'core/paragraph',
    ),
);

$eventForm  = get_field('form_code__registration_cta', $post_id);
$formText   = get_field('form_text', $post_id);
?>
<div <?php echo $blockAttrs; ?>>
    <div class="container-narrow">
        <div class="right-sidebar-layout-wrapper">
            <div class="main-column">
                <div class="event-text-section">
                    <div class="container-narrow">
                        <div class="event-text-section-wrapper">
                            <div class="bordered-title green"><?php _e($title); ?></div>
                            <article>
                                <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $my_block_template ) ); ?>" />
                            </article>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar">
                <div class="form-block green">
                    <div class="form-block-wrapper">
                        <div class="form-title"><?php echo $formText; ?></div>
                        <?php echo $eventForm; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>