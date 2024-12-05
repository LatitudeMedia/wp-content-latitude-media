<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Team
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Team post type.
 */
class Team {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'team';

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
                    'name'                  => __( 'Team members', 'ltm' ),
                    'singular_name'         => __( 'Team member', 'ltm' ),
                    'add_new'               => __( 'Add New Team member', 'ltm' ),
                    'add_new_item'          => __( 'Add New Team member', 'ltm' ),
                    'edit_item'             => __( 'Edit Team member', 'ltm' ),
                    'new_item'              => __( 'New Team member', 'ltm' ),
                    'view_item'             => __( 'View Team member', 'ltm' ),
                    'view_items'            => __( 'View Team members', 'ltm' ),
                    'search_items'          => __( 'Search Team members', 'ltm' ),
                    'not_found'             => __( 'No Team members found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Team members found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Team member:', 'ltm' ),
                    'all_items'             => __( 'All Team members', 'ltm' ),
                    'archives'              => __( 'Team member Archives', 'ltm' ),
                    'attributes'            => __( 'Team member Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Team member', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Team member', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Team members list', 'ltm' ),
                    'items_list_navigation' => __( 'Team members list navigation', 'ltm' ),
                    'items_list'            => __( 'Team members list', 'ltm' ),
                    'menu_name'             => __( 'Team members', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-groups',
                'public'        => true,
                'map_meta_cap'  => true,
                'has_archive'   => false,
                'show_ui'       => true,
                'show_in_rest'  => true,
                'exclude_from_search' => true,
                'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
            ]
        );
    }
}
