// Final non-PHP gate: validate the isolated runner's aggregate after it exits.
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const relativeDir = (process.argv[2] || '').replace(/^\.\//, '').replace(/\/+$/, '');
if (!relativeDir) {
  console.error('usage: assert-phase-complete.mjs <test-directory>');
  process.exit(2);
}
const testDir = path.resolve(root, relativeDir);
if (!testDir.startsWith(root + path.sep) || !fs.existsSync(testDir)) {
  console.error(`invalid test directory: ${relativeDir}`);
  process.exit(2);
}
const expectedFiles = fs.readdirSync(testDir).filter((name) => /^test-task-\d+\.php$/.test(name)).length;
const resultPath = path.join(root, 'tools/harness/last-phase-result.json');
const result = fs.existsSync(resultPath) ? JSON.parse(fs.readFileSync(resultPath, 'utf8')) : null;
const generatedAt = Date.parse(result?.at || '');
const valid = result
  && result.phase === path.basename(path.dirname(testDir))
  && Number.isInteger(result.files)
  && result.files === expectedFiles
  && expectedFiles > 0
  && Number(result.passed) > 0
  && Number(result.failed) === 0
  && Number(result.pending) === 0
  && Number.isFinite(generatedAt)
  && Date.now() - generatedAt < 60 * 60 * 1000;
if (!valid) {
  console.error('Phase gate failed: aggregate is missing, stale, incomplete, or contains failures/pending checks.');
  console.error(JSON.stringify(result, null, 2));
  process.exit(1);
}
console.log(`PHASE GATE: OK (${result.files} test files, ${result.passed} assertions, 0 failing files, 0 pending).`);
