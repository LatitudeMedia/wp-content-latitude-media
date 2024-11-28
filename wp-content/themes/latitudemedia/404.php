<?php
/**
 * The main template file
 *
 */

get_header();

?>
    <div class="four-o-four-section">
        <div class="container-narrow">
            <div class="four-o-four-section-wrapper">
                <h1><?php _e('404', 'ltm'); ?></h1>
                <h2><?php _e('Oops! Nothing was found.', 'ltm'); ?></h2>
                <p><?php _e('The page you are looking for might have been removed, had it’s name changed, or is temporarily unavailable.', 'ltm'); ?></p>
                <a href="<?php echo home_url(); ?>" class="strict-button"><?php _e('Go back home', 'ltm'); ?></a>
            </div>
        </div>
    </div>
<?php get_footer();
