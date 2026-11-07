#!/usr/bin/env node
/** P08-T006 — product filters JSDOM: real endpoint via CLI bridge, URL state, clear. */
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

const html = execFileSync(join(root, 'temp/tools/php'), [join(here, 'build-shop-fixture.php')], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
const dom = new JSDOM(html, { url: 'https://example.test/shop/', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom, { document } = window;
const meta = JSON.parse(document.getElementById('es-fixture-meta').textContent);
window.matchMedia ||= () => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} });
window.IntersectionObserver ||= class { observe() {} unobserve() {} disconnect() {} };
window.scrollTo ||= () => {};
window.HTMLElement.prototype.scrollIntoView ||= () => {};
window.URLSearchParams = URLSearchParams;
window.AbortController = AbortController;
window.CustomEvent = window.CustomEvent || CustomEvent;
window.ES_AJAX = { url: '/wp-admin/admin-ajax.php', nonces: { product_filters: meta.nonce } };

// fetch → real endpoint through the PHP CLI bridge (synchronous under the hood).
window.fetch = (url, opts) => {
  const params = new URLSearchParams(opts.body);
  const request = Object.fromEntries(params.entries());
  const action = request.action; delete request.action;
  const out = execFileSync(join(root, 'temp/tools/php'), [join(here, 'ajax-bridge.php'), JSON.stringify({ action, request })], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
  const json = out.slice(out.indexOf('\n') + 1);
  return Promise.resolve({ json: () => Promise.resolve(JSON.parse(json)) });
};

window.eval(readFileSync(join(root, 'erfansanat/assets/js/product-filters.js'), 'utf8'));
document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));

const grid = document.querySelector('[data-product-grid="true"]');
const archive = document.querySelector('[data-products-archive="true"]');
const box = id => archive.querySelector(`[data-product-cat-filter="${id}"]`);
const initialCount = grid.querySelectorAll('[data-product-item="true"]').length;
check('شمار اولیه: هر سه کارت رندر شده‌اند', initialCount === 3, `got ${initialCount}`);

// SC-01: check category → grid swaps without refresh, URL reflects state.
box(meta.cats[0]).checked = true;
box(meta.cats[0]).dispatchEvent(new window.Event('change', { bubbles: true }));
await sleep(600);
const titles = grid.textContent;
check('SC-01: انتخاب دسته کارت‌ها را فیلتر می‌کند', titles.includes('ریسه سوزنی DOM') && !titles.includes('درخت نوری DOM'));
check('AC-03: کارت مرجع حفظ شده (data-product-item)', !!grid.querySelector('[data-product-item="true"]'));
check('AC-02: وضعیت در URL قابل اشتراک است', window.location.search.includes(`es_cats=${meta.cats[0]}`));
check('شمارنده به‌روز می‌شود', archive.querySelector('[data-product-count="true"]').textContent.trim() === '۲');

// SC-02: clear button restores the initial state and URL.
archive.querySelector('[data-product-filters-clear="true"]').click();
await sleep(600);
check('SC-02: حذف فیلترها همه کارت‌ها را برمی‌گرداند', grid.querySelectorAll('[data-product-item="true"]').length === 3);
check('SC-02: چک‌باکس‌ها پاک و URL تمیز می‌شود', !box(meta.cats[0]).checked && !window.location.search.includes('es_cats'));

// Search input drives the same pipeline (debounced).
const search = archive.querySelector('[data-product-search="true"]');
search.value = 'درخت نوری DOM';
search.dispatchEvent(new window.Event('input', { bubbles: true }));
await sleep(600);
check('جستجوی متنی گرید را فیلتر می‌کند', grid.textContent.includes('درخت نوری DOM') && !grid.textContent.includes('ریسه سوزنی DOM') && window.location.search.includes('es_q='));

// Empty state visible for no matches.
search.value = 'چیزناموجود DOM';
search.dispatchEvent(new window.Event('input', { bubbles: true }));
await sleep(600);
const empty = archive.querySelector('[data-product-empty="true"]');
check('حالت خالی مرجع نمایش داده می‌شود', !empty.classList.contains('hidden') && grid.querySelectorAll('[data-product-item="true"]').length === 0);

// Cleanup fixture data from the shared DB.
execFileSync(join(root, 'temp/tools/php'), [join(here, 'ajax-bridge.php'), JSON.stringify({ cleanup: { posts: meta.ids, terms: meta.cats.map(c => [c, 'product_cat']) } })], { cwd: root, encoding: 'utf8' });

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
window.close();
process.exitCode = fail ? 1 : 0;
