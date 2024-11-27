<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Sidebar ad banner block', 'ltm') . '</h3>';
}
// Set defaults Sidebar ad banner block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'dynamic_ad_banner' => null,
        'display'           => false,
        'blockAttributes'   => [],
    ]
);


extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($dynamic_ad_banner) ) {
    return;
}
?>

<div
        <?php
        echo wp_kses_data(
            get_block_wrapper_attributes(
                [
                    "class" => 'sidebar-block banner-ad-block',
                    "id" => 'sidebar-ad-banner-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
                ]
            )
        );
        ?>
>
    <div class="banner-ad-block-wrapper">
        <?php
        get_template_part(
            'template-parts/components/ad',
            'banner',
            array(
                'banner_id'  => $dynamic_ad_banner,
            )
        );
        ?>
    </div>
</div>
