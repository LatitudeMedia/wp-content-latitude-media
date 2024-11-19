<?php
global $wp_query;
$current_page = max(1, $wp_query->get('paged'));

get_header();

?>
    <div class="container"><h1><?php _e('Resource Library', 'ltm')?></h1></div>
    <div class="load-more-container">
        <div class="load-more-posts">
            <?php
            if( $current_page === 1 ) {
                get_template_part( 'template-parts/resource/landing', 'feature');
            }

            get_template_part( 'template-parts/content-archive', 'resources' );
            ?>
        </div>
    </div>
<?php
get_footer();

