<?php
/**
 * Enqueue site scripts
 */

if( !function_exists( 'ltm_scripts' ) ) {
    /**
     * Enqueue site JavaScript
     */

    function ltm_scripts() {
        if ( ! defined( 'WP_ENVIRONMENT_TYPE' ) || WP_ENVIRONMENT_TYPE === 'dev') {
            wp_enqueue_script( 'ltm-ads', get_template_directory_uri() . '/src/assets/js/dfp-ads.js', array(), filemtime( get_template_directory() . '/src/assets/js/dfp-ads.js' ), true );
            wp_enqueue_script( 'theme-app', get_template_directory_uri() . '/src/assets/js/custom.js', array('jquery', 'jquery-core'), filemtime( get_template_directory() . '/src/assets/js/custom.js'),true );
            wp_enqueue_script( 'load-more-app', get_template_directory_uri() . '/src/assets/js/load-more.js', array('jquery', 'jquery-core'), filemtime( get_template_directory() . '/src/assets/js/load-more.js'),true );
        }
        else {
            wp_enqueue_script( 'ltm-ads', get_template_directory_uri() . '/dist/js/dfp-ads.min.js', array(), filemtime( get_template_directory() . '/dist/js/dfp-ads.min.js' ), true );
            wp_enqueue_script( 'theme-app', get_template_directory_uri() . '/dist/js/custom.min.js', array('jquery', 'jquery-core'), filemtime( get_template_directory() . '/dist/js/custom.min.js'),true );
            wp_enqueue_script( 'load-more-app', get_template_directory_uri() . '/dist/js/load-more.min.js', array('jquery', 'jquery-core'), filemtime( get_template_directory() . '/dist/js/load-more.min.js'),true );
        }

        wp_localize_script(
            'ltm-ads',
            'wpDfpAdsSettings',
            array(
                'slots'   => get_field('dfp_ad_slots', 'options'),
            )
        );
    }

    add_action( 'wp_enqueue_scripts', 'ltm_scripts' );
}

if( !function_exists( 'ltm_styles' ) ) {
    /**
     * Enqueue site styles
     */
    function ltm_styles() {
        wp_register_style('ltm-fonts', get_template_directory_uri() . '/dist/css/fonts.min.css', array(), filemtime( get_template_directory() . '/dist/css/fonts.min.css') );
        wp_register_style('ltm-footer', get_template_directory_uri() . '/dist/css/footer.min.css', array(), filemtime( get_template_directory() . '/dist/css/footer.min.css') );
        wp_register_style('block-acf-news-list-section', get_template_directory_uri() . '/dist/css/blocks/news-list-section.min.css', array(), filemtime( get_template_directory() . '/dist/css/blocks/news-list-section.min.css') );
        wp_enqueue_style('block-acf-news-list-section');
        wp_register_style('block-acf-signup-form-section', get_template_directory_uri() . '/dist/css/blocks/signup-form-section.min.css', array(), filemtime( get_template_directory() . '/dist/css/blocks/signup-form-section.min.css') );
        wp_enqueue_style('block-acf-signup-form-section');
        if( is_front_page() ) {
            wp_register_style('ltm-homepage', get_template_directory_uri() . '/dist/css/homepage.min.css', array(), filemtime( get_template_directory() . '/dist/css/homepage.min.css') );
            wp_enqueue_style('ltm-homepage');
        }

        if( !is_front_page() ) {
            wp_register_style('ltm-pages', get_template_directory_uri() . '/dist/css/pages.min.css', array(), filemtime( get_template_directory() . '/dist/css/pages.min.css') );
            wp_enqueue_style('ltm-pages');
        }

        if( is_singular('research')) {
            wp_register_style('block-acf-news-plates-section', get_template_directory_uri() . '/dist/css/blocks/news-plates-section.min.css', array(), filemtime( get_template_directory() . '/dist/css/blocks/news-plates-section.min.css') );
            wp_enqueue_style('block-acf-news-plates-section');

        }
        wp_enqueue_style( array( 'ltm-fonts', 'ltm-footer') );
    }
    add_action( 'wp_enqueue_scripts', 'ltm_styles' );
}

if( !function_exists( 'add_rel_preload' ) ) {

    function add_rel_preload($tag, $handle, $href, $media)
    {
        if (!is_admin()) {
            return str_replace('rel=\'stylesheet\'', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $tag)
                . '<noscript><link rel="stylesheet" href="' . $href . '"></noscript>';

        } else {
            return $tag;
        }
    }

    add_filter('style_loader_tag', 'add_rel_preload', 10, 4);
}

add_action('wp_head', 'ltm_add_pubads_script');
function ltm_add_pubads_script()
{
    ?>
    <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
    <script>
        window.googletag = window.googletag || {cmd: []};
        googletag.cmd.push(function() {
            googletag.pubads().enableSingleRequest();
            googletag.enableServices();
        });
    </script>
    <?php
}

add_action('wp_head', 'ltm_add_google_analytics_script');
function ltm_add_google_analytics_script()
{
    ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4827S951CT"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-4827S951CT');
    </script>
    <?php
}

add_action('wp_head', 'ltm_add_hubspot_tracking_script');
function ltm_add_hubspot_tracking_script()
{
    ?>
    <!-- Start of HubSpot Embed Code -->
    <script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/44409563.js"></script>
    <!-- End of HubSpot Embed Code -->
    <?php
}
//Dequeue Styles
function ltm_dequeue_unnecessary_styles() {
    wp_dequeue_style( 'multiple-authors-page-css' );
    wp_deregister_style( 'multiple-authors-page-css' );
}
add_action( 'wp_print_styles', 'ltm_dequeue_unnecessary_styles' );


function hook_critical_css() {
    $critical_css = file_get_contents( get_template_directory_uri() . '/dist/css/base.min.css' );
    $critical_css .= file_get_contents( get_template_directory_uri() . '/dist/css/header.min.css' );

    // Detect the news-with-hero-section or news-list-with-hero-section-block and loading assets as critical.
    if( has_block( 'acf/news-with-hero-section' )
        || has_block( 'acf/news-list-with-hero-section-block' ) ) {
        $critical_css .= file_get_contents( get_template_directory_uri() . '/dist/css/blocks/news-with-hero.min.css' );
    }

    // Detect the categories-section-block and loading assets as critical.
    if ( has_block( 'acf/categories-section-block' ) ) {
        $critical_css .= file_get_contents( get_template_directory_uri() . '/dist/css/blocks/categories-section-block.min.css' );
    }

    // Detect the categories-section-block and loading assets as critical.
    if ( is_front_page() ) {
        $critical_css .= file_get_contents( get_template_directory_uri() . '/dist/css/blocks/sidebar-editors-picks-section.min.css' );
    }
    echo '<style id="ltm-critical-css">' . $critical_css . '</style>';
}
add_action('wp_head','hook_critical_css');

add_filter('script_loader_tag', 'add_script_defer_attribute', 10, 2);

function add_script_defer_attribute($tag, $handle)
{
    if( !str_contains($tag, 'defer') ) {
        $tag = str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}