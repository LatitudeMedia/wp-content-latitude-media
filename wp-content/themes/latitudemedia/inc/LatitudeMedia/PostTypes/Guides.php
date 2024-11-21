<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Guides
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Guides post type.
 */
class Guides {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'guides';

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
                    'name'                  => __( 'Guides', 'ltm' ),
                    'singular_name'         => __( 'Guide', 'ltm' ),
                    'add_new'               => __( 'Add New Guide', 'ltm' ),
                    'add_new_item'          => __( 'Add New Guide', 'ltm' ),
                    'edit_item'             => __( 'Edit Guide', 'ltm' ),
                    'new_item'              => __( 'New Guide', 'ltm' ),
                    'view_item'             => __( 'View Guide', 'ltm' ),
                    'view_items'            => __( 'View Guides', 'ltm' ),
                    'search_items'          => __( 'Search Guides', 'ltm' ),
                    'not_found'             => __( 'No Guides found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Guides found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Guide:', 'ltm' ),
                    'all_items'             => __( 'All Guides', 'ltm' ),
                    'archives'              => __( 'Guide Archives', 'ltm' ),
                    'attributes'            => __( 'Guide Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Guide', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Guide', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Guides list', 'ltm' ),
                    'items_list_navigation' => __( 'Guides list navigation', 'ltm' ),
                    'items_list'            => __( 'Guides list', 'ltm' ),
                    'menu_name'             => __( 'Guides', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-info',
                'public'        => false,
                'map_meta_cap'  => true,
                'has_archive'   => false,
                'show_ui'       => true,
                'show_in_rest'  => true,
                'supports'      => [ 'title', 'editor' ],
                'menu_position' => 100,
            ]
        );
    }
}
