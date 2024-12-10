<?php
get_header();

get_template_part( 'template-parts/team/landing', 'info', ['team_member' => get_the_ID()] );

get_footer();

?>