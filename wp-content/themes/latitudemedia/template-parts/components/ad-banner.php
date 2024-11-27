
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

$bannerData = get_ad_banner_data($banner_id);
$adSizes = array_column($bannerData['size_mapping'], 'ad_size_dynamic');
$adSizes = array_unique(array_merge(...$adSizes));

$width  = 0;
$height = 0;
foreach ($adSizes as $key => $adSize) {
    $size = explode('x', $adSize);
    if( $key == 0 ) {
        $width = $size[0];
        $height = $size[1];
        continue;
    }
    if( $size[0] < $width ) {
        $width  = $size[0];
    }
    if( $size[1] < $height ) {
        $height = $size[1];
    }
}

$style = '';
if($width) {
    $style .= 'min-width: ' . $width . 'px;';
}
if($height) {
    $style .= ' min-height: ' . $height . 'px;';
}

if($wrap) ob_start();
?>
<!-- <?php echo $bannerData['dfp_path']; ?> -->
<div id='<?php echo $banner_id; ?>' style='<?php echo $style; ?>'>
    <script>
        window.addEventListener('gpt-slots-ready', () => {
            googletag.cmd.push(function() { googletag.display('<?php echo $banner_id; ?>'); });
        });
    </script>
</div>
<?php
if($wrap) $banner = ob_get_clean();

if($wrap) {
    printf($wrap, $banner);
}
?>