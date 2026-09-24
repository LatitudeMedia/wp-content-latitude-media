/**
 * ACF's block.json `"mode": "preview"` renders a live front-end preview in
 * the editor, and `render.php` handles both, so there is no edit component
 * here. This entry exists only to bundle the stylesheet: webpack's block.json
 * scan compiles styles reachable from a script entry, and the `"style"` key
 * alone does not create one.
 */
import './style.scss';
