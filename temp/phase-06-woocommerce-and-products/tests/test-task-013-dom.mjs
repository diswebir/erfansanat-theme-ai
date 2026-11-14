/**
 * P06-T013 DOM behaviour test — runs the REAL assets/js/main.js (immutable)
 * against a rendered FAQ accordion fixture.
 * Run: node temp/phase-06-woocommerce-and-products/tests/test-task-013-dom.mjs
 */
import { execFileSync } from 'node:child_process';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { createRequire } from 'node:module';

const here = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(here, '../../..');
const require = createRequire(path.join(root, 'temp/tools/package.json'));
const { JSDOM } = require('jsdom');

let pass = 0, fail = 0;
const check = (label, ok) => {
  if (ok) { pass++; console.log(`  [PASS] ${label}`); }
  else { fail++; console.log(`  [FAIL] ${label}`); }
};

const php = path.join(root, 'temp/tools/php');
const fixture = execFileSync(php, [path.join(here, 'build-task-013-fixture.php')], { cwd: root, encoding: 'utf8' });
const mainJs = readFileSync(path.join(root, 'erfansanat/assets/js/main.js'), 'utf8');

const dom = new JSDOM(`<!doctype html><html><head></head><body>${fixture}</body></html>`, {
  url: 'http://localhost/product/delsa-light-tree/',
  runScripts: 'outside-only',
  pretendToBeVisual: true,
});
const { window } = dom;
window.matchMedia = window.matchMedia || (() => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} }));
window.IntersectionObserver = window.IntersectionObserver || class { observe() {} unobserve() {} disconnect() {} };
window.requestAnimationFrame = window.requestAnimationFrame || ((cb) => setTimeout(cb, 0));
window.scrollTo = window.scrollTo || (() => {});

window.eval(mainJs);
if (window.document.readyState === 'loading') {
  window.document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));
}

const doc = window.document;
const body0 = doc.querySelector('[data-faq-body="0"]');
const body1 = doc.querySelector('[data-faq-body="1"]');
const icon1 = doc.querySelector('[data-faq-icon="1"]');
const toggle0 = doc.querySelector('[data-faq-toggle="0"]');
const toggle1 = doc.querySelector('[data-faq-toggle="1"]');

check('SC-01: سوال اول ابتدا باز است', body0.classList.contains('block') && !body0.classList.contains('hidden'));
check('SC-01: سوال دوم ابتدا بسته است', body1.classList.contains('hidden'));

toggle1.click();
check('SC-01: کلیک سوال دوم آن را باز می‌کند', body1.classList.contains('block') && !body1.classList.contains('hidden'));
check('AC-02: آیکون سوال بازشده می‌چرخد', icon1.classList.contains('rotate-45') && icon1.classList.contains('bg-emerald-800'));

toggle1.click();
check('SC-01: کلیک دوباره سوال دوم را می‌بندد', body1.classList.contains('hidden'));
check('AC-02: آیکون بسته بازنشانی می‌شود', !icon1.classList.contains('rotate-45') && icon1.classList.contains('bg-emerald-100'));

toggle0.click();
check('SC-01: سوال اول بسته می‌شود', body0.classList.contains('hidden'));
toggle0.click();
check('SC-01: سوال اول دوباره باز می‌شود', body0.classList.contains('block'));

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
