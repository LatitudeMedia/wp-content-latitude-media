<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Short Description block
 * (acf/event-short-description-block), migrated from the theme's
 * acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data. The
 * group's own key (field_6745ca5e34889) uses a `field_`-prefixed key rather
 * than the usual `group_` prefix -- a pre-existing naming oddity in the
 * original theme export, kept exactly as-is rather than "fixed" here.
 *
 * @package LTMCore
 */
class EventShortDescription {

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
	 * Warns in wp-admin that the Event Short Description block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Short Description block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the "Event short description block" field group.
	 */
	public function register_field_group() {
		acf_add_local_field_group( array(
			'key' => 'field_6745ca5e34889',
			'title' => 'Event short description block',
			'fields' => array(
				array(
					'key' => 'field_6745ca5e3488b',
					'label' => 'Event short description block',
					'name' => '',
					'type' => 'message',
					'message' => 'Display the text from the post Excerpt field.',
				),
				array(
					'key' => 'field_6745ca5e3488c',
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
						'value' => 'acf/event-short-description-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		) );
	}
}
