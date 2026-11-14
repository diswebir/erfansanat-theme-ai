#!/usr/bin/env bash
# ============================================================
# P08-T025 — repeatable proof of SC-02: the release zip installs,
# activates and renders on a clean WordPress (empty SQLite DB).
# Recreates temp/wp-clean from the harness tree, wipes the DB,
# installs WP, unzips temp/dist/erfansanat.zip, activates the
# theme and asserts the homepage template renders. ~20s.
# Usage: bash temp/tools/verify-clean-install.sh
# ============================================================
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
ZIP="$ROOT/temp/dist/erfansanat.zip"
WP="$ROOT/temp/wp-clean"
# Resolve the WASM PHP wrapper: PATH first, fall back to temp/tools/php.
if command -v php >/dev/null 2>&1; then
	PHPC="$(command -v php)"
else
	PHPC="$ROOT/temp/tools/php"
fi
CLI="$ROOT/temp/tools/bin/wp-cli.phar"

[ -f "$ZIP" ] || { echo "clean-install: temp/dist/erfansanat.zip missing — run temp/tools/package-theme.sh first" >&2; exit 2; }
[ -d "$ROOT/temp/wp" ] || { echo "clean-install: temp/wp harness missing — run temp/tools/setup-harness.sh first" >&2; exit 2; }

echo "==> کپی تازه از هارنس + دیتابیس خالی"
rm -rf "$WP"
cp -a "$ROOT/temp/wp" "$WP"
# Dotfile trap: wipe the whole database dir (rm database/* keeps .ht.sqlite).
rm -rf "$WP/wp-content/database" "$WP/wp-content/mu-plugins" "$WP/wp-content/upgrade" "$WP/wp-content/themes/erfansanat"
mkdir -p "$WP/wp-content/database"

wp() { "$PHPC" "$CLI" --path="$WP" --allow-root "$@"; }

echo "==> نصب WordPress روی DB خالی"
wp core install --url=http://localhost:8090 --title="Clean Install Test" --admin_user=admin --admin_password=admin --admin_email=clean@example.test --skip-email >/dev/null

echo "==> بازکردن زیپ انتشار در wp-content/themes (بدون شیم هارنس)"
unzip -qo "$ZIP" -d "$WP/wp-content/themes/"

echo "==> فعال‌سازی تم + پیوندهای یکتا"
wp theme activate erfansanat >/dev/null
# `wp rewrite structure` spawns a nested wp process (proc_open) — unsupported
# by php-wasm. Set the option directly and flush in-process instead.
wp option update permalink_structure '/%postname%/' >/dev/null
wp eval 'flush_rewrite_rules();' >/dev/null

echo "==> اعتبارسنجی هدر تم و رندر صفحه خانه"
cat > "$WP/es-verify-front.php" <<'PHP'
<?php
// Front-page render on the clean install (same emulation as P08-T023).
$es_saved = $GLOBALS['wp_query'];
$es_pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1, 'orderby' => 'ID', 'order' => 'ASC' ) );
$GLOBALS['post']    = $es_pages ? $es_pages[0] : null;
$GLOBALS['wp_query'] = new WP_Query( $es_pages ? array( 'page_id' => $es_pages[0]->ID ) : array( 'post_type' => 'page' ) );
ob_start();
include get_theme_file_path( 'front-page.php' );
$es_html = (string) ob_get_clean();
$GLOBALS['wp_query'] = $es_saved;
echo 'front_len=', strlen( $es_html ), '|front_ok=', strlen( $es_html ) > 3000 ? '1' : '0', "\n";
PHP

VERIFY="$(wp eval 'echo "theme=", wp_get_theme()->get("Name"), "|ver=", wp_get_theme()->get("Version"), "|front=", locate_template("front-page.php") ? "found" : "missing", "|style=", file_exists( get_stylesheet_directory() . "/style.css" ) ? "present" : "absent", "\n";')"
FRONT="$(wp eval-file "$WP/es-verify-front.php")"
rm -rf "$WP"

echo "$VERIFY"
echo "$FRONT"
if echo "$VERIFY" | grep -q 'theme=Erfan Sanat|ver=0.1.0|front=found|style=present' \
	&& echo "$FRONT" | grep -q 'front_ok=1'; then
	echo "CLEAN INSTALL OK: نصب تمیز از زیپ انتشار موفق — SC-02 اثبات شد"
	exit 0
fi
echo "clean-install: verification FAILED" >&2
exit 3
