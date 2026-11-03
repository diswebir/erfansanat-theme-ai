#!/usr/bin/env bash
# ============================================================
# ErfanSanat Theme AI — QA Harness Setup (reproducible)
# Installs: PHP 8.4 (WASM via @php-wasm/node), WP-CLI 2.12,
#           WordPress 7.1.4 + SQLite drop-in (temp/wp)
# Network: only github.com / api.github.com / codeload.github.com /
#          registry.npmjs.org are reachable from the sandbox.
# Usage:   bash temp/tools/setup-harness.sh
# ============================================================
set -euo pipefail
TOOLS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$TOOLS_DIR/../.." && pwd)"
cd "$TOOLS_DIR"

echo "==> [1/4] PHP 8.4 WASM runtime (@php-wasm/node)"
if [ ! -d node_modules/@php-wasm/node ]; then
  [ -f package.json ] || npm init -y >/dev/null
  npm install @php-wasm/node@3.1.62 --no-audit --no-fund
fi

echo "==> [2/4] WP-CLI phar (via GitHub blob API)"
mkdir -p bin
if [ ! -f bin/wp-cli.phar ]; then
  python3 - <<'EOF'
import json, urllib.request, base64
def get(url):
    req = urllib.request.Request(url, headers={'User-Agent':'arena-agent','Accept':'application/vnd.github+json'})
    return json.load(urllib.request.urlopen(req, timeout=60))
meta = get('https://api.github.com/repos/wp-cli/builds/contents/phar/wp-cli.phar')
blob = get(meta['git_url'])
open('bin/wp-cli.phar','wb').write(base64.b64decode(blob['content']))
print('wp-cli.phar saved')
EOF
fi

echo "==> [3/4] WordPress 7.1.4 core + SQLite drop-in"
if [ ! -f "$ROOT_DIR/temp/wp/wp-settings.php" ]; then
  mkdir -p "$ROOT_DIR/temp/wp"
  curl -sL --max-time 240 -o /tmp/wp-core.tar.gz \
    https://codeload.github.com/WordPress/WordPress/tar.gz/refs/tags/7.1.4
  tar -xzf /tmp/wp-core.tar.gz -C "$ROOT_DIR/temp/wp" --strip-components=1
  curl -sL --max-time 60 -o /tmp/sqlite-db.tar.gz \
    https://codeload.github.com/aaemnnosttv/wp-sqlite-db/tar.gz/refs/heads/master
  mkdir -p /tmp/sqlite-plugin
  tar -xzf /tmp/sqlite-db.tar.gz -C /tmp/sqlite-plugin --strip-components=1
  cp /tmp/sqlite-plugin/src/db.php "$ROOT_DIR/temp/wp/wp-content/db.php"
  mkdir -p "$ROOT_DIR/temp/wp/wp-content/uploads"
fi
# wp-config.php template is committed in temp/tools (temp/wp itself is gitignored)
if [ ! -f "$ROOT_DIR/temp/wp/wp-config.php" ]; then
  cp "$TOOLS_DIR/wp-config.php" "$ROOT_DIR/temp/wp/wp-config.php"
fi
# mu-plugin: register repo root as a theme directory (discovers erfansanat/ theme)
mkdir -p "$ROOT_DIR/temp/wp/wp-content/mu-plugins"
cp "$TOOLS_DIR/es-harness-mu-plugin.php" "$ROOT_DIR/temp/wp/wp-content/mu-plugins/es-harness.php"

echo "==> [4/4] Install WordPress (SQLite) if not installed"
export PATH="$TOOLS_DIR:$PATH"
if ! php bin/wp-cli.phar --path="$ROOT_DIR/temp/wp" --allow-root core is-installed 2>/dev/null; then
  php bin/wp-cli.phar --path="$ROOT_DIR/temp/wp" --allow-root core install \
    --url=http://localhost:8090 --title="ErfanSanat QA" \
    --admin_user=admin --admin_password=admin --admin_email=qa@example.com --skip-email
fi

php -v
php bin/wp-cli.phar --path="$ROOT_DIR/temp/wp" --allow-root core version
echo "==> Harness ready. Use: export PATH=\"$TOOLS_DIR:\$PATH\"  then  php / wp"
