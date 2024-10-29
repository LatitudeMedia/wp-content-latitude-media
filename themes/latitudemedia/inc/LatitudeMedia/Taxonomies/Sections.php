<?php
namespace LatitudeMedia\Taxonomies;

/**
 * Taxonomy for Sections
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Sections taxonomy.
 */
class Sections {

	/**
	 * Name of the taxonomy.
	 *
	 * @var string
	 */
	public $name = 'sections';

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
		$this->object_types = [ 'post' ];

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
					'name'                  => __( 'Sections', 'ltm' ),
					'singular_name'         => __( 'Section', 'ltm' ),
					'search_items'          => __( 'Search Sections', 'ltm' ),
					'popular_items'         => __( 'Popular Sections', 'ltm' ),
					'all_items'             => __( 'All Sections', 'ltm' ),
					'parent_item'           => __( 'Parent Section', 'ltm' ),
					'parent_item_colon'     => __( 'Parent Sections', 'ltm' ),
					'edit_item'             => __( 'Edit Section', 'ltm' ),
					'view_item'             => __( 'View Section', 'ltm' ),
					'update_item'           => __( 'Update Section', 'ltm' ),
					'add_new_item'          => __( 'Add New Section', 'ltm' ),
					'new_item_name'         => __( 'New Section Name', 'ltm' ),
					'add_or_remove_items'   => __( 'Add or remove Sections', 'ltm' ),
					'choose_from_most_used' => __( 'Choose from the most used Sections', 'ltm' ),
					'not_found'             => __( 'No Sections found', 'ltm' ),
					'no_terms'              => __( 'No Sections', 'ltm' ),
					'items_list_navigation' => __( 'Sections list navigation', 'ltm' ),
					'items_list'            => __( 'Sections list', 'ltm' ),
					'back_to_items'         => __( '&larr; Back to Sections', 'ltm' ),
					'menu_name'             => __( 'Sections', 'ltm' ),
					'name_admin_bar'        => __( 'Sections', 'ltm' ),
				],
                'show_in_rest' => true,
                'hierarchical' => true,
                'show_admin_column' => true
			]
		);
	}
}
