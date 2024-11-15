<?php
get_header();

$term = get_queried_object();

get_template_part( 'template-parts/author/landing', 'info', ['author' => $term] );

get_template_part( 'template-parts/content-archive', 'listing' );

get_footer();

?>