#!/usr/bin/env node
/** P07-T014 — immutable main.js initShareButtons copies the URL and swaps the label. */
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
const html = execFileSync(join(root, 'temp/tools/php'), [join(here, 'build-single-fixture.php'), 'headings'], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
const dom = new JSDOM(html, { url: 'https://example.test/blog/share-post/', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom, { document } = window;
window.matchMedia ||= () => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} });
window.IntersectionObserver ||= class { observe() {} unobserve() {} disconnect() {} };
window.scrollTo ||= () => {};
window.HTMLElement.prototype.scrollIntoView ||= () => {};
let copied = null;
Object.defineProperty(window.navigator, 'clipboard', { value: { writeText: (t) => { copied = t; return Promise.resolve(); } }, configurable: true });
window.eval(readFileSync(join(root, 'erfansanat/assets/js/main.js'), 'utf8'));
document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));

const btn = document.querySelector('[data-share-btn="true"]');
const label = document.querySelector('[data-share-label="true"]');
check('AC-03: دکمه/برچسب مرجع حاضرند', !!btn && label?.textContent.trim() === 'اشتراک‌گذاری مقاله');
btn.click();
check('SC-01: کلیک، URL جاری را کپی می‌کند', copied === window.location.href, String(copied));
check('SC-01: برچسب به تأیید کپی تغییر می‌کند', label.textContent.includes('کپی شد'), label.textContent);
const tg = document.querySelector('[data-share-network="telegram"]');
const shareUrl = document.querySelector('[data-share-url]').value;
check('SC-02: لینک تلگرام آدرس encode‌شده نوشته را دارد', tg?.href.startsWith('https://t.me/share/url?url=') && tg.href.includes('url=' + encodeURIComponent(shareUrl)), tg?.href);
check('AC-02: fallback لینک قابل انتخاب است', document.querySelector('[data-share-url]')?.readOnly === true && document.querySelector('[data-share-url]').value.length > 0);
await new Promise((r) => setTimeout(r, 2400));
check('برچسب پس از ۲.۲ ثانیه برمی‌گردد', label.textContent.trim() === 'اشتراک‌گذاری مقاله', label.textContent);
console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
window.close();
process.exitCode = fail ? 1 : 0;
