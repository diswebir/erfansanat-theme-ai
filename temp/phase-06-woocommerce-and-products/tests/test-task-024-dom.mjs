/**
 * P06-T024 DOM behaviour test — real main.js buyer-type toggle (legal fields).
 * Run: node temp/phase-06-woocommerce-and-products/tests/test-task-024-dom.mjs
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
const fixture = execFileSync(php, [path.join(here, 'build-task-024-fixture.php')], { cwd: root, encoding: 'utf8' });
const mainJs = readFileSync(path.join(root, 'erfansanat/assets/js/main.js'), 'utf8');

const dom = new JSDOM(fixture, { url: 'http://localhost/checkout/', runScripts: 'outside-only', pretendToBeVisual: true });
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
const checkoutPage = doc.querySelector('[data-checkout-page="true"]');
const legalFields = checkoutPage.querySelector('[data-legal-fields="true"]');
const radios = Array.from(checkoutPage.querySelectorAll('[data-buyer-type-radio]'));
const realRadio = radios.find((r) => r.value === 'real');
const legalRadio = radios.find((r) => r.value === 'legal');

check('AC-01: صفحه تسویه و فرم', !!checkoutPage && !!checkoutPage.querySelector('[data-checkout-form="true"]'));
check('AC-03: دو رادیو حقیقی/حقوقی', !!realRadio && !!legalRadio);
check('AC-03: حالت پیش‌فرض حقیقی → فیلدهای حقوقی کم‌رنگ', legalFields.classList.contains('opacity-60'));

legalRadio.checked = true;
legalRadio.dispatchEvent(new window.Event('change', { bubbles: true }));
check('AC-03: انتخاب حقوقی → فیلدهای حقوقی فعال', !legalFields.classList.contains('opacity-60'));

realRadio.checked = true;
realRadio.dispatchEvent(new window.Event('change', { bubbles: true }));
check('AC-03: بازگشت به حقیقی → فیلدهای حقوقی کم‌رنگ', legalFields.classList.contains('opacity-60'));

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
