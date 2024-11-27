
<?php
// Set defaults footer socials.

$options = wp_parse_args(
    $args,
    [
        'banner_id' => null,
        'wrap' => '',
    ]
);

extract($options);

if( empty($banner_id) ) {
    return;
}

$bannerPost = get_published_post_by_id($banner_id, ['post_type' => 'in-house-ads']);
if( !$bannerPost ) {
    return;
}

$code = get_field('google_ad_code', $bannerPost->ID);

if($wrap) ob_start();
?>
<div class="banner-ad-block">
    <div class="container">
        <div class="banner-ad-block-wrapper">
            <?php echo $code; ?>
        </div>
    </div>
</div>
<?php
if($wrap) $banner = ob_get_clean();

if($wrap) {
    printf($wrap, $banner);
}
?>