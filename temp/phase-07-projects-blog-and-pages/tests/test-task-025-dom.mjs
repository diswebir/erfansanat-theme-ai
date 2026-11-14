#!/usr/bin/env node
/** P07-T025 — immutable main.js recruitment tab switching (SC-01). */
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
const html = execFileSync(join(root, 'temp/tools/php'), [join(here, 'build-recruit-fixture.php')], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
const dom = new JSDOM(html, { url: 'https://example.test/recruitment/', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom, { document } = window;
window.matchMedia ||= () => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} });
window.IntersectionObserver ||= class { observe() {} unobserve() {} disconnect() {} };
window.scrollTo ||= () => {};
window.HTMLElement.prototype.scrollIntoView ||= () => {};
window.eval(readFileSync(join(root, 'erfansanat/assets/js/main.js'), 'utf8'));
document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));

const btn = (k) => document.querySelector(`[data-recruit-tab-btn="${k}"]`);
const panel = (k) => document.querySelector(`[data-recruit-panel="${k}"]`);
const shown = (k) => !panel(k).classList.contains('hidden');
check('مرجع: ۴ دکمه و ۴ پنل حاضرند', document.querySelectorAll('[data-recruit-tab-btn]').length === 4 && document.querySelectorAll('[data-recruit-panel]').length === 4);
check('حالت اولیه: فقط R&D باز است', shown('rnd') && !shown('project') && !shown('accounting') && !shown('it'));
btn('it').click();
check('SC-01: کلیک IT پنل IT را باز می‌کند', shown('it') && !shown('rnd'));
check('SC-01: کلاس فعال به دکمه IT منتقل می‌شود', btn('it').classList.contains('bg-emerald-700') && btn('it').classList.contains('text-white') && !btn('rnd').classList.contains('bg-emerald-700'));
btn('accounting').click();
check('SC-01: سوئیچ به حسابداری', shown('accounting') && !shown('it'));
check('SC-01: محتوای پنل حسابداری درست است', panel('accounting').textContent.includes('کارشناس ارشد حسابداری صنعتی و پیمانکاری'));
btn('project').click();
check('SC-01: سوئیچ به پروژه (۲ موقعیت)', shown('project') && panel('project').querySelectorAll('article').length === 2);
btn('rnd').click();
check('SC-01: بازگشت به R&D', shown('rnd') && !shown('project'));
console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
window.close();
process.exitCode = fail ? 1 : 0;
