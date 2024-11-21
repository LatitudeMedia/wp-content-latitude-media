<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Featured research block', 'ltm') . '</h3>';
}
// Set defaults Featured research block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'display'       => false,
        'research'      => null,
        'description'   => '',
        'base_color'        => '#0095DA',
        'shadow_color'      => '#E5F4FC',
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( !$research ) {
    return;
}

?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "style" => '--custom-block-base-color: ' . $base_color . '; --custom-block-shadow-color: ' . $shadow_color,
                "class" => 'content-block event-large-item-section',
                "id" => 'featured-research-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
            ]
        )
    );
    ?>
>
    <div class="container-narrow">
        <div class="event-large-item-section-wrapper">
            <div class="eyebrow">
                <div class="eyebrow-label">
                    <?php _e('Featured research', 'ltm'); ?>
                </div>
            </div>
            <h1 class="event-title"><?php echo get_the_title($research); ?></h1>
            <?php echo $description; ?>
            <div class="image-folder">
                <?php
                    if( $date = get_research_date($research->ID) ) {
                        printf('<div class="date">%s</div>', $date);
                    }
                    
                    if( has_post_thumbnail($research) ) {
                        do_action('thumbnail_formatting', $research->ID, ['link' => true]);
                    }
                    ?>
            </div>
            <a href="<?php the_permalink($research); ?>" class="cta-button"><span><?php _e('learn more', 'ltm')?></span></a>
        </div>
    </div>
</div>