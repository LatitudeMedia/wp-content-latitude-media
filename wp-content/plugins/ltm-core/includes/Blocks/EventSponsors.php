<?php
namespace LTMCore\Blocks;

/**
 * Registers the ACF field group backing the Event Sponsors block
 * (acf/event-sponsors-block), migrated from the theme's acf-export.php.
 *
 * The field group key and every field key are unchanged from the theme
 * version so existing post content keeps resolving to the same data.
 *
 * This class only covers the block's own fields. The sponsors post type lives
 * in this plugin too (see LTMCore\PostTypes\Sponsors), but the per-sponsor
 * "Sponsor options" fields (website_link) are still registered by the theme and
 * read by sponsor ID from render.php.
 *
 * The retired acf/event-partners-block was a clone of this block whose field
 * names were identical; its stored content is folded into this group by
 * migrate_partners_blocks() below, with the `title` field carrying the
 * "Sponsors" / "Partners" section label.
 *
 * @package LTMCore
 */
class EventSponsors {

	/**
	 * Option guarding the one-time acf/event-partners-block content migration.
	 */
	const MIGRATED_OPTION = 'ltm_event_partners_block_migrated';

	/**
	 * Option holding the blocks the migration had to drop, for later reference.
	 */
	const ORPHANS_OPTION = 'ltm_event_partners_block_orphans';

	/**
	 * partners field key => sponsors field key.
	 *
	 * Verified 1:1 against acf_get_fields() for both groups: identical field
	 * names, types and repeater structure, differing only in key.
	 */
	const FIELD_KEY_MAP = array(
		'field_67465bd00137b' => 'field_6745aa4b00418', // title.
		'field_67f9e3a4b1c01' => 'field_6745aa7b0041b', // different_sizes.
		'field_67f9e3a4b1c02' => 'field_6745aa6b00419', // sponsors_category (repeater).
		'field_67f9e3a4b1c03' => 'field_6745ad27e802f', // sponsors_category > title.
		'field_67f9e3a4b1c04' => 'field_6745ad27e802g', // sponsors_category > size.
		'field_67f9e3a4b1c05' => 'field_6745ad2ee8030', // sponsors_category > sponsors.
		'field_67f9e3a4b1c06' => 'field_6745aa7b0041a', // show_read_more_button.
		'field_6746576f2ae1d' => 'field_67449c54b1bf1', // display.
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Registered before the ACF check below: rewriting stored block markup
		// does not need ACF, and the old block name must stop appearing in content
		// whether or not ACF happens to be active on this environment.
		add_action( 'init', array( $this, 'migrate_partners_blocks' ), 20 );

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_acf_notice' ) );
			return;
		}

		add_action( 'acf/include_fields', array( $this, 'register_field_group' ) );
	}

	/**
	 * Folds the retired acf/event-partners-block into this block, once per
	 * environment, as the code reaches it.
	 *
	 * The partners block was a clone of the sponsors block -- same template, same
	 * stylesheet, and a field group whose field names and types were identical
	 * under different keys -- so it was retired rather than migrated to this
	 * plugin. Two generations of stored content exist:
	 *
	 *   1. `sponsors_category`, the current shape and a 1:1 match for this block.
	 *      Rewritten in place: block name swapped, field keys remapped. The
	 *      `title` field already holds "Partners", so the label survives.
	 *   2. `logos`, an older shape (a repeater of attachment ids) whose field
	 *      group was deleted long ago. Those blocks already render nothing, and
	 *      attachment ids cannot be mapped onto `sponsors` posts, so they are
	 *      removed and recorded in self::ORPHANS_OPTION for an editor to rebuild.
	 *
	 * Revisions are included, so restoring an old revision cannot resurrect a
	 * block name that is no longer registered. The rewrite is idempotent -- a
	 * second pass matches nothing -- so a run interrupted part way is simply
	 * retried on the next request.
	 */
	public function migrate_partners_blocks() {
		global $wpdb;

		if ( get_option( self::MIGRATED_OPTION ) ) {
			return;
		}

		$rows = $wpdb->get_results(
			"SELECT ID, post_type, post_title, post_content
			 FROM {$wpdb->posts}
			 WHERE post_content LIKE '%acf/event-partners-block%'"
		);

		// Every occurrence is a single-line self-closing block comment; the
		// line-bounded pattern keeps a replacement from ever spanning two blocks.
		$pattern = '/^[ \t]*<!-- wp:acf\/event-partners-block (\{.*\}) \/-->[ \t]*\r?\n/m';

		$orphans = get_option( self::ORPHANS_OPTION, array() );

		foreach ( $rows as $row ) {
			$stripped = 0;

			$content = preg_replace_callback(
				$pattern,
				function ( $match ) use ( $row, &$stripped, &$orphans ) {
					$attrs = json_decode( $match[1], true );

					if ( is_array( $attrs ) && isset( $attrs['data']['logos'] ) ) {
						++$stripped;

						if ( 'revision' !== $row->post_type ) {
							$orphans[] = array(
								'post_id'    => (int) $row->ID,
								'post_title' => $row->post_title,
								'title'      => $attrs['data']['title'] ?? '',
								'displayed'  => ! empty( $attrs['data']['display'] ),
								'logos'      => $this->orphan_logo_ids( $attrs['data'] ),
							);
						}

						return '';
					}

					// String surgery on the matched line only, rather than decoding and
					// re-encoding the attributes: every other byte -- `style`,
					// `metadata`, `className`, key order, number formatting -- stays
					// exactly as stored.
					$line = str_replace(
						array_keys( self::FIELD_KEY_MAP ),
						array_values( self::FIELD_KEY_MAP ),
						$match[0]
					);

					return str_replace( 'acf/event-partners-block', 'acf/event-sponsors-block', $line );
				},
				$row->post_content
			);

			if ( null === $content ) {
				// preg error: leave this row untouched and let the next request retry,
				// rather than writing a half-rewritten post.
				return;
			}

			// Collapse the blank line a removed block leaves behind.
			if ( $stripped ) {
				$content = preg_replace( "/\n{3,}/", "\n\n", $content );
			}

			if ( $content === $row->post_content ) {
				continue;
			}

			// $wpdb->update() rather than wp_update_post(): revisions are themselves
			// being rewritten here, and wp_update_post() would create fresh revisions
			// and fire save hooks across every affected row.
			$wpdb->update( $wpdb->posts, array( 'post_content' => $content ), array( 'ID' => (int) $row->ID ) );
			clean_post_cache( $row->ID );
		}

		if ( $orphans ) {
			update_option( self::ORPHANS_OPTION, $orphans, false );
		}

		update_option( self::MIGRATED_OPTION, 1, false );
	}

	/**
	 * Collects the attachment ids out of a removed `logos` repeater.
	 *
	 * @param array $data The block's stored ACF data.
	 * @return array
	 */
	private function orphan_logo_ids( array $data ) {
		$ids = array();

		foreach ( $data as $field => $value ) {
			if ( preg_match( '/^logos_\d+_partner_logo$/', $field ) ) {
				$ids[] = (int) $value;
			}
		}

		return $ids;
	}

	/**
	 * Warns in wp-admin that the Event Sponsors block needs ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Event Sponsors block requires Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Registers the field group. Copied verbatim from the theme's acf-export.php.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_6745aa44544dc',
			'title' => 'Event sponsors block',
			'fields' => array(
				array(
					'key' => 'field_67449c54b1bf0',
					'label' => 'Event sponsors block',
					'name' => '',
					'type' => 'message',
					'esc_html' => 0,
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_6745aa4b00418',
					'label' => 'Title',
					'name' => 'title',
					'type' => 'text',
				),
				array(
					'key' => 'field_6745aa7b0041b',
					'label' => 'Use different sizes per category',
					'name' => 'different_sizes',
					'type' => 'true_false',
					'ui' => 1,
				),
				array(
					'key' => 'field_6745aa6b00419',
					'label' => 'Sponsors category',
					'name' => 'sponsors_category',
					'type' => 'repeater',
					'layout' => 'table',
					'sub_fields' => array(
						array(
							'key' => 'field_6745ad27e802f',
							'label' => 'Title',
							'name' => 'title',
							'type' => 'text',
							'parent_repeater' => 'field_6745aa6b00419',
						),
						array(
							'key' => 'field_6745ad27e802g',
							'label' => 'Size',
							'name' => 'size',
							'aria-label' => '',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_6745aa7b0041b',
										'operator' => '==',
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
							'choices' => array(
								'xlarge' => 'X-Large',
								'large' => 'Large',
								'medium' => 'Medium',
								'small' => 'Small',
								'xsmall' => 'X-Small',
							),
							'default_value' => 'medium',
							'return_format' => 'value',
							'multiple' => 0,
							'allow_null' => 0,
							'allow_in_bindings' => 1,
							'ui' => 1,
							'ajax' => 0,
							'placeholder' => '',
							'create_options' => 0,
							'save_options' => 0,
							'parent_repeater' => 'field_6745aa6b00419',
						),
						array(
							'key' => 'field_6745ad2ee8030',
							'label' => 'Sponsors',
							'name' => 'sponsors',
							'type' => 'relationship',
							'post_type' => array(
								0 => 'sponsors',
							),
							'post_status' => '',
							'taxonomy' => '',
							'filters' => array(
								0 => 'search',
							),
							'return_format' => 'id',
							'elements' => '',
							'parent_repeater' => 'field_6745aa6b00419',
						),
					),
				),
				array(
					'key' => 'field_6745aa7b0041a',
					'label' => 'Show "Read more" button',
					'name' => 'show_read_more_button',
					'type' => 'true_false',
					'ui' => 1,
				),
				array(
					'key' => 'field_67449c54b1bf1',
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
						'value' => 'acf/event-sponsors-block',
					),
				),
			),
			'style' => 'seamless',
			'active' => true,
		));
	}
}
