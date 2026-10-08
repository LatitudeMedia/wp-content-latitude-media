<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Contact Us block
 * (acf/event-contact-us-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * @package LTMCore
 */
class EventContactUs {

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
	 * Warns in wp-admin that the Event Contact Us block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Contact Us block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the field group. Copied verbatim from the theme's acf-export.php.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_693f1ae98dd74',
			'title' => 'Event contact us block',
			'fields' => array(
				array(
					'key' => 'field_693f249f2370a',
					'label' => 'Event contact us block',
					'name' => '',
					'type' => 'message',
					'esc_html' => 0,
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_693f1ae9a9d04',
					'label' => 'Title',
					'name' => 'title',
					'type' => 'text',
				),
				array(
					'key' => 'field_693f1b0da9d05',
					'label' => 'Contact Links',
					'name' => 'contact_links',
					'type' => 'repeater',
					'layout' => 'table',
					'min' => 0,
					'max' => 0,
					'button_label' => 'Add Row',
					'rows_per_page' => 20,
					'sub_fields' => array(
						array(
							'key' => 'field_693f1b2473331',
							'label' => 'Title',
							'name' => 'title',
							'type' => 'text',
							'parent_repeater' => 'field_693f1b0da9d05',
						),
						array(
							'key' => 'field_693f1b2d73332',
							'label' => 'Description',
							'name' => 'description',
							'type' => 'wysiwyg',
							'tabs' => 'all',
							'toolbar' => 'full',
							'media_upload' => 1,
							'delay' => 0,
							'parent_repeater' => 'field_693f1b0da9d05',
						),
						array(
							'key' => 'field_693f1b4873333',
							'label' => 'CTA Link',
							'name' => 'cta_link',
							'type' => 'link',
							'return_format' => 'array',
							'parent_repeater' => 'field_693f1b0da9d05',
						),
					),
				),
				array(
					'key' => 'field_693f2615b556c',
					'label' => 'Display',
					'name' => 'display',
					'type' => 'true_false',
					'default_value' => 1,
					'ui' => 1,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/event-contact-us-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		));
	}
}
