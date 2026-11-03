#!/usr/bin/env python3
"""Install pinned PHPUnit sources and a Composer-compatible development autoloader.

Downloads only from GitHub's allowlisted API/codeload hosts. This is tooling,
not a theme dependency. The resulting vendor tree lives in the tool cache.
"""
from pathlib import Path
import concurrent.futures
import io
import json
import os
import tarfile
import urllib.request

CACHE = Path(os.environ.get("ERFANSANAT_TOOL_CACHE", str(Path.home() / ".cache/erfansanat-tools")))
VENDOR = CACHE / "phpunit-vendor"
VENDOR.mkdir(parents=True, exist_ok=True)


def get(url):
    request = urllib.request.Request(url, headers={"Accept": "application/vnd.github.raw+json", "User-Agent": "Erfansanat-Discovery"})
    with urllib.request.urlopen(request, timeout=90) as response:
        return response.read()


def extract(data, target):
    if target.exists():
        return
    with tarfile.open(fileobj=io.BytesIO(data), mode="r:gz") as archive:
        members = archive.getmembers()
        root = members[0].name.split("/")[0]
        prefix = root + "/"
        for member in members:
            if member.name == root:
                continue
            if not member.name.startswith(prefix) or ".." in Path(member.name).parts or not (member.isfile() or member.isdir()):
                raise RuntimeError("Unsafe archive member: " + member.name)
            member.name = str(target) + "/" + member.name[len(prefix):]
        archive.extractall(CACHE)


lock = json.loads(get("https://api.github.com/repos/sebastianbergmann/phpunit/contents/composer.lock?ref=12.4.2"))
packages = [("phpunit/phpunit", "12.4.2", "sebastianbergmann/phpunit", "12.4.2")]
packages += [(p["name"], p["version"], p["source"]["url"].removeprefix("https://github.com/").removesuffix(".git"), p["source"]["reference"]) for p in lock["packages"]]


def install(package):
    name, version, repo, reference = package
    target = VENDOR / name
    if not (target / "composer.json").exists():
        extract(get(f"https://codeload.github.com/{repo}/tar.gz/{reference}"), target)
    metadata = json.loads((target / "composer.json").read_text())
    return [name, version, metadata.get("autoload", {})]


with concurrent.futures.ThreadPoolExecutor(max_workers=6) as pool:
    results = list(pool.map(install, packages))
(VENDOR / "package-map.json").write_text(json.dumps(results, indent=2))
composer = VENDOR / "composer"
composer.mkdir(exist_ok=True)
(composer / "InstalledVersions.php").write_bytes(get("https://api.github.com/repos/composer/composer/contents/src/Composer/InstalledVersions.php?ref=2.9.5"))
versions = {
    name: {"pretty_version": version, "version": version.lstrip("v") + ".0", "reference": None,
           "type": "library", "install_path": str(VENDOR / name), "aliases": [], "dev_requirement": False}
    for name, version, _ in results
}
installed = {
    "root": {"name": "erfansanat/phpunit-tools", "pretty_version": "1.0.0", "version": "1.0.0.0",
             "reference": None, "type": "project", "install_path": str(VENDOR), "aliases": [], "dev": False},
    "versions": versions,
}


def php(value):
    if value is None:
        return "null"
    if isinstance(value, bool):
        return "true" if value else "false"
    if isinstance(value, dict):
        return "[" + ", ".join(php(k) + " => " + php(v) for k, v in value.items()) + "]"
    if isinstance(value, list):
        return "[" + ", ".join(php(v) for v in value) + "]"
    return "'" + str(value).replace("\\", "\\\\").replace("'", "\\'") + "'"


(composer / "installed.php").write_text("<?php return " + php(installed) + ";\n")
loader = r'''<?php
// Development-only autoloader generated from the packages' Composer metadata.
$root = __DIR__;
$packages = json_decode(file_get_contents($root . '/package-map.json'), true);
$psr4 = [];
$classmap = [];
$files = [];
foreach ($packages as [$name, $version, $autoload]) {
    $base = $root . '/' . $name;
    foreach ($autoload['psr-4'] ?? [] as $prefix => $paths) {
        foreach ((array) $paths as $path) $psr4[] = [$prefix, $base . '/' . $path];
    }
    foreach ($autoload['classmap'] ?? [] as $path) {
        $full = $base . '/' . $path;
        if (is_file($full)) { $classmap[] = $full; continue; }
        if (!is_dir($full)) continue;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($full));
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') continue;
            $source = file_get_contents($file->getPathname());
            $namespace = '';
            if (preg_match('/\bnamespace\s+([^;{\s]+)/', $source, $match)) $namespace = $match[1] . '\\';
            if (preg_match_all('/\b(?:class|interface|trait|enum)\s+(\w+)/', $source, $matches)) {
                foreach ($matches[1] as $class) $classmap[$namespace . $class] = $file->getPathname();
            }
        }
    }
    foreach ($autoload['files'] ?? [] as $path) $files[] = $base . '/' . $path;
}
spl_autoload_register(function ($class) use ($psr4, $classmap) {
    if (isset($classmap[$class])) { require_once $classmap[$class]; return; }
    foreach ($psr4 as [$prefix, $path]) {
        if (str_starts_with($class, $prefix)) {
            $file = rtrim($path, '/') . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) { require_once $file; return; }
        }
    }
});
require_once __DIR__ . '/composer/InstalledVersions.php';
foreach ($files as $file) require_once $file;
'''
(VENDOR / "autoload.php").write_text(loader)
print(f"Prepared PHPUnit 12.4.2 with {len(results)} pinned source packages.")
