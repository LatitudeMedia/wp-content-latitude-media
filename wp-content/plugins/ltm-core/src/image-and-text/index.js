/**
 * ACF's block.json `"mode": "preview"` renders a live front-end preview in
 * the editor, and `render.php` handles both, so there is no edit component
 * here. This entry bundles the stylesheet -- webpack's block.json scan compiles
 * styles reachable from a script entry, and the `"style"` key alone does not
 * create one -- and the editor behaviour.
 *
 * `editor.js` must stay imported here: it is not referenced from block.json, so
 * dropping this line silently removes the Layout dropdown from the bundle, with
 * no build error.
 */
import './style.scss';
import './editor.js';
