<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Resources
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Resources post type.
 */
class Resources {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'resources';

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
                    'name'                  => __( 'Resources', 'ltm' ),
                    'singular_name'         => __( 'Resource', 'ltm' ),
                    'add_new'               => __( 'Add New Resource', 'ltm' ),
                    'add_new_item'          => __( 'Add New Resource', 'ltm' ),
                    'edit_item'             => __( 'Edit Resource', 'ltm' ),
                    'new_item'              => __( 'New Resource', 'ltm' ),
                    'view_item'             => __( 'View Resource', 'ltm' ),
                    'view_items'            => __( 'View Resources', 'ltm' ),
                    'search_items'          => __( 'Search Resources', 'ltm' ),
                    'not_found'             => __( 'No Resources found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Resources found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Resource:', 'ltm' ),
                    'all_items'             => __( 'All Resources', 'ltm' ),
                    'archives'              => __( 'Resource Archives', 'ltm' ),
                    'attributes'            => __( 'Resource Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Resource', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Resource', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Resources list', 'ltm' ),
                    'items_list_navigation' => __( 'Resources list navigation', 'ltm' ),
                    'items_list'            => __( 'Resources list', 'ltm' ),
                    'menu_name'             => __( 'Resources', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-index-card',
                'public'        => true,
                'map_meta_cap'  => true,
                'has_archive'   => false,
                'show_ui'       => true,
                'show_in_rest'  => true,
//                'taxonomies'    => [ 'sectors' ],
                'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            ]
        );
    }
}
