#!/usr/bin/env bash
# Serve the isolated local WordPress site with the asset-safe router.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
PORT="${PORT:-8080}"
exec tools/php -S "0.0.0.0:${PORT}" -t .local/wordpress tools/local/router.php
