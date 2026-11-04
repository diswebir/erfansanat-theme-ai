#!/bin/sh
# Reproducible full regression + DOM and headless-browser front-end E2E audit.
set -eu
ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
PHP_BIN="${PHP_BIN:-$ROOT/node_modules/.bin/php-wasm-cli}"
NODE_BIN="${NODE_BIN:-node}"
WP_TEST_DIR="${WP_TEST_DIR:-$ROOT/node_modules/.cache/ese-wp-test}"
OUTPUT="$(mktemp)"
LINT_OUTPUT="$(mktemp)"
trap 'rm -f "$OUTPUT" "$LINT_OUTPUT"' EXIT HUP INT TERM

command -v "$NODE_BIN" >/dev/null 2>&1 || { echo "Node.js is required for the DOM and browser E2E tests." >&2; exit 2; }
if [ ! -x "$PHP_BIN" ]; then
  echo "PHP WASM is missing; installing locked test dependencies with npm ci."
  (cd "$ROOT" && npm ci --ignore-scripts)
  PHP_BIN="$ROOT/node_modules/.bin/php-wasm-cli"
fi
if [ ! -x "$PHP_BIN" ]; then
  echo "PHP WASM CLI is unavailable at $PHP_BIN" >&2
  exit 2
fi
for package in jsdom puppeteer-core @sparticuz/chromium; do
  if [ ! -f "$ROOT/node_modules/$package/package.json" ]; then
    echo "$package is missing; install the locked dependencies with npm ci." >&2
    exit 2
  fi
done

mkdir -p "$WP_TEST_DIR"
: > "$WP_TEST_DIR/wp-debug.log"
echo "=== Provisioning disposable WordPress with WP_DEBUG=true ==="
PHP_WASM_DIR="$ROOT" WP_TEST_DIR="$WP_TEST_DIR" sh "$ROOT/temp/setup-wordpress-test.sh"

for phase in \
  phase-01-core-and-assets \
  phase-02-custom-admin-dashboard \
  phase-03-cpt-and-legacy-meta \
  phase-04-header-footer-navigation \
  phase-05-homepage-and-sections \
  phase-06-woocommerce-and-products \
  phase-07-projects-blog-and-pages \
  phase-08-ajax-filters-and-audit
do
  echo "=== ${phase} ==="
  if ! PHP_BIN="$PHP_BIN" WP_TEST_DIR="$WP_TEST_DIR" sh "$ROOT/temp/$phase/tests/run-phase-tests.sh" >"$OUTPUT" 2>&1; then
    cat "$OUTPUT"
    exit 1
  fi
  cat "$OUTPUT"
  if grep -Eiq '^(FAIL|.*critical error|.*fatal error|.*parse error)' "$OUTPUT"; then
    echo "${phase} printed a failure marker." >&2
    exit 1
  fi
done

echo "=== DOM UI E2E ==="
"$NODE_BIN" "$ROOT/temp/phase-08-ajax-filters-and-audit/tests/e2e-ui.test.js"
echo "=== Headless Chromium responsive E2E ==="
"$NODE_BIN" "$ROOT/temp/phase-08-ajax-filters-and-audit/tests/browser-e2e.test.js"

# Lint the whole theme/test PHP surface with the available PHP WASM runtime.
echo "=== PHP syntax audit ==="
find "$ROOT" -type f -name '*.php' \
  -not -path "$ROOT/node_modules/*" \
  -not -path "$ROOT/static-mirror/*" \
  -not -path "$ROOT/.git/*" -print | while IFS= read -r file; do
    if ! "$PHP_BIN" -l "$file" >>"$LINT_OUTPUT" 2>&1; then
      cat "$LINT_OUTPUT"
      echo "PHP lint failed: $file" >&2
      exit 1
    fi
  done
cat "$LINT_OUTPUT"
if grep -Eiq 'parse error|fatal error|errors parsing' "$LINT_OUTPUT"; then
  echo "PHP lint output contains a syntax/runtime failure marker." >&2
  exit 1
fi

"$NODE_BIN" --check "$ROOT/assets/js/main.js"
"$NODE_BIN" --check "$ROOT/temp/phase-08-ajax-filters-and-audit/tests/e2e-ui.test.js"
"$NODE_BIN" --check "$ROOT/temp/phase-08-ajax-filters-and-audit/tests/browser-e2e.test.js"
find "$ROOT/temp" -type f -name '*.sh' -print | while IFS= read -r file; do sh -n "$file"; done
git -C "$ROOT" diff --check
git -C "$ROOT" diff --cached --check
git -C "$ROOT" ls-files --others --exclude-standard | while IFS= read -r file; do
  if grep -nE '[[:blank:]]+$' "$ROOT/$file"; then
    echo "Untracked file contains trailing whitespace: $file" >&2
    exit 1
  fi
done

grep -Fq "define('WP_DEBUG', true)" "$ROOT/temp/setup-wordpress-test.sh" || { echo "WP_DEBUG=true is not configured in the harness." >&2; exit 1; }
if [ -s "$WP_TEST_DIR/wp-debug.log" ]; then
  cat "$WP_TEST_DIR/wp-debug.log" >&2
  echo "WP_DEBUG log is not empty." >&2
  exit 1
fi

echo "PASS all eight phase runners, DOM and headless Chromium E2E, PHP/JS/shell syntax, diff checks, and empty WP_DEBUG log."
