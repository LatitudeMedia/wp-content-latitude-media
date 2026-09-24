<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Agenda V2 block
 * (acf/event-agenda-v2-block), migrated from the theme's acf-export.php.
 * Attached directly to the block (an ACF Block Version 3, `usePostMeta: true`
 * block — see its block.json) so field values still live in ordinary post
 * meta, but the editing form is opened via the block's "Open Expanded Editor"
 * modal (block.json's `hideFieldsInSidebar: true`) instead of ACF's cramped
 * Inspector sidebar.
 *
 * The field group key and every field key are unchanged from prior versions
 * so existing post content keeps resolving to the same data.
 *
 * @package LTMCore
 */
class EventAgendaV2 {

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
	 * Warns in wp-admin that the Event Agenda V2 block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Agenda V2 block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Resolves a block instance's className to its theme colour class.
	 *
	 * The block's styles are registered in block.json as `pink-theme` and
	 * `blue-theme`, which the editor writes as `is-style-pink-theme` /
	 * `is-style-blue-theme`. style.scss targets the bare `.pink-theme` /
	 * `.blue-theme` tokens (as the theme has always done, and as posts using
	 * an Additional-CSS-class still do), so render.php echoes the bare token
	 * alongside. Default (green) maps to no extra class.
	 *
	 * Matches exact class tokens rather than the theme's ltm_get_block_style()
	 * for the reasons spelled out in EventDescription::is_type2().
	 *
	 * @param string $class_name The block's className attribute.
	 * @return string `blue-theme`, `pink-theme`, or an empty string.
	 */
	public static function theme_class( string $class_name ): string {
		$classes = preg_split( '/\s+/', $class_name, -1, PREG_SPLIT_NO_EMPTY ) ?: [];

		foreach ( [ 'blue-theme', 'pink-theme' ] as $theme ) {
			if ( in_array( 'is-style-' . $theme, $classes, true ) ) {
				return $theme;
			}
		}

		return '';
	}

	/**
	 * Registers the "Event agenda V2 block" field group.
	 *
	 * Adds a `collapsed` row-summary field to the `days` and `agenda_items`
	 * repeaters (day title / item title respectively) so each row collapses
	 * to a single summary line by default instead of showing every field for
	 * every day and session at once. This is a UI-only setting on the two
	 * existing repeater fields — no field is added, renamed, or removed.
	 */
	public function register_field_group() {
		acf_add_local_field_group( array(
			'key' => 'group_6940b8de501ac',
			'title' => 'Event agenda V2 block',
			'fields' => array(
				array(
					'key' => 'field_6940b8dea1eb0',
					'label' => 'Title',
					'name' => 'title',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'relevanssi_exclude' => 0,
					'default_value' => '',
					'maxlength' => '',
					'allow_in_bindings' => 0,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'field_6940b928a1eb1',
					'label' => 'Days',
					'name' => 'days',
					'aria-label' => '',
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'relevanssi_exclude' => 0,
					'layout' => 'row',
					'pagination' => 0,
					'min' => 0,
					'max' => 0,
					'collapsed' => 'field_6940b947a1eb2',
					'button_label' => 'Add Row',
					'rows_per_page' => 20,
					'sub_fields' => array(
						array(
							'key' => 'field_6940b947a1eb2',
							'label' => 'Day Title',
							'name' => 'day_title',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'relevanssi_exclude' => 0,
							'default_value' => '',
							'maxlength' => '',
							'allow_in_bindings' => 0,
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_6940b928a1eb1',
						),
						array(
							'key' => 'field_6940ba294ae51',
							'label' => 'Agenda Items',
							'name' => 'agenda_items',
							'aria-label' => '',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'relevanssi_exclude' => 0,
							'layout' => 'row',
							'min' => 0,
							'max' => 0,
							'collapsed' => 'field_6940ba494ae52',
							'button_label' => 'Add Row',
							'rows_per_page' => 20,
							'sub_fields' => array(
								array(
									'key' => 'field_6942c8f1a3b01',
									'label' => 'Same Time as Previous Item?',
									'name' => 'same_time_as_previous_item',
									'aria-label' => '',
									'type' => 'true_false',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'relevanssi_exclude' => 0,
									'message' => '',
									'default_value' => 0,
									'allow_in_bindings' => 0,
									'ui' => 1,
									'ui_on_text' => '',
									'ui_off_text' => '',
									'parent_repeater' => 'field_6940ba294ae51',
								),
								array(
									'key' => 'field_6940ba854ae54',
									'label' => 'Start time',
									'name' => 'time',
									'aria-label' => '',
									'type' => 'time_picker',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => array(
										array(
											array(
												'field' => 'field_6942c8f1a3b01',
												'operator' => '!=',
												'value' => '1',
											),
										),
									),
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'relevanssi_exclude' => 0,
									'display_format' => 'g:i a',
									'return_format' => 'g:i a',
									'allow_in_bindings' => 0,
									'parent_repeater' => 'field_6940ba294ae51',
								),
								array(
									'key' => 'field_6943d1e2a4c02',
									'label' => 'End time',
									'name' => 'end_time',
									'aria-label' => '',
									'type' => 'time_picker',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => array(
										array(
											array(
												'field' => 'field_6942c8f1a3b01',
												'operator' => '!=',
												'value' => '1',
											),
										),
									),
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'relevanssi_exclude' => 0,
									'display_format' => 'g:i a',
									'return_format' => 'g:i a',
									'allow_in_bindings' => 0,
									'parent_repeater' => 'field_6940ba294ae51',
								),
								array(
									'key' => 'field_6940ba494ae52',
									'label' => 'Title',
									'name' => 'title',
									'aria-label' => '',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'relevanssi_exclude' => 0,
									'default_value' => '',
									'maxlength' => '',
									'allow_in_bindings' => 0,
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'parent_repeater' => 'field_6940ba294ae51',
								),
								array(
									'key' => 'field_6941a3c7d5e8b',
									'label' => 'Title Image',
									'name' => 'title_image',
									'type' => 'image',
									'return_format' => 'array',
									'library' => 'all',
									'preview_size' => 'medium',
									'parent_repeater' => 'field_6940ba294ae51',
								),
								array(
									'key' => 'field_6940ba754ae53',
									'label' => 'Description',
									'name' => 'description',
									'aria-label' => '',
									'type' => 'textarea',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'relevanssi_exclude' => 0,
									'default_value' => '',
									'maxlength' => '',
									'allow_in_bindings' => 0,
									'rows' => '',
									'placeholder' => '',
									'new_lines' => '',
									'parent_repeater' => 'field_6940ba294ae51',
								),
								array(
									'key' => 'field_6940ba984ae55',
									'label' => 'Speakers',
									'name' => 'speakers',
									'type' => 'relationship',
									'post_type' => array(
										0 => 'speakers',
									),
									'filters' => array(
										0 => 'search',
									),
									'return_format' => 'id',
									'parent_repeater' => 'field_6940ba294ae51',
								),
								array(
									'key' => 'field_6940c3a7b9d1e',
									'label' => 'Show moderator',
									'name' => 'show_moderator',
									'type' => 'true_false',
									'default_value' => 0,
									'ui' => 1,
									'parent_repeater' => 'field_6940ba294ae51',
								),
							),
							'parent_repeater' => 'field_6940b928a1eb1',
						),
					),
				),
				array(
					'key' => 'field_6940bdb6e0eac',
					'label' => 'Display',
					'name' => 'display',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'relevanssi_exclude' => 0,
					'message' => '',
					'default_value' => 0,
					'allow_in_bindings' => 0,
					'ui' => 1,
					'ui_on_text' => '',
					'ui_off_text' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/event-agenda-v2-block',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		) );
	}
}
