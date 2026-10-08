/**
 * ACF's block.json `"mode": "preview"` renders a live front-end preview in the
 * editor, and `render.php` handles both the preview and the front end, so there
 * is no edit component here. This entry exists only to bundle `style.scss` --
 * webpack's block.json scan only compiles styles reachable from a script entry.
 */
import './style.scss';
