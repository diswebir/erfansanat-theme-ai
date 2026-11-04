// Performance budget report for the phase gate.
// usage: node tools/harness/perf.mjs "/" "/contact/" ...
// writes tools/harness/perf-report.json and prints a table.
import fs from 'node:fs';
import path from 'node:path';
import { fetchUrl } from './fetch.mjs';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '../..');
const urls = process.argv.slice(2);
if (!urls.length) urls.push('/');

const rows = [];
for (const url of urls) {
  const d = await fetchUrl(url);
  const html = d.html;
  const css = [...html.matchAll(/<link[^>]+rel=['"]stylesheet['"][^>]*>/gi)].length;
  const js = [...html.matchAll(/<script[^>]+src=/gi)].length;
  const preloads = [...html.matchAll(/<link[^>]+rel=['"]preload['"][^>]*>/gi)].length;
  rows.push({
    url: d.path,
    status: d.status,
    template: d.template,
    bytes: d.bytes,
    queries: d.queries,
    time_ms: d.time_ms,
    theme_ms: d.theme_ms ?? null,
    stylesheets: css,
    scripts: js,
    preloads,
    warnings: d.warnings,
    theme_assets: (html.match(/wp-content\/themes\/erfansanat\/assets\//g) || []).length,
  });
}

const report = {
  generated_at: new Date().toISOString(),
  rows,
  totals: {
    max_stylesheets: Math.max(...rows.map((r) => r.stylesheets)),
    max_scripts: Math.max(...rows.map((r) => r.scripts)),
    max_time_ms: Math.max(...rows.map((r) => r.time_ms)),
    max_theme_ms: Math.max(...rows.map((r) => r.theme_ms ?? 0)),
    warnings: rows.reduce((n, r) => n + r.warnings.length, 0),
  },
};

fs.writeFileSync(path.join(ROOT, 'tools/harness/perf-report.json'), JSON.stringify(report, null, 2));

console.log('URL                     STATUS  TPL          BYTES   Q    MS   CSS  JS  PRE');
for (const r of rows) {
  console.log(
    `${r.url.padEnd(23)} ${String(r.status).padEnd(7)} ${(r.template || '-').padEnd(12)} ${String(r.bytes).padStart(5)} ${String(r.queries).padStart(3)} ${String(r.time_ms).padStart(6)} ${String(r.stylesheets).padStart(4)} ${String(r.scripts).padStart(3)} ${String(r.preloads).padStart(4)}`
  );
  for (const w of r.warnings) console.log('   ⚠ ' + w);
}
console.log(`\nmax css=${report.totals.max_stylesheets} max js=${report.totals.max_scripts} max time=${report.totals.max_time_ms}ms (theme ${report.totals.max_theme_ms}ms) warnings=${report.totals.warnings}`);

// php-wasm keeps a runtime handle alive, so CLI wrappers must exit explicitly.
process.exit(0);
