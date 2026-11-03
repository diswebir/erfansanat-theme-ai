// Render a single front-end URL as JSON: {status, html, queries, warnings, template}
import { PHP } from '@php-wasm/universal';
import { loadNodeRuntime, useHostFilesystem } from '@php-wasm/node';
import fs from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '../..');
const dec = (v) => (typeof v === 'string' ? v : Buffer.from(v instanceof Uint8Array ? v : Object.values(v ?? {})).toString('utf8'));

export async function runPhp(scriptPath) {
  const id = await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 42 } });
  const php = new PHP(id);
  await useHostFilesystem(php);
  const res = await php.run({ scriptPath });
  return { out: dec(res.text), err: dec(res.errors ?? ''), code: res.exitCode ?? 0 };
}

export async function fetchUrl(url = '/', opts = {}) {
  fs.writeFileSync(path.join(ROOT, 'tools/harness/fetch.json'), JSON.stringify({ path: url, html: opts.html !== false }));
  const { out, err, code } = await runPhp(path.join(ROOT, 'tools/harness/fetch.php'));
  const marker = '<<<ES_JSON>>>';
  const i = out.indexOf(marker);
  if (i < 0) throw new Error('fetch failed (exit ' + code + ')\nSTDOUT:\n' + out.slice(0, 1500) + '\nSTDERR:\n' + err.slice(0, 1500));
  const j = out.indexOf('<<<END>>>', i);
  return JSON.parse(out.slice(i + marker.length, j));
}

if (import.meta.url === `file://${process.argv[1]}`) {
  const d = await fetchUrl(process.argv[2] || '/');
  console.log(`${d.path}  status=${d.status}  template=${d.template || '-'}  bytes=${d.bytes}  queries=${d.queries}  ${d.time_ms}ms`);
  for (const w of d.warnings.slice(0, 10)) console.log('  ⚠ ' + w);
  if (process.env.ES_HTML) fs.writeFileSync('/tmp/page.html', d.html);
  if (process.env.ES_DUMP) console.log(d.html.slice(0, Number(process.env.ES_DUMP)));
}
