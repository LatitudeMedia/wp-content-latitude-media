<?php
namespace LatitudeMedia\Taxonomies;

/**
 * Taxonomy for Thematic Page Type
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Thematic Page Type taxonomy.
 */
class ThematicPageTypes {

	/**
	 * Name of the taxonomy.
	 *
	 * @var string
	 */
	public $name = 'thematic-page-types';

	/**
	 * Object types for this taxonomy.
	 *
	 * @var array
	 */
	public $object_types;


	/**
	 * Build the taxonomy object.
	 */
	public function __construct() {
		$this->object_types = [ 'thematic-page-types', 'post' ];

        add_action( 'init', array( $this, 'create_taxonomy' ) );
	}

	/**
	 * Creates the taxonomy.
	 */
	public function create_taxonomy() {
		register_taxonomy(
			$this->name,
			$this->object_types,
			[
				'labels' => [
					'name'                  => __( 'Thematic Page Types', 'ltm' ),
					'singular_name'         => __( 'Thematic Page Type', 'ltm' ),
					'search_items'          => __( 'Search Thematic Page Types', 'ltm' ),
					'popular_items'         => __( 'Popular Thematic Page Types', 'ltm' ),
					'all_items'             => __( 'All Thematic Page Types', 'ltm' ),
					'parent_item'           => __( 'Parent Thematic Page Type', 'ltm' ),
					'parent_item_colon'     => __( 'Parent Thematic Page Types', 'ltm' ),
					'edit_item'             => __( 'Edit Thematic Page Type', 'ltm' ),
					'view_item'             => __( 'View Thematic Page Type', 'ltm' ),
					'update_item'           => __( 'Update Thematic Page Type', 'ltm' ),
					'add_new_item'          => __( 'Add New Thematic Page Type', 'ltm' ),
					'new_item_name'         => __( 'New Thematic Page Type Name', 'ltm' ),
					'add_or_remove_items'   => __( 'Add or remove Thematic Page Types', 'ltm' ),
					'choose_from_most_used' => __( 'Choose from the most used Thematic Page Types', 'ltm' ),
					'not_found'             => __( 'No Thematic Page Types found', 'ltm' ),
					'no_terms'              => __( 'No Thematic Page Types', 'ltm' ),
					'items_list_navigation' => __( 'Thematic Page Types list navigation', 'ltm' ),
					'items_list'            => __( 'Thematic Page Types list', 'ltm' ),
					'back_to_items'         => __( '&larr; Back to Thematic Page Types', 'ltm' ),
					'menu_name'             => __( 'Display in Thematic Page', 'ltm' ),
					'name_admin_bar'        => __( 'Thematic Page Types', 'ltm' ),
				],
                'show_in_rest' => true,
                'hierarchical' => true,
                'show_admin_column' => true,
                'capabilities' => [
                    'manage_terms' => 'do_not_allow',
                    'edit_terms'   => 'do_not_allow',
                    'delete_terms' => 'do_not_allow',
                    'assign_terms' => 'edit_posts',
                ],
			]
		);
	}
}
