<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Large event block', 'ltm') . '</h3>';
}
// Set defaults Large event block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'event'             => null,
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

$eventStartDate = get_event_start_date($event->ID);
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "style" => '--event-embed-info-color: ' . $base_color . '; --event-embed-info-shadow-color: ' . $shadow_color,
                "class" => 'content-block event-large-item-section',
                "id" => 'large-event-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <div class="container">
        <div class="event-large-item-section-wrapper">
            <div class="eyebrow ">
                <div class="eyebrow-label">
                    <?php _e('Events', 'ltm'); ?>
                </div>
            </div>
            <h3 class="event-title"><?php _e($event->post_title); ?></h3>
            <div class="image-folder">
                <div class="date "><?php _e($eventStartDate); ?></div>
                <?php
                    do_action('thumbnail_formatting', $event->ID, ['size' => 'large-event-thumbnail', 'link' => true, 'img_attr' => ['class' => 'event-thumbnail']]);
                ?>
            </div>
            <div class="description-bar">
                <div class="decription-title"><?php _e('Latitude events Presents:', 'ltm')?></div>
                <?php do_action('print_article_excerpt', $event->ID); ?>
            </div>
            <a href="<?php the_permalink($event->ID)?>" class="event-button "><span><?php _e('register now', 'ltm'); ?></span></a>
        </div>
    </div>
</div>