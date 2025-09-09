<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Categories header block', 'ltm') . '</h3>';
}

$term = get_queried_object();
// Set defaults Categories section block.
$options = wp_parse_args(
    $args,
    [
        'display'           => false,
        'section'           => null,
        'blockAttributes'   => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if(!$section) {
    return;
}
$sectionLanding = get_section_landing_type_by_term($section->term_id);

$categories = get_section_cats($section->term_id, 'all');
?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block section-top-nav-section section-categories-top-nav-section',
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>
    <div class="container">
        <div class="section-top-nav-section-wrapper">
            <div class="section-breadcrumb">
                <ul>
                    <?php
                        printf('<li><span>%1$s</span>/</li>',  $section->name);
                        printf('<li><a href="#">%1$s</a></li>',  $term->name);
                    ?>
                </ul>
            </div>
            <div class="section-categories">
                <span class="title">TAGS:</span>
                <div class="selected-option"><?php echo $term->name; ?></div>
                <ul>
                    <?php
                    printf('<li><a href="%s">All in %s</a></li>', get_the_permalink($sectionLanding), $section->name);

                    foreach ($categories as $category) {
                        printf('<li><a href="%s">%s</a></li>', get_term_link($category->term_id, 'category'), $category->name);
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>