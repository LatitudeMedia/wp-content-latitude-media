<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Recap Video block
 * (acf/recap-video-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * @package LTMCore
 */
class RecapVideo {

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
	 * Warns in wp-admin that the Recap Video block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Recap Video block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the field group. Copied verbatim from the theme's acf-export.php.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_6759d1f713a46',
			'title' => 'Recap video block',
			'fields' => array(
				array(
					'key' => 'field_6759d0706f204',
					'label' => 'Recap video block',
					'name' => '',
					'type' => 'message',
					'esc_html' => 0,
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_6759d1f983e50',
					'label' => 'Title',
					'name' => 'title',
					'type' => 'text',
				),
				array(
					'key' => 'field_6759d1fe83e51',
					'label' => 'Video',
					'name' => 'video',
					'type' => 'oembed',
					'width' => '',
					'height' => '',
				),
				array(
					'key' => 'field_6759d0706f205',
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
						'value' => 'acf/recap-video-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		));
	}
}
