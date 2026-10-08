<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Venue block
 * (acf/event-venue-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * @package LTMCore
 */
class EventVenue {

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
	 * Warns in wp-admin that the Event Venue block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Venue block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the field group. Copied verbatim from the theme's acf-export.php.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_6745c3ee06935',
			'title' => 'Event venue block',
			'fields' => array(
				array(
					'key' => 'field_6744a68e8404a',
					'label' => 'Event venue block',
					'name' => '',
					'type' => 'message',
					'esc_html' => 0,
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_6745c801bdaff',
					'label' => 'Additional info',
					'name' => 'additional_info',
					'type' => 'wysiwyg',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 1,
					'delay' => 0,
				),
				array(
					'key' => 'field_6745c3f2c517f',
					'label' => 'Embed code',
					'name' => 'embed_code',
					'type' => 'textarea',
					'rows' => '',
				),
				array(
					'key' => 'field_6745c7fcbdafe',
					'label' => 'Location details',
					'name' => 'location_details',
					'type' => 'wysiwyg',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 1,
					'delay' => 0,
				),
				array(
					'key' => 'field_6744a68e8404b',
					'label' => 'Display',
					'name' => 'display',
					'type' => 'true_false',
					'ui' => 1,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/event-venue-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		));
	}
}
