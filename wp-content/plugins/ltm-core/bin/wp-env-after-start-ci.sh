#!/usr/bin/env bash
#
# wp-env afterStart lifecycle script for CI only.
#
# Same provisioning as bin/wp-env-after-start.sh, minus ACF Pro: it's a
# licensed, Pressable-managed plugin that isn't committed to git (see the
# plugin's .gitignore), so it isn't present on a fresh CI checkout and can't
# be mapped into wp-env there. Wired up via a CI-only .wp-env.override.json
# (see .github/workflows/ltm-core-tests.yml) rather than editing
# .wp-env.json, which stays accurate for local dev where ACF Pro is present.
#
# Keep this in sync with bin/wp-env-after-start.sh for everything except the
# ACF Pro activation.

set -euo pipefail

for CONTAINER in cli tests-cli; do
	echo "[ltm-core] Configuring '${CONTAINER}' container..."

	wp-env run "${CONTAINER}" wp --allow-root plugin activate ltm-core

	wp-env run "${CONTAINER}" wp --allow-root theme activate latitudemedia

	wp-env run "${CONTAINER}" wp --allow-root rewrite structure '/%postname%/' --hard
	wp-env run "${CONTAINER}" wp --allow-root rewrite flush --hard

	wp-env run "${CONTAINER}" wp --allow-root post-type list --field=name | grep -q 'thematic-pages' \
		|| { echo "[ltm-core] ERROR: thematic-pages post type is not registered in '${CONTAINER}'."; exit 1; }

	echo "[ltm-core] '${CONTAINER}' ready."
done
