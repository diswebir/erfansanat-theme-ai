#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
export ERFANSANAT_TOOL_CACHE="${ERFANSANAT_TOOL_CACHE:-$HOME/.cache/erfansanat-tools}"
CACHE="$ERFANSANAT_TOOL_CACHE"
SYSTEM_NPM="$(command -v npm)"
mkdir -p "$CACHE"
cp "$ROOT/tools/package.json" "$ROOT/tools/package-lock.json" "$CACHE/"
if [[ ! -x "$CACHE/node_modules/node/bin/node" ]]; then
  npm install --prefix "$CACHE" --no-audit --no-fund node@24.18.0 npm@11.16.0
fi
export PATH="$CACHE/node_modules/node/bin:$PATH"
# Use the bootstrap npm so native optional package install scripts still run.
# The pinned Node 24 runtime, not the system Node 22, executes project tools.
"$SYSTEM_NPM" ci --prefix "$CACHE" --no-audit --no-fund
python3 - "$CACHE" <<'PY'
from pathlib import Path
import hashlib,io,json,sys,tarfile,urllib.request
cache=Path(sys.argv[1])
def download(url,raw=False):
    headers={'Accept':'application/vnd.github.raw+json' if raw else 'application/vnd.github+json','User-Agent':'Erfansanat-Discovery'}
    with urllib.request.urlopen(urllib.request.Request(url,headers=headers),timeout=90) as r:return r.read()
def extract(repo,revision,name):
    target=cache/name
    if name=='phpcs' and target.exists():
        config=target/'src/Config.php'
        if not config.exists() or "const VERSION = '3.13.5';" not in config.read_text():
            import shutil
            shutil.rmtree(target)
    if target.exists():return
    data=download(f'https://codeload.github.com/{repo}/tar.gz/{revision}')
    with tarfile.open(fileobj=io.BytesIO(data),mode='r:gz') as z:
        members=z.getmembers();root=members[0].name.split('/')[0];prefix=root+'/'
        for m in members:
            if m.name==root:continue
            if not m.name.startswith(prefix) or '..' in Path(m.name).parts or not (m.isfile() or m.isdir()):raise RuntimeError('Unsafe archive member')
            m.name=str(target)+'/'+m.name[len(prefix):]
        z.extractall(cache)
wp=cache/'wp-cli.phar'
if not wp.exists():
    data=download('https://api.github.com/repos/wp-cli/builds/git/blobs/4432e71d61f7425ed028b23e58b44ad5ecb0f5bf',raw=True)
    sha=hashlib.sha1(b'blob '+str(len(data)).encode()+b'\0'+data).hexdigest()
    if sha!='4432e71d61f7425ed028b23e58b44ad5ecb0f5bf':raise RuntimeError('WP-CLI checksum mismatch')
    wp.write_bytes(data)
for repo,sha,name in [
    ('PHPCSStandards/PHP_CodeSniffer','refs/tags/3.13.5','phpcs'),
    ('WordPress/WordPress-Coding-Standards','ec2ff942335f33683a5957a85d138753876a05cf','wpcs'),
    ('PHPCSStandards/PHPCSUtils','5f35d9408c54d7b529501f3c688b6eae562aea1f','phpcsutils'),
    ('PHPCSStandards/PHPCSExtra','39467533fdb742446d68c1d10ac33d625ee0311c','phpcsextra')]:extract(repo,sha,name)
PY
python3 "$ROOT/tools/prepare-phpunit.py"
"$ROOT/tools/phpcs" --config-set installed_paths "$CACHE/wpcs,$CACHE/phpcsutils,$CACHE/phpcsextra"
chmod +x "$ROOT/tools/php" "$ROOT/tools/wp" "$ROOT/tools/phpunit" "$ROOT/tools/phpcs" "$ROOT/tools/xmllint"
"$ROOT/tools/php" -v
"$ROOT/tools/wp" --version --allow-root
"$ROOT/tools/phpunit" --version
"$ROOT/tools/phpcs" -i
printf '%s\n' 'Tools ready. PHP runs in WebAssembly; native staging validation is still required.'
