<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('In house ad block', 'ltm') . '</h3>';
}

// Set defaults In house ad block.
$options = wp_parse_args(
    $args,
    [
        'in_house_ad'   => false,
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($in_house_ad) ) {
    return;
}

$inHouseAdPost = get_published_post_by_id($in_house_ad, ['post_type' => 'in-house-ads']);
if(!$inHouseAdPost) {
    return;
}
$inHouseAdPostData = ltm_get_inhouse_ad_data($in_house_ad);

$backgroundImage = $inHouseAdPostData['background_image']['url'] ?? '';
if ( !empty($backgroundImage) && class_exists('Jetpack_PostImages')) {
    $backgroundImage = Jetpack_PostImages::fit_image_url($backgroundImage, 700, 285);
}

$baseColor = "#0095DA";
$shadowColor  = "#E5F4FC";

if( !empty($inHouseAdPostData['base_color']) ) {
    $baseColor = $inHouseAdPostData['base_color'];
}
if( !empty($inHouseAdPostData['shadow_color']) ) {
    $shadowColor  = $inHouseAdPostData['shadow_color'];
}
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "style" => '--custom-block-base-color: ' . $baseColor . '; --custom-block-shadow-color: ' . $shadowColor . '; ',
                "class" => 'content-block',
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>
    <div class="news-rec-banner-section" style="background-image: url(<?php echo $backgroundImage; ?>)">
        <div class="overlay"></div>
        <div class="news-rec-banner-section-wrapper">
            <div class="eyebrow">
                <div class="eyebrow-label"><?php _e($inHouseAdPostData['banner_text']); ?></div>
            </div>
            <h5><?php _e($inHouseAdPostData['heading']); ?></h5>
            <?php echo apply_filters( 'the_content', $inHouseAdPost->post_content ); ?>
            <?php do_action('button_unit', $inHouseAdPostData['button'], null, 'cta-button'); ?>
        </div>
    </div>
</div>
