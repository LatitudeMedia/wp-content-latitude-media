#!/usr/bin/env bash
#
# wp-env afterStart lifecycle script for ltm-core.
#
# Runs against BOTH the development (8888) and tests (8889) instances every
# time `wp-env start` completes. Everything here must be idempotent.
#
# Why this exists:
#   1. The latitudemedia theme is mapped in by .wp-env.json but not activated.
#      src/featured-post-block/render.php calls get_template_part(), so
#      without the theme active that block renders an empty wrapper and
#      frontend specs fail for the wrong reason.
#   2. wp-env defaults to plain permalinks. The thematic-pages CPT rewrites
#      to /themes/{slug}, which 404s until a pretty permalink structure is
#      set and the rewrite rules are flushed. ltm_core_activate() only
#      flushes on the activation hook, which has already run by now.
#   3. wp-env activates mapped plugins when it *creates* an instance, but that
#      does not reliably survive a `wp-env destroy` followed by `start` — the
#      containers can come back with ltm-core and ACF inactive, and every spec
#      then fails with "Invalid post type." Activating explicitly here makes
#      provisioning idempotent instead of depending on creation-time state.

set -euo pipefail

# Container names, not environment names: the development instance is served
# by `cli`, the tests instance (port 8889) by `tests-cli`.
for CONTAINER in cli tests-cli; do
	echo "[ltm-core] Configuring '${CONTAINER}' container..."

	# Explicit and idempotent: `wp plugin activate` on an already-active plugin
	# is a no-op, so this is safe to repeat on every start.
	wp-env run "${CONTAINER}" wp plugin activate ltm-core advanced-custom-fields-pro

	wp-env run "${CONTAINER}" wp theme activate latitudemedia

	# The thematic-pages CPT's /themes/{slug} URLs require pretty permalinks.
	wp-env run "${CONTAINER}" wp rewrite structure '/%postname%/' --hard
	wp-env run "${CONTAINER}" wp rewrite flush --hard

	# Fail loudly here rather than inside an opaque Playwright timeout.
	wp-env run "${CONTAINER}" wp post-type list --field=name | grep -q 'thematic-pages' \
		|| { echo "[ltm-core] ERROR: thematic-pages post type is not registered in '${CONTAINER}'."; exit 1; }

	echo "[ltm-core] '${CONTAINER}' ready."
done

