#!/bin/sh
# HTTP smoke test for every migrated asset; requires the disposable WP test server.
set -eu
ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)"
BASE_URL="${WP_TEST_BASE_URL:-http://127.0.0.1:8123/wp-content/themes/erfansanat-theme}"
count=0
while IFS= read -r file; do
  relative=${file#"$ROOT/"}
  code=$(curl -sS --max-time 30 -o /dev/null -w '%{http_code}' "$BASE_URL/$relative")
  if [ "$code" != 200 ]; then
    printf 'FAIL HTTP %s: %s\n' "$code" "$relative" >&2
    exit 1
  fi
  count=$((count + 1))
done <<EOF
$(find "$ROOT/assets" -type f | sort)
EOF
printf 'PASS HTTP 200 for %s migrated assets at %s\n' "$count" "$BASE_URL"
