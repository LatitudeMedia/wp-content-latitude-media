<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Ad banner block', 'ltm') . '</h3>';
}
// Set defaults Ad banner block.
$options = wp_parse_args(
    $args,
    [
        'dynamic_ad_banner' => null,
        'screen_type'       => null,
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
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>
    <?php
    get_template_part(
        'template-parts/components/ad',
        'banner',
        array(
            'banner_id'  => $dynamic_ad_banner,
            'screen_type'=> $screen_type,
            'wrap'       => '
            <div class="banner-ad-block">
                <div class="container">
                    <div class="banner-ad-block-wrapper">
                        %s
                    </div>
                </div>
            </div>
            ',
        )
    );
    ?>

</div>