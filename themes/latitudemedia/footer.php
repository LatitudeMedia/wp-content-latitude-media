<?php
/**
 * The template for displaying the footer
 *
 */

$footerData = get_field('footer_data', 'options');

?>

<div class="footer-push"></div>

</div>

<footer>
    <div class="container">
        <div class="footer-wrapper">
            <div class="top-row">
                <a href="<?php echo home_url('/'); ?>" class="footer-logo">
                    <img alt="logo" src="<?php echo get_stylesheet_directory_uri(); ?>/src/images/Logo.svg">
                </a>
                <?php
                $args = array(
                    'theme_location' => 'footer-main',
                    'container' => 'ul',
                    'menu_class'    => 'footer-navigation',
                );
                wp_nav_menu($args);
                ?>
            </div>
            <div class="middle-row">
                <?php
                $args = array(
                    'theme_location' => 'footer',
                    'container' => 'ul',
                    'menu_class'    => 'middle-menu',
                );
                wp_nav_menu($args);

                $args = array(
                    'theme_location' => 'footer-logos',
                    'container' => 'ul',
                    'menu_class'    => 'footer-logos',
                );
                wp_nav_menu($args);
                ?>
            </div>
            <div class="bottom-row">
                <div class="footer-links">
                    <?php
                    $args = array(
                        'theme_location' => 'footer-privacy',
                        'container' => 'ul',
                    );
                    wp_nav_menu($args);
                    ?>
                </div>
                <div class="footer-socials">
                    <?php
                    $args = array(
                        'theme_location' => 'footer-social',
                        'container' => 'ul',
                    );
                    wp_nav_menu($args);
                    ?>
                </div>
                <div class="footer-copyright">
                    <?php printf('copyright %1$s Latitude media', date('Y')); ?>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>

</body>
</html>
