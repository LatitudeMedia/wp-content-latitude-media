<?php
// Set defaults footer socials.

$options = wp_parse_args(
    $args,
    [
        'title'     => '',
        'section'   => null,
    ]
);

if( empty($options['title']) ) {
    return;
}

$sectionLanding = false;
if( $options['section'] ) {
    $sectionLanding = get_section_landing_type_by_term($options['section']->term_id);
}
?>

<div class="topics-title-block">
    <div class="container">
        <div class="topics-title-block-wrapper">
            <?php if($options['section'] && $sectionLanding) : ?>
                <a href="<?php echo get_the_permalink($sectionLanding); ?>" class="parent-category-link"><?php echo esc_html__($options['section']->name) ?></a>
            <?php endif; ?>
            <h1 class="topics-title"><?php echo esc_html__($options['title']) ?></h1>
        </div>
    </div>
</div>