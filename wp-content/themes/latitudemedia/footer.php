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
                    <?php printf('copyright &commat; %1$s Latitude media.inc', date('Y')); ?>
                </div>
            </div>
        </div>
    </div>



    <div id="modal2" class="advertise-modal-content pink">
        <div class="modal-folder">
            <a href="#" class="cross js-modal-close"></a>
            <div class="podcast-modal-content">
                <ul>
                    <li>
                        <h5>Every week is a must listen.</h5>
                        <p>“This is the best show on climate/clean tech – make sure you’re ready to go deep! Shayle is a great host and knows how to get into the core issues with each guest. Every week is a must listen.”</p>
                        <div class="stars">
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                        </div>
                    </li>
                    <li>
                        <h5>Every week is a must listen.</h5>
                        <p>“This is the best show on climate/clean tech – make sure you’re ready to go deep! Shayle is a great host and knows how to get into the core issues with each guest. Every week is a must listen.”</p>
                        <div class="stars">
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                            <div class="star">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.5 0L10.4084 5.87336L16.584 5.87336L11.5878 9.50329L13.4962 15.3766L8.5 11.7467L3.50383 15.3766L5.41219 9.50329L0.416019 5.87336L6.59163 5.87336L8.5 0Z" fill="#C6168D"></path>
                                </svg>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <a href="#" class="close-button js-modal-close">Close</a>
        </div>
    </div>



</footer>
<?php wp_footer(); ?>

</body>
</html>
