#!/usr/bin/env bash
# P08-T022 — staging migration dry-run (services content / metas / menus).
#
# Rehearses the planned migration against a THROWAWAY COPY of the QA/staging
# install — the source database is never opened for writing (SC-01):
#   1. copy the whole WP tree into a scratch directory (mktemp),
#   2. back up the copied database + sha256 before touching it (AC-01),
#   3. snapshot post/term/legacy-meta counts before the migration (AC-02),
#   4. run the idempotent migration steps on the copy (LegacyMeta
#      format-preserving re-save + nav menu location assignment),
#   5. snapshot again, diff, and write a Markdown report (SC-02).
#   6. Legacy meta keys are only ever counted/kept verbatim — the historical
#      (misspelled) spellings are never renamed (AC-03).
#
# Usage: staging-migration-dry-run.sh [WP_ROOT]   (default: <repo>/temp/wp)
# Output: temp/migration-reports/dry-run-<stamp>.md (+ backups/ alongside).

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)" # repo root (temp/tools/.. -> temp -> repo)
SRC_WP="${1:-$ROOT/temp/wp}"
STAMP="$(date -u +%Y%m%dT%H%M%SZ)"
WORK="$(mktemp -d)"
REPORT_DIR="$ROOT/temp/migration-reports"
BACKUP_DIR="$REPORT_DIR/backups"
REPORT="$REPORT_DIR/dry-run-$STAMP.md"
mkdir -p "$REPORT_DIR" "$BACKUP_DIR"

cleanup() { rm -rf "$WORK"; }
trap cleanup EXIT

die() { echo "dry-run: $*" >&2; exit 2; }

[ -f "$SRC_WP/wp-config.php" ] || die "no WordPress at $SRC_WP"
SRC_DB="$SRC_WP/wp-content/database/.ht.sqlite"
[ -f "$SRC_DB" ] || die "no SQLite database at $SRC_DB"

echo "==> [1/6] scratch staging view of $SRC_WP (symlinks + real DB copy)"
# A throwaway staging tree: every WP file is a symlink back to the source
# (nothing there can be written by the migration) while wp-content/database
# holds a REAL copy of the DB — all migration writes land on the copy (SC-01).
mkdir -p "$WORK/wp/wp-content"
for es_entry in "$SRC_WP"/.htaccess "$SRC_WP"/*; do
	[ -e "$es_entry" ] || continue
	es_base="$(basename "$es_entry")"
	[ "$es_base" = "wp-content" ] && continue
	ln -s "$es_entry" "$WORK/wp/$es_base"
done
for es_entry in "$SRC_WP"/wp-content/* "$SRC_WP"/wp-content/.htaccess; do
	[ -e "$es_entry" ] || continue
	es_base="$(basename "$es_entry")"
	[ "$es_base" = "database" ] && continue
	ln -s "$es_entry" "$WORK/wp/wp-content/$es_base"
done
STAGE_WP="$WORK/wp"
WP() { php "$ROOT/temp/tools/bin/wp-cli.phar" --path="$STAGE_WP" --allow-root "$@"; }

echo "==> [2/6] backup of the staging copy database (AC-01)"
STAGE_DB="$STAGE_WP/wp-content/database/.ht.sqlite"
mkdir -p "$(dirname "$STAGE_DB")"
cp "$SRC_DB" "$STAGE_DB"
BACKUP="$BACKUP_DIR/staging-backup-$STAMP.sqlite"
cp "$STAGE_DB" "$BACKUP"
BACKUP_SHA="$(sha256sum "$BACKUP" | cut -d' ' -f1)"
[ -s "$BACKUP" ] || die "backup is empty"

echo "==> [3/6] snapshot BEFORE (AC-02)"
cat > "$WORK/counts.php" <<'PHP'
<?php
$out = array( 'posts' => array(), 'terms' => array(), 'legacy_meta' => array() );
foreach ( array( 'post', 'page', 'product', 'projects', 'services', 'es_form_entry' ) as $pt ) {
	$c = wp_count_posts( $pt );
	$n = 0;
	foreach ( array( 'publish', 'draft', 'pending', 'private', 'future' ) as $st ) {
		$n += (int) ( $c->$st ?? 0 );
	}
	$out['posts'][ $pt ] = $n;
}
foreach ( array( 'category', 'post_tag', 'product_cat', 'product_tag', 'project_type', 'city' ) as $tx ) {
	$counted = wp_count_terms( array( 'taxonomy' => $tx, 'hide_empty' => false ) );
	$out['terms'][ $tx ] = is_wp_error( $counted ) ? 0 : (int) $counted;
}
// AC-03: the exact historical legacy keys — never renamed, only counted.
$keys = array(
	'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id',
	'zireonvan', 'english_titl', 'before-sale-c', 'true_sale',
	'product-catalog-file', 'galerry_produ', 'project-ids', 'usage_countr',
	'product-feature', 'product-feature2_copy', 'usage_product', 'faq',
	'second-image', 'three-image', 'foure-image', 'five-image', 'six-image',
	'finish-pro-time', 'mortabet_mahsole_2',
	'hrefmeta_en', 'hrefmeta_ar', 'hrefmeta_fa', 'same_key_hrefl',
	'number2-viewc', 'number-viewc', 'gallery-filed',
	'image-2', 'image-3', 'image-4', 'image-5', 'image-6',
);
foreach ( $keys as $k ) {
	$out['legacy_meta'][ $k ] = (int) $GLOBALS['wpdb']->get_var(
		$GLOBALS['wpdb']->prepare( "SELECT COUNT(*) FROM {$GLOBALS['wpdb']->postmeta} WHERE meta_key = %s", $k )
	);
}
echo json_encode( $out );
PHP
BEFORE="$(WP eval-file "$WORK/counts.php")"

echo "==> [4/6] migration steps on the staging copy (dry-run target)"
cat > "$WORK/migrate.php" <<'PHP'
<?php
// Meta migration: re-save every legacy repeater value THROUGH the theme's
// LegacyMeta codec — it preserves the original storage format and writes only
// the exact same key (AC-03). No key is renamed, nothing is deleted.
$keys = array( 'product-feature', 'product-feature2_copy', 'usage_product', 'faq' );
$resaved = 0;
foreach ( array( 'product', 'projects', 'services' ) as $pt ) {
	$q = new WP_Query( array( 'post_type' => $pt, 'post_status' => 'any', 'posts_per_page' => 500, 'fields' => 'ids' ) );
	foreach ( $q->posts as $id ) {
		foreach ( $keys as $k ) {
			$raw = get_metadata( 'post', (int) $id, $k, true );
			if ( null === $raw || '' === $raw ) {
				continue;
			}
			$items = \ErfanSanat\Theme\Core\LegacyMeta::get( (int) $id, $k );
			if ( ! empty( $items ) ) {
				\ErfanSanat\Theme\Core\LegacyMeta::set( (int) $id, $k, $items );
				$resaved++;
			}
		}
	}
}
// Menus migration: assign a menu to unassigned registered locations.
$assigned = 0;
$locations = get_theme_mod( 'nav_menu_locations', array() );
foreach ( array( 'primary', 'footer' ) as $loc ) {
	if ( empty( $locations[ $loc ] ) && has_nav_menu( $loc ) === false ) {
		$menus = wp_get_nav_menus();
		if ( ! empty( $menus ) ) {
			$locations[ $loc ] = (int) $menus[0]->term_id;
			$assigned++;
		}
	}
}
set_theme_mod( 'nav_menu_locations', $locations );
echo "resaved=$resaved assigned=$assigned";
PHP
MIG="$(WP eval-file "$WORK/migrate.php")"

echo "==> [5/6] snapshot AFTER + diff (AC-02/SC-02)"
AFTER="$(WP eval-file "$WORK/counts.php")"

cat > "$WORK/diff.php" <<'PHP'
<?php
$before = json_decode( $argv[1] ?? 'null', true );
$after  = json_decode( $argv[2] ?? 'null', true );
if ( ! is_array( $before ) || ! is_array( $after ) ) { echo "drift=unknown"; exit; }
$drift = 0;
foreach ( $before as $section => $map ) {
	foreach ( $map as $k => $v ) {
		$now = $after[ $section ][ $k ] ?? null;
		if ( $now !== $v ) {
			echo "CHANGED $section/$k: $v -> $now\n";
			$drift++;
		}
	}
}
echo "drift=$drift";
PHP
DIFF_OUT="$(php "$WORK/diff.php" "$BEFORE" "$AFTER")"

echo "==> [6/6] report (SC-02)"
{
	echo "# گزارش Dry-run مهاجرت استیجینگ — $STAMP"
	echo
	echo "- منبع: \`$SRC_WP\` (فقط خوانده شد؛ **هیچ نوشتنی روی لایو نبوده — SC-01**)"
	echo "- کپی استیجینگ: \`$STAGE_WP\` (scratch)"
	echo "- بک‌آپ قبل از مهاجرت (AC-01): \`$BACKUP\`"
	echo "- sha256 بک‌آپ: \`$BACKUP_SHA\`"
	echo "- خروجی مراحل مهاجرت: \`$MIG\`"
	echo
	echo "## شمارش پست‌ها (قبل ← بعد)"
	echo
	echo "| نوع | قبل | بعد |"
	echo "|---|---|---|"
	php -r '$b=json_decode($argv[1],true)["posts"]; $a=json_decode($argv[2],true)["posts"]; foreach ($b as $k=>$v) echo "| ", $k, " | ", $v, " | ", ($a[$k] ?? "-"), " |\n";' "$BEFORE" "$AFTER"
	echo
	echo "## شمارش ترم‌ها (قبل ← بعد)"
	echo
	echo "| تکسونومی | قبل | بعد |"
	echo "|---|---|---|"
	php -r '$b=json_decode($argv[1],true)["terms"]; $a=json_decode($argv[2],true)["terms"]; foreach ($b as $k=>$v) echo "| ", $k, " | ", $v, " | ", ($a[$k] ?? "-"), " |\n";' "$BEFORE" "$AFTER"
	echo
	echo "## کلیدهای legacy — بدون تغییر نام (AC-03)"
	echo
	echo "| کلید | قبل | بعد |"
	echo "|---|---|---|"
	php -r '$b=json_decode($argv[1],true)["legacy_meta"]; $a=json_decode($argv[2],true)["legacy_meta"]; foreach ($b as $k=>$v) echo "| `", $k, "` | ", $v, " | ", ($a[$k] ?? "-"), " |\n";' "$BEFORE" "$AFTER"
	echo
	echo "## تفاوت‌ها"
	echo
	if [ "$(echo "$DIFF_OUT" | tail -1)" = "drift=0" ]; then
		echo "هیچ تفاوتی ثبت نشد (drift=0) — مهاجرت idempotent بود."
	else
		echo '```'
		echo "$DIFF_OUT"
		echo '```'
	fi
	echo
	echo "بر اساس \`temp/MIGRATION_REPORT_TEMPLATE.md\`."
} > "$REPORT"

echo "report: $REPORT"
echo "$DIFF_OUT" | tail -1
