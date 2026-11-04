// Run each PHP-WASM test in an isolated request so WordPress stubs cannot collide.
// Usage: node tools/harness/run-phase-isolated.mjs <test-directory>
import fs from 'node:fs';
import path from 'node:path';
import { spawnSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const relativeDir = (process.argv[2] || '').replace(/^\.\//, '').replace(/\/+$/, '');
if (!relativeDir) {
  console.error('usage: run-phase-isolated.mjs <test-directory>');
  process.exit(2);
}
const dir = path.resolve(root, relativeDir);
if (!dir.startsWith(root + path.sep) || !fs.existsSync(dir)) {
  console.error(`invalid test directory: ${relativeDir}`);
  process.exit(2);
}
const files = fs.readdirSync(dir).filter((file) => /^test-task-\d+\.php$/.test(file)).sort();
if (!files.length) {
  console.error(`no test-task files in ${relativeDir}`);
  process.exit(2);
}
const resultFile = path.join(root, 'tools/harness/last-phase-result.json');
const rows = [];
const totals = () => rows.reduce((sum, row) => ({
  passed: sum.passed + row.passed,
  failed: sum.failed + row.failed,
  pending: sum.pending + row.pending,
}), { passed: 0, failed: 0, pending: 0 });
for (const file of files) {
  const testPath = path.join(relativeDir, file);
  const result = spawnSync(process.execPath, [path.join(root, 'tools/harness/run-tests.mjs'), testPath], {
    cwd: root,
    encoding: 'utf8',
    maxBuffer: 10 * 1024 * 1024,
  });
  const output = `${result.stdout || ''}${result.stderr || ''}`;
  process.stdout.write(`\n=== ${file} ===\n${output}`);
  const match = output.match(/PASS\s*=\s*(\d+)\s+FAIL\s*=\s*(\d+)\s+PENDING\s*=\s*(\d+)/);
  const row = match
    ? { file, passed: Number(match[1]), failed: Number(match[2]), pending: Number(match[3]) }
    : { file, passed: 0, failed: 1, pending: 0 };
  if (result.status !== 0 || result.error || row.failed || row.pending || !row.passed) {
    if (!row.failed && !row.pending && row.passed) row.failed = 1;
  }
  rows.push(row);
  const sum = totals();
  fs.writeFileSync(resultFile, JSON.stringify({
    phase: path.basename(path.dirname(dir)),
    files: rows.length,
    passed: sum.passed,
    failed: rows.filter((r) => r.failed || r.pending || !r.passed).length,
    pending: sum.pending,
    at: new Date().toISOString(),
  }, null, 2));
}
const sum = totals();
const badFiles = rows.filter((row) => row.failed || row.pending || !row.passed).length;
console.log('\nTEST FILE                  PASS   FAIL  PENDING');
for (const row of rows) console.log(`${row.file.padEnd(26)} ${String(row.passed).padStart(4)} ${String(row.failed).padStart(7)} ${String(row.pending).padStart(8)}`);
console.log(`TOTAL                      ${String(sum.passed).padStart(4)} ${String(sum.failed).padStart(7)} ${String(sum.pending).padStart(8)}`);
console.log(badFiles ? `PHASE RESULT: NOT OK (${badFiles} failing test files)` : `PHASE RESULT: OK (${rows.length} files, ${sum.passed} assertions)`);
process.exit(badFiles ? 1 : 0);
