/**
 * P07-T009: real main.js blog pagination and search, not a mock paginator.
 * Run: node temp/phase-07-projects-blog-and-pages/tests/test-task-009-dom.mjs
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
const check = (label, ok) => { ok ? pass++ : fail++; console.log(`  [${ok ? 'PASS' : 'FAIL'}] ${label}`); };
const html = execFileSync(path.join(root, 'temp/tools/php'), [path.join(here, 'build-blog-fixture.php'), '7', '4'], { cwd: root, encoding: 'utf8' });
const dom = new JSDOM(html, { url: 'http://localhost/blog/', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom;
window.matchMedia ||= () => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} });
window.IntersectionObserver ||= class { observe() {} unobserve() {} disconnect() {} };
window.requestAnimationFrame ||= (cb) => setTimeout(cb, 0);
window.scrollTo ||= () => {};
window.HTMLElement.prototype.scrollIntoView ||= () => {};
window.eval(readFileSync(path.join(root, 'erfansanat/assets/js/main.js'), 'utf8'));
window.document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));
const doc = window.document;
const archive = doc.querySelector('[data-blog-archive="true"]');
const visible = () => [...archive.querySelectorAll('[data-blog-item="true"]')].filter(el => !el.classList.contains('hidden'));
check('SC-01: هفت مقاله واقعی در DOM', archive.querySelectorAll('[data-blog-item="true"]').length === 7);
check('SC-02: صفحه اول چهار مقاله', visible().length === 4);
archive.querySelector('[data-page-btn="2"]').click();
check('SC-02: صفحه دوم سه مقاله', visible().length === 3);
check('SC-02: شماره صفحه جاری و وضعیت بعدی', archive.querySelector('[data-page-btn="2"]').getAttribute('aria-current') === 'page' && archive.querySelector('[data-page-next]').disabled);
archive.querySelector('[data-page-prev="true"]').click();
check('SC-02: دکمه قبلی به صفحه اول برمی‌گردد', visible().length === 4 && archive.querySelector('[data-page-prev]').disabled);
const search = doc.querySelector('[data-blog-search="true"]');
search.value = 'مقاله آزمایشی 7';
search.dispatchEvent(new window.Event('input', { bubbles: true }));
check('AC-03: جستجوی واقعی یک نتیجه دارد', visible().length === 1 && visible()[0].textContent.includes('مقاله آزمایشی 7'));
check('AC-03: جستجوی خالی حالت خالی و پنهان‌شدن صفحه‌بندی', archive.querySelector('[data-blog-count]').textContent === '۱' && archive.querySelector('[data-pagination-root]').classList.contains('hidden'));
console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
