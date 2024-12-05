<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Sections Landing
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Sections Landing post type.
 */
class SectionsLanding {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'sections-landing';

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
                    'name'                  => __( 'Sections Landing', 'ltm' ),
                    'singular_name'         => __( 'Section Landing', 'ltm' ),
                    'add_new'               => __( 'Add New Section Landing', 'ltm' ),
                    'add_new_item'          => __( 'Add New Section Landing', 'ltm' ),
                    'edit_item'             => __( 'Edit Section Landing', 'ltm' ),
                    'new_item'              => __( 'New Section Landing', 'ltm' ),
                    'view_item'             => __( 'View Section Landing', 'ltm' ),
                    'view_items'            => __( 'View Sections Landing', 'ltm' ),
                    'search_items'          => __( 'Search Sections Landing', 'ltm' ),
                    'not_found'             => __( 'No Sections Landing found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Sections Landing found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Section Landing:', 'ltm' ),
                    'all_items'             => __( 'Sections Landing', 'ltm' ),
                    'archives'              => __( 'Section Landing Archives', 'ltm' ),
                    'attributes'            => __( 'Section Landing Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Section Landing', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Section Landing', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Sections Landing list', 'ltm' ),
                    'items_list_navigation' => __( 'Sections Landing list navigation', 'ltm' ),
                    'items_list'            => __( 'Sections Landing list', 'ltm' ),
                    'menu_name'             => __( 'Sections Landing', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-category',
                'public'        => true,
                'map_meta_cap'  => true,
                'has_archive'   => false,
                'show_ui'       => true,
                'show_in_menu'       => 'edit.php',
                'show_in_rest'  => true,
                'rewrite' => array(
                    'slug' => 'section',
                    'with_front' => false
                ),
                'exclude_from_search' => true,
                'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
            ]
        );
    }
}
