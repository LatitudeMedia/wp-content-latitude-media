/**
 * ACF's block.json `"mode": "preview"` renders a live front-end preview in
 * the editor, and `render.php` handles both, so there is no edit component
 * here. This entry bundles the stylesheets (webpack's block.json scan only
 * compiles styles reachable from a script entry — see
 * event-preview-block/index.js): `style.scss` becomes style-index.css (front
 * end + editor), `editor.scss` becomes index.css (editor only, see
 * `editorStyle` in block.json). `editor.js` is the one bit of real editor
 * behaviour: mirroring the "Form text" meta box input into the type2 preview.
 */
import './style.scss';
import './editor.js';
