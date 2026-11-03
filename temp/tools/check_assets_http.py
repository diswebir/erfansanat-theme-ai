#!/usr/bin/env python3
"""Serve erfansanat/ locally and verify every asset returns HTTP 200.
Writes temp/asset-http-report.json for the PHP acceptance tests.
"""
from __future__ import annotations
import functools, http.server, json, pathlib, socketserver, sys, threading, urllib.request

ROOT = pathlib.Path(__file__).resolve().parents[2]
THEME = ROOT / 'erfansanat'
REPORT = ROOT / 'temp' / 'asset-http-report.json'

class Quiet(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=str(THEME), **kwargs)

    def log_message(self, *args):
        pass

with socketserver.ThreadingTCPServer(('127.0.0.1', 0), Quiet) as httpd:
    port = httpd.server_address[1]
    thread = threading.Thread(target=httpd.serve_forever, daemon=True)
    thread.start()
    results = []
    ok = True
    for path in sorted((THEME / 'assets').rglob('*')):
        if not path.is_file():
            continue
        rel = path.relative_to(THEME).as_posix()
        url = f'http://127.0.0.1:{port}/{rel}'
        try:
            with urllib.request.urlopen(url, timeout=10) as resp:
                data = resp.read()
                status = resp.status
                size = len(data)
                match = size == path.stat().st_size
        except Exception as exc:
            status = 0
            size = 0
            match = False
        good = status == 200 and match
        ok = ok and good
        results.append({'path': rel, 'url': url, 'status': status, 'bytes': size, 'size_match': match, 'ok': good})
    httpd.shutdown()

REPORT.write_text(json.dumps({'ok': ok, 'count': len(results), 'results': results}, ensure_ascii=False, indent=2), encoding='utf-8')
print(f'Asset HTTP check: {sum(r["ok"] for r in results)}/{len(results)} OK -> {REPORT}')
sys.exit(0 if ok else 1)
