/**
 * ACF's block.json `"mode": "preview"` renders a live front-end preview in
 * the editor, and `render.php` handles both the preview and the front end, so
 * there is no edit component here. This entry bundles `style.scss` (webpack's
 * block.json scan only compiles styles reachable from a script entry — see
 * title-block/index.js for the same pattern) and `editor.js`, which adds the
 * "Make menu sticky" toggle to the inspector's Styles tab.
 */
import './style.scss';
import './editor.scss';
import './editor.js';
