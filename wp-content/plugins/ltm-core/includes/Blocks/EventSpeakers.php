<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Speakers block
 * (acf/event-speakers-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * The speakers themselves remain a theme-registered post type, and their
 * per-speaker fields (job_title, company, linkedin_link) stay in the theme's
 * "Speaker options" group — this class only covers the block's own fields.
 *
 * @package LTMCore
 */
class EventSpeakers {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_acf_notice' ) );
			return;
		}

		add_action( 'acf/include_fields', array( $this, 'register_field_group' ) );
	}

	/**
	 * Warns in wp-admin that the Event Speakers block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Speakers block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the field group. Copied verbatim from the theme's acf-export.php.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_674597e182742',
			'title' => 'Event speakers block',
			'fields' => array(
				array(
					'key' => 'field_67449c4ecdcd9',
					'label' => 'Event speakers block',
					'name' => '',
					'type' => 'message',
					'esc_html' => 0,
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_674597fdb07ad',
					'label' => 'Title',
					'name' => 'title',
					'type' => 'text',
				),
				array(
					'key' => 'field_67459804b07ae',
					'label' => 'Speakers',
					'name' => 'speakers',
					'type' => 'relationship',
					'post_type' => array(
						0 => 'speakers',
					),
					'post_status' => '',
					'taxonomy' => '',
					'filters' => array(
						0 => 'search',
					),
					'return_format' => 'id',
					'elements' => '',
				),
				array(
					'key' => 'field_67449c4ecdcda',
					'label' => 'Display',
					'name' => 'display',
					'type' => 'true_false',
					'ui' => 1,
				),
				array(
					'key' => 'field_6746402d35367',
					'label' => 'Show "Read more" button',
					'name' => 'show_read_more_button',
					'type' => 'true_false',
					'ui' => 1,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/event-speakers-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		));
	}
}
