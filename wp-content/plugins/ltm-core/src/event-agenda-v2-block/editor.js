/**
 * Adds the "Make menu sticky" toggle to this block's Styles tab in the
 * inspector sidebar.
 *
 * It is not a block style (block.json `styles`): those are mutually
 * exclusive — the editor writes exactly one `is-style-*` class and
 * replaceActiveStyle() strips any other — so a "Sticky" entry would clear the
 * Pink/Blue theme the moment it was picked. Sticky is an independent axis, so
 * it gets its own control, placed in the same sidebar tab via
 * InspectorControls' `group="styles"`.
 *
 * ACF renders this block in `preview` mode, so there is no edit component of
 * our own to hang the panel off; the `editor.BlockEdit` filter wraps ACF's.
 * The `makeSticky` attribute is declared in block.json, which ACF passes
 * through to the client registration verbatim (pro/blocks.php localizes the
 * whole block type, and its "strip empty defaults" pass leaves a boolean
 * `false` alone), so both the editor and render.php see it.
 */
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const BLOCK_NAME = 'acf/event-agenda-v2-block';

const withStickyMenuToggle = createHigherOrderComponent(
	( BlockEdit ) => ( props ) => {
		if ( props.name !== BLOCK_NAME ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls group="styles">
					<PanelBody title={ __( 'Agenda menu', 'ltm' ) }>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Make menu sticky', 'ltm' ) }
							help={ __(
								'Pins the day buttons to the top of the viewport while the agenda scrolls past.',
								'ltm'
							) }
							checked={ !! attributes.makeSticky }
							onChange={ ( makeSticky ) =>
								setAttributes( { makeSticky } )
							}
						/>
					</PanelBody>
				</InspectorControls>
			</>
		);
	},
	'withStickyMenuToggle'
);

addFilter(
	'editor.BlockEdit',
	'ltm/event-agenda-v2-sticky-menu',
	withStickyMenuToggle
);
