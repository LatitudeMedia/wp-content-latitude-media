/**
 * ACF's block.json `"mode": "preview"` renders a live front-end preview in
 * the editor, and `render.php` handles both, so there is no edit component
 * here. This entry bundles `style.scss` (webpack's block.json scan only
 * compiles styles reachable from a script entry — see
 * event-preview-block/index.js) into style-index.css, for the front end and
 * the editor.
 *
 * `editor.js` is the editor behaviour, and must stay imported here: it is not
 * referenced from block.json, so dropping this line silently removes both the
 * "Displays page form?" inspector toggle and the "Form text" meta box
 * mirroring from the bundle, with no build error.
 */
import './style.scss';
import './editor.js';
