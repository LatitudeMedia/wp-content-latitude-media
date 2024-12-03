<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for In House Ads
 *
 * @package LatitudeMedia
 */

/**
 * Class for the In House Ads post type.
 */
class InHouseAds {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'in-house-ads';

    /**
     * Constructor.
     */
    public function __construct() {
        // Create the post type
        add_action( 'init', array( $this, 'create_post_type' ) );
    }

    /**
     * Creates the post type.
     */
    public function create_post_type() {
        register_post_type(
            $this->name,
            [
                'labels' => [
                    'name'                  => __( 'In House Ads', 'ltm' ),
                    'singular_name'         => __( 'In House Ad', 'ltm' ),
                    'add_new'               => __( 'Add New In House Ad', 'ltm' ),
                    'add_new_item'          => __( 'Add New In House Ad', 'ltm' ),
                    'edit_item'             => __( 'Edit In House Ad', 'ltm' ),
                    'new_item'              => __( 'New In House Ad', 'ltm' ),
                    'view_item'             => __( 'View In House Ad', 'ltm' ),
                    'view_items'            => __( 'View In House Ads', 'ltm' ),
                    'search_items'          => __( 'Search In House Ads', 'ltm' ),
                    'not_found'             => __( 'No In House Ads found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No In House Ads found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent In House Ad:', 'ltm' ),
                    'all_items'             => __( 'All In House Ads', 'ltm' ),
                    'archives'              => __( 'In House Ad Archives', 'ltm' ),
                    'attributes'            => __( 'In House Ad Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into In House Ad', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this In House Ad', 'ltm' ),
                    'filter_items_list'     => __( 'Filter In House Ads list', 'ltm' ),
                    'items_list_navigation' => __( 'In House Ads list navigation', 'ltm' ),
                    'items_list'            => __( 'In House Ads list', 'ltm' ),
                    'menu_name'             => __( 'In House Ads', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-media-document',
                'public'        => true,
                'map_meta_cap'  => true,
                'has_archive'   => false,
                'show_ui'       => true,
                'show_in_rest'  => true,
                'exclude_from_search' => true,
                'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            ]
        );
    }
}
