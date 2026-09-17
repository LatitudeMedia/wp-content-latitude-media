/**
 * No editor JS is needed — ACF's block.json `"mode": "preview"` renders a
 * live front-end preview in the editor, and `render.php` handles both. This
 * entry exists only so wp-scripts bundles `style.scss` (webpack's block.json
 * scan only compiles styles reachable from a script entry — see
 * event-preview-block/index.js for the same pattern).
 */
import './style.scss';
