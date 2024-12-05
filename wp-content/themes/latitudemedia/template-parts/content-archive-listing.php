<?php
global $wp_query;
$term = get_queried_object();
$current_page = max(1, $wp_query->get('paged'));

$options = wp_parse_args(
    $args,
    [
        'display'       => true,
        'forced'        => true,
        'pagination'    => true,
        'layout_wrapper'=> 'container-narrow',
    ]
);

//Listing template
get_template_part('template-parts/components/articles-list', 'block', $options);

