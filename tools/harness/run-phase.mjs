// Run all test files of one phase in a single php-wasm boot.
// usage: node tools/harness/run-phase.mjs temp/phase-01-core-and-assets/tests [pattern]
import { PHP } from '@php-wasm/universal';
import { loadNodeRuntime, useHostFilesystem } from '@php-wasm/node';
import fs from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '../..');
const relDir = (process.argv[2] || '').replace(/^\.\//, '').replace(/\/+$/, '');
if (!relDir) { console.error('usage: run-phase.mjs <relative-test-dir> [pattern]'); process.exit(2); }

const cfg = { dir: relDir, pattern: process.argv[3] || 'test-task-*.php' };
const cfgFile = path.join(ROOT, 'tools/harness/phase-run.json');
fs.writeFileSync(cfgFile, JSON.stringify(cfg));

// Fail fast with a clear message when the config never reaches PHP.
const run = async () => {
  const id = await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 42 } });
  const php = new PHP(id);
  await useHostFilesystem(php);
  const dec = (v) => (typeof v === 'string' ? v : Buffer.from(v instanceof Uint8Array ? v : Object.values(v ?? {})).toString('utf8'));
  try {
    const res = await php.run({ scriptPath: path.join(ROOT, 'tools/harness/run-phase.php') });
    return { text: res.text ? dec(res.text) : '', code: res.exitCode ?? 0 };
  } catch (e) {
    const res = e.response || e;
    if (res?.text) return { text: dec(res.text), code: res.exitCode ?? 1 };
    throw e;
  }
};

const { text, code } = await run();
process.stdout.write(text);
if (!text.includes('TEST FILE')) {
  console.error('\n[fatal] the phase runner produced no report; is tools/harness/phase-run.json readable from PHP?');
  process.exit(2);
}
process.exit(code !== 0 ? 1 : 0);
