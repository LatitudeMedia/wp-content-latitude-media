<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Image and text block
 * (acf/image-and-text), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * In the theme this block's eight `is-style-*` variants were eight nearly
 * identical template parts under template-parts/components/image-and-text/,
 * reached through a dispatcher template part. They differed only in the
 * wrapper's class string, the container class, the element that holds the
 * image, the registered image size, and whether the image slot is guarded
 * against an empty logo / rendered before the text. That difference is the
 * STYLES map below, and render.php is now a single template driven by it.
 *
 * Note: the five image sizes the map names (image-text-default, -type4, -type5,
 * -type6, -type7) are still registered by the THEME, in inc/media.php. That
 * inverts the arrangement used for the Speakers CPT, whose sizes moved into
 * this plugin -- deliberately, because image-text-default and image-text-type7
 * are also consumed by four theme templates that are not migrating
 * (our-approach-block, event-about-sponsors-block, research-preview-block,
 * order-preview-block). Image sizes resolve by name, so nothing here breaks as
 * long as those add_image_size() calls stay put.
 *
 * @package LTMCore
 */
class ImageAndText {

	/**
	 * Per-style rendering configuration, keyed by the `is-style-*` suffix.
	 *
	 * Fields:
	 *  - classes:     appended after the shared `content-block` class.
	 *  - container:   the inner container class.
	 *  - wrapper:     the element wrapping the image slot and the text slot.
	 *  - image_slot:  class on the element holding the image.
	 *  - text_slot:   class on the element holding the InnerBlocks.
	 *  - size:        registered image size (see the class docblock).
	 *  - guard_image: false renders the image slot even when there is no logo,
	 *                 producing an empty div. Preserved from the theme, where
	 *                 type6 and type8 alone lacked the !empty($logo) check.
	 *  - link_image:  false renders a bare <img> with no wrapper element and
	 *                 ignores the image_link field. Only `default` does this:
	 *                 it called thumbnail_formatting() directly instead of the
	 *                 print_image_and_text_image() helper every other style
	 *                 used. Almost certainly an oversight, but it is the
	 *                 current front-end output, so it is preserved here rather
	 *                 than silently changed under existing content.
	 *  - text_first:  true renders the text slot before the image slot.
	 */
	const STYLES = array(
		'default' => array(
			'classes'     => 'logo-description-block',
			'container'   => 'container-narrow',
			'wrapper'     => 'logo-description-block-wrapper',
			'image_slot'  => 'logo-image',
			'text_slot'   => 'description',
			'size'        => 'image-text-default',
			'guard_image' => true,
			'link_image'  => false,
			'text_first'  => false,
		),
		'type2'   => array(
			'classes'     => 'logo-description-block reverse logo-description-block-bordered',
			'container'   => 'container-narrow',
			'wrapper'     => 'logo-description-block-wrapper',
			'image_slot'  => 'logo-image',
			'text_slot'   => 'description',
			'size'        => 'image-text-default',
			'guard_image' => true,
			'link_image'  => true,
			'text_first'  => false,
		),
		'type3'   => array(
			'classes'     => 'icon-text-block',
			'container'   => 'container-narrow',
			'wrapper'     => 'icon-text-block-wrapper',
			'image_slot'  => 'icon-folder',
			'text_slot'   => 'content-folder',
			'size'        => 'image-text-default',
			'guard_image' => true,
			'link_image'  => true,
			'text_first'  => false,
		),
		'type4'   => array(
			'classes'     => 'image-text-section',
			'container'   => 'container-narrow',
			'wrapper'     => 'image-text-section-wrapper',
			'image_slot'  => 'image-folder',
			'text_slot'   => 'content-folder',
			'size'        => 'image-text-type4',
			'guard_image' => true,
			'link_image'  => true,
			'text_first'  => false,
		),
		'type5'   => array(
			'classes'     => 'podcasts-sponsorship-section',
			'container'   => 'container-narrow',
			'wrapper'     => 'podcasts-sponsorship-section-wrapper',
			'image_slot'  => 'image-folder',
			'text_slot'   => 'content-folder',
			'size'        => 'image-text-type5',
			'guard_image' => true,
			'link_image'  => true,
			'text_first'  => false,
		),
		'type6'   => array(
			'classes'     => 'image-text-section',
			'container'   => 'container',
			'wrapper'     => 'image-text-section-wrapper',
			'image_slot'  => 'image-folder',
			'text_slot'   => 'content-folder',
			'size'        => 'image-text-type6',
			'guard_image' => false,
			'link_image'  => true,
			'text_first'  => false,
		),
		'type7'   => array(
			'classes'     => 'image-text-section tall-it-block',
			'container'   => 'container-narrow',
			'wrapper'     => 'image-text-section-wrapper',
			'image_slot'  => 'image-folder',
			'text_slot'   => 'content-folder',
			'size'        => 'image-text-type7',
			'guard_image' => true,
			'link_image'  => true,
			'text_first'  => false,
		),
		'type8'   => array(
			'classes'     => 'image-text-section tall-it-block reverted',
			'container'   => 'container-narrow',
			'wrapper'     => 'image-text-section-wrapper',
			'image_slot'  => 'image-folder',
			'text_slot'   => 'content-folder',
			'size'        => 'image-text-type7',
			'guard_image' => false,
			'link_image'  => true,
			'text_first'  => true,
		),
	);

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
	 * Warns in wp-admin that the Image and text block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Image and text block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Resolves a block instance's className to its rendering configuration.
	 *
	 * A token match rather than the theme's ltm_get_block_style(): that helper
	 * lives in the theme, and ltm-core must not hard-depend on theme globals.
	 * It also parses with end( explode( '-', ... ) ), which would mis-route a
	 * future `is-style-type2-compact` to "compact", and reads $classStyle[0]
	 * after an array_filter() that preserves keys -- so it only works when the
	 * `is-style-*` class happens to come first. Matching exact class tokens
	 * here is position-independent and needs nothing from the theme.
	 *
	 * Falls back to the default style for an absent, empty or unrecognised
	 * style class, matching the theme dispatcher's behaviour.
	 *
	 * @param string $class_name The block's className attribute.
	 * @return array One of the self::STYLES entries.
	 */
	public static function style_config( string $class_name ): array {
		$classes = preg_split( '/\s+/', $class_name, -1, PREG_SPLIT_NO_EMPTY );

		foreach ( (array) $classes as $class ) {
			if ( 0 !== strpos( $class, 'is-style-' ) ) {
				continue;
			}

			$style = substr( $class, strlen( 'is-style-' ) );

			if ( isset( self::STYLES[ $style ] ) ) {
				return self::STYLES[ $style ];
			}
		}

		return self::STYLES['default'];
	}

	/**
	 * Registers the "Image and text" field group.
	 */
	public function register_field_group() {
		acf_add_local_field_group( array(
			'key' => 'group_6735515080ec9',
			'title' => 'Image and text',
			'fields' => array(
				array(
					'key' => 'field_67354f9994827',
					'label' => 'Image and text',
					'name' => '',
					'type' => 'message',
					'esc_html' => 0,
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_6735515f7ffa2',
					'label' => 'Title',
					'name' => 'title',
					'type' => 'text',
				),
				array(
					'key' => 'field_673551687ffa3',
					'label' => 'Logo',
					'name' => 'logo',
					'type' => 'image',
					'return_format' => 'array',
					'library' => 'all',
					'preview_size' => 'medium',
				),
				array(
					'key' => 'field_674f062c18efb',
					'label' => 'Image link',
					'name' => 'image_link',
					'type' => 'text',
				),
				array(
					'key' => 'field_67362db82424e',
					'label' => 'Base color',
					'name' => 'base_color',
					'type' => 'color_picker',
					'default_value' => '#C6168D',
					'enable_opacity' => 0,
					'return_format' => 'string',
				),
				array(
					'key' => 'field_6745b76159f4c',
					'label' => 'Shadow color',
					'name' => 'shadow_color',
					'type' => 'color_picker',
					'default_value' => '#F9E8F4',
					'enable_opacity' => 0,
					'return_format' => 'string',
				),
				array(
					'key' => 'field_67354f9994828',
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
						'value' => 'acf/image-and-text',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		) );
	}
}
