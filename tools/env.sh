#!/usr/bin/env bash
# Source from the wrappers only. All binaries/caches stay outside the theme.
export ERFANSANAT_TOOL_CACHE="${ERFANSANAT_TOOL_CACHE:-$HOME/.cache/erfansanat-tools}"
export PATH="$ERFANSANAT_TOOL_CACHE/node_modules/node/bin:$PATH"
export PHP=8.4
ERFANSANAT_NODE="$ERFANSANAT_TOOL_CACHE/node_modules/node/bin/node"
ERFANSANAT_PHP_CLI="$ERFANSANAT_TOOL_CACHE/node_modules/@php-wasm/cli/php-wasm.js"
if [[ ! -x "$ERFANSANAT_NODE" || ! -f "$ERFANSANAT_PHP_CLI" ]]; then
  printf '%s\n' 'Development tools are missing. Run: bash tools/setup.sh' >&2
  exit 127
fi
