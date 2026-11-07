#!/usr/bin/env node
/** P07-T023 — main.js accordion open/close + faq-search.js filtering. */
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
const html = execFileSync(join(root, 'temp/tools/php'), [join(here, 'build-faq-fixture.php')], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
const dom = new JSDOM(html, { url: 'https://example.test/faq/', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom, { document } = window;
window.matchMedia ||= () => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} });
window.IntersectionObserver ||= class { observe() {} unobserve() {} disconnect() {} };
window.scrollTo ||= () => {};
window.HTMLElement.prototype.scrollIntoView ||= () => {};
window.eval(readFileSync(join(root, 'erfansanat/assets/js/main.js'), 'utf8'));
window.eval(readFileSync(join(root, 'erfansanat/assets/js/faq-search.js'), 'utf8'));
document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));

const items = [...document.querySelectorAll('[data-faq-item]')];
check('مرجع: ۶ آیتم آکاردئون حاضرند', items.length === 6, String(items.length));
const body0 = document.querySelector('[data-faq-body="0"]');
const body1 = document.querySelector('[data-faq-body="1"]');
const toggle1 = document.querySelector('[data-faq-toggle="1"]');
check('SC-01: آیتم اول باز، دوم بسته است', !body0.classList.contains('hidden') && body1.classList.contains('hidden'));
toggle1.click();
check('SC-01: کلیک، سوال دوم را باز می‌کند', !body1.classList.contains('hidden') && body1.classList.contains('block'));
const icon1 = document.querySelector('[data-faq-icon="1"]');
check('SC-01: آیکون به حالت باز می‌چرخد', icon1.classList.contains('rotate-45') && icon1.classList.contains('bg-emerald-800'));
toggle1.click();
check('SC-01: کلیک دوباره، سوال را می‌بندد', body1.classList.contains('hidden'));

const input = document.querySelector('[data-faq-search]');
const visible = () => items.filter((it) => !it.classList.contains('hidden')).length;
input.value = 'گارانتی';
input.dispatchEvent(new window.Event('input', { bubbles: true }));
check('SC-02: جستجو نتایج را فیلتر می‌کند', visible() < 6 && visible() > 0, String(visible()));
const empty = document.querySelector('[data-faq-empty]');
input.value = 'عبارت‌ناموجود‌xyz';
input.dispatchEvent(new window.Event('input', { bubbles: true }));
check('SC-02: جستجوی بی‌نتیجه پیام خالی نشان می‌دهد', visible() === 0 && !empty.classList.contains('hidden'), String(visible()));
input.value = '';
input.dispatchEvent(new window.Event('input', { bubbles: true }));
check('SC-02: پاک‌کردن جستجو همه را برمی‌گرداند', visible() === 6, String(visible()));
const count = document.querySelector('[data-faq-count]');
check('شمارنده به‌روز می‌شود', count && count.textContent.trim() === '۶', count?.textContent);
console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
window.close();
process.exitCode = fail ? 1 : 0;
