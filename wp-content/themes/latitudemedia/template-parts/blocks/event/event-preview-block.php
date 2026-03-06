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
        'main_logo' => [],
        'label_below' => false,
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
            <?php if (!$label_below): ?>
                <div class="data-row">
                    <?php
                    if (in_array('date', $rows)) {
                        echo '<div class="date">';
                        do_action('print_event_date_range', $post_id);
                        echo '</div>';
                    }

                    if (in_array('type', $rows)) {
                        $eventTypeDisplay = $eventData['event_type'] ?? '';
                        if ($eventTypeDisplay === 'frontier-forum') {
                            $eventTypeDisplay = 'Frontier Forum';
                        } elseif ($eventTypeDisplay === 'live-podcast') {
                            $eventTypeDisplay = 'Live Podcast';
                        } else {
                            $eventTypeDisplay = ucfirst($eventTypeDisplay);
                        }
                        printf('<div class="location">%s</div>', $eventTypeDisplay);
                    }
                    ?>
                </div>
            <?php endif; ?>
            <?php

            if (!empty($main_logo) && is_array($main_logo)) {
                $imageHtml = $main_logo['url'];
                printf('<div class="main-logo-wrapper"><img alt="img" class="main-logo" src="%s"></div>', $imageHtml);
            } else {
                if (in_array('title', $rows)) {
                    printf('<h1>%s</h1>', get_the_title($post_id));
                }
            }

            if (!$label_below) {
                if (in_array('location', $rows)) {
                    do_action('print_event_location', $post_id);
                }
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
            if ($label_below) {
                ob_start();
                do_action('print_event_date_range', $post_id);
                $date_output = ob_get_clean();
                $location_label = isset($eventData['location']) ? $eventData['location'] : '';
                printf(
                    '<div class="labels-wrapper"><div class="location label-text">%s</div><div class="date label-text">%s</div></div>',
                    esc_html($location_label),
                    $date_output
                );
            }
            ?>
        </div>
    </div>
</div>