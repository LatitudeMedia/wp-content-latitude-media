/**
 * Keeps the type2 preview's form title in step with the "Form text" input.
 *
 * "Form text" (field_6713ae8de1570) lives in the theme's "General event
 * options" side meta box, not on the block, and render.php reads it from post
 * meta. ACF only re-renders a block preview when the block's OWN attributes
 * change, and even then the unsaved meta box value isn't in the database yet
 * -- so without this the canvas shows the stale saved text until the post is
 * saved and reloaded. Patching the preview DOM directly is the only way to
 * reflect the live value.
 */

const FIELD_KEY = 'field_6713ae8de1570';
const INPUT_SELECTOR = `.acf-field[data-key="${ FIELD_KEY }"] input`;
const TITLE_SELECTOR =
	'.wp-block-acf-event-description-block .right-sidebar-layout .form-title';

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

function syncFormTitle() {
	const input = document.querySelector( INPUT_SELECTOR );
	if ( ! input ) {
		return;
	}
	canvasDocuments().forEach( ( doc ) => {
		doc.querySelectorAll( TITLE_SELECTOR ).forEach( ( title ) => {
			title.textContent = input.value;
		} );
	} );
}

// Delegated: the meta box may render after this script runs.
document.addEventListener( 'input', ( event ) => {
	if ( event.target.matches?.( INPUT_SELECTOR ) ) {
		syncFormTitle();
	}
} );

// A fresh preview (fetched after any block attribute change) comes back with
// the saved value, so re-apply the live one on top of it.
function hookPreviewRenders() {
	window.acf?.addAction?.( 'render_block_preview', ( _$el, block ) => {
		if ( block?.name === 'acf/event-description-block' ) {
			syncFormTitle();
		}
	} );
}

if ( window.acf ) {
	hookPreviewRenders();
} else {
	document.addEventListener( 'DOMContentLoaded', hookPreviewRenders );
}
