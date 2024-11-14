<header>
    <div class="header-wrapper">
        <div class="top-head">
            <div class="container">
                <?php
                get_template_part( 'template-parts/components/search' );

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
                <a class="logo" href="<?php echo home_url('/'); ?>">
                    <img src="<?php echo get_template_directory_uri() . '/src/images/Logo.svg'; ?>" alt="logo" />
                </a>
                <div class="logo-description"><?php _e('Covering the new frontiers of the energy transition','ltm'); ?></div>
            </div>
        </div>
        <div class="bottom-head">
            <div class="container toggle-container">
                <a class="menu-toggle" href="#">
                    <span></span><span></span><span></span>
                </a>
            </div>

            <?php
            $args = array(
                'theme_location' => 'main',
                'container' => 'ul',
                'menu_class'    => 'bottom-head-menu',
                'walker' => new Walker_Main_Menu()
            );
            wp_nav_menu($args);
            ?>
            <div class="hamburger-menu" style="display: none;">
                <div class="mobile-search">
                    <div class="container">
                        <?php get_template_part( 'template-parts/components/search', 'mobile' ); ?>
                    </div>
                </div>
                <div class="container">
                    <div class="left-side">
                        <?php
                        $args = array(
                            'theme_location' => 'main',
                            'container' => 'ul',
                            'menu_class'    => 'desktop-menu',
                        );
                        wp_nav_menu($args);
                        ?>
                        <div class="accordion-menu">
                            <div class="hamburger-accordion">
                                <?php
                                $args = array(
                                    'theme_location' => 'main',
                                    'items_wrap' => '%3$s',
                                    'walker'    => new AccordionMenu_Walker(),
                                );
                                wp_nav_menu($args);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="right-side">
                        <?php
                        $args = array(
                            'theme_location' => 'tab-menu',
                            'container' => 'ul',
                            'menu_class'    => 'right-menu',
                            'walker' => new Walker_Main_Menu()
                        );
                        wp_nav_menu($args);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
