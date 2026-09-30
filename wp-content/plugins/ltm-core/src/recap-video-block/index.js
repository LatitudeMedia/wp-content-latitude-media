/**
 * ACF's block.json `"mode": "preview"` renders a live front-end preview in the
 * editor, and `render.php` handles both the preview and the front end, so there
 * is no edit component here. This entry bundles `style.scss` -- webpack's
 * block.json scan only compiles styles reachable from a script entry -- and
 * fixes the video preview in the editor canvas (below).
 */
import './style.scss';

/*
 * The editor canvas is an iframe loaded from a `blob:` URL, and browsers send
 * no Referer for requests made from a `blob:` document. YouTube rejects embeds
 * without one ("Error 153: Video player configuration error"), so the preview's
 * oEmbed <iframe> never plays in the canvas. The front end is unaffected.
 *
 * Same fix as core's Embed block (the SandBox component): move the embed into
 * an about:blank iframe whose document is written from this script. The
 * document.open() call runs in the top admin window, which gives the sandbox
 * that window's URL, so the embed request carries a normal Referer.
 *
 * editorScript runs in the top window only -- core keeps editor scripts out of
 * the canvas (_wp_get_iframed_editor_assets()) -- which this relies on.
 */

const EMBED = '.recap-iframe-folder > iframe:not(.recap-video-sandbox)';

const SANDBOX_CSS =
	'html,body{margin:0;height:100%;overflow:hidden}' +
	'iframe{display:block;border:0;width:100%;height:100%}';

/**
 * Replaces an embed iframe in the canvas with a sandbox iframe containing it.
 *
 * @param {HTMLIFrameElement} embed oEmbed iframe from render.php.
 */
const sandbox = ( embed ) => {
	const frame = embed.ownerDocument.createElement( 'iframe' );
	frame.className = 'recap-video-sandbox';
	frame.title = embed.title;
	// A nested iframe only gets the features its parent is allowed.
	frame.setAttribute( 'allow', embed.getAttribute( 'allow' ) || '' );
	frame.allowFullscreen = embed.allowFullscreen;
	embed.replaceWith( frame );

	const doc = frame.contentDocument;
	doc.open();
	doc.write(
		`<!doctype html><style>${ SANDBOX_CSS }</style>${ embed.outerHTML }`
	);
	doc.close();
};

/**
 * Sandboxes every embed in a canvas document, now and whenever ACF re-renders
 * the block preview.
 *
 * @param {Document} doc Editor canvas document.
 */
const watchCanvas = ( doc ) => {
	const run = () => doc.querySelectorAll( EMBED ).forEach( sandbox );
	new window.MutationObserver( run ).observe( doc.documentElement, {
		childList: true,
		subtree: true,
	} );
	run();
};

// The canvas iframe can be created late and recreated (e.g. switching device
// preview), and its blob document loads asynchronously. This script is loaded
// in <head>, before <body> exists, hence observing documentElement.
const canvases = new WeakSet();
const findCanvases = () => {
	document
		.querySelectorAll( 'iframe[name="editor-canvas"]' )
		.forEach( ( canvas ) => {
			if ( canvases.has( canvas ) ) {
				return;
			}
			canvases.add( canvas );
			canvas.addEventListener( 'load', () =>
				watchCanvas( canvas.contentDocument )
			);
			if ( canvas.contentDocument?.readyState === 'complete' ) {
				watchCanvas( canvas.contentDocument );
			}
		} );
};

new window.MutationObserver( findCanvases ).observe( document.documentElement, {
	childList: true,
	subtree: true,
} );
findCanvases();
