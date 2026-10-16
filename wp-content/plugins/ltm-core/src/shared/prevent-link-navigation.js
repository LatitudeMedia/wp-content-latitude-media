/**
 * Editor-only props that stop links inside a block's preview (e.g. a
 * ServerSideRender of real post cards) from navigating away from the editor
 * when clicked, middle-clicked, or activated with the keyboard. Spread onto
 * the element wrapping the preview.
 *
 * @param {MouseEvent} event Click or auxclick event from the preview.
 */
function preventLinkNavigation( event ) {
	if ( event.target.closest( 'a' ) ) {
		event.preventDefault();
	}
}

export const preventLinkNavigationProps = {
	onClickCapture: preventLinkNavigation,
	onAuxClickCapture: preventLinkNavigation,
};
