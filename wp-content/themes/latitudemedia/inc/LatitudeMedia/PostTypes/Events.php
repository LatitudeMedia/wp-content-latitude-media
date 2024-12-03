<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Events
 *
 * @package LatitudeMedia
 */

/**
 * Class for the events post type.
 */
class Events {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'events';

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
                    'name'                  => __( 'Events', 'ltm' ),
                    'singular_name'         => __( 'Event', 'ltm' ),
                    'add_new'               => __( 'Add New Event', 'ltm' ),
                    'add_new_item'          => __( 'Add New Event', 'ltm' ),
                    'edit_item'             => __( 'Edit Event', 'ltm' ),
                    'new_item'              => __( 'New Event', 'ltm' ),
                    'view_item'             => __( 'View Event', 'ltm' ),
                    'view_items'            => __( 'View Events', 'ltm' ),
                    'search_items'          => __( 'Search Events', 'ltm' ),
                    'not_found'             => __( 'No Events found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Events found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Event:', 'ltm' ),
                    'all_items'             => __( 'All Events', 'ltm' ),
                    'archives'              => __( 'Event Archives', 'ltm' ),
                    'attributes'            => __( 'Event Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Event', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Event', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Events list', 'ltm' ),
                    'items_list_navigation' => __( 'Events list navigation', 'ltm' ),
                    'items_list'            => __( 'Events list', 'ltm' ),
                    'menu_name'             => __( 'Events', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-tickets-alt',
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
