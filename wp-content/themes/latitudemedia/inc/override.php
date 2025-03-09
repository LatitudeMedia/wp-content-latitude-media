<?php

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
    $page_offset = $offset + ( ($query->query_vars['paged']-1) * $ppp );
    $query->set('offset', $page_offset );
}
add_action( 'pre_get_posts', 'resources_archive_custom_query', 20 );

function news_page_posts_number( $query ) {
    if ( is_admin() || !$query->is_main_query() || !is_home() ) {
        return;
    }
    $query->set( 'posts_per_page', 15 );
}
add_action( 'pre_get_posts', 'news_page_posts_number', 20 );

add_filter( 'pre_get_posts', 'exclude_pages_from_search' );
function exclude_pages_from_search($query) {
    if ( !is_admin() && $query->is_main_query() && $query->is_search ) {
        $query->set( 'post_type', 'post' );
    }

    return $query;
}

add_filter('posts_orderby','search_page_order_by_date', 10, 2);
function search_page_order_by_date( $orderby, $query ){
    global $wpdb;

    if(!is_admin() && is_search())
        $orderby = " {$wpdb->posts}.post_date DESC ";

    return  $orderby;
}

/**
 * Add post rewrite slug url segment /news/
 * @param $args
 * @param $post_type
 * @return mixed
 */
function ltm_custom_post_type_args( $args, $post_type ) {
    if ( $post_type == "post" ) {
        $args['rewrite'] = array(
            'slug' => 'news'
        );
    }

    return $args;
}
add_filter( 'register_post_type_args', 'ltm_custom_post_type_args', 20, 2 );

/**
 * Append to permalink segment /news/
 */
add_filter('pre_post_link', 'ltm_change_post_link', 10, 3);
function ltm_change_post_link($permalink, $post, $leavename) {
    if (get_post_type($post) == 'post') {
        return "/news" . $permalink;
    }
    return $permalink;
}

add_filter( 'register_taxonomy_args', 'ltm_change_author_base_slug', 100, 3);
/**
 * @param $args
 * @param $taxonomy
 * @param $object_type
 * @return mixed
 */
function ltm_change_author_base_slug( $args, $taxonomy, $object_type )
{
    // Only target the built-in taxonomy 'author'
    if ( 'author' !== $taxonomy ) {
        return $args;
    }

    $args["rewrite"] = [
        'slug' => 'authors',
        'with_front' => true
    ];

    return $args;
}
