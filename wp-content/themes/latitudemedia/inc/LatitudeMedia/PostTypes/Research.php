<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Research
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Research post type.
 */
class Research {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'research';

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
                    'add_new'               => __( 'Add New Research', 'ltm' ),
                    'add_new_item'          => __( 'Add New Research', 'ltm' ),
                    'edit_item'             => __( 'Edit Research', 'ltm' ),
                    'new_item'              => __( 'New Research', 'ltm' ),
                    'view_item'             => __( 'View Research', 'ltm' ),
                    'view_items'            => __( 'View Researches', 'ltm' ),
                    'search_items'          => __( 'Search Researches', 'ltm' ),
                    'not_found'             => __( 'No Researches found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Researches found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Researche:', 'ltm' ),
                    'all_items'             => __( 'All Researches', 'ltm' ),
                    'archives'              => __( 'Researche Archives', 'ltm' ),
                    'attributes'            => __( 'Researche Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Research', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Research', 'ltm' ),
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
