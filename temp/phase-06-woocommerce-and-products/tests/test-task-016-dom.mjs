/**
 * P06-T016 DOM behaviour test — runs the REAL assets/js/main.js (immutable)
 * against a rendered product-archive fixture (filter/sort/pagination contract).
 * Run: node temp/phase-06-woocommerce-and-products/tests/test-task-016-dom.mjs
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
const fixture = execFileSync(php, [path.join(here, 'build-task-016-fixture.php')], { cwd: root, encoding: 'utf8' });
const mainJs = readFileSync(path.join(root, 'erfansanat/assets/js/main.js'), 'utf8');

const dom = new JSDOM(`<!doctype html><html><head></head><body>${fixture}</body></html>`, {
  url: 'http://localhost/shop/',
  runScripts: 'outside-only',
  pretendToBeVisual: true,
});
const { window } = dom;
window.matchMedia = window.matchMedia || (() => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} }));
window.IntersectionObserver = window.IntersectionObserver || class { observe() {} unobserve() {} disconnect() {} };
window.requestAnimationFrame = window.requestAnimationFrame || ((cb) => setTimeout(cb, 0));
window.scrollTo = window.scrollTo || (() => {});
window.HTMLElement.prototype.scrollIntoView = window.HTMLElement.prototype.scrollIntoView || (() => {});

window.eval(mainJs);
if (window.document.readyState === 'loading') {
  window.document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));
}

const doc = window.document;
const archive = doc.querySelector('[data-products-archive]');
const grid = archive.querySelector('[data-product-grid]');
const visible = () => Array.from(grid.querySelectorAll('[data-product-item]')).filter((el) => !el.classList.contains('hidden'));
const sortSelect = archive.querySelector('[data-product-sort="true"]');
const setMode = (mode) => {
  const radio = archive.querySelector(`input[data-product-purchase-mode="${mode}"]`);
  radio.checked = true;
  radio.dispatchEvent(new window.Event('change', { bubbles: true }));
};

// Initial state: 12 products, page 1 shows 9.
check('SC-01: ۱۲ کارت رندر شده', grid.querySelectorAll('[data-product-item]').length === 12);
check('AC-02: صفحه اول ۹ کارت نشان می‌دهد', visible().length === 9);
check('AC-02: صفحه‌بندی ۲ صفحه دارد', archive.querySelectorAll('[data-page-btn]').length === 2);

// Sort price ascending → cheapest first.
sortSelect.value = 'price-asc';
sortSelect.dispatchEvent(new window.Event('change', { bubbles: true }));
check('AC-01: مرتب‌سازی ارزان‌ترین → ارزان‌ترین آنلاین اول (۲۰۰۰)', visible()[0].getAttribute('data-product-price') === '200000');

// Sort price descending → most expensive first.
sortSelect.value = 'price-desc';
sortSelect.dispatchEvent(new window.Event('change', { bubbles: true }));
check('AC-01: مرتب‌سازی گران‌ترین → اولین کارت ۱۲۰۰۰۰', visible()[0].getAttribute('data-product-price') === '1200000');

// Purchase mode online → only purchasable (6 even products).
sortSelect.value = 'default';
sortSelect.dispatchEvent(new window.Event('change', { bubbles: true }));
setMode('online');
check('AC-01: فیلتر خرید آنلاین → ۶ محصول', visible().length === 6);
check('AC-01: همه نمایش‌داده‌شده آنلاین هستند', visible().every((el) => el.getAttribute('data-product-purchasable') === 'true'));

// Inquiry mode (call) → only non-purchasable (6 odd products).
setMode('call');
check('AC-01: فیلتر استعلامی → ۶ محصول', visible().length === 6);
check('AC-01: همه نمایش‌داده‌شده استعلامی هستند', visible().every((el) => el.getAttribute('data-product-purchasable') === 'false'));

// Back to all + pagination page 2.
setMode('all');
check('AC-02: بازگشت به همه → ۹ کارت صفحه اول', visible().length === 9);
const page2 = archive.querySelector('[data-page-btn="2"]');
page2.click();
check('AC-02: صفحه دوم ۳ کارت باقی‌مانده', visible().length === 3);
check('AC-02: خلاصه صفحه‌بندی به‌روز شد', archive.querySelector('[data-pagination-summary]').textContent.includes('۲'));

// Search filters by name.
page2.click(); // ensure bound; go back to page 1 via prev
const prev = archive.querySelector('[data-page-prev="true"]');
prev.click();
const search = archive.querySelector('[data-product-search="true"]');
search.value = 'محصول 12';
search.dispatchEvent(new window.Event('input', { bubbles: true }));
check('AC-01: جستجو نام محصول را فیلتر می‌کند', visible().length === 1 && visible()[0].getAttribute('data-product-search-text').includes('محصول 12'));

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
