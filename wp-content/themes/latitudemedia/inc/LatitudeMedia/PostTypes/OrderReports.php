<?php
namespace LatitudeMedia\PostTypes;

/**
 * Custom post type for Order Reports
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Order Reports post type.
 */
class OrderReports {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    public $name = 'order-reports';

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
                    'name'                  => __( 'Order Reports', 'ltm' ),
                    'singular_name'         => __( 'Order Report', 'ltm' ),
                    'add_new'               => __( 'Add New Order Report', 'ltm' ),
                    'add_new_item'          => __( 'Add New Order Report', 'ltm' ),
                    'edit_item'             => __( 'Edit Order Report', 'ltm' ),
                    'new_item'              => __( 'New Order Report', 'ltm' ),
                    'view_item'             => __( 'View Order Report', 'ltm' ),
                    'view_items'            => __( 'View Order Reports', 'ltm' ),
                    'search_items'          => __( 'Search Order Reports', 'ltm' ),
                    'not_found'             => __( 'No Order Reports found', 'ltm' ),
                    'not_found_in_trash'    => __( 'No Order Reports found in Trash', 'ltm' ),
                    'parent_item_colon'     => __( 'Parent Order Report:', 'ltm' ),
                    'all_items'             => __( 'All Order Reports', 'ltm' ),
                    'archives'              => __( 'Order Report Archives', 'ltm' ),
                    'attributes'            => __( 'Order Report Attributes', 'ltm' ),
                    'insert_into_item'      => __( 'Insert into Order Report', 'ltm' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Order Report', 'ltm' ),
                    'filter_items_list'     => __( 'Filter Order Reports list', 'ltm' ),
                    'items_list_navigation' => __( 'Order Reports list navigation', 'ltm' ),
                    'items_list'            => __( 'Order Reports list', 'ltm' ),
                    'menu_name'             => __( 'Order Reports', 'ltm' ),
                ],
                'menu_icon'     => 'dashicons-pdf',
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
