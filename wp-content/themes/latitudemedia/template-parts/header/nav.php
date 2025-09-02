<header>
    <div class="header-wrapper">
        <div class="top-head">
            <div class="container">
                <div class="top-head-wrapper">
                <div class="search-container">
                <?php
                get_template_part( 'template-parts/components/search' );
                /*
                                $args = array(
                                    'theme_location'    => 'top',
                                    'container_class'   => 'top-head-menu',
                                );
                                wp_nav_menu($args);
                                */ ?>
                </div>
                <div class="subscribe-container">
                    <div class="podcast-dropdown" aria-haspopup="listbox" aria-expanded="false">
                        <div class="default-option" role="button" tabindex="0">
                            <span class="default">All podcasts</span>
                            <span class="arrow" aria-hidden="true">
                                <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle;transition:transform .2s ease">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                        <ul class="options">
                            <li><a href="#">Catalyst</a></li>
                            <li><a href="#">Open Circuit</a></li>
                            <li><a href="#">The Green Blueprint</a></li>
                            <li><a href="#">Political Climate</a></li>
                        </ul>
                    </div>
                    <a class="subscribe" href="https://www.latitudemedia.com/newsletter/">Subscribe</a>
                </div>
                </div>
            </div>
        </div>
        <div class="middle-head">
            <div class="container">
                <a class="logo" href="<?php echo home_url('/'); ?>">
                    <img class="skip-lazy" src="<?php echo get_template_directory_uri() . '/src/images/Logo.svg'; ?>" width="380" height="53" alt="logo" />
                </a>
                <div class="logo-description"><?php _e('Covering the new frontiers of the energy transition','ltm'); ?></div>
                <a class="menu-toggle" href="#">
                    <span></span><span></span><span></span>
                </a>
            </div>
            <div class="hamburger-menu" style="display: none;">
                <div class="container triangle"></div>
                <div class="hamburger-menu-wrapper">
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
                        <div class="half">
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
                        <div class="half">
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
        </div>
        <div class="bottom-head has-dark-blue-background-color">
            <div class="container">
                <div class="left-bottom-menu">
                    <ul class="bottom-menu">
                        <li><a href="#">TECH</a></li>
                        <li><a href="#">MARKET</a></li>
                        <li><a href="#">DEALS</a></li>
                    </ul>
                </div>
                <div class="right-bottom-menu">
                    <ul class="bottom-menu">
                        <li><a href="#">PODCASTS</a></li>
                        <li><a href="#">EVENTS</a></li>
                        <li><a href="#">RESEARCH</a></li>
                        <li><a href="#">RESOURCES</a></li>
                    </ul>
                </div>
            </div>
            <?php /*
            $args = array(
                'theme_location' => 'main',
                'container' => 'ul',
                'menu_class'    => 'bottom-head-menu',
                'walker' => new Walker_Main_Menu()
            );
            wp_nav_menu($args);
            */
            ?>
        </div>
    </div>
</header>
