<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Industry News
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Industry News post type.
 */
class IndustryNews {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'industry-news';

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
                    'name'                  => __( 'Industry News', 'ltm' ),
                    'singular_name'         => __( 'Industry News', 'ltm' ),
                    'add_new'               => __( 'Add Industry News', 'ltm' ),
                    'add_new_item'          => __( 'Add Industry News', 'ltm' ),
                    'edit_item'             => __( 'Edit Industry News', 'ltm' ),
                    'new_item'              => __( 'New Industry News', 'ltm' ),
                    'view_item'             => __( 'View Industry News', 'ltm' ),
                    'view_items'            => __( 'View Industry News', 'ltm' ),
                    'search_items'          => __( 'Search Industry News', 'ltm' ),
                    'not_found'             => __( 'No Industry News found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Industry News found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Industry News:', 'ltm' ),
                    'all_items'             => __( 'All Industry News', 'ltm' ),
                    'archives'              => __( 'Industry News Archives', 'ltm' ),
                    'attributes'            => __( 'Industry News Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Industry News', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Industry News', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Industry News list', 'ltm' ),
                    'items_list_navigation' => __( 'Industry News list navigation', 'ltm' ),
                    'items_list'            => __( 'Industry News list', 'ltm' ),
                    'menu_name'             => __( 'Industry News', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-analytics',
                'public'        => true,
                'map_meta_cap'  => true,
                'has_archive'   => true,
                'show_ui'       => true,
                'show_in_rest'  => true,
//                'taxonomies'    => [ 'sectors' ],
                'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            ]
        );
    }
}
