<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Sponsors
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Sponsors post type.
 */
class Sponsors {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'sponsors';

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
                    'name'                  => __( 'Sponsors', 'ltm' ),
                    'singular_name'         => __( 'Sponsor', 'ltm' ),
                    'add_new'               => __( 'Add New Sponsor', 'ltm' ),
                    'add_new_item'          => __( 'Add New Sponsor', 'ltm' ),
                    'edit_item'             => __( 'Edit Sponsor', 'ltm' ),
                    'new_item'              => __( 'New Sponsor', 'ltm' ),
                    'view_item'             => __( 'View Sponsor', 'ltm' ),
                    'view_items'            => __( 'View Sponsors', 'ltm' ),
                    'search_items'          => __( 'Search Sponsors', 'ltm' ),
                    'not_found'             => __( 'No Sponsors found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Sponsors found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Sponsor:', 'ltm' ),
                    'all_items'             => __( 'All Sponsors', 'ltm' ),
                    'archives'              => __( 'Sponsor Archives', 'ltm' ),
                    'attributes'            => __( 'Sponsor Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Sponsor', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Sponsor', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Sponsors list', 'ltm' ),
                    'items_list_navigation' => __( 'Sponsors list navigation', 'ltm' ),
                    'items_list'            => __( 'Sponsors list', 'ltm' ),
                    'menu_name'             => __( 'Sponsors', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-money-alt',
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
