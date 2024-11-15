<?php
global $wp_query;
$term = get_queried_object();
$current_page = max(1, $wp_query->get('paged'));


$args = [
    'display'       => true,
    'forced'        => true,
    'pagination'    => true,
];

//Listing template
get_template_part('template-parts/components/articles-list', 'block', $args);

