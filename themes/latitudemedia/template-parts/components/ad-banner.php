
<?php
// Set defaults footer socials.

$options = wp_parse_args(
    $args,
    [
        'code' => '',
        'wrap' => '',
    ]
);

extract($options);

if( empty($code) ) {
    return;
}

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