/**
 * P06-T021 DOM behaviour test — real main.js: price-desc sort + page 2/3.
 * Run: node temp/phase-06-woocommerce-and-products/tests/test-task-021-dom.mjs
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
const fixture = execFileSync(php, [path.join(here, 'build-task-021-fixture.php')], { cwd: root, encoding: 'utf8' });
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
const FA = Array.from({ length: 10 }, (_, i) => String.fromCodePoint(0x6f0 + i)).join('');
const fa = (n) => String(n).replace(/\d/g, (d) => FA[Number(d)]);

check('AC-03: result-count واقعی = ۲۰', archive.querySelector('[data-product-count]').textContent === fa(20));
check('AC-02: سه دکمه صفحه (۲۰/۹)', archive.querySelectorAll('[data-page-btn]').length === 3);
check('SC-01: صفحه اول ۹ کارت', visible().length === 9);

// SC-02: price descending.
sortSelect.value = 'price-desc';
sortSelect.dispatchEvent(new window.Event('change', { bubbles: true }));
check('SC-02: مرتب‌سازی نزولی قیمت → گران‌ترین اول (۲۰۰۰۰۰۰)', visible()[0].getAttribute('data-product-price') === '2000000');
const descPrices = visible().map((el) => parseInt(el.getAttribute('data-product-price'), 10));
check('SC-02: قیمت‌ها نزولی هستند', descPrices.every((p, i) => i === 0 || p <= descPrices[i - 1]));

// SC-01: page 2 shows the next 9 cards.
sortSelect.value = 'default';
sortSelect.dispatchEvent(new window.Event('change', { bubbles: true }));
archive.querySelector('[data-page-btn="2"]').click();
check('SC-01: صفحه دوم ۹ کارت', visible().length === 9);
check('SC-01: خلاصه صفحه ۲', archive.querySelector('[data-pagination-summary]').textContent.includes(fa(2)));
check('SC-01: دکمه صفحه ۲ فعال است', archive.querySelector('[data-page-btn="2"]').getAttribute('aria-current') === 'page');

// Page 3 shows the remaining 2 cards.
archive.querySelector('[data-page-btn="3"]').click();
check('SC-01: صفحه سوم ۲ کارت باقی‌مانده', visible().length === 2);
check('AC-02: دکمه بعدی در صفحه آخر غیرفعال', archive.querySelector('[data-page-next="true"]').disabled === true);

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
