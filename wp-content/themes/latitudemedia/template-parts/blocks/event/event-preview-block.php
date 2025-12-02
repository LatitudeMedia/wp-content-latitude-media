<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event preview block', 'ltm') . '</h3>';
}
// Set defaults Event preview block.

$options = wp_parse_args(
    $args,
    [
        'post_id'   => null,
        'rows'      => [
            'date',
            'type',
            'title',
            'location',
            'button',
        ],
        'subtitle'      => '',
        'logos_title' => 'Co-hosted with:',
        'co_hosted_logo' => null,
        'display'   => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if (!$display && !is_admin()) {
    return;
}

$blockAttrs = wp_kses_data(
    get_block_wrapper_attributes(
        [
            "class" => 'content-block single-event-hero-section',
            "id" => $blockAttributes['anchor'] ?: '',
        ]
    )
);

$date       = get_event_start_date($post_id, 'F j, Y - h:i A');
$endDate    = get_event__end_date($post_id, 'F j, Y - h:i A');
$eventData  = get_fields($post_id);
$registerButtonTitle = 'Register';
if (!empty($eventData['event_type']) && ($eventData['event_type'] === 'virtual' || $eventData['event_type'] === 'webinar')) {
    $registerButtonTitle = 'Watch Recording';
}
?>

<div <?php echo $blockAttrs; ?>>
    <?php
    if (!empty($eventData['background_image'])) {
        printf('<img alt="img" class="bg-img" src="%s">', $eventData['background_image']['url']);
    }
    ?>
    <div class="container">
        <div class="single-event-hero-section-wrapper">
            <div class="data-row">
                <?php
                if (in_array('date', $rows)) {
                    echo '<div class="date">';
                    do_action('print_event_date_range', $post_id);
                    echo '</div>';
                }

                if (in_array('type', $rows)) {
                    printf('<div class="location">%s</div>', ucfirst($eventData['event_type'] ?? ''));
                }
                ?>
            </div>
            <?php

            if (in_array('title', $rows)) {
                printf('<h1>%s</h1>', get_the_title($post_id));
            }

            if (in_array('location', $rows)) {
                do_action('print_event_location', $post_id);
            }

            if (in_array('button', $rows) && !empty($eventData['link'])) {
                do_action('button_unit', ['title' => $registerButtonTitle, 'url' => $eventData['link']], null, 'strict-button green');
            }

            if (!empty($subtitle)) {
                printf('<div class="subtitle">%s</div>', $subtitle);
            }
            if (!empty($co_hosted_logo) && is_array($co_hosted_logo)) {
                $imageHtml = "";
                foreach ($co_hosted_logo as $logo) {
                    $imageHtml .= thumbnail_formatting(null, ['image_id' => $logo, 'size' => 'event-preview-hosted', 'link' => false, 'img_attr' => ['class' => 'logo']], false);
                }
                printf('<div class="logo-wrapper"><div class="label">%s</div>%s</div>', $logos_title ?? 'Co-hosted with:', $imageHtml);
            }
            ?>
        </div>
    </div>
</div>