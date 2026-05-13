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

$ad_banner_instance_id = wp_unique_id('ad-banner-');

if (!$display && !is_admin()) {
    return;
}

if (empty($dynamic_ad_banner)) {
    return;
}
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class"                  => 'content-block',
                "id"                     => $blockAttributes['anchor'] ?: '',
                'data-ad-banner-instance' => $ad_banner_instance_id,
            ]
        )
    );
    ?>>
    <?php
    get_template_part(
        'template-parts/components/ad',
        'banner',
        array(
            'banner_id'  => $dynamic_ad_banner,
            'screen_type' => $screen_type,
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
<script>
    (() => {
        const instanceId = <?php echo wp_json_encode($ad_banner_instance_id); ?>;
        const root = document.querySelector(`[data-ad-banner-instance="${instanceId}"]`);
        if (!root) {
            return;
        }

        let pendingFrameId = null;

        const syncBannerWrapperFromGoogleAds = () => {
            pendingFrameId = null;
            const bannerWrapper = root.querySelector('.banner-ad-block-wrapper');
            const googleWrappers = root.querySelectorAll('div[id^="google_ads_iframe"]');

            if (!bannerWrapper) {
                return;
            }

            const has250Height =
                googleWrappers.length > 0 && [...googleWrappers].some((el) => Math.round(el.offsetHeight) === 250);
            bannerWrapper.classList.toggle('with-button', has250Height);
        };

        const scheduleSync = () => {
            if (pendingFrameId !== null) {
                return;
            }
            pendingFrameId = requestAnimationFrame(syncBannerWrapperFromGoogleAds);
        };

        scheduleSync();
        window.addEventListener('gpt-slots-ready', scheduleSync);
        new MutationObserver(scheduleSync).observe(root, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class'],
        });
    })();
</script>