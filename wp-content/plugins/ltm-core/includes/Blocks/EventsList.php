<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Events list block
 * (acf/events-list-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * @package LTMCore
 */
class EventsList {

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
	 * Warns in wp-admin that the Events list block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Events list block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the field group. Copied verbatim from the theme's acf-export.php.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_673f9cef9b6de',
			'title' => 'Events list block',
			'fields' => array(
				array(
					'key' => 'field_673f9b6dd2721',
					'label' => 'Events list block',
					'name' => '',
					'type' => 'message',
					'message' => 'Will pulled automatically by event <b>End date</b>.
Or select event manually.',
					'new_lines' => 'wpautop',
					'esc_html' => 0,
				),
				array(
					'key' => 'field_673faa95e2af4',
					'label' => 'Title',
					'name' => 'title',
					'type' => 'text',
					'default_value' => 'Upcoming events',
				),
				array(
					'key' => 'field_673fad446ef02',
					'label' => 'Type',
					'name' => 'type',
					'type' => 'select',
					'choices' => array(
						'upcoming' => 'Upcoming events',
						'past' => 'Past events',
						'custom' => 'Custom',
					),
					'default_value' => false,
					'return_format' => 'value',
					'allow_null' => 0,
				),
				array(
					'key' => 'field_673f9cf3cdb8e',
					'label' => 'Events',
					'name' => 'events',
					'type' => 'relationship',
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_673fad446ef02',
								'operator' => '==',
								'value' => 'custom',
							),
						),
					),
					'post_type' => array(
						0 => 'events',
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
					'key' => 'field_673f9b6dd2722',
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
						'value' => 'acf/events-list-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		));
	}
}
