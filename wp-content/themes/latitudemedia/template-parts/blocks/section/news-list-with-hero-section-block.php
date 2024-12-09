<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('News list with hero section block', 'ltm') . '</h3>';
}
// Set defaults News list with hero section block.
$options = wp_parse_args(
    $args,
    [
        'display' => false,
        'blockAttributes' => [],
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

$categories = get_section_cats($section->term_id, 'ids');
get_template_part(
    'template-parts/blocks/homepage/news-with-hero',
    'section',
    array(
        'number_of_items' => 4,
        'type'            => 'category',
        'category'        => $categories,
        'style'           => 'hero_left_three',
        'display'         => true,
        'blockAttributes' => $blockAttributes,
    )
);

?>