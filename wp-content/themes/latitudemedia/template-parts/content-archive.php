<?php get_header();

global $wp_query;
$term = get_queried_object();
$current_page = max(1, $wp_query->get('paged'));

$args = [
    'display'       => true,
    'forced'        => true,
    'pagination'    => true,
];

$headArgs = [
    'title'     => $term->name,
    'title_template'      => '',
];

if(is_tax('section')) {
    $section_args = array(
        'hide_empty' => false, // also retrieve terms which are not used yet
        'meta_query' => array(
            array(
                'key'       => 'section',
                'value'     => $term->term_id,
                'compare'   => '='
            )
        ),
        'taxonomy'  => 'category',
        'fields'  => 'ids',
    );
    $cats = get_terms( $section_args );

    $args = [
        'display' => true,
        'custom_args' => [
                'cat' => $cats
        ],
        'pagination'    => true,
    ];
}

if(is_category()) {
    $section = get_field('section', 'category_' . $term->term_id, true);
    $headArgs['section']        = $section;
    $headArgs['title_template'] = 'section';
}


if(is_category()) {
    get_template_part('template-parts/blocks/section/categories-header', 'block',
        [
            'display'           => true,
            'section'           => $section,
            'blockAttributes'   => [],
        ]
    );
}

//Title template
get_template_part('template-parts/components/titles/topic-title', $headArgs['title_template'], $headArgs);

//Listing template
get_template_part('template-parts/components/articles-list', 'block', $args);

get_footer();
