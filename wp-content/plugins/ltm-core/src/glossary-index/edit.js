import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { preventLinkNavigationProps } from '../shared/prevent-link-navigation';

/**
 * No settings: the block always lists every published glossary term. The
 * search field stays hidden in the editor because view.js only runs on the
 * front end.
 *
 * @param {Object} props            Block edit props.
 * @param {Object} props.attributes Block attributes (passed to the preview).
 * @param {string} props.name       Block name.
 * @return {Element} Editor preview.
 */
export default function Edit( { attributes, name } ) {
	return (
		<div { ...useBlockProps() } { ...preventLinkNavigationProps }>
			<ServerSideRender block={ name } attributes={ attributes } />
		</div>
	);
}
