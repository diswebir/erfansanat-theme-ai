/**
 * P07-T002 DOM behaviour test — real main.js gallery + lightbox.
 * Run: node temp/phase-07-projects-blog-and-pages/tests/test-task-002-dom.mjs
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
const fixture = execFileSync(php, [path.join(here, 'build-task-002-fixture.php')], { cwd: root, encoding: 'utf8' });
const mainJs = readFileSync(path.join(root, 'erfansanat/assets/js/main.js'), 'utf8');

const dom = new JSDOM(fixture, { url: 'http://localhost/projects/g/', runScripts: 'outside-only', pretendToBeVisual: true });
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
const rootEl = doc.querySelector('[data-gallery-root="true"]');
const thumbs = Array.from(rootEl.querySelectorAll('[data-gallery-thumb]'));
const mainImg = rootEl.querySelector('[data-gallery-main-img="true"]');
const counter = rootEl.querySelector('[data-gallery-counter="true"]');
const lightbox = rootEl.querySelector('[data-gallery-lightbox="true"]');
const srcs = thumbs.map((t) => t.getAttribute('data-gallery-src'));
const FA = Array.from({ length: 10 }, (_, i) => String.fromCodePoint(0x6f0 + i)).join('');
const fa = (n) => String(n).replace(/\d/g, (d) => FA[Number(d)]);

check('SC-01: گالری با ۴ بندانگشتی', thumbs.length === 4 && !!mainImg);
check('SC-01: تصویر اصلی = اولین تصویر', mainImg.getAttribute('src') === srcs[0]);

// Click third thumb → main image + counter update.
thumbs[2].click();
check('SC-02: کلیک بندانگشتی تصویر اصلی را عوض می‌کند', mainImg.getAttribute('src') === srcs[2]);
check('SC-02: شمارنده به‌روز شد (۳ / ۴)', counter.textContent === fa(3) + ' / ' + fa(4));

// Expand → lightbox opens.
rootEl.querySelector('[data-gallery-expand="true"]').click();
check('SC-02: بزرگ‌نمایی لایت‌باکس را باز می‌کند', !lightbox.classList.contains('hidden') && lightbox.classList.contains('flex'));
check('SC-02: تصویر لایت‌باکس = تصویر فعال', rootEl.querySelector('[data-gallery-lightbox-img="true"]').getAttribute('src') === srcs[2]);

// Lightbox next → advances.
rootEl.querySelector('[data-gallery-lightbox-next="true"]').click();
check('SC-02: بعدی در لایت‌باکس', mainImg.getAttribute('src') === srcs[3]);

// Close via close button.
rootEl.querySelector('[data-gallery-lightbox-close="true"]').click();
check('SC-02: دکمه بستن لایت‌باکس را می‌بندد', lightbox.classList.contains('hidden'));

// Reopen via main image click, close via backdrop.
mainImg.click();
check('SC-02: کلیک تصویر اصلی لایت‌باکس را باز می‌کند', !lightbox.classList.contains('hidden'));
lightbox.click();
check('SC-02: کلیک پس‌زمینه لایت‌باکس را می‌بندد', lightbox.classList.contains('hidden'));

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
