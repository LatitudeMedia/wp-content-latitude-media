<?php
/**
 * The main template file
 *
 */

get_header();
$editorsPicksGlobal = get_field('editors_picks_global', 'options');
\LatitudeMedia\Page_Data()->addItems($editorsPicksGlobal);

the_content();

get_footer();
