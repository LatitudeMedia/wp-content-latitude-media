<header>
    <div class="header-wrapper">
        <div class="top-head">
            <div class="container">
                <?php get_template_part( 'template-parts/components/search' ); ?>
                <?php
                $args = array(
                    'theme_location'    => 'top',
                    'container_class'   => 'top-head-menu',
                );
                wp_nav_menu($args);
                ?>
            </div>
        </div>
        <div class="middle-head">
            <div class="container">
                <a class="logo" href="<?php echo home_url('/events/'); ?>">
                    <img class="event-background skip-lazy" src="<?php echo get_template_directory_uri() . '/src/images/events_logo.svg'; ?>" alt="logo" width="180" height="70" />
                </a>
                <div class="logo-description"><?php _e('Covering the new frontiers of the energy transition','ltm'); ?></div>
            </div>
        </div>
    </div>
</header>