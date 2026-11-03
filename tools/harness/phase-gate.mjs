// Phase gate: run the whole phase test suite, then the performance budget,
// then print a single verdict. Exit code 0 = the phase may be closed.
//
// usage: node tools/harness/phase-gate.mjs 01 "/" "/contact/"
import { execFileSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '../..');
const phase = process.argv[2] || '01';
const urls = process.argv.slice(3);

const dirs = fs.readdirSync(path.join(ROOT, 'temp')).filter((d) => d.startsWith(`phase-${phase}-`));
if (!dirs.length) { console.error(`no temp/phase-${phase}-* directory`); process.exit(2); }
const dir = `temp/${dirs[0]}/tests`;

const run = (cmd, args) => {
  try {
    return { ok: true, out: execFileSync(cmd, args, { cwd: ROOT, encoding: 'utf8' }) };
  } catch (e) {
    return { ok: false, out: String(e.stdout || '') + String(e.stderr || '') };
  }
};

console.log(`\n### SUITE: ${dir}`);
const suite = run('node', ['tools/harness/run-phase.mjs', dir]);
process.stdout.write(suite.out);

console.log(`\n### PERF BUDGET: ${urls.length ? urls.join(' ') : '/'}`);
const perf = run('node', ['tools/harness/perf.mjs', ...(urls.length ? urls : ['/'])]);
process.stdout.write(perf.out);

const report = JSON.parse(fs.readFileSync(path.join(ROOT, 'tools/harness/perf-report.json'), 'utf8'));
const gatePhp = run('node', ['tools/harness/run-php.mjs', 'tools/harness/gate.php']);
process.stdout.write('\n' + gatePhp.out);

const totals = report.totals;
const budgetOk =
  suite.ok && gatePhp.ok &&
  totals.max_stylesheets <= 2 && totals.max_scripts <= 2 && totals.warnings === 0;

console.log('\n' + '═'.repeat(64));
console.log(`PHASE ${phase} GATE: ${budgetOk ? 'OK' : 'NOT OK'}`);
console.log(`  suite        : ${suite.ok ? 'OK' : 'FAILED'}`);
console.log(`  css/js       : ${totals.max_stylesheets}/${totals.max_scripts}`);
console.log(`  max render   : ${totals.max_time_ms}ms`);
console.log(`  warnings     : ${totals.warnings}`);
console.log('═'.repeat(64));
process.exit(budgetOk ? 0 : 1);
