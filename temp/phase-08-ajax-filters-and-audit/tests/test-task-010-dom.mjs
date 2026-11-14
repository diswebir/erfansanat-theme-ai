#!/usr/bin/env node
/** P08-T010 — project filters JSDOM: real endpoint via CLI bridge, counters, URL, clear. */
import { execFileSync } from 'node:child_process';
import { readFileSync } from 'node:fs';
import { createRequire } from 'node:module';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const root = join(here, '../../..');
const require = createRequire(join(root, 'temp/tools/package.json'));
const { JSDOM } = require('jsdom');
let pass = 0, fail = 0;
const check = (name, ok, detail = '') => { ok ? pass++ : fail++; console.log(`  [${ok ? 'PASS' : 'FAIL'}] ${name}${ok ? '' : ' — ' + detail}`); };
const sleep = ms => new Promise(r => setTimeout(r, ms));

const html = execFileSync(join(root, 'temp/tools/php'), [join(here, 'build-projects-fixture.php')], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
const dom = new JSDOM(html, { url: 'https://example.test/projects/', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom, { document } = window;
const meta = JSON.parse(document.getElementById('es-fixture-meta').textContent);
window.matchMedia ||= () => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} });
window.IntersectionObserver ||= class { observe() {} unobserve() {} disconnect() {} };
window.scrollTo ||= () => {};
window.HTMLElement.prototype.scrollIntoView ||= () => {};
window.URLSearchParams = URLSearchParams;
window.AbortController = AbortController;
window.CustomEvent = window.CustomEvent || CustomEvent;
window.ES_AJAX = { url: '/wp-admin/admin-ajax.php', nonces: { project_filters: meta.nonce } };

window.fetch = (url, opts) => {
  const params = new URLSearchParams(opts.body);
  const request = Object.fromEntries(params.entries());
  const action = request.action; delete request.action;
  const out = execFileSync(join(root, 'temp/tools/php'), [join(here, 'ajax-bridge.php'), JSON.stringify({ action, request })], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
  return Promise.resolve({ json: () => Promise.resolve(JSON.parse(out.slice(out.indexOf('\n') + 1))) });
};

window.eval(readFileSync(join(root, 'erfansanat/assets/js/project-filters.js'), 'utf8'));
document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));

const archive = document.querySelector('[data-projects-archive="true"]');
const grid = document.querySelector('[data-project-grid="true"]');
const countEl = archive.querySelector('[data-project-count="true"]');
check('شمار اولیه: چهار کارت پروژه رندر شده‌اند', grid.querySelectorAll('[data-project-card="true"], [data-project-item="true"], article').length >= 4, `got ${grid.children.length}`);

// SC-01: city checkbox → grid swaps without refresh; URL reflects state.
const isfBox = archive.querySelector(`[data-project-city-filter="${meta.isf}"]`);
isfBox.checked = true;
isfBox.dispatchEvent(new window.Event('change', { bubbles: true }));
await sleep(600);
check('SC-01: فیلتر اصفهان فقط پروژه‌های همان شهر را می‌دهد', grid.textContent.includes('میدان نوری DOM') && grid.textContent.includes('المان ورودی DOM') && !grid.textContent.includes('تونل نوری DOM'));
check('AC-02: شمارنده کل به‌روز می‌شود', countEl.textContent.trim() === '۲');
check('AC-02: شمارنده ترم‌ها facet می‌گیرد', archive.querySelector(`[data-project-term-count="project_type:${meta.type_a}"]`).textContent.trim() === '۱');
check('AC-01/URL: وضعیت شهر در URL می‌آید', window.location.search.includes(`es_pcities=${meta.isf}`));

// Combined with type.
const typeBox = archive.querySelector(`[data-project-type-filter="${meta.type_b}"]`);
typeBox.checked = true;
typeBox.dispatchEvent(new window.Event('change', { bubbles: true }));
await sleep(600);
check('ترکیب نوع+شهر فقط المان ورودی را می‌دهد', grid.querySelectorAll('[data-project-card="true"], [data-project-item="true"], article').length === 1 && grid.textContent.includes('المان ورودی DOM'));
check('وضعیت نوع در URL می‌آید', window.location.search.includes(`es_ptypes=${meta.type_b}`));

// SC-02: clear restores everything (cards, counters, URL).
archive.querySelector('[data-project-filters-clear="true"]').click();
await sleep(600);
check('SC-02: حذف فیلترها همه کارت‌ها را برمی‌گرداند', !isfBox.checked && !typeBox.checked && !window.location.search.includes('es_pcities') && !window.location.search.includes('es_ptypes') && countEl.textContent.trim() === '۴' && archive.querySelector(`[data-project-term-count="project_type:${meta.type_a}"]`).textContent.trim() === '۲');

// Empty state.
const search = archive.querySelector('[data-project-search="true"]');
search.value = 'چیزناموجود DOM';
search.dispatchEvent(new window.Event('input', { bubbles: true }));
await sleep(600);
const empty = archive.querySelector('[data-project-empty="true"]');
check('حالت خالی مرجع نمایش داده می‌شود', !empty.classList.contains('hidden') && grid.children.length === 0);

// Cleanup fixture data from the shared DB.
execFileSync(join(root, 'temp/tools/php'), [join(here, 'ajax-bridge.php'), JSON.stringify({ cleanup: { posts: meta.ids, terms: [[meta.type_a, 'project_type'], [meta.type_b, 'project_type'], [meta.isf, 'city'], [meta.kh, 'city']] } })], { cwd: root, encoding: 'utf8' });

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
window.close();
process.exitCode = fail ? 1 : 0;
