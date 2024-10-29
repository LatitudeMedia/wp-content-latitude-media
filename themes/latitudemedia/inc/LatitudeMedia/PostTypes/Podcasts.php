<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Podcasts
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Podcasts post type.
 */
class Podcasts {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'podcasts';

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
                    'name'                  => __( 'Podcasts', 'ltm' ),
                    'singular_name'         => __( 'Podcast', 'ltm' ),
                    'add_new'               => __( 'Add New Podcast', 'ltm' ),
                    'add_new_item'          => __( 'Add New Podcast', 'ltm' ),
                    'edit_item'             => __( 'Edit Podcast', 'ltm' ),
                    'new_item'              => __( 'New Podcast', 'ltm' ),
                    'view_item'             => __( 'View Podcast', 'ltm' ),
                    'view_items'            => __( 'View Podcasts', 'ltm' ),
                    'search_items'          => __( 'Search Podcasts', 'ltm' ),
                    'not_found'             => __( 'No Podcasts found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Podcasts found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Podcast:', 'ltm' ),
                    'all_items'             => __( 'All Podcasts', 'ltm' ),
                    'archives'              => __( 'Podcast Archives', 'ltm' ),
                    'attributes'            => __( 'Podcast Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Podcast', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Podcast', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Podcasts list', 'ltm' ),
                    'items_list_navigation' => __( 'Podcasts list navigation', 'ltm' ),
                    'items_list'            => __( 'Podcasts list', 'ltm' ),
                    'menu_name'             => __( 'Podcasts', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-microphone',
                'public'        => true,
                'map_meta_cap'  => true,
                'has_archive'   => false,
                'show_ui'       => true,
                'show_in_rest'  => true,
                'taxonomies'    => [ 'podcast-type' ],
                'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author' ],
            ]
        );
    }
}
