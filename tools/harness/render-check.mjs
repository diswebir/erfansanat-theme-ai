// Render the site and dump HTML to /tmp/page.html for inspection.
// usage: node tools/harness/render-check.mjs "/" [outfile]
import fs from 'node:fs';
import { fetchUrl } from './fetch.mjs';

const url = process.argv[2] || '/';
const out = process.argv[3] || '/tmp/page.html';
const d = await fetchUrl(url);
fs.writeFileSync(out, d.html);
console.log(`${d.path} status=${d.status} tpl=${d.template} bytes=${d.bytes} queries=${d.queries} ${d.time_ms}ms`);
for (const w of d.warnings.slice(0, 6)) console.log('  ⚠ ' + w);
