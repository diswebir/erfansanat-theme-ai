/**
 * P06-T014 DOM behaviour test — runs the REAL assets/js/main.js (immutable)
 * against a rendered reviews panel fixture (star UI + client-side insert).
 * Run: node temp/phase-06-woocommerce-and-products/tests/test-task-014-dom.mjs
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
const fixture = execFileSync(php, [path.join(here, 'build-task-014-fixture.php')], { cwd: root, encoding: 'utf8' });
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
const form = doc.querySelector('[data-review-form="true"]');
const ratingInput = form.querySelector('[data-review-rating-input="true"]');
const ratingLabel = form.querySelector('[data-review-rating-label="true"]');
const stars = form.querySelectorAll('[data-review-star]');
const list = doc.querySelector('[data-reviews-list="true"]');
const success = form.querySelector('[data-review-success="true"]');

check('AC-02: فرم و ورودی امتیاز موجودند', !!form && ratingInput.value === '5');

// Star selection.
stars[2].click(); // 3 stars
check('AC-02: کلیک ستاره سوم مقدار امتیاز را ۳ می‌کند', ratingInput.value === '3');
check('AC-02: برچسب امتیاز به‌روز می‌شود', ratingLabel.textContent.includes('۳'));
const svg3 = stars[2].querySelector('[data-review-star-svg="true"]');
const svg4 = stars[3].querySelector('[data-review-star-svg="true"]');
check('AC-02: ستاره‌های ۱ تا ۳ پر می‌شوند', svg3.classList.contains('fill-amber-400'));
check('AC-02: ستاره ۴ خالی می‌ماند', !svg4.classList.contains('fill-amber-400'));

// Submit a review.
const before = list.querySelectorAll('[data-review-item], .rounded-2xl').length;
form.querySelector('[data-review-name="true"]').value = 'مهندس آزمایشی';
form.querySelector('[data-review-org="true"]').value = 'واحد آزمایش';
form.querySelector('[data-review-comment="true"]').value = 'این یک دیدگاه آزمایشی است.';
form.dispatchEvent(new window.Event('submit', { bubbles: true, cancelable: true }));

const cards = list.children;
check('SC-01: دیدگاه جدید به ابتدای لیست اضافه شد', cards.length === 2);
check('SC-01: نام دیدگاه جدید درست است', cards[0].textContent.includes('مهندس آزمایشی'));
check('SC-01: متن دیدگاه جدید درست است', cards[0].textContent.includes('این یک دیدگاه آزمایشی است.'));
check('AC-02: پیام موفقیت نمایش داده شد', !success.classList.contains('hidden'));
check('AC-02: فیلد نام پس از ثبت پاک شد', form.querySelector('[data-review-name="true"]').value === '');
check('AC-02: امتیاز پس از ثبت به ۵ بازنشانی شد', ratingInput.value === '5');

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
