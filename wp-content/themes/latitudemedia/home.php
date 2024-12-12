<?php
/**
 * The main template file
 *
 */

get_header();
$args = [
    'display'       => true,
    'forced'        => true,
    'pagination'    => true,
];

get_template_part('template-parts/components/titles/topic-title', '', ['title' => 'Latest News']);

get_template_part('template-parts/components/articles-list', 'block', $args);

get_footer();
