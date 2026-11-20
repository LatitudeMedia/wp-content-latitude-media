/**
 * Registers the Glossary Index block. Rendering is server-side (render.php);
 * the editor shows a ServerSideRender preview.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

import './style.scss';

import Edit from './edit';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit: Edit,
} );
