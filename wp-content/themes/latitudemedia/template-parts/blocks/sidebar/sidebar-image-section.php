<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Sidebar image', 'ltm') . '</h3>';
}

$args = isset($args) && is_array($args) ? $args : [];

$options = wp_parse_args(
    $args,
    [
        'image'           => [],
        'image_link'      => [],
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
        <?php
        $image_link_url = is_array($image_link) ? ($image_link['url'] ?? '') : '';
        if (!empty($image_link_url)) :
            $image_link_target = !empty($image_link['target']) ? $image_link['target'] : '_self';
        ?>
            <a href="<?php echo esc_url($image_link_url); ?>" target="<?php echo esc_attr($image_link_target); ?>">
                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" loading="lazy">
            </a>
        <?php else : ?>
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" loading="lazy">
        <?php endif; ?>
    </div>
</div>