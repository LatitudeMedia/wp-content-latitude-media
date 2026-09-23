<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Gray Icon block
 * (acf/event-gray-icon-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * The block is restricted (block.json `parent`) to the Event Description
 * block's inner blocks. A few legacy Event posts still carry it at the top
 * level; `parent` only gates the inserter, so those keep rendering and
 * editing in place.
 *
 * @package LTMCore
 */
class EventGrayIcon {

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
	 * Warns in wp-admin that the Event Gray Icon block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Gray Icon block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the "Event gray icon block" field group.
	 */
	public function register_field_group() {
		acf_add_local_field_group( array(
			'key' => 'group_6745f79613c6a',
			'title' => 'Event gray icon block',
			'fields' => array(
				array(
					'key' => 'field_6745ed84d47f7',
					'label' => 'Event gray icon block',
					'name' => '',
					'type' => 'message',
					'esc_html' => 0,
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_6746402d35366',
					'label' => 'Settings',
					'name' => '',
					'type' => 'tab',
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_6745f79a85ad8',
					'label' => 'Type',
					'name' => 'type',
					'type' => 'select',
					'choices' => array(
						'default' => 'Default (one column)',
						'type2' => 'Type 2 (two columns)',
					),
					'default_value' => 'default',
					'return_format' => 'value',
					'allow_null' => 0,
					'ui' => 1,
					'ajax' => 0,
				),
				array(
					'key' => 'field_6745ed84d47f8',
					'label' => 'Display',
					'name' => 'display',
					'type' => 'true_false',
					'ui' => 1,
				),
				array(
					'key' => 'field_67463f2a9524f',
					'label' => 'Column one',
					'name' => '',
					'type' => 'tab',
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_6745f7bf729c4',
					'label' => 'Logo',
					'name' => 'logo',
					'type' => 'image',
					'return_format' => 'id',
					'library' => 'all',
					'preview_size' => 'medium',
				),
				array(
					'key' => 'field_67463cd4b8eea',
					'label' => 'Content',
					'name' => 'content',
					'type' => 'wysiwyg',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 1,
					'delay' => 0,
				),
				array(
					'key' => 'field_67463f139524e',
					'label' => 'Column two',
					'name' => '',
					'type' => 'tab',
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_6745f79a85ad8',
								'operator' => '==',
								'value' => 'type2',
							),
						),
					),
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_6745f7d9729c5',
					'label' => 'Logo',
					'name' => 'logo_2',
					'type' => 'image',
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_6745f79a85ad8',
								'operator' => '==',
								'value' => 'type2',
							),
						),
					),
					'return_format' => 'id',
					'library' => 'all',
					'preview_size' => 'medium',
				),
				array(
					'key' => 'field_67463d457c479',
					'label' => 'Content',
					'name' => 'content_2',
					'type' => 'wysiwyg',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 1,
					'delay' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/event-gray-icon-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		) );
	}
}
