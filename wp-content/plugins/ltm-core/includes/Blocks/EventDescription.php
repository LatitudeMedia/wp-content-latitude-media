<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Description block
 * (acf/event-description-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * Note: this block's "type2" style also reads two fields from a second,
 * broader field group ("General event options", post_type == events) that is
 * NOT part of this class or this migration — that group stays defined in the
 * theme and is read via plain get_field( ..., $post_id ) in render.php, same
 * as before the move. The two fields are form_text (field_6713ae8de1570) and
 * form_code__registration_cta (field_6713aea8e1571).
 *
 * @package LTMCore
 */
class EventDescription {

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
	 * Warns in wp-admin that the Event Description block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Description block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Whether a block instance uses the "type2" (with form) style.
	 *
	 * A token match rather than the theme's ltm_get_block_style(): that helper
	 * lives in the theme, and ltm-core must not hard-depend on theme globals.
	 * It also parses with end( explode( '-', ... ) ), which would mis-route a
	 * future `is-style-type2-compact` to "compact", and reads $classStyle[0]
	 * after an array_filter() that preserves keys — so it only works when the
	 * `is-style-*` class happens to come first. Matching the exact class token
	 * here is position-independent and needs nothing from the theme.
	 *
	 * @param string $class_name The block's className attribute.
	 * @return bool
	 */
	public static function is_type2( string $class_name ): bool {
		$classes = preg_split( '/\s+/', $class_name, -1, PREG_SPLIT_NO_EMPTY );

		return is_array( $classes ) && in_array( 'is-style-type2', $classes, true );
	}

	/**
	 * Registers the "Event description block" field group.
	 */
	public function register_field_group() {
		acf_add_local_field_group( array(
			'key' => 'group_674595857108f',
			'title' => 'Event description block',
			'fields' => array(
				array(
					'key' => 'field_674481518f9b4',
					'label' => 'Event description block',
					'name' => '',
					'type' => 'message',
					'esc_html' => 0,
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_674595b6064e9',
					'label' => 'Title',
					'name' => 'title',
					'type' => 'text',
				),
				array(
					'key' => 'field_674481518f9b5',
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
						'value' => 'acf/event-description-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		) );
	}
}
