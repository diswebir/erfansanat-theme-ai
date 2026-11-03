// Run one standalone PHP script on the php-wasm runtime.
// usage: node tools/harness/run-php.mjs tools/harness/gate.php
import { PHP } from '@php-wasm/universal';
import { loadNodeRuntime, useHostFilesystem } from '@php-wasm/node';
import path from 'node:path';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '../..');
const script = process.argv[2];
if (!script) { console.error('usage: run-php.mjs <script.php>'); process.exit(2); }

const id = await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 42 } });
const php = new PHP(id);
await useHostFilesystem(php);
const dec = (v) => (typeof v === 'string' ? v : Buffer.from(v instanceof Uint8Array ? v : Object.values(v ?? {})).toString('utf8'));

try {
  const res = await php.run({ scriptPath: path.resolve(ROOT, script) });
  if (res.text) process.stdout.write(dec(res.text));
  if (res.errors) { const e = dec(res.errors); if (e.trim()) process.stderr.write(e); }
  process.exit(res.exitCode ?? 0);
} catch (e) {
  const res = e.response || e;
  if (res?.text) process.stdout.write(dec(res.text));
  process.exit(res?.exitCode || 1);
}
