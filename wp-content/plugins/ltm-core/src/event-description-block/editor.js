/**
 * Editor-only behaviour for acf/event-description-block. Two separate concerns:
 *
 * 1. The "Displays page form?" inspector toggle (see the bottom of this file).
 * 2. Keeping that form preview in step with the "General event options" meta
 *    box, which is what the rest of this file does.
 */
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * -- 1. Meta box mirroring --------------------------------------------------
 *
 * Keeps the form preview in step with the "General event options" meta box.
 *
 * "Form text" (field_6713ae8de1570) and "Form code / Registration CTA"
 * (field_6713aea8e1571) live in the theme's side meta box, not on the block,
 * and render.php reads them from post meta. ACF only re-renders a block
 * preview when the block's OWN attributes change, and even then the unsaved
 * meta box value isn't in the database yet -- so without this the canvas shows
 * the stale saved values until the post is saved and reloaded. Patching the
 * preview DOM directly is the only way to reflect the live values.
 *
 * The form code itself is never rendered in the canvas; render.php emits a
 * placeholder paragraph instead, already carrying the right wording, and marks
 * it [hidden] when the saved field is empty. So "reflecting" a form code edit
 * means toggling that attribute on whether the field currently has content.
 */

const TEXT_FIELD_KEY = 'field_6713ae8de1570';
const CODE_FIELD_KEY = 'field_6713aea8e1571';
const TEXT_INPUT_SELECTOR = `.acf-field[data-key="${ TEXT_FIELD_KEY }"] input`;
const CODE_INPUT_SELECTOR = `.acf-field[data-key="${ CODE_FIELD_KEY }"] textarea`;
const BLOCK_SELECTOR =
	'.wp-block-acf-event-description-block .right-sidebar-layout';
const TITLE_SELECTOR = `${ BLOCK_SELECTOR } .form-title`;
const PLACEHOLDER_SELECTOR = `${ BLOCK_SELECTOR } .form-embed-placeholder`;

/**
 * The main document plus the editor canvas iframe, when there is one.
 * Gutenberg iframes the canvas unless a registered block still uses
 * apiVersion 1/2 (see specs/editor/featured-post-block.spec.js).
 */
function canvasDocuments() {
	const docs = [ document ];
	document
		.querySelectorAll( 'iframe[name="editor-canvas"]' )
		.forEach( ( frame ) => {
			if ( frame.contentDocument ) {
				docs.push( frame.contentDocument );
			}
		} );
	return docs;
}

function eachPreviewNode( selector, callback ) {
	canvasDocuments().forEach( ( doc ) => {
		doc.querySelectorAll( selector ).forEach( callback );
	} );
}

function syncFormTitle() {
	const input = document.querySelector( TEXT_INPUT_SELECTOR );
	if ( ! input ) {
		return;
	}
	eachPreviewNode( TITLE_SELECTOR, ( title ) => {
		title.textContent = input.value;
	} );
}

function syncFormCode() {
	const input = document.querySelector( CODE_INPUT_SELECTOR );
	if ( ! input ) {
		return;
	}
	const hasCode = input.value.trim() !== '';
	eachPreviewNode( PLACEHOLDER_SELECTOR, ( placeholder ) => {
		placeholder.hidden = ! hasCode;
	} );
}

function syncPreview() {
	syncFormTitle();
	syncFormCode();
}

// Delegated: the meta box may render after this script runs.
document.addEventListener( 'input', ( event ) => {
	if ( event.target.matches?.( TEXT_INPUT_SELECTOR ) ) {
		syncFormTitle();
	} else if ( event.target.matches?.( CODE_INPUT_SELECTOR ) ) {
		syncFormCode();
	}
} );

// A fresh preview (fetched after any block attribute change) comes back with
// the saved values, so re-apply the live ones on top of it.
function hookPreviewRenders() {
	window.acf?.addAction?.( 'render_block_preview', ( _$el, block ) => {
		if ( block?.name === 'acf/event-description-block' ) {
			syncPreview();
		}
	} );
}

if ( window.acf ) {
	hookPreviewRenders();
} else {
	document.addEventListener( 'DOMContentLoaded', hookPreviewRenders );
}

/**
 * -- 2. The "Displays page form?" toggle -------------------------------------
 *
 * This used to be a block style (block.json `styles`: default / type2), which
 * meant a layout-and-data switch was sitting in the Styles tab labelled "Type 2
 * (with form)", and it permanently occupied the one mutually-exclusive
 * `is-style-*` slot -- so no actual visual style could ever be added alongside
 * it. Same reasoning as `makeSticky` in event-agenda-v2-block/editor.js.
 *
 * ACF renders this block in `preview` mode, so there is no edit component of
 * our own to hang the panel off; the `editor.BlockEdit` filter wraps ACF's.
 *
 * `showForm` is declared in block.json with NO default, which is load-bearing:
 * it makes `undefined` a third state meaning "saved before this toggle
 * existed". Those blocks still carry the old `is-style-type2` class, so both
 * here and in EventDescription::shows_form() they fall back to it and keep
 * rendering the form -- no data migration.
 */

const BLOCK_NAME = 'acf/event-description-block';

/**
 * Mirrors EventDescription::has_legacy_type2_class().
 *
 * @param {string} className The block's className attribute.
 * @return {boolean} Whether it carries the retired type2 block style class.
 */
function hasLegacyType2Class( className ) {
	return ( className ?? '' ).split( /\s+/ ).includes( 'is-style-type2' );
}

const withShowFormToggle = createHigherOrderComponent(
	( BlockEdit ) => ( props ) => {
		if ( props.name !== BLOCK_NAME ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;
		const checked =
			attributes.showForm ?? hasLegacyType2Class( attributes.className );

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls group="styles">
					<PanelBody title={ __( 'Layout', 'ltm' ) }>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Displays page form?', 'ltm' ) }
							help={ __(
								"Adds a sidebar column with the event's registration form, taken from the Event's General event options.",
								'ltm'
							) }
							checked={ !! checked }
							onChange={ ( showForm ) =>
								setAttributes( { showForm } )
							}
						/>
					</PanelBody>
				</InspectorControls>
			</>
		);
	},
	'withShowFormToggle'
);

addFilter(
	'editor.BlockEdit',
	'ltm/event-description-show-form',
	withShowFormToggle
);
