<?php
namespace LTMCore\PostTypes;

/**
 * Custom post type for Speakers
 *
 * Migrated from the theme's LatitudeMedia\PostTypes\Speakers (unchanged
 * behavior — same post type args).
 *
 * The "Speaker options" field group (job_title, company, linkedin_link) and
 * the two speaker image sizes came along with it: after acf/event-speakers-block
 * and acf/event-agenda-v2-block moved into this plugin, every live reader of
 * those fields lives here, so the whole speakers domain now sits together.
 *
 * @package LTMCore
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
		add_action( 'after_setup_theme', array( $this, 'register_image_sizes' ) );

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_acf_notice' ) );
			return;
		}

		add_action( 'acf/include_fields', array( $this, 'register_field_group' ) );
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
				'menu_icon'           => 'dashicons-megaphone',
				'public'              => true,
				'map_meta_cap'        => true,
				'has_archive'         => false,
				'show_ui'             => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
			]
		);
	}

	/**
	 * Registers the speaker image sizes, migrated from the theme's inc/media.php.
	 *
	 * Both names and dimensions are unchanged on purpose: generated thumbnail
	 * files are recorded against the size *name* in each attachment's
	 * _wp_attachment_metadata['sizes'], so renaming one would orphan every
	 * already-generated file and force a full regeneration.
	 *
	 * 'event-speakers-modal' is only used by this plugin's event-speakers-block.
	 * 'event-speakers-list' is also consumed by the *theme's* our-team-block and
	 * authors-list-block (for team members and author logos, not speakers), so it
	 * must keep being registered here even if the speakers block itself changes.
	 */
	public function register_image_sizes() {
		add_image_size( 'event-speakers-list', 230, 230, true );
		add_image_size( 'event-speakers-modal', 200, 200, true );
	}

	/**
	 * Warns in wp-admin that the speaker fields need ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Speaker options fields require Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the "Speaker options" field group, shown as a side meta box on
	 * each speaker. Copied verbatim from the theme's acf-export.php — the group
	 * key and every field key are unchanged so existing speaker data keeps
	 * resolving.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_67050ea568064',
			'title' => 'Speaker options',
			'fields' => array(
				array(
					'key' => 'field_67050ea565c46',
					'label' => 'Job Title',
					'name' => 'job_title',
					'type' => 'text',
				),
				array(
					'key' => 'field_67050ec665c47',
					'label' => 'Company',
					'name' => 'company',
					'type' => 'text',
				),
				array(
					'key' => 'field_67050ecd65c48',
					'label' => 'Linkedin Link',
					'name' => 'linkedin_link',
					'type' => 'text',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'speakers',
					),
				),
			),
			'position' => 'side',
			'style' => 'default',
			'active' => true,
		));
	}
}
