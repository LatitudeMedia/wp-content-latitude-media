<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Researches
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Researches post type.
 */
class Researches {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'researches';

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
                    'name'                  => __( 'Researches', 'ltm' ),
                    'singular_name'         => __( 'Research', 'ltm' ),
                    'add_new'               => __( 'Add New Researche', 'ltm' ),
                    'add_new_item'          => __( 'Add New Researche', 'ltm' ),
                    'edit_item'             => __( 'Edit Researche', 'ltm' ),
                    'new_item'              => __( 'New Researche', 'ltm' ),
                    'view_item'             => __( 'View Researche', 'ltm' ),
                    'view_items'            => __( 'View Researches', 'ltm' ),
                    'search_items'          => __( 'Search Researches', 'ltm' ),
                    'not_found'             => __( 'No Researches found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Researches found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Researche:', 'ltm' ),
                    'all_items'             => __( 'All Researches', 'ltm' ),
                    'archives'              => __( 'Researche Archives', 'ltm' ),
                    'attributes'            => __( 'Researche Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Researche', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Researche', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Researches list', 'ltm' ),
                    'items_list_navigation' => __( 'Researches list navigation', 'ltm' ),
                    'items_list'            => __( 'Researches list', 'ltm' ),
                    'menu_name'             => __( 'Researches', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-code-standards',
                'public'        => true,
                'map_meta_cap'  => true,
                'has_archive'   => false,
                'show_ui'       => true,
                'show_in_rest'  => true,
                'taxonomies'    => [ 'category', 'report-category' ],
                'supports'      => [ 'title', 'editor', 'thumbnail', 'author', 'excerpt' ],
            ]
        );
    }
}
