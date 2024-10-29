<?php

if ( ! function_exists( 'ltm_setup' ) ) {
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * @return void
     */
    function ltm_setup() {
        add_theme_support( 'title-tag' );

        add_theme_support( 'post-thumbnails' );

        add_post_type_support( 'page', 'excerpt' );

        register_nav_menus(
            array(
                'top'           => __( 'Top menu', 'ltm' ),
                'main'          => __( 'Main menu', 'ltm' ),
                'tab-menu'      => __( 'Header tab menu', 'ltm' ),
                'footer-main'   => __( 'Footer main menu', 'ltm' ),
                'footer-logos'  => __( 'Footer logos menu', 'ltm' ),
                'footer-social' => __( 'Footer social menu', 'ltm' ),
                'footer-privacy'=> __( 'Footer privacy menu', 'ltm' ),
                'footer'        => __( 'Footer menu', 'ltm' )
            )
        );


        /*
         * Add support for core custom logo.
         */
        $logo_width  = 450;
        $logo_height = 96;

        add_theme_support(
            'custom-logo',
            array(
                'height'               => $logo_height,
                'width'                => $logo_width,
                'flex-width'           => true,
                'flex-height'          => true,
                'unlink-homepage-logo' => true,
            )
        );
    }
}
add_action( 'after_setup_theme', 'ltm_setup' );


/**
 * Register widget area.
 *
 * @return void
 */
function wp_blank_widgets_init() {
    // Article Sidebar.
    register_sidebar(
        array(
            'name'        => __( 'Article Sidebar', 'innolead' ),
            'id'          => 'article-sidebar',
            'description' => __( 'Sidebar for article.', 'innolead' ),
            'before_widget' => '',
            'after_widget' => '',
            'show_in_rest' => true,
        )
    );

    if ( function_exists( 'get_field' ) ) {
        if ($sidebars = get_field('sidebar_areas', 'options')) {
            foreach ($sidebars as $key => $sidebar) {
                register_sidebar(
                    array(
                        'name' => __($sidebar['name'], 'innolead'),
                        'id' => 'custom-sidebar-' . $key,
                        'description' => __('Sidebar for ' . $sidebar['name'] . ' .', 'innolead'),
                        'before_widget' => '<div class="sidebar-holder">',
                        'after_widget' => '</div>',
                    )
                );
            }
        }
    }
}
add_action( 'widgets_init', 'wp_blank_widgets_init' );



/**
 * Predefine event blocks on create new event.
 */
function create_post_predefine_blocks() {
    $page_type_object = get_post_type_object( 'post' );
    $page_type_object->template = array(
        array( 'core/paragraph', array('placeholder' => 'Content Body')),
        array( 'acf/signup-form-section', array('data' => array('field_6707ca1167942' => 1)) ),
        array( 'core/paragraph', array('placeholder' => 'Content Body')),
    );
}
add_action( 'init', 'create_post_predefine_blocks', 100 );
