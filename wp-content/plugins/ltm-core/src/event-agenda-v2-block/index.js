/**
 * No editor JS is needed — ACF's block.json `"mode": "edit"` renders the
 * fields form directly, and `render.php` handles both the editor preview and
 * the front end. This entry exists only so wp-scripts bundles `style.scss`
 * (webpack's block.json scan only compiles styles reachable from a script
 * entry — see title-block/index.js for the same pattern).
 */
import './style.scss';
