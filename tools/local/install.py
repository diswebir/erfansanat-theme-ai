#!/usr/bin/env python3
"""Install the pinned, isolated WordPress/WooCommerce runtime into .local/.

Only GitHub/codeload/API hosts are used. Generated core, plugins, database and
credentials stay outside Git. The official Woo release ZIP is unavailable behind
a redirect outside the sandbox allowlist, so a pinned distribution mirror is
verified against its Git tree and against every common file in the official tag.
"""
from __future__ import annotations

import base64
import hashlib
import io
import json
import os
import secrets
import shutil
import tarfile
import urllib.request
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
LOCAL = ROOT / ".local"
WP = LOCAL / "wordpress"
CACHE = Path(os.environ.get("ERFANSANAT_RUNTIME_CACHE", Path.home() / ".cache/erfansanat-runtime"))
MANIFEST = json.loads((ROOT / "tools/local/runtime-manifest.json").read_text())
ALLOWED = ("https://github.com/", "https://codeload.github.com/", "https://api.github.com/")


def fetch(url: str) -> bytes:
    if not url.startswith(ALLOWED):
        raise RuntimeError(f"Disallowed URL: {url}")
    req = urllib.request.Request(url, headers={"User-Agent": "Erfansanat-Local-Installer", "Accept": "application/vnd.github+json"})
    with urllib.request.urlopen(req, timeout=180) as response:
        return response.read()


def blob(repo: str, sha: str) -> bytes:
    req = urllib.request.Request(
        f"https://api.github.com/repos/{repo}/git/blobs/{sha}",
        headers={"User-Agent": "Erfansanat-Local-Installer", "Accept": "application/vnd.github.raw+json"},
    )
    with urllib.request.urlopen(req, timeout=180) as response:
        data = response.read()
    digest = hashlib.sha1(b"blob " + str(len(data)).encode() + b"\0" + data).hexdigest()
    if digest != sha:
        raise RuntimeError(f"Git blob mismatch: {repo}/{sha}")
    return data


def tree(repo: str, rev: str) -> list[dict]:
    data = json.loads(fetch(f"https://api.github.com/repos/{repo}/git/trees/{rev}?recursive=1"))
    if data.get("truncated"):
        raise RuntimeError(f"Truncated tree: {repo}@{rev}")
    return data["tree"]


def sha256(data: bytes) -> str:
    return hashlib.sha256(data).hexdigest()


def git_sha(data: bytes) -> str:
    return hashlib.sha1(b"blob " + str(len(data)).encode() + b"\0" + data).hexdigest()


def safe_tar_extract(data: bytes, target: Path, prefix_filter: str | None = None) -> int:
    count = 0
    with tarfile.open(fileobj=io.BytesIO(data), mode="r:gz") as archive:
        members = archive.getmembers()
        root = members[0].name.split("/")[0] + "/"
        for member in members:
            if member.name == root.rstrip("/"):
                continue
            if not member.name.startswith(root):
                raise RuntimeError(f"Unexpected archive root: {member.name}")
            relative = member.name[len(root):]
            if prefix_filter and not relative.startswith(prefix_filter):
                continue
            if not relative or ".." in Path(relative).parts or not (member.isfile() or member.isdir()):
                raise RuntimeError(f"Unsafe archive member: {member.name}")
            destination = target / relative
            if member.isdir():
                destination.mkdir(parents=True, exist_ok=True)
            else:
                destination.parent.mkdir(parents=True, exist_ok=True)
                destination.write_bytes(archive.extractfile(member).read())
                count += 1
    return count


def install_wordpress() -> None:
    spec = MANIFEST["wordpress"]
    if (WP / "wp-includes/version.php").exists() and f"$wp_version = '{spec['version']}'" in (WP / "wp-includes/version.php").read_text():
        print("WordPress core already present")
        return
    archive = CACHE / f"wordpress-{spec['version']}.tar.gz"
    if not archive.exists():
        print("Downloading WordPress", spec["version"])
        archive.write_bytes(fetch(f"https://codeload.github.com/{spec['repo']}/tar.gz/{spec['commit']}"))
    data = archive.read_bytes()
    if sha256(data) != spec["archive_sha256"]:
        raise RuntimeError("WordPress archive checksum mismatch")
    WP.mkdir(parents=True, exist_ok=True)
    print("Extracted WordPress files:", safe_tar_extract(data, WP))


def install_sqlite() -> None:
    spec = MANIFEST["sqlite"]
    plugin = WP / "wp-content/plugins/sqlite-database-integration"
    if (plugin / "load.php").exists() and (plugin / "wp-includes/database/version.php").exists():
        print("SQLite integration already present")
        return
    print("Assembling SQLite integration", spec["version"])
    entries = tree(spec["repo"], spec["commit"])
    plugin_prefix = "packages/plugin-sqlite-database-integration/"
    driver_prefix = "packages/mysql-on-sqlite/src/"
    for entry in entries:
        if entry["type"] != "blob" or entry["mode"] == "120000":
            continue
        path = entry["path"]
        if path.startswith(plugin_prefix):
            destination = plugin / path[len(plugin_prefix):]
        elif path.startswith(driver_prefix):
            destination = plugin / "wp-includes/database" / path[len(driver_prefix):]
        else:
            continue
        destination.parent.mkdir(parents=True, exist_ok=True)
        destination.write_bytes(blob(spec["repo"], entry["sha"]))
    if not (plugin / "wp-includes/sqlite/db.php").exists():
        raise RuntimeError("SQLite legacy drop-in target missing")


def install_woocommerce() -> None:
    spec = MANIFEST["woocommerce"]
    plugin = WP / "wp-content/plugins/woocommerce"
    if (plugin / "woocommerce.php").exists() and f"Version: {spec['version']}" in (plugin / "woocommerce.php").read_text():
        print("WooCommerce already present")
        return
    archive = CACHE / f"woocommerce-{spec['version']}-mirror.tar.gz"
    if not archive.exists():
        print("Downloading pinned WooCommerce distribution mirror")
        archive.write_bytes(fetch(f"https://codeload.github.com/{spec['distribution_mirror_repo']}/tar.gz/{spec['distribution_mirror_commit']}"))
    data = archive.read_bytes()
    if sha256(data) != spec["archive_sha256"]:
        raise RuntimeError("WooCommerce mirror archive checksum mismatch")
    mirror = {x["path"]: x["sha"] for x in tree(spec["distribution_mirror_repo"], spec["distribution_mirror_commit"]) if x["type"] == "blob" and x["path"].startswith("woocommerce/")}
    official = {x["path"][len("plugins/woocommerce/"):]: x["sha"] for x in tree(spec["official_repo"], spec["official_commit"]) if x["type"] == "blob" and x["path"].startswith("plugins/woocommerce/")}
    common = set(mirror) & {f"woocommerce/{k}" for k in official}
    for path in common:
        if mirror[path] != official[path[len("woocommerce/"):]]:
            raise RuntimeError(f"WooCommerce mirror differs from official tag: {path}")
    count = 0
    with tarfile.open(fileobj=io.BytesIO(data), mode="r:gz") as archive_handle:
        root = archive_handle.getmembers()[0].name.split("/")[0] + "/"
        for member in archive_handle:
            relative = member.name[len(root):] if member.name.startswith(root) else ""
            if not relative.startswith("woocommerce/") or member.isdir():
                continue
            if not member.isfile() or ".." in Path(relative).parts:
                raise RuntimeError(f"Unsafe Woo member: {member.name}")
            content = archive_handle.extractfile(member).read()
            if git_sha(content) != mirror.get(relative):
                raise RuntimeError(f"Woo mirror tree mismatch: {relative}")
            destination = WP / "wp-content/plugins" / relative
            destination.parent.mkdir(parents=True, exist_ok=True)
            destination.write_bytes(content)
            count += 1
    print(f"Extracted and verified WooCommerce files: {count}; official-tag common files: {len(common)}")


def install_languages() -> None:
    woo = MANIFEST["woocommerce_fa_ir"]
    dest = WP / "wp-content/languages/plugins"
    dest.mkdir(parents=True, exist_ok=True)
    if not (dest / "woocommerce-fa_IR.mo").exists():
        data = blob(woo["repo"], woo["blob"])
        with zipfile.ZipFile(io.BytesIO(data)) as archive:
            for member in archive.infolist():
                if member.is_dir() or "/" in member.filename or not member.filename.endswith((".mo", ".po", ".l10n.php")):
                    continue
                (dest / member.filename).write_bytes(archive.read(member))
        print("Installed official WooCommerce fa_IR language pack")
    core = MANIFEST["core_fa_ir"]
    core_dest = WP / "wp-content/languages"
    if not (core_dest / "fa_IR.mo").exists():
        for entry in tree(core["repo"], core["commit"]):
            path = entry["path"]
            if entry["type"] == "blob" and path.startswith("repository/") and path.endswith(".mo") and "woocommerce" not in path:
                (core_dest / Path(path).name).write_bytes(blob(core["repo"], entry["sha"]))
        print("Installed Persian core translation mirror (older strings; English fallback)")


def write_config_and_credentials() -> None:
    LOCAL.mkdir(exist_ok=True)
    data = LOCAL / "data"
    data.mkdir(exist_ok=True)
    credentials = LOCAL / "credentials.json"
    if not credentials.exists():
        credentials.write_text(json.dumps({
            "admin_user": "erfansanat_admin",
            "admin_password": secrets.token_urlsafe(18),
            "admin_email": "admin@example.invalid",
            "customer_user": "test_customer",
            "customer_password": secrets.token_urlsafe(15),
        }, indent=2))
        credentials.chmod(0o600)
    config = WP / "wp-config.php"
    if not config.exists():
        salts = "\n".join(f"define('{key}', '{secrets.token_urlsafe(48)}');" for key in [
            "AUTH_KEY", "SECURE_AUTH_KEY", "LOGGED_IN_KEY", "NONCE_KEY",
            "AUTH_SALT", "SECURE_AUTH_SALT", "LOGGED_IN_SALT", "NONCE_SALT",
        ])
        config.write_text(f"""<?php
// Local-only configuration. Never deploy this file or its SQLite database to production.
define('DB_NAME', 'erfansanat_local');
define('DB_USER', 'local');
define('DB_PASSWORD', 'local');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
define('DB_ENGINE', 'sqlite');
define('DB_PATH', '{data}/database.sqlite');
{salts}
$table_prefix = 'es_';
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', '{data}/debug.log');
define('WP_DEBUG_DISPLAY', false);
define('WP_ENVIRONMENT_TYPE', 'local');
define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);
define('DISABLE_WP_CRON', true);
define('WP_HTTP_BLOCK_EXTERNAL', true);
define('FS_METHOD', 'direct');
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '1024M');
$es_host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8080';
if (!preg_match('/^(?:localhost|127\\.0\\.0\\.1)(?::\\d+)?$|^[a-z0-9-]+\\.e2b\\.app$/i', $es_host)) {{
    $es_host = '127.0.0.1:8080';
}}
$es_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
if ($es_https) {{ $_SERVER['HTTPS'] = 'on'; }}
$es_url = ($es_https ? 'https://' : 'http://') . $es_host;
if (defined('WP_CLI') && WP_CLI) {{ $es_url = getenv('ERFANSANAT_LOCAL_URL') ?: 'http://127.0.0.1:8080'; }}
define('WP_HOME', $es_url);
define('WP_SITEURL', $es_url);
if (!defined('ABSPATH')) {{ define('ABSPATH', __DIR__ . '/'); }}
require_once ABSPATH . 'wp-settings.php';
""")
    plugin = WP / "wp-content/plugins/sqlite-database-integration"
    dropin = WP / "wp-content/db.php"
    if not dropin.exists():
        dropin.write_text((plugin / "db.copy").read_text()
                          .replace("{SQLITE_IMPLEMENTATION_FOLDER_PATH}", str(plugin))
                          .replace("{SQLITE_PLUGIN}", "sqlite-database-integration/load.php"))
    mu = WP / "wp-content/mu-plugins"
    mu.mkdir(exist_ok=True)
    shutil.copy2(ROOT / "tools/local/fixture-types.php", mu / "erfansanat-local-fixture-types.php")


def main() -> None:
    CACHE.mkdir(parents=True, exist_ok=True)
    install_wordpress()
    install_sqlite()
    install_woocommerce()
    install_languages()
    write_config_and_credentials()
    print("Local runtime files ready in", WP)


if __name__ == "__main__":
    main()
