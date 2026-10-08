<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Preview block
 * (acf/event-preview-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * Note: this block also reads a second, broader field group ("General
 * event options", post_type == events) that is NOT part of this class or
 * this migration — that group stays defined in the theme and is read via
 * plain get_fields( $post_id ) in render.php, same as before the move.
 *
 * @package LTMCore
 */
class EventPreview {

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
	 * Warns in wp-admin that the Event Preview block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Preview block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the "Event preview block" field group.
	 */
	public function register_field_group() {
		acf_add_local_field_group( array(
			'key' => 'group_67473ac305a05',
			'title' => 'Event preview block',
			'fields' => array(
				array(
					'key' => 'field_67447f65a31a6',
					'label' => 'Event preview block',
					'name' => '',
					'type' => 'message',
					'message' => 'Display event general information: <ul><li>Date</li><li>Type</li><li>Title</li><li>Location</li><li>Register link</li></ul> From <b>General event options</b> in sidebar',
					'new_lines' => 'wpautop',
					'esc_html' => 0,
				),
				array(
					'key' => 'field_67473ac9a1f7c',
					'label' => 'Rows',
					'name' => 'rows',
					'type' => 'checkbox',
					'choices' => array(
						'date' => 'Date',
						'type' => 'Type',
						'title' => 'title',
						'location' => 'Location',
						'button' => 'Button',
					),
					'default_value' => array(
						0 => 'date',
						1 => 'type',
						3 => 'title',
						4 => 'location',
						5 => 'button',
					),
					'return_format' => 'value',
					'allow_custom' => 0,
					'layout' => 'horizontal',
					'toggle' => 0,
					'save_custom' => 0,
					'custom_choice_button_text' => 'Add new choice',
				),
				array(
					'key' => 'field_6945db3e392a4',
					'label' => 'Main Logo',
					'name' => 'main_logo',
					'aria-label' => '',
					'type' => 'image',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'relevanssi_exclude' => 0,
					'return_format' => 'array',
					'library' => 'all',
					'min_width' => '',
					'min_height' => '',
					'min_size' => '',
					'max_width' => '',
					'max_height' => '',
					'max_size' => '',
					'mime_types' => '',
					'allow_in_bindings' => 0,
					'preview_size' => 'medium',
				),
				array(
					'key' => 'field_6a2f8c4d1e905',
					'label' => 'Show event collaborator',
					'name' => 'show_event_collaborator',
					'type' => 'true_false',
					'default_value' => 0,
					'ui' => 1,
				),
				array(
					'key' => 'field_6a2f8c4d1e903',
					'label' => 'Event collaborator text',
					'name' => 'event_collaborator_text',
					'type' => 'text',
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_6a2f8c4d1e905',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
				),
				array(
					'key' => 'field_6a2f8c4d1e904',
					'label' => 'Event collaborator logo',
					'name' => 'event_collaborator_logo',
					'type' => 'image',
					'return_format' => 'array',
					'library' => 'all',
					'preview_size' => 'medium',
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_6a2f8c4d1e905',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
				),
				array(
					'key' => 'field_6a2f8c4d1e906',
					'label' => 'Event collaborator logo URL',
					'name' => 'event_collaborator_logo_url',
					'type' => 'url',
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_6a2f8c4d1e905',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
				),
				array(
					'key' => 'field_67447f65a31a8',
					'label' => 'Use labels below image',
					'name' => 'label_below',
					'type' => 'true_false',
					'ui' => 1,
				),
				array(
					'key' => 'field_67473b75a1f7d',
					'label' => 'Subtitle',
					'name' => 'subtitle',
					'type' => 'text',
				),
				array(
					'key' => 'field_67c85bd4fda75',
					'label' => 'Logos title',
					'name' => 'logos_title',
					'type' => 'text',
					'default_value' => 'Co-hosted with:',
				),
				array(
					'key' => 'field_67473b7ea1f7e',
					'label' => 'Logos',
					'name' => 'co_hosted_logo',
					'type' => 'gallery',
					'return_format' => 'id',
					'library' => 'all',
					'min' => '',
					'max' => 2,
					'mime_types' => '',
					'insert' => 'append',
					'preview_size' => 'medium',
				),
				array(
					'key' => 'field_67447f65a31a7',
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
						'value' => 'acf/event-preview-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		) );
	}
}
