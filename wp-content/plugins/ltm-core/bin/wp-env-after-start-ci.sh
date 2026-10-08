#!/usr/bin/env bash
#
# wp-env afterStart lifecycle script for CI only.
#
# Same provisioning as bin/wp-env-after-start.sh, except that ACF Pro is
# installed from the lock file (`composer install`) using the COMPOSER_AUTH
# secret, since there is no local auth.json in CI. Wired up via a CI-only .wp-env.override.json (see
# .github/workflows/ltm-core-tests.yml).
#
# Keep this in sync with bin/wp-env-after-start.sh for everything else.

set -euo pipefail

# ACF Pro is installed from connect.advancedcustomfields.com, which needs the
# license credentials. CI provides them as COMPOSER_AUTH (a repository secret),
# but `wp-env run` doesn't forward host env vars into the container, so write
# them to a temporary auth.json in the bind-mounted plugin dir, where Composer
# picks it up, and remove it on exit. An existing auth.json is left alone.
PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
if [[ -n "${COMPOSER_AUTH:-}" && ! -e "${PLUGIN_DIR}/auth.json" ]]; then
	( umask 077 && printf '%s' "${COMPOSER_AUTH}" > "${PLUGIN_DIR}/auth.json" )
	trap 'rm -f "${PLUGIN_DIR}/auth.json"' EXIT
fi

# The plugin dir is a bind mount shared by both instances, so vendor/ only
# needs installing once.
wp-env run cli --env-cwd=wp-content/plugins/ltm-core composer install -n

for CONTAINER in cli tests-cli; do
	echo "[ltm-core] Configuring '${CONTAINER}' container..."

	wp-env run "${CONTAINER}" wp --allow-root plugin activate \
		ltm-core advanced-custom-fields-pro

	wp-env run "${CONTAINER}" wp --allow-root theme activate latitudemedia

	wp-env run "${CONTAINER}" wp --allow-root rewrite structure '/%postname%/' --hard
	wp-env run "${CONTAINER}" wp --allow-root rewrite flush --hard

	wp-env run "${CONTAINER}" wp --allow-root post-type list --field=name | grep -q 'thematic-pages' \
		|| { echo "[ltm-core] ERROR: thematic-pages post type is not registered in '${CONTAINER}'."; exit 1; }

	echo "[ltm-core] '${CONTAINER}' ready."
done
