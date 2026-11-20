<?php
/**
 * Single glossary term. The glossary CPT is registered by the ltm-core plugin
 * (includes/PostTypes/Glossary.php); without this template WordPress would
 * fall back to the blog-post single.php.
 */

get_header();

get_template_part( 'template-parts/glossary/term' );

get_footer();
