<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Ad banner block', 'ltm') . '</h3>';
}
// Set defaults Ad banner block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
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
                "class" => 'content-block',
                "id" => 'ad-banner-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>

    <div class="banner-ad-block">
        <div class="container">
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
    </div>
</div>