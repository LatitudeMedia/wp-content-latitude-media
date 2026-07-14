<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Sidebar image', 'ltm') . '</h3>';
}

$args = isset($args) && is_array($args) ? $args : [];

$options = wp_parse_args(
    $args,
    [
        'image'           => [],
        'display'         => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if (!$display && !is_admin()) {
    return;
}

if (empty($image)) {
    return;
}
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                'class' => 'sidebar-block sidebar-image-block',
                'id'    => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>>
    <div class="sidebar-image-block-wrapper">
        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" loading="lazy">
    </div>
</div>