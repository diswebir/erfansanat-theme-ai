/**
 * P06-T007 DOM behaviour test — runs the REAL assets/js/main.js (immutable)
 * plus the additive product-quantity guard against a rendered PDP fixture.
 * Run: node temp/phase-06-woocommerce-and-products/tests/test-task-007-dom.mjs
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
const fixture = execFileSync(php, [path.join(here, 'build-task-007-fixture.php')], { cwd: root, encoding: 'utf8' });
const mainJs = readFileSync(path.join(root, 'erfansanat/assets/js/main.js'), 'utf8');
const qtyJs = readFileSync(path.join(root, 'erfansanat/assets/js/product-quantity.js'), 'utf8');

const dom = new JSDOM(`<!doctype html><html><head></head><body>${fixture}</body></html>`, {
  url: 'http://localhost/product/fandoghi-string/',
  runScripts: 'outside-only',
  pretendToBeVisual: true,
});
const { window } = dom;
window.matchMedia = window.matchMedia || (() => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} }));
window.IntersectionObserver = window.IntersectionObserver || class { observe() {} unobserve() {} disconnect() {} };
window.requestAnimationFrame = window.requestAnimationFrame || ((cb) => setTimeout(cb, 0));
window.scrollTo = window.scrollTo || (() => {});

window.eval(mainJs);
// main.js defers initAll to DOMContentLoaded when the document is still loading.
if (window.document.readyState === 'loading') {
  window.document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));
}
window.eval(qtyJs);

const doc = window.document;
const FA = Array.from({ length: 10 }, (_, i) => String.fromCodePoint(0x6f0 + i)).join('');
const fa = (n) => String(n).replace(/\d/g, (d) => FA[Number(d)]);

// AC-03: colour swatch selection.
const colorGroup = doc.querySelector('[data-opt-group="رنگ نور"]');
const colorBtns = colorGroup.querySelectorAll('[data-opt-btn]');
colorBtns[1].click();
check('AC-03: کلیک سواچ رنگ، برچسب انتخاب را عوض می‌کند', colorGroup.querySelector('[data-opt-selected-label]').textContent === 'قرمز');
check('AC-03: سواچ انتخاب‌شده ring-2 می‌گیرد', colorBtns[1].classList.contains('ring-2'));
check('AC-03: سواچ قبلی ring-1 می‌شود', colorBtns[0].classList.contains('ring-1'));

// AC-03: select swatch.
const sizeGroup = doc.querySelector('[data-opt-group="طول و تراکم ریسه"]');
const sizeBtns = sizeGroup.querySelectorAll('[data-opt-btn]');
sizeBtns[1].click();
check('AC-03: سواچ ویژگی انتخاب می‌شود', sizeGroup.querySelector('[data-opt-selected-label]').textContent === '20m' && sizeBtns[1].classList.contains('bg-emerald-700'));

// AC-02: quantity stepper min/step/max.
const rootEl = doc.querySelector('[data-pdp-qty-root]');
const valEl = rootEl.querySelector('[data-pdp-qty-value]');
const inc = rootEl.querySelector('[data-pdp-qty-inc]');
const dec = rootEl.querySelector('[data-pdp-qty-dec]');
check('AC-02: مقدار اولیه = حداقل (۵)', valEl.textContent === fa(5));
inc.click();
check('AC-02: افزایش با گام ۵ → ۱۰', valEl.textContent === fa(10));
inc.click(); inc.click(); // 15, 20
check('AC-02: رسیدن به حداکثر ۲۰', valEl.textContent === fa(20));
inc.click(); // guard must block > max
check('AC-02: guard حداکثر مانع افزایش بیش از ۲۰ می‌شود', valEl.textContent === fa(20));
dec.click();
check('AC-02: کاهش → ۱۵', valEl.textContent === fa(15));

// SC-01: add to cart writes localStorage + shows toast.
const addBtn = doc.querySelector('[data-pdp-add-to-cart]');
addBtn.click();
const toast = doc.querySelector('[data-pdp-cart-toast]');
check('SC-01: بنر افزودن به سبد نمایان می‌شود', toast.classList.contains('flex') && !toast.classList.contains('hidden'));
let cartRaw = '';
for (let i = 0; i < window.localStorage.length; i++) {
  const k = window.localStorage.key(i);
  const v = window.localStorage.getItem(k);
  if (v && v.includes('fandoghi-string')) { cartRaw = v; break; }
}
check('SC-01: محصول در localStorage سبد ذخیره می‌شود', cartRaw.includes('fandoghi-string'));
let qtyOk = false;
try { const arr = JSON.parse(cartRaw); qtyOk = Array.isArray(arr) && arr[0] && arr[0].qty === 15; } catch (e) {}
check('SC-01: تعداد ذخیره‌شده = ۱۵', qtyOk);

console.log(`\n  DOM RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
