// Run one or more PHP test files on the php-wasm runtime.
// usage: node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-01.php
import { PHP } from '@php-wasm/universal';
import { loadNodeRuntime, useHostFilesystem } from '@php-wasm/node';
import fs from 'node:fs';
import path from 'node:path';

const files = process.argv.slice(2);
if (!files.length) { console.error('usage: run-tests.mjs <test.php> [...]'); process.exit(2); }
const missing = files.filter(f => !fs.existsSync(f));
if (missing.length) { console.error('missing:', missing.join(', ')); process.exit(2); }

const id = await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 42 } });
const php = new PHP(id);
await useHostFilesystem(php);

let failed = 0;
for (const f of files) {
  const abs = path.resolve(f);

  const res = await php.run({ scriptPath: abs, argv: [abs] });
  const dec = (v) => (typeof v === 'string' ? v : Buffer.from(v instanceof Uint8Array ? v : Object.values(v)).toString('utf8'));
  if (res.text) process.stdout.write(dec(res.text));
  if (res.errors) { const e = dec(res.errors); if (e.trim()) process.stderr.write('\n[stderr] ' + e + '\n'); }
  if ((res.exitCode ?? 0) !== 0) failed++;
}
process.exit(failed ? 1 : 0);
