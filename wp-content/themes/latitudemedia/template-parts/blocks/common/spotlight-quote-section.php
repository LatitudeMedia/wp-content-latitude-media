<?php
// Set defaults Spotlight quote block.
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Spotlight quote block', 'ltm') . '</h3>';
}

$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'copy'      => false,
        'name'      => false,
        'display'   => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($copy) ) {
    return;
}

?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block testimonial-block',
                "id" => 'spotlight-quote-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <div class="testimonial-block-wrapper">
        <h5><?php _e($copy); ?></h5>
        <?php if( !empty($name) ) : ?>
            <div class="author"><?php _e($name); ?></div>
        <?php endif;?>
    </div>
</div>
