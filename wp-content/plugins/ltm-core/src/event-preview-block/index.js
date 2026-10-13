/**
 * ACF's block.json `"mode": "preview"` renders a live front-end preview in
 * the editor via `render.php`, so no edit component is needed. This entry
 * bundles `style.scss` (webpack's block.json scan only compiles styles
 * reachable from a script entry — see event-agenda-v2-block/index.js for the
 * same pattern) and adds one editor-only warning, below.
 *
 * The "Button" row links to the Event's `link` field, which lives in the
 * "General event options" meta box (sidebar, Post tab), not in the block.
 * render.php skips the button while that field is empty, so ticking "Button"
 * would otherwise change nothing in the preview, with no explanation. While
 * "Button" is ticked and the link is empty, show a warning under the Rows
 * checkboxes that jumps to the link field.
 */

/**
 * WordPress dependencies
 */
import { dispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import './style.scss';

const ROWS_FIELD_KEY = 'field_67473ac9a1f7c';
const LINK_FIELD_KEY = 'field_6713ae62e156d';
const EVENT_OPTIONS_BOX_ID = 'acf-group_6744b701b9197';

function getLinkInput() {
	return document.getElementById( `acf-${ LINK_FIELD_KEY }` );
}

/**
 * Opens the Post tab, expands the "General event options" box if collapsed,
 * then scrolls to and focuses the link input once it is on screen.
 *
 * @param {Event} event Click event.
 */
function revealLinkField( event ) {
	event.preventDefault();
	dispatch( 'core/edit-post' ).openGeneralSidebar( 'edit-post/document' );

	// The sidebar panel mounts on a later render; wait for it (up to ~2s).
	let frames = 120;
	const tryFocus = () => {
		const input = getLinkInput();
		if ( ! input?.offsetParent ) {
			const box = document.getElementById( EVENT_OPTIONS_BOX_ID );
			if ( box?.classList.contains( 'closed' ) ) {
				box.querySelector( '.handlediv' )?.click();
			}
			if ( --frames > 0 ) {
				window.requestAnimationFrame( tryFocus );
			}
			return;
		}
		input.scrollIntoView( { block: 'center' } );
		input.focus();
	};
	tryFocus();
}

function createNotice() {
	const notice = document.createElement( 'div' );
	notice.className = 'acf-notice -warning ltm-event-preview-link-notice';
	notice.setAttribute( 'role', 'alert' );

	const message = document.createElement( 'p' );
	message.textContent = __( 'Link is missing from Event data.', 'ltm' ) + ' ';

	const link = document.createElement( 'a' );
	link.href = `#acf-${ LINK_FIELD_KEY }`;
	link.textContent = __( 'Add it in General event options', 'ltm' );
	link.addEventListener( 'click', revealLinkField );

	message.append( link );
	notice.append( message );
	return notice;
}

/**
 * @param {HTMLElement} notice Notice inside a Rows field.
 */
function updateNotice( notice ) {
	const buttonRow = notice
		.closest( '.acf-field' )
		.querySelector( 'input[type="checkbox"][value="button"]' );
	const show = buttonRow?.checked && ! getLinkInput()?.value.trim();
	// Not `hidden`: .acf-notice sets `display`, which overrides it.
	notice.style.display = show ? '' : 'none';
}

function updateAllNotices() {
	document
		.querySelectorAll( '.ltm-event-preview-link-notice' )
		.forEach( updateNotice );
}

function init() {
	const { acf } = window;
	if ( ! acf ) {
		return;
	}

	// Fires for each block form ACF mounts in the sidebar.
	acf.addAction( `new_field/key=${ ROWS_FIELD_KEY }`, ( field ) => {
		const notice = createNotice();
		field.$el.find( '.acf-input' ).first().append( notice );
		updateNotice( notice );
	} );

	// Ticking a Rows checkbox, or typing in the link field.
	document.addEventListener( 'change', updateAllNotices );
	document.addEventListener( 'input', updateAllNotices );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
