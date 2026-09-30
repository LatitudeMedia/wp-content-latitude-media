/**
 * Adds the "Layout" dropdown to this block's Styles tab in the inspector.
 *
 * The eight layouts used to be block styles (block.json `styles`, written as
 * `is-style-typeN`). That was the wrong mechanism: they are structural
 * variants, not visual skins, and as styles they permanently occupied the one
 * mutually-exclusive `is-style-*` slot, so no actual style could ever be added
 * alongside them. Same reasoning as `showForm` in
 * event-description-block/editor.js and `makeSticky` in
 * event-agenda-v2-block/editor.js.
 *
 * ACF renders this block in `preview` mode, so there is no edit component of
 * our own to hang the panel off; the `editor.BlockEdit` filter wraps ACF's.
 * The `layout` attribute is declared in block.json, which ACF passes through to
 * the client registration verbatim, so both the editor and render.php see it.
 *
 * `layout` is declared with NO default, which is load-bearing: it makes
 * `undefined` a third state meaning "saved before this dropdown existed".
 * Those blocks still carry their old `is-style-typeN` class, so both here and
 * in ImageAndText::layout_key() they fall back to it -- no data migration.
 */
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const BLOCK_NAME = 'acf/image-and-text';

/**
 * The labels carried over from the retired block.json `styles` entries. Values
 * are the keys of ImageAndText::LAYOUTS -- the two lists have to stay in step.
 */
const LAYOUTS = [
	{ label: __( 'Default', 'ltm' ), value: 'default' },
	{ label: __( 'Type 2 (inverse)', 'ltm' ), value: 'type2' },
	{ label: __( 'Type 3', 'ltm' ), value: 'type3' },
	{ label: __( 'Type 4', 'ltm' ), value: 'type4' },
	{ label: __( 'Type 5 (background)', 'ltm' ), value: 'type5' },
	{ label: __( 'Type 6 (square)', 'ltm' ), value: 'type6' },
	{ label: __( 'Type 7', 'ltm' ), value: 'type7' },
	{ label: __( 'Type 8 (inverse)', 'ltm' ), value: 'type8' },
];

/**
 * Mirrors ImageAndText::legacy_layout_key(), so a block saved under the retired
 * block styles opens with the right layout selected rather than "Default".
 * Change both together.
 *
 * @param {string} className The block's className attribute.
 * @return {string} A LAYOUTS value; 'default' when nothing matches.
 */
function legacyLayoutKey( className ) {
	const match = ( className ?? '' )
		.split( /\s+/ )
		.filter( ( name ) => name.startsWith( 'is-style-' ) )
		.map( ( name ) => name.slice( 'is-style-'.length ) )
		.find( ( name ) =>
			LAYOUTS.some( ( layout ) => layout.value === name )
		);

	return match ?? 'default';
}

const withLayoutControl = createHigherOrderComponent(
	( BlockEdit ) => ( props ) => {
		if ( props.name !== BLOCK_NAME ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls group="styles">
					<PanelBody title={ __( 'Layout', 'ltm' ) }>
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Layout', 'ltm' ) }
							help={ __(
								'Which arrangement of the image and the text this block renders.',
								'ltm'
							) }
							value={
								attributes.layout ??
								legacyLayoutKey( attributes.className )
							}
							options={ LAYOUTS }
							onChange={ ( layout ) =>
								setAttributes( { layout } )
							}
						/>
					</PanelBody>
				</InspectorControls>
			</>
		);
	},
	'withLayoutControl'
);

addFilter( 'editor.BlockEdit', 'ltm/image-and-text-layout', withLayoutControl );
