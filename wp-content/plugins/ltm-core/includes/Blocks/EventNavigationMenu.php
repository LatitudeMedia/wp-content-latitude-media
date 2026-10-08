<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Navigation Menu block
 * (acf/event-navigation-menu-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * @package LTMCore
 */
class EventNavigationMenu {

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
	 * Warns in wp-admin that the Event Navigation Menu block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Navigation Menu block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the field group. Copied verbatim from the theme's acf-export.php.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_693f9e7f697b4',
			'title' => 'Event navigation menu',
			'fields' => array(
				array(
					'key' => 'field_693f9e7fe2b84',
					'label' => 'Event navigation menu',
					'name' => '',
					'aria-label' => '',
					'type' => 'message',
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
					'new_lines' => 'wpautop',
					'esc_html' => 0,
				),
				array(
					'key' => 'field_693f9e96e2b85',
					'label' => 'Navigation Links',
					'name' => 'navigation_links',
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
					'layout' => 'table',
					'pagination' => 0,
					'min' => 0,
					'max' => 0,
					'collapsed' => '',
					'button_label' => 'Add Row',
					'rows_per_page' => 20,
					'sub_fields' => array(
						array(
							'key' => 'field_693f9eb4e2b86',
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
							'parent_repeater' => 'field_693f9e96e2b85',
						),
						array(
							'key' => 'field_693f9eede2b87',
							'label' => 'Anchor',
							'name' => 'anchor',
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
							'parent_repeater' => 'field_693f9e96e2b85',
						),
					),
				),
				array(
					'key' => 'field_6940019f6c2cb',
					'label' => 'Button One',
					'name' => 'button_one',
					'aria-label' => '',
					'type' => 'link',
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
					'allow_in_bindings' => 0,
				),
				array(
					'key' => 'field_694001bf6c2cc',
					'label' => 'Button Two',
					'name' => 'button_two',
					'aria-label' => '',
					'type' => 'link',
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
					'allow_in_bindings' => 0,
				),
				array(
					'key' => 'field_693fa5029694a',
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
					'default_value' => 1,
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
						'value' => 'acf/event-navigation-menu-block',
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
		));
	}
}
