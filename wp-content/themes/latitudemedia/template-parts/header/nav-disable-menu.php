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
    </div>
</header>
