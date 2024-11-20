<?php
/**
 * Enqueue site scripts
 */

if( !function_exists( 'ltm_scripts' ) ) {
    /**
     * Enqueue site JavaScript
     */

    function ltm_scripts() {
        if ( ! defined( 'ENV_TYPE' ) || ENV_TYPE === 'dev') {
            wp_enqueue_script( 'theme-app', get_template_directory_uri() . '/src/assets/js/custom.js', array('jquery', 'jquery-core'), filemtime( get_template_directory() . '/src/assets/js/custom.js'),true );
            wp_enqueue_script( 'load-more-app', get_template_directory_uri() . '/src/assets/js/load-more.js', array('jquery', 'jquery-core'), filemtime( get_template_directory() . '/src/assets/js/load-more.js'),true );
        }
        else {
            wp_enqueue_script( 'theme-app', get_template_directory_uri() . '/dist/js/custom.min.js', array('jquery', 'jquery-core'), filemtime( get_template_directory() . '/dist/js/custom.min.js'),true );
            wp_enqueue_script( 'load-more-app', get_template_directory_uri() . '/dist/js/load-more.min.js', array('jquery', 'jquery-core'), filemtime( get_template_directory() . '/dist/js/load-more.min.js'),true );
        }
    }

    add_action( 'wp_enqueue_scripts', 'ltm_scripts' );
}

if( !function_exists( 'ltm_styles' ) ) {
    /**
     * Enqueue site styles
     */
    function ltm_styles() {
        wp_register_style('ltm-fonts', get_template_directory_uri() . '/dist/css/fonts.min.css', array(), filemtime( get_template_directory() . '/dist/css/fonts.min.css') );
        wp_register_style('ltm-base', get_template_directory_uri() . '/dist/css/base.min.css', array(), filemtime( get_template_directory() . '/dist/css/base.min.css') );
        wp_register_style('ltm-header', get_template_directory_uri() . '/dist/css/header.min.css', array(), filemtime( get_template_directory() . '/dist/css/header.min.css') );
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
        wp_enqueue_style( array( 'ltm-base', 'ltm-fonts', 'ltm-header', 'ltm-footer') );
    }
    add_action( 'wp_enqueue_scripts', 'ltm_styles' );
}

if( !function_exists( 'add_rel_preload' ) ) {

    function add_rel_preload($tag, $handle, $href, $media)
    {
        if (!is_admin()) {
            if ($handle !== 'ltm-base') {
                return str_replace('rel=\'stylesheet\'', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $tag)
                    . '<noscript><link rel="stylesheet" href="' . $href . '"></noscript>';
            } else {
                return $tag;
            }
        } else {
            return $tag;
        }
    }

    add_filter('style_loader_tag', 'add_rel_preload', 10, 4);
}


//if ( ! function_exists( 'custom_block_editor_scripts' ) ) {
//    /**
//     * Enqueue block editor scripts
//     *
//     */
//    function custom_block_editor_scripts() {
//        wp_enqueue_script( 'admin-custom', get_template_directory_uri() . '/dist/js/admin.min.js', array('jquery', 'jquery-core'), filemtime( get_template_directory() . '/dist/js/admin.min.js'),true );
//    }
//    add_action( 'enqueue_block_editor_assets', 'custom_block_editor_scripts' );
//
//}