<?php
/**
 * Template Name: Full width template
 *
 */

get_header();

$hide_title = get_field('hide_title', get_the_ID());
if( !$hide_title ) {
    printf('<div class="container-narrow"><h1>%s</h1></div>', get_the_title() );
}

the_content();

get_footer();
