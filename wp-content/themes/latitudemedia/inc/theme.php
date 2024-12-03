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

        add_theme_support('custom-spacing');

        $black              = '#000000';
        $white              = '#ffffff';
        $primaryPink        = '#C6168D';
        $pinkShadow         = '#F9E8F4';
        $blue               = '#0095da';
        $blueShadow         = '#E5F4FC';
        $green              = '#00B48D';
        $greenShadow        = '#CCF0E8';
        $orange             = '#F99D1C';
        $orangeShadow       = '#FEEBD2';
        $primaryDarkBlue    = '#0F1E42';
        $greyBackground     = '#F5F5F5';

        add_theme_support(
            'editor-color-palette',
            array(
                array(
                    'name'  => esc_html__( 'Black', 'ltm' ),
                    'slug'  => 'black',
                    'color' => $black,
                ),
                array(
                    'name'  => esc_html__( 'White', 'ltm' ),
                    'slug'  => 'white',
                    'color' => $white,
                ),
                array(
                    'name'  => esc_html__( 'Dark blue', 'ltm' ),
                    'slug'  => 'dark-blue',
                    'color' => $primaryDarkBlue,
                ),
                array(
                    'name'  => esc_html__( 'Grey', 'ltm' ),
                    'slug'  => 'grey',
                    'color' => $greyBackground,
                ),
                array(
                    'name'  => esc_html__( 'Primary pink', 'ltm' ),
                    'slug'  => 'primary-pink',
                    'color' => $primaryPink,
                ),
                array(
                    'name'  => esc_html__( 'Pink shadow', 'ltm' ),
                    'slug'  => 'pink-shadow',
                    'color' => $pinkShadow,
                ),
                array(
                    'name'  => esc_html__( 'Blue', 'ltm' ),
                    'slug'  => 'blue',
                    'color' => $blue,
                ),
                array(
                    'name'  => esc_html__( 'Blue shadow', 'ltm' ),
                    'slug'  => 'blue-shadow',
                    'color' => $blueShadow,
                ),
                array(
                    'name'  => esc_html__( 'Green', 'ltm' ),
                    'slug'  => 'green',
                    'color' => $green,
                ),
                array(
                    'name'  => esc_html__( 'Green shadow', 'ltm' ),
                    'slug'  => 'green-shadow',
                    'color' => $greenShadow,
                ),
                array(
                    'name'  => esc_html__( 'Orange', 'ltm' ),
                    'slug'  => 'orange',
                    'color' => $orange,
                ),
                array(
                    'name'  => esc_html__( 'Orange shadow', 'ltm' ),
                    'slug'  => 'orange-shadow',
                    'color' => $orangeShadow,
                ),
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
            'name'        => __( 'Article Sidebar', 'ltm' ),
            'id'          => 'article-sidebar',
            'description' => __( 'Sidebar for article.', 'ltm' ),
            'before_widget' => '',
            'after_widget' => '',
            'show_in_rest' => true,
        )
    );
    // Podcast default sidebar.
    register_sidebar(
        array(
            'name'        => __( 'Podcast default Sidebar', 'ltm' ),
            'id'          => 'podcast-default-sidebar',
            'description' => __( 'Sidebar for podcasts list.', 'ltm' ),
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
                        'name' => __($sidebar['name'], 'ltm'),
                        'id' => 'custom-sidebar-' . $key,
                        'description' => __('Sidebar for ' . $sidebar['name'] . ' .', 'ltm'),
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
        array( 'acf/signup-form-section', array() ),
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
        array( 'acf/image-and-text', array('data' => array('field_67354f9994828' => 1))),
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

// Modify pagination base to use a GET parameter
function resources_archive_custom_query( $query ) {
    if ( is_admin() || !$query->is_main_query() || !is_post_type_archive( 'resources' ) ) {
        return;
    }
    $offset = 1;
    $ppp = 9;
    $query->set( 'posts_per_page', $ppp );
    if ( isset( $_GET['page'] ) ) {
        $page_offset = $offset + ( ($query->query_vars['paged']-1) * $ppp );
        $query->set('offset', $page_offset );
    }
    else {
        $query->set('offset', $offset);
    }
}
add_action( 'pre_get_posts', 'resources_archive_custom_query' );

add_filter( 'pre_get_posts', 'exclude_pages_from_search' );
function exclude_pages_from_search($query) {
    if ( !is_admin() && $query->is_main_query() && $query->is_search ) {
        $query->set( 'post_type', 'post' );
    }

    return $query;
}

register_block_pattern(
    'ltm-patterns/event-images-and-quotes',
    array(
        'title'       => __( 'Event images and quotes', 'ltm' ),
        'description' => _x( 'Two horizontal images, and quotes below.', '', 'ltm' ),
        'categories'  => array('ltm-events'),
        'content'     => "<!-- wp:acf/content-wrapper {\"name\":\"acf/content-wrapper\",\"align\":\"center\",\"mode\":\"preview\"} -->
<!-- wp:columns -->
<div class=\"wp-block-columns\"><!-- wp:column -->
<div class=\"wp-block-column\"><!-- wp:image -->
<figure class=\"wp-block-image\"><img alt=\"\"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class=\"wp-block-column\"><!-- wp:image -->
<figure class=\"wp-block-image\"><img alt=\"\"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {\"height\":\"32px\"} -->
<div style=\"height:32px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>
<!-- /wp:spacer -->

<!-- wp:acf/spotlight-quote-section {\"name\":\"acf/spotlight-quote-section\",\"data\":{\"field_6706990eeed8e\":\"Copy\",\"field_67069930eed8f\":\"Name\",\"field_67069833ec942\":\"1\"},\"mode\":\"edit\"} /-->
<!-- /wp:acf/content-wrapper -->",
    )
);

register_block_pattern_category(
    'ltm-events',
    array( 'label' => __( 'Latitude Media Event', 'ltm' ) )
);