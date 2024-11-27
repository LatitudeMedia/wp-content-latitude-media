<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Ad banner block', 'ltm') . '</h3>';
}
// Set defaults Ad banner block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?: []),
    [
        'banner'        => null,
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($banner) ) {
    return;
}

$bannerPost = get_published_post_by_id($banner, ['post_type' => 'in-house-ads']);

if( !$bannerPost ) {
    return;
}

$code = get_field('google_ad_code', $bannerPost->ID);
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
    <?php
    get_template_part(
        'template-parts/components/ad',
        'banner',
        array(
            'code'  => $code,
        )
    );
    ?>
</div>