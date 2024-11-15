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
 * Predefine event blocks on create new post.
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

/**
 * Predefine event blocks on create new section landing.
 */
function create_section_landing_predefine_blocks() {
    $page_type_object = get_post_type_object( 'sections-landing' );
    $page_type_object->template = array(
        array( 'acf/categories-section-block', array('data' => array('field_672ccb402a55a' => 1))),
        array( 'acf/news-list-with-hero-section-block', array('data' => array('field_672ccb5048a74' => 1))),
        array( 'acf/subscribe-form-block', array('data' => array('field_672ca9403c777' => 1))),
        array( 'acf/news-with-sidebar-section-block', array('data' => array('field_672ccb6767b8e' => 1))),
    );
}
add_action( 'init', 'create_section_landing_predefine_blocks', 100 );

/**
 * Predefine event blocks on create new single research.
 */
function create_single_research_predefine_blocks() {
    $page_type_object = get_post_type_object( 'research' );
    $page_type_object->template = array(
        array( 'acf/research-banner-block', array('data' => array('field_67354ddb2f441' => 1))),
        array( 'acf/research-preview-block', array('data' => array('field_67354de6440d2' => 1))),
        array( 'acf/research-overview-block', array('data' => array('field_67354f7fc926c' => 1))),
        array( 'acf/logo-and-text', array('data' => array('field_67354f9994828' => 1))),
    );
}
add_action( 'init', 'create_single_research_predefine_blocks', 100 );

function set_pagination_base () {

    global $wp_rewrite;

    $wp_rewrite->pagination_base = '';

}
add_action( 'init', 'set_pagination_base' );

// Modify pagination base to use a GET parameter
function custom_pagination_base( $query ) {
    if ( !is_admin() && ($query->is_main_query() || is_tax('section')) ) {
        // Check if the `page` GET parameter is set
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        if ( isset( $_GET['page'] ) ) {
            $paged = $_GET['page'];
        }
        $query->set( 'paged', $paged );
    }
}
add_action( 'pre_get_posts', 'custom_pagination_base' );
