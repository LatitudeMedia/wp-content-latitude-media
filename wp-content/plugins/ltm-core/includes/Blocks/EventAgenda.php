<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Agenda block
 * (acf/event-agenda-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * This is the original agenda block, superseded by acf/event-agenda-v2-block
 * (see LTMCore\Blocks\EventAgendaV2) for every event from 2026 on. It is kept
 * because four past events -- Transition-AI 2024, Transition-AI 2025,
 * Transition-AI: Boston and Transition-AI: New York -- still render it, and
 * their pages are public. v2 is a different field group with a different
 * layout, so those pages cannot move to it without changing how they look.
 *
 * @package LTMCore
 */
class EventAgenda {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'register_image_sizes' ) );

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_acf_notice' ) );
			return;
		}

		add_action( 'acf/include_fields', array( $this, 'register_field_group' ) );
	}

	/**
	 * Registers the agenda thumbnail size, migrated from the theme's inc/media.php.
	 *
	 * The name and width are unchanged on purpose: generated thumbnail files are
	 * recorded against the size *name* in each attachment's
	 * _wp_attachment_metadata['sizes'], so renaming it would orphan every
	 * already-generated file and force a full regeneration. This block's
	 * render.php is the only reader of the size. Same rationale as
	 * LTMCore\PostTypes\Speakers::register_image_sizes().
	 */
	public function register_image_sizes() {
		add_image_size( 'event-agenda', 552 );
	}

	/**
	 * Warns in wp-admin that the Event Agenda block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Agenda block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the field group. Copied verbatim from the theme's acf-export.php.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_6745d58b04065',
			'title' => 'Event agenda block',
			'fields' => array(
				array(
					'key' => 'field_6744a1d23baa4',
					'label' => 'Event agenda block',
					'name' => '',
					'type' => 'message',
					'esc_html' => 0,
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_6745d5914809b',
					'label' => 'Title',
					'name' => 'title',
					'type' => 'text',
				),
				array(
					'key' => 'field_6745d9e1987af',
					'label' => 'Schedule',
					'name' => 'schedule',
					'type' => 'repeater',
					'layout' => 'row',
					'sub_fields' => array(
						array(
							'key' => 'field_6745e4dd03a8e',
							'label' => 'Schedule item',
							'name' => '',
							'type' => 'accordion',
							'open' => 1,
							'multi_expand' => 0,
							'endpoint' => 0,
							'parent_repeater' => 'field_6745d9e1987af',
						),
						array(
							'key' => 'field_6745e334c6af8',
							'label' => 'General',
							'name' => '',
							'type' => 'tab',
							'placement' => 'top',
							'endpoint' => 0,
							'parent_repeater' => 'field_6745d9e1987af',
						),
						array(
							'key' => 'field_6745de12af52d',
							'label' => 'Time',
							'name' => 'time',
							'type' => 'text',
							'wrapper' => array(
								'width' => '30',
								'class' => '',
								'id' => '',
							),
							'parent_repeater' => 'field_6745d9e1987af',
						),
						array(
							'key' => 'field_6745de0caf52c',
							'label' => 'Title',
							'name' => 'title',
							'type' => 'text',
							'parent_repeater' => 'field_6745d9e1987af',
						),
						array(
							'key' => 'field_6745e13b47d00',
							'label' => 'Details',
							'name' => '',
							'type' => 'tab',
							'placement' => 'top',
							'endpoint' => 0,
							'parent_repeater' => 'field_6745d9e1987af',
						),
						array(
							'key' => 'field_6745de97eafa4',
							'label' => 'Description',
							'name' => 'description',
							'type' => 'textarea',
							'rows' => 3,
							'parent_repeater' => 'field_6745d9e1987af',
						),
						array(
							'key' => 'field_6745ded1af1a3',
							'label' => 'Speakers',
							'name' => 'speakers',
							'type' => 'post_object',
							'post_type' => array(
								0 => 'speakers',
							),
							'post_status' => '',
							'taxonomy' => '',
							'return_format' => 'id',
							'multiple' => 1,
							'allow_null' => 0,
							'bidirectional' => 0,
							'ui' => 1,
							'bidirectional_target' => array(),
							'parent_repeater' => 'field_6745d9e1987af',
						),
						array(
							'key' => 'field_6745def4af1a4',
							'label' => 'Image',
							'name' => 'image',
							'type' => 'image',
							'return_format' => 'id',
							'library' => 'all',
							'preview_size' => 'medium',
							'parent_repeater' => 'field_6745d9e1987af',
						),
					),
				),
				array(
					'key' => 'field_6744a1d23baa5',
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
						'value' => 'acf/event-agenda-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		));
	}
}
