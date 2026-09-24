<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Styled button block
 * (acf/styled-button-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data. Note
 * that the group's own key is `field_673b3369c9f5e`, not the `group_`-prefixed
 * key ACF normally generates -- that is what the theme exported, and ACF keys
 * groups by whatever string it is given, so it is reproduced verbatim here.
 *
 * The block is styled entirely by the theme's shared `.cta-button` rule
 * (src/assets/scss/base/_global.scss), which ~8 other, unmigrated theme
 * templates also emit. It reaches the front end through hook_critical_css()
 * and the editor canvas through add_editor_style(), so this block ships no
 * stylesheet of its own.
 *
 * The theme's info-cta-block (template-parts/blocks/common/info-cta-block.php)
 * still names acf/styled-button-block in its InnerBlocks template; block names
 * resolve globally, so that keeps working from here.
 *
 * @package LTMCore
 */
class StyledButton {

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
	 * Warns in wp-admin that the Styled button block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Styled button block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the "Styled button block" field group.
	 */
	public function register_field_group() {
		acf_add_local_field_group( array(
			'key' => 'field_673b3369c9f5e',
			'title' => 'Styled button block',
			'fields' => array(
				array(
					'key' => 'field_673b3369c9f5f',
					'label' => 'Styled button block',
					'name' => '',
					'type' => 'message',
				),
				array(
					'key' => 'field_673b3369c9f60',
					'label' => 'Button',
					'name' => 'button',
					'type' => 'link',
					'return_format' => 'array',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/styled-button-block',
					),
				),
			),
			'style' => 'seamless'
		));
	}
}
