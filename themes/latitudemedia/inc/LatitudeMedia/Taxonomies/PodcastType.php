<?php
namespace LatitudeMedia\Taxonomies;

/**
 * Taxonomy for Podcast Type
 *
 * @package LatitudeMedia
 */

/**
 * Class for the Podcast Type taxonomy.
 */
class PodcastType {

	/**
	 * Name of the taxonomy.
	 *
	 * @var string
	 */
	public $name = 'podcast-type';

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
		$this->object_types = [ 'podcasts' ];

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
					'name'                  => __( 'Podcast Types', 'ltm' ),
					'singular_name'         => __( 'Podcast Type', 'ltm' ),
					'search_items'          => __( 'Search Podcast Types', 'ltm' ),
					'popular_items'         => __( 'Popular Podcast Types', 'ltm' ),
					'all_items'             => __( 'All Podcast Types', 'ltm' ),
					'parent_item'           => __( 'Parent Podcast Type', 'ltm' ),
					'parent_item_colon'     => __( 'Parent Podcast Types', 'ltm' ),
					'edit_item'             => __( 'Edit Podcast Type', 'ltm' ),
					'view_item'             => __( 'View Podcast Type', 'ltm' ),
					'update_item'           => __( 'Update Podcast Type', 'ltm' ),
					'add_new_item'          => __( 'Add New Podcast Type', 'ltm' ),
					'new_item_name'         => __( 'New Podcast Type Name', 'ltm' ),
					'add_or_remove_items'   => __( 'Add or remove Podcast Types', 'ltm' ),
					'choose_from_most_used' => __( 'Choose from the most used Podcast Types', 'ltm' ),
					'not_found'             => __( 'No Podcast Types found', 'ltm' ),
					'no_terms'              => __( 'No Podcast Types', 'ltm' ),
					'items_list_navigation' => __( 'Podcast Types list navigation', 'ltm' ),
					'items_list'            => __( 'Podcast Types list', 'ltm' ),
					'back_to_items'         => __( '&larr; Back to Podcast Types', 'ltm' ),
					'menu_name'             => __( 'Podcast Types', 'ltm' ),
					'name_admin_bar'        => __( 'Podcast Types', 'ltm' ),
				],
                'show_in_rest' => true,
                'hierarchical' => true,
                'show_admin_column' => true
			]
		);
	}
}
