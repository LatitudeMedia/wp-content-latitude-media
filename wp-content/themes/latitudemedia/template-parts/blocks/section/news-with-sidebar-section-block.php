<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('News with sidebar section block', 'ltm') . '</h3>';
}
// Set defaults News with sidebar section block.
$options = wp_parse_args(
    $args,
    [
        'post_id'           => null,
        'sidebar_widget'    => null,
        'display'           => false,
        'blockAttributes'   => [],
    ]
);

extract($options);


if(!$display && !is_admin()) {
    return;
}

$section = get_field('section_type', $post_id);
if(!$section) {
    return;
}

?>

<div
    <?php
    echo wp_kses_data(
        get_block_wrapper_attributes(
            [
                "class" => 'content-block section-topics-section',
                "id" => $blockAttributes['anchor'] ?: '',
            ]
        )
    );
    ?>
>
    <div class="container">
        <div class="section-topics-section-wrapper">
            <?php get_template_part('template-parts/blocks/homepage/begin','sidebar'); ?>
            <div class="topics-archive-section-wrapper">
                <div class="posts-list-section">
                    <?php
                    $categories = get_section_cats($section->term_id, 'ids');
                    $args = [
                        'number_of_items' => 8,
                        'display' => true,
                        'custom_args' => [
                            'cat' => $categories
                        ]
                    ];

                    get_template_part('template-parts/category/articles', 'list',
                        $args
                    );
                    ?>
                    <a href="<?php echo get_term_link($section->term_id, 'section'); ?>" class="cta-button pink"><span><?php _e('See more', 'ltm'); ?></span></a>
                </div>
            </div>
            <?php get_template_part('template-parts/blocks/homepage/end','sidebar', array('sidebar_widget' => $sidebar_widget)); ?>
        </div>
    </div>
</div>