# Latitude Media 

This is the repo connected to the Pressable webhost. 

We're tracking the theme and our first-party `ltm-core` plugin here (see section 12 below). Pressable is managing everything else's plugin upgrades, so we do not want those in version control.

As there is no current way to disable it, I must state a warning here: **DO NOT USE WORDPRESS STUDIO SYNC PUSH. ONLY PUSH TO GITHUB. SYNC PUSH WILL OVERWRITE PROD DB.**

# Recommended Workflow

This assumes you've already gotten a working localdev setup via the `Getting Started` section below.

- run a pull sync down in WP Studio
- pull down the main branch
- create a feature branch
- make your changes
- if you touched theme frontend assets, run the webpack build (see `Frontend asset build` below) and commit the resulting `dist/` changes
- push the changes to github
- make a pull request
- go into Pressable and run a data transfer from Production to Staging
- deploy your feature branch to Staging
- check that everything works
- run a sync again in WP Studio incase new changes made it to prod by means other than version control 
- commit those changes (if there are any) and upload them
- merge your feature branch into main in the pull request
- deploy main to production

# Getting Started

## 1. Download WordPress Studio Sync

Pressable prefers that you use the WordPress Studio localdev environment. 

Download it here: https://developer.wordpress.com/studio/

**NOTE: you _MUST_ have the GUI version of it installed. This will require Windows, OSX, or Debian-based Linux**

## 2. Sign up for Jetpack using the same login you use for latitudemedia.com

Sign up is here: https://jetpack.com/

You will have to pay the $10/m for the storage. WP Studio pulls from a Jetpack backup, and that backup exceeds 10GB. 

## 3. Connecting a Local Studio Site to Pressable

- Launch WordPress Studio on your machine.
- In the sidebar, select the local site to which you want to connect.
- Navigate to the Sync tab.
- Click Connect site.
- Log in with your WordPress.com account if prompted (**must be the same as the one used for latitudemedia.com and Jetpack**)
- Select the appropriate Pressable site (production, staging, or sandbox).

**NOTE: This step will fail at the db import phase. We will fix this manually. Just close the program when it starts trying to import the db during this initial pulldown. All we need is all the files.**

## 4. Download a database import

Go here: https://my.pressable.com/sites/1357119/backups_restores 

Download a copy of the production database.

## 5. Patching the database

This is a problem that will be fixed later.

At present, the production database has non-UTF-8 characters in them. This will cause the WP Studio db import to crash. 

Here's how we fix it.

- Download Docker locally
- Run this, changing the `YOUR_PROD_DB_BACKUP.sql` out for the actual file path:

```
docker run --rm -i \
  -v "/home/kim/Downloads/":/data \
  -w /data \
  python:3-slim \
  python3 - <<'EOF'
import re
with open("pressable-backup-latitudemediaprod-2026-07-28-23-00.sql", "rb") as f:
    sql = f.read()
blocks = re.split(rb"(?=-- Table structure for table)", sql)
kept = []
for block in blocks:
    m = re.match(rb"-- Table structure for table `(\w+)`", block)
    if m and re.match(rb"wp_pmxi_[A-Za-z0-9_]*$|wp_wf[A-Za-z0-9_]*$", m.group(1)):
        continue
    kept.append(block)
with open("no_pmxi_or_wf.sql", "wb") as f:
    f.write(b"".join(kept))
EOF
```

- A file called `no_pmxi_or_wf.sql` will be created. Check to see if it contains any non-UTF-8 characters:

```
# silence is golden.
# if this produces output, contact me@kimdcottrell.com via email or the Latitude Media Slack
grep -axv '.*' no_pmxi_or_wf.sql 
```

## 6. Import that to the WordPress Studio instance

- Open WordPress Studio
- Go to the Latitude Media instance
- Go to import/export
- Import `no_pxmi_or_wf.sql`

ALTERNATIVELY:

```
# from your latitudemedia instance Studio path, e.g. ~/Studio/latitudemedia
studio import no_pxmi_or_wf.sql
```

The CLI version is handy for more verbose output. The GUI can appear like it's just hanging.

## 7. Once the db is imported, try logging in at /wp-admin

You should see in the WP Studio GUI that you can access wp-admin now. 

Try doing so.

**You will hit a Jetpack error.**

Disable the plugin.

`studio wp plugin deactivate jetpack`

**SUCCESS: You should now be good to go and wp-admin and the localdev site should load**.

## 9. Hook up the git repo

- run `git clone https://github.com/LatitudeMedia/wp-content-latitude-media/`
- move everything in the `wp-content-latitude-media` dir into the WP Studio latitude media dir

## 10. Frontend asset build (webpack)

Theme CSS/JS is compiled from `wp-content/themes/latitudemedia/` via webpack. Node is managed with [asdf](https://asdf-vm.com/), pinned via `.tool-versions` at the project root.

- Install the asdf `nodejs` plugin if you don't already have it: `asdf plugin add nodejs`
- From the theme directory, install the pinned Node version and dependencies:

```
cd wp-content/themes/latitudemedia
asdf install
npm install
```

- Available scripts:
  - `npm run watch` — rebuild on file change, for local dev
  - `npm run build:assets` — one-off local build
  - `npm run build:assets:prod` — minified production build

`dist/` is committed to the repo (not gitignored), so run `npm run build:assets` (or `build:assets:prod` before deploying) after changing anything under `src/assets/`, and commit the resulting `dist/` changes alongside your source changes.

## 11. Recommendations

In WP Studio's GUI, enable debugging by going to:

- `Latitude Media` -> `Site Settings` -> `Debugging`
- Enable `Xdebug`
- Enable `Debug log`
- Disable `Debug display`

The in-browser display of errors can be misleading in certain instances. Just run a `tail -f wp-content/debug.log` as you write your code.

## 12. ltm-core plugin (blocks, CPTs, taxonomies, REST)

Native Gutenberg blocks, custom post types/taxonomies, and REST endpoints are gradually being
migrated out of the theme into a first-party plugin at `wp-content/plugins/ltm-core/`. It has its
own dependency install, build, and test setup, separate from everything above — see
[its readme](wp-content/plugins/ltm-core/readme.txt) and
[TESTING.md](wp-content/plugins/ltm-core/TESTING.md) for details. Tests run against `wp-env`
(Docker) on ports 8888/8889, completely separate from the Studio site on 8881.

CI (`.github/workflows/ltm-core-tests.yml`) runs the plugin's PHPUnit and Playwright suites on
every push/PR touching the plugin or theme.
