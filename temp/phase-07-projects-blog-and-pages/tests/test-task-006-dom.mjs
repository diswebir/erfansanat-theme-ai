/**
 * P07-T006 DOM behaviour test — real main.js projects archive pagination/search/sort.
 * Run: node temp/phase-07-projects-blog-and-pages/tests/test-task-006-dom.mjs
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
const fixture = execFileSync(php, [path.join(here, 'build-task-006-fixture.php')], { cwd: root, encoding: 'utf8' });
const mainJs = readFileSync(path.join(root, 'erfansanat/assets/js/main.js'), 'utf8');

const dom = new JSDOM(fixture, { url: 'http://localhost/projects/', runScripts: 'outside-only', pretendToBeVisual: true });
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
const archive = doc.querySelector('[data-projects-archive="true"]');
const grid = archive.querySelector('[data-project-grid="true"]');
const cards = Array.from(grid.querySelectorAll('[data-project-item="true"]'));
const visible = () => cards.filter((c) => !c.classList.contains('hidden'));
const FA = Array.from({ length: 10 }, (_, i) => String.fromCodePoint(0x6f0 + i)).join('');
const fa = (n) => String(n).replace(/\d/g, (d) => FA[Number(d)]);

check('SC-01: هشت کارت رندر شد', cards.length === 8);
check('SC-02: صفحه اول ۶ کارت نمایان', visible().length === 6);

// Page 2.
const page2 = archive.querySelector('[data-page-btn="2"]');
page2.click();
check('SC-02: صفحه دوم ۲ کارت نمایان', visible().length === 2);
check('SC-02: صفحه دوم aria-current گرفت', page2.getAttribute('aria-current') === 'page');
check('SC-02: خلاصه صفحه به‌روز شد', archive.querySelector('[data-pagination-summary="true"]').textContent.includes(fa(2)));

// Back to page 1, then search.
archive.querySelector('[data-page-btn="1"]').click();
const search = archive.querySelector('#project-sidebar-search');
search.value = 'پروژه 8';
search.dispatchEvent(new window.Event('input', { bubbles: true }));
check('AC-01: جستجو فیلتر می‌کند (۱ نتیجه)', visible().length === 1 && visible()[0].getAttribute('data-project-search-text').includes('پروژه 8'));

// Clear search, sort by popular.
search.value = '';
search.dispatchEvent(new window.Event('input', { bubbles: true }));
const sort = archive.querySelector('[data-project-sort="true"]');
sort.value = 'popular';
sort.dispatchEvent(new window.Event('change', { bubbles: true }));
// Re-query in the new DOM order (sort reorders the grid children).
const reordered = Array.from(grid.querySelectorAll('[data-project-item="true"]')).filter((c) => !c.classList.contains('hidden'));
check('AC-01: مرتب‌سازی پربازدید (اولین = ۸۰ بازدید)', reordered[0].getAttribute('data-project-views') === '80');

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
