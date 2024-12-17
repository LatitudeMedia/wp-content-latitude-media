<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('News with sidebar section block', 'ltm') . '</h3>';
}
global $wp_query;
$current_page = intval(max(1, $_GET['current_page'] ?? 1));

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
            <div class="topics-archive-section-wrapper load-more-container">
                <div class="posts-list-section load-more-posts">
                    <?php
                    $categories = get_section_cats($section->term_id, 'ids');
                    $args = [
                        'number_of_items'   => 8,
                        'display'           => true,
                        'page_data'         => true,
                        'pagination'        => true,
                        'pagination_base'   => 'current_page',
                        'custom_args' => [
                            'cat'   => $categories,
                            'paged' => $current_page,
                        ]
                    ];

                    get_template_part('template-parts/category/articles', 'list',
                        $args
                    );

                    ?>
                </div>
            </div>
            <?php get_template_part('template-parts/blocks/homepage/end','sidebar', array('sidebar_widget' => $sidebar_widget)); ?>
        </div>
    </div>
</div>