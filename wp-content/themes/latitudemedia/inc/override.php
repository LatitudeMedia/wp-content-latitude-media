<?php

add_filter('paginate_links', function($link) {
    if (!is_search()) {
        $link = str_replace('/page/', '', $link);
    }

    return $link;
});

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

add_filter( 'pre_get_posts', 'exclude_pages_from_search', 20, 1);
function exclude_pages_from_search($query) {
    if ( !is_admin() && $query->is_main_query() && $query->is_search ) {
        $query->set( 'post_type', 'post' );
    }

    return $query;
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

function get_in_content_article_ad($post = null) {
    $_post = get_post( $post );

    try {
        if ( ! ( $_post instanceof WP_Post ) ) {
            throw new Exception();
        }

        $postTags = get_the_tags($_post->ID);
        if (!$postTags) {
            throw new Exception();
        }

        $postTagIds = wp_list_pluck($postTags, 'term_id');
        $adsSettings = get_field('tags_ad_matching', 'options');

        if( empty($adsSettings) ) {
            throw new Exception();
        }

        foreach ($adsSettings as $adsSetting) {
            $hasMatch = array_intersect($adsSetting['tags'], $postTagIds);
            if( !empty($hasMatch) ) {
                return $adsSetting;
            }
        }
    } catch (\Exception) {}

    return [];
}

add_filter( 'the_content_ads', 'insert_in_content_ads', 10, 1 );
function insert_in_content_ads($sourceContent) {

    $adBanner = get_in_content_article_ad();

    if(!$adBanner) {
        return $sourceContent;
    }

    $content = get_content_intro_paragraph($sourceContent, $adBanner['after_paragraph']);

    $content .= force_balance_tags(do_blocks('<!-- wp:acf/ad-banner-section {"name":"acf/ad-banner-section","data":{"field_67475a59d0aef":{"field_67475a59d0aef_field_6747599474bb6":"' . $adBanner['dynamic_ad_banner'] . '","field_67475a59d0aef_field_674759f174bb7":""},"field_671298a6ae568":"1"}} /-->'));

    $content .= get_content_body_paragraph($sourceContent, $adBanner['after_paragraph']);

    return $content;
}

function get_content_intro_paragraph($content, $cut_paragraph_count = 1) {
    $parts = explode("</p>", $content);
    $output = '';
    foreach($parts AS $k => $paragraph)
    {
        if($k >= $cut_paragraph_count) break;

        $output .= $paragraph.'</p>';
    }
    unset($parts);

    return force_balance_tags( $output );
}

function get_content_body_paragraph($content, $cut_paragraph_count = 1) {
    $parts = explode("</p>", $content);
    $output = '';
    foreach($parts AS $k => $paragraph)
    {
        if($k >= $cut_paragraph_count) {
            $output .= $paragraph . '</p>';
        }
    }
    unset($parts);

    return force_balance_tags( $output );
}