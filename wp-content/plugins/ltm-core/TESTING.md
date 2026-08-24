# Testing ltm-core

Two suites, both running against [`wp-env`](https://www.npmjs.com/package/@wordpress/env) Docker containers:

| Suite | Command | Runs on |
| --- | --- | --- |
| PHPUnit (integration) | `npm run test:php` | `tests-cli` container |
| Playwright (E2E) | `npm run test:e2e` | tests instance, `http://localhost:8889` |

## Prerequisites

- Docker running
- Node — this repo is asdf-managed (Node 20.6.0). In non-interactive shells Node may not be on
  `PATH`; use `export PATH="$HOME/.asdf/shims:$PATH"` first.
- `npm install` and `composer install` in this directory
- Playwright's browser binary, once per machine: `npm run test:e2e:install`

## Running

```bash
npm run env:start        # boots dev (8888) + tests (8889)
npm run test:php         # PHPUnit
npm run test:e2e         # Playwright (starts wp-env automatically if needed)
npm run test:e2e:ui      # interactive UI mode
npm run test:e2e:debug   # step through with the inspector
npm run test:e2e:report  # open the last HTML report
```

The Studio site on port 8881 is completely separate and is never touched by the test suites.

## How the environment is wired

`.wp-env.json` maps in three things the suites need:

- `ltm-core` itself (`.`)
- `../advanced-custom-fields-pro` — kept for real sponsored-path coverage even though
  `includes/PostTypes/Sponsors.php`'s `get_field()` call is now guarded (see
  [Unguarded `get_field()`](#unguarded-get_field-in-sponsorsphp) — fixed). See also the ACF
  field-group gap below for why mapping it in still doesn't buy real sponsored-toggle E2E coverage.
- `../../themes/latitudemedia` — `src/featured-post-block/render.php` calls `get_template_part()`,
  so without this theme the block renders an empty wrapper and frontend assertions fail for the
  wrong reason.

`bin/wp-env-after-start.sh` (wired via `lifecycleScripts.afterStart`) then activates the theme and
sets `/%postname%/` permalinks plus a rewrite flush on both containers. The `thematic-pages` CPT
rewrites to `/themes/{slug}`, which 404s under wp-env's default plain permalinks —
`ltm_core_activate()` only flushes on the activation hook, which has already run by then.

## Gotchas worth knowing

**Cold-start race.** Playwright's `webServer` only waits for port 8889 to accept TCP connections,
which happens *before* wp-env finishes installing WordPress and activating plugins. The symptom is
an "Invalid post type." error page failing only the first spec, while later specs pass because
provisioning completed in the meantime. `specs/global-setup.js` gates the suite on
`/wp-json/wp/v2/types/thematic-pages` returning OK before any test runs.

**Plugin activation does not survive `destroy` + `start`.** wp-env activates mapped plugins when it
*creates* an instance, but after `npx wp-env destroy` the containers can come back with `ltm-core`
and ACF Pro **inactive**. Symptom is identical to the cold-start race above — `Invalid post type.`
— but the cause is different, so check activation before assuming a timing problem:

```bash
npx wp-env run tests-cli wp plugin list --fields=name,status
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8889/wp-json/wp/v2/types/thematic-pages
```

`bin/wp-env-after-start.sh` now runs `wp plugin activate` explicitly on both containers, so a plain
`npm run env:start` re-provisions correctly. If the readiness gate in `specs/global-setup.js` times
out, run those two commands — a `200` plus two `active` rows means the environment is fine.

**The thematic-pages editor seeds an empty `title-block`.** So an inserted title block is the *last*
match, not the first — `blocks.find()` reads the empty default and gives you a confusing failure.
Filter and assert across all matches instead.

**No "Add title" field in the canvas** for thematic-pages; the Title Block renders the heading
itself. Seed titles via `admin.createNewPost( { title } )`.

**`actionTimeout` is raised to 30s, `expect.timeout` to 15s** (from the bundled 10s / 5s) because
this stack loads Yoast, ACF Pro and a large theme.

**The block editor is not iframed on this site.** Several ACF Composer blocks (`acf/content-wrapper`,
`acf/ad-banner-section`, etc.) are registered with `apiVersion` 2 or lower, which makes Gutenberg fall
back to the legacy non-iframed canvas — there is no `[name="editor-canvas"]` frame to find. Confirmed
via the browser console warning ("This means that the post editor may work as a non-iframe editor").
**Don't use `editor.canvas`** — assert against `page` directly instead. This is why
`specs/editor/featured-post-block.spec.js` uses `page.getByLabel('Editor content').getByText(...)`
rather than `editor.canvas.getByText(...)`.

**Classic meta box saves race the reload that follows them.** The "Is Sponsored By" meta box
(`LTMCore\Taxonomies\PostSponsor`) is a classic PHP meta box; the block editor submits its data via a
*separate* background request to `post.php` after the main REST save completes. Clicking "Save"/
"Update" and immediately reloading reads back the pre-save value. Wait for that request explicitly:

```js
const metaBoxesSaved = page.waitForResponse(
	( response ) =>
		response.url().includes( '/wp-admin/post.php' ) &&
		response.request().method() === 'POST'
);
await page.getByRole( 'button', { name: 'Save', exact: false } ).first().click();
await metaBoxesSaved;
```

See `specs/admin/sponsor-meta-box.spec.js`, `sponsored-column.spec.js`.

**The classic meta box's `id="ltm_sponsor"` collides with its own wrapper `<div>`.** WordPress gives
the `.postbox` wrapper the same `id` as the meta box registration (`ltm_sponsor`), and the `<select
id="ltm_sponsor">` inside it shares that id — `page.locator('#ltm_sponsor')` is a strict-mode
violation. Use `select#ltm_sponsor`.

## Known code-level concerns

One pre-existing issue found while scaffolding the suite, not caused by the tests and not fixed —
recorded here so it isn't rediscovered from scratch. (The unguarded `get_field()` call this section
used to describe has since been fixed: `includes/PostTypes/Sponsors.php`'s `render_sponsored_column()`
now mirrors the `function_exists( 'get_field' )` guard already used in
`includes/Taxonomies/PostSponsor.php`.)

### `title-block` declares both `save.js` and `render.php`

`src/title-block/block.json` sets `"render": "file:./render.php"` while `src/title-block/save.js`
also exists. When `render` is present it wins, so **all** front-end markup comes from PHP and
`save.js` output is only a fallback in the saved post content. Nothing signals this at a glance, and
`src/featured-post-block/` has no `save.js` at all — so the two blocks follow different conventions
for no documented reason.

The risk is silent: dropping `render.php` (or renaming the `render` key) would switch the output
source to `save.js` with no error, changing the rendered markup. The
`renders server-side output on the frontend` test in `specs/editor/title-block.spec.js` asserts
against `.ltm-title-block`, a class only `render.php` emits, specifically to catch that.

If the `save.js` fallback is intentional, a comment in `block.json` or `save.js` should say so. If it
is vestigial, delete it so the block matches `featured-post-block`.

## Known gap: ACF field groups

The two ACF field groups live in the **database**, not in version control — there is no `acf-json/`
directory anywhere in the theme or plugin. Mapping ACF Pro into wp-env therefore provides the
plugin but *no field data*: `get_field( 'sponsored', ... )` returns `null` on a fresh instance, so
sponsored-flag behaviour is not genuinely covered by E2E tests yet.

Fixing this properly means enabling ACF local JSON (`acf/settings/save_json`) and committing the
exported field groups, after which they auto-sync into any fresh install. Until then, treat
sponsor/sponsored assertions as unverified in E2E and rely on the PHPUnit suite, which sets term
meta directly.

## Coverage

PHPUnit (`tests/`) covers CPT/taxonomy registration and sync-on-save/delete logic, the sponsor meta
box's save/replace/clear semantics, the `ltm_sponsor` REST field, the `featured-post/search` REST
endpoint's query shaping, block registration/allowed-block-types filtering, and `title-block/render.php`.
It does not cover `featured-post-block/render.php` beyond its early-return guard — that block's render
pulls in the theme's `get_wrap_rows_from_template()`/`Page_Data()`/`post-item` template-part chain, a
poor PHPUnit target.

Playwright (`specs/`) covers the block editor UI (Title Block, Featured Post Block's live search
combobox), the sponsor meta box round-tripped through real save/reload cycles, the Sponsored admin
column, the Thematic Page Types taxonomy's hidden-but-assignable behavior, and frontend rendering of
both blocks and the `/themes/{slug}` URL.
