#!/usr/bin/env bash
# Install/reuse the isolated local WordPress test site. Never points at production.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
WP="$ROOT/.local/wordpress"

if [[ ! -x tools/php || ! -x tools/wp ]]; then
  bash tools/setup.sh
fi
python3 tools/local/install.py

CREDS="$ROOT/.local/credentials.json"
ADMIN_USER="$(python3 -c "import json;print(json.load(open('$CREDS'))['admin_user'])")"
ADMIN_PASS="$(python3 -c "import json;print(json.load(open('$CREDS'))['admin_password'])")"
ADMIN_EMAIL="$(python3 -c "import json;print(json.load(open('$CREDS'))['admin_email'])")"

if ! tools/wp --path="$WP" core is-installed --skip-plugins --skip-themes 2>/dev/null; then
  tools/wp --path="$WP" core install \
    --url=http://127.0.0.1:8080 \
    --title='عرفان صنعت | محیط آزمایشی' \
    --admin_user="$ADMIN_USER" \
    --admin_password="$ADMIN_PASS" \
    --admin_email="$ADMIN_EMAIL" \
    --skip-email \
    --locale=fa_IR
fi

tools/wp --path="$WP" plugin activate woocommerce sqlite-database-integration/load.php
tools/wp --path="$WP" theme activate twentytwentyfive
tools/wp --path="$WP" option update WPLANG fa_IR
tools/wp --path="$WP" user update "$ADMIN_USER" --locale=fa_IR
# The Persian WooCommerce catalogue is incomplete on the storefront screens;
# extend it before seeding so the seeded block pages hold Persian markup.
python3 tools/patch-woocommerce-translations.py
tools/wp --path="$WP" eval-file tools/local/seed.php
# Permalink structure and rewrite rules are set by the idempotent seeder;
# the PHP-Wasm router does not use Apache .htaccess files.

printf '%s\n' 'Local WordPress is ready. Start preview with:'
printf '%s\n' '  tools/php -S 0.0.0.0:8080 -t .local/wordpress tools/local/router.php'
printf '%s\n' 'Admin: http://127.0.0.1:8080/wp-admin/ (credentials: .local/credentials.json)'
printf '%s\n' 'Run checks: bash tools/test-local.sh'
