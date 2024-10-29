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
class ReportCategory {

	/**
	 * Name of the taxonomy.
	 *
	 * @var string
	 */
	public $name = 'report-category';

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
		$this->object_types = [ 'researches' ];

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
					'name'                  => __( 'Report Categories', 'ltm' ),
					'singular_name'         => __( 'Report Category', 'ltm' ),
					'search_items'          => __( 'Search Report Categories', 'ltm' ),
					'popular_items'         => __( 'Popular Report Categories', 'ltm' ),
					'all_items'             => __( 'All Report Categories', 'ltm' ),
					'parent_item'           => __( 'Parent Report Category', 'ltm' ),
					'parent_item_colon'     => __( 'Parent Report Categories', 'ltm' ),
					'edit_item'             => __( 'Edit Report Category', 'ltm' ),
					'view_item'             => __( 'View Report Category', 'ltm' ),
					'update_item'           => __( 'Update Report Category', 'ltm' ),
					'add_new_item'          => __( 'Add New Report Category', 'ltm' ),
					'new_item_name'         => __( 'New Report Category Name', 'ltm' ),
					'add_or_remove_items'   => __( 'Add or remove Report Categories', 'ltm' ),
					'choose_from_most_used' => __( 'Choose from the most used Report Categories', 'ltm' ),
					'not_found'             => __( 'No Report Categories found', 'ltm' ),
					'no_terms'              => __( 'No Report Categories', 'ltm' ),
					'items_list_navigation' => __( 'Report Categories list navigation', 'ltm' ),
					'items_list'            => __( 'Report Categories list', 'ltm' ),
					'back_to_items'         => __( '&larr; Back to Report Categories', 'ltm' ),
					'menu_name'             => __( 'Report Categories', 'ltm' ),
					'name_admin_bar'        => __( 'Report Categories', 'ltm' ),
				],
                'show_in_rest' => true,
                'hierarchical' => true,
                'show_admin_column' => true
			]
		);
	}
}
