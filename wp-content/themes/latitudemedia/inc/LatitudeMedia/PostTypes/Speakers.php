<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Speakers
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Speakers post type.
 */
class Speakers {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'speakers';

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
                    'name'                  => __( 'Speakers', 'ltm' ),
                    'singular_name'         => __( 'Speaker', 'ltm' ),
                    'add_new'               => __( 'Add New Speaker', 'ltm' ),
                    'add_new_item'          => __( 'Add New Speaker', 'ltm' ),
                    'edit_item'             => __( 'Edit Speaker', 'ltm' ),
                    'new_item'              => __( 'New Speaker', 'ltm' ),
                    'view_item'             => __( 'View Speaker', 'ltm' ),
                    'view_items'            => __( 'View Speakers', 'ltm' ),
                    'search_items'          => __( 'Search Speakers', 'ltm' ),
                    'not_found'             => __( 'No Speakers found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Speakers found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Speaker:', 'ltm' ),
                    'all_items'             => __( 'All Speakers', 'ltm' ),
                    'archives'              => __( 'Speaker Archives', 'ltm' ),
                    'attributes'            => __( 'Speaker Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Speaker', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Speaker', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Speakers list', 'ltm' ),
                    'items_list_navigation' => __( 'Speakers list navigation', 'ltm' ),
                    'items_list'            => __( 'Speakers list', 'ltm' ),
                    'menu_name'             => __( 'Speakers', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-megaphone',
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
