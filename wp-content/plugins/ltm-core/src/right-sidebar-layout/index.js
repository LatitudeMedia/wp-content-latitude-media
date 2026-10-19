/**
 * Registers the "Right Sidebar Layout" variation of core/columns.
 *
 * Not a new block type — no block.json, no edit/save component. Plain
 * core/columns with two core/column children (66% / 33%), using only native
 * WordPress attributes:
 *
 *   core/columns (isStackedOnMobile: true)
 *     > core/column, width: 66%  ("Main Column", .ltm-right-sidebar-layout__main-column)
 *     > core/column, width: 33%  ("Sidebar", .ltm-right-sidebar-layout__sidebar)
 *
 * The layout and mobile stacking come entirely from core/columns' own
 * built-in behavior. The column classNames are only hooks for blocks that
 * style themselves differently per column (e.g. featured-single-item-block).
 * Keep them in sync with the Thematic Pages template (ThematicPages.php).
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/core-blocks/core-blocks-design/core-block-columns/
 * @see https://developer.wordpress.org/block-editor/reference-guides/core-blocks/core-blocks-design/core-block-column/
 */

/**
 * WordPress dependencies
 */
import { registerBlockVariation } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

registerBlockVariation( 'core/columns', {
	name: 'right-sidebar-layout',
	title: __( 'Right Sidebar Layout', 'ltm' ),
	description: __(
		'Two-column layout with a 66/33 split. Stacks on mobile.',
		'ltm'
	),
	icon: 'align-pull-right',
	scope: [ 'inserter' ],
	attributes: {
		isStackedOnMobile: true,
		metadata: { name: __( 'Right Sidebar Layout', 'ltm' ) },
	},
	innerBlocks: [
		[
			'core/column',
			{
				width: '66%',
				className: 'ltm-right-sidebar-layout__main-column',
				metadata: { name: __( 'Main Column', 'ltm' ) },
			},
			[],
		],
		[
			'core/column',
			{
				width: '33%',
				className: 'ltm-right-sidebar-layout__sidebar',
				metadata: { name: __( 'Sidebar', 'ltm' ) },
			},
			[],
		],
	],
} );
