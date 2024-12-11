<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Large event block', 'ltm') . '</h3>';
}
// Set defaults Large event block.
$options = wp_parse_args(
    $args,
    [
        'event'             => null,
        'title'             => __('Events', 'ltm'),
        'button_copy'       => __('Register now', 'ltm'),
        'base_color'        => '#C6168D',
        'shadow_color'      => '#F9E8F4',
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($event) ) {
    return;
}

\LatitudeMedia\Page_Data()->addItems([$event->ID]);
$eventStartDate = get_event_start_date($event->ID);
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "style" => "--custom-block-base-color: {$base_color}; --custom-block-shadow-color: {$shadow_color};",
                "class" => 'content-block event-large-item-section',
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>
    <div class="container-narrow">
        <div class="event-large-item-section-wrapper">
            <div class="eyebrow ">
                <div class="eyebrow-label">
                    <?php echo $title; ?>
                </div>
            </div>
            <h3 class="event-title"><?php _e($event->post_title); ?></h3>
            <div class="image-folder">
                <div class="date "><?php _e($eventStartDate); ?></div>
                <?php
                    do_action('thumbnail_formatting', $event->ID, ['size' => 'large-event', 'link' => true, 'img_attr' => ['class' => 'event-thumbnail']]);
                ?>
            </div>
            <div class="description-bar">
                <div class="decription-title"><?php _e('Latitude events Presents:', 'ltm')?></div>
                <?php do_action('print_article_excerpt', $event->ID); ?>
            </div>
            <a href="<?php the_permalink($event->ID)?>" class="cta-button "><span><?php _e($button_copy); ?></span></a>
        </div>
    </div>
</div>