#!/usr/bin/env node
/** P08-T004 — search.js behavior: debounce, last-request-wins, keyboard, empty state. */
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

const html = execFileSync(join(root, 'temp/tools/php'), [join(here, 'build-search-fixture.php')], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
const dom = new JSDOM(html, { url: 'https://example.test/', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom, { document } = window;
window.matchMedia ||= () => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} });
window.IntersectionObserver ||= class { observe() {} unobserve() {} disconnect() {} };
window.scrollTo ||= () => {};
window.HTMLElement.prototype.scrollIntoView ||= () => {};
window.URLSearchParams = URLSearchParams;
window.AbortController = AbortController;

// Mock fetch: per-term controlled latency + payload; records aborts.
const calls = [];
window.ES_AJAX = { url: '/wp-admin/admin-ajax.php', nonces: { product_search: 'n1', content_search: 'n2' } };
window.fetch = (url, opts) => {
  const params = new URLSearchParams(opts.body);
  const action = params.get('action'), term = params.get('term');
  const call = { action, term, aborted: false };
  calls.push(call);
  const delay = term === 'کند' ? 120 : 10; // first query is slow, second fast.
  return new Promise((resolve, reject) => {
    const t = setTimeout(() => {
      const results = action === 'es_product_search'
        ? [{ id: 1, type: 'product', title: `محصول ${term}`, url: `/p/${encodeURIComponent(term)}/`, subtitle: 'دسته', image: '/img.jpg', true_sale: true }]
        : [{ id: 2, type: 'project', title: `پروژه ${term}`, url: `/j/${encodeURIComponent(term)}/`, subtitle: 'شهر', image: '/img.jpg' }];
      resolve({ json: () => Promise.resolve({ success: true, data: { results, total: 1, message: '' } }) });
    }, delay);
    opts.signal?.addEventListener('abort', () => { call.aborted = true; clearTimeout(t); const e = new Error('aborted'); e.name = 'AbortError'; reject(e); });
  });
};

window.eval(readFileSync(join(root, 'erfansanat/assets/js/search.js'), 'utf8'));
document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));

const input = document.querySelector('[data-global-search-input="true"]');
const products = document.querySelector('[data-global-search-products="true"]');
const projects = document.querySelector('[data-global-search-projects="true"]');
const type = async value => { input.value = value; input.dispatchEvent(new window.Event('input', { bubbles: true })); };
const key = k => input.dispatchEvent(new window.KeyboardEvent('keydown', { key: k, bubbles: true, cancelable: true }));

// SC-01: fast typing — slow first request aborted, only the last term shown.
await type('کند');
await sleep(300); // debounce fires, slow request in flight.
await type('تند');
await sleep(400); // second (fast) request resolved.
check('SC-01: فقط نتایج آخرین عبارت نمایش داده می‌شود', products.textContent.includes('محصول تند') && !products.textContent.includes('محصول کند'));
check('AC-02: درخواست قبلی abort شد', calls.some(c => c.term === 'کند' && c.aborted));
check('AC-01: ردیف‌ها data-search-item دارند', !!products.querySelector('[data-search-item]'));
check('نوع پروژه در ستون خودش می‌آید', projects.textContent.includes('پروژه تند'));

// SC-02: keyboard selects and Enter opens the row.
let opened = null;
projects.querySelector('a').addEventListener('click', e => { e.preventDefault(); opened = e.currentTarget.getAttribute('href'); });
const rowsCount = document.querySelectorAll('[data-search-result-row]').length;
key('ArrowDown'); // first row (product)
key('ArrowDown'); // second row (project)
const active = document.querySelector('[data-search-result-row][aria-current="true"]');
check('SC-02: ArrowDown ردیف را active می‌کند', !!active && active.getAttribute('data-search-result-row') === 'project');
key('Enter');
check('SC-02: Enter لینک ردیف فعال را باز می‌کند', opened !== null && opened.includes('/j/'));
key('ArrowUp');
check('AC-03: ArrowUp به ردیف قبل برمی‌گردد', document.querySelector('[data-search-result-row][aria-current="true"]')?.getAttribute('data-search-result-row') === 'product');
key('Escape');
check('AC-03: Escape حالت active را پاک می‌کند', !document.querySelector('[data-search-result-row][aria-current="true"]'));

// Debounce: rapid input events produce ONE request batch.
calls.length = 0;
await type('الف'); await type('الب'); await type('الپ');
await sleep(400);
check('debounce: سه تایپ سریع = یک بَچ درخواست', calls.filter(c => c.action === 'es_product_search').length === 1 && calls.every(c => c.term === 'الپ'));

// Short input restores the initial server-rendered rows.
await type('ا');
await sleep(50);
check('عبارت کوتاه: حالت اولیه سرور برمی‌گردد', !products.querySelector('[data-search-result-row]'));

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
window.close();
process.exitCode = fail ? 1 : 0;
