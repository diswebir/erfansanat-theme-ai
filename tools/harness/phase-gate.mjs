// Phase gate: whole phase suite -> performance budget -> verdict.
// Child processes inherit stdio (piping php-wasm output through a buffer hangs).
//
// usage: node tools/harness/phase-gate.mjs 01 "/" "/contact/"
import { spawnSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '../..');
const phase = process.argv[2] || '01';
const urls = process.argv.slice(3);

const dirs = fs.readdirSync(path.join(ROOT, 'temp')).filter((d) => d.startsWith(`phase-${phase}-`));
if (!dirs.length) { console.error(`no temp/phase-${phase}-* directory`); process.exit(2); }
const testDir = `temp/${dirs[0]}/tests`;

const step = (label, args) => {
  console.log(`\n### ${label}`);
  const r = spawnSync('node', args, { cwd: ROOT, stdio: 'inherit' });
  return r.status === 0;
};

const suiteOk = step(`SUITE: ${testDir}`, ['tools/harness/run-phase.mjs', testDir]);
const perfOk = step(`PERF: ${urls.length ? urls.join(' ') : '/'}`, ['tools/harness/perf.mjs', ...(urls.length ? urls : ['/'])]);

let report = null;
try { report = JSON.parse(fs.readFileSync(path.join(ROOT, 'tools/harness/perf-report.json'), 'utf8')); } catch { /* handled below */ }

const budgetOk = step('BUDGET', ['tools/harness/run-php.mjs', 'tools/harness/gate.php']);
const t = report?.totals ?? {};

console.log('\n' + '═'.repeat(64));
const ok = suiteOk && perfOk && budgetOk;
console.log(`PHASE ${phase} GATE: ${ok ? 'OK' : 'NOT OK'}`);
console.log(`  suite      : ${suiteOk ? 'OK' : 'FAILED'}`);
console.log(`  css/js     : ${t.max_stylesheets}/${t.max_scripts}`);
console.log(`  theme render: ${t.max_theme_ms}ms   full boot: ${t.max_time_ms}ms`);
console.log(`  warnings   : ${t.warnings}`);
console.log('═'.repeat(64));
process.exit(ok ? 0 : 1);
