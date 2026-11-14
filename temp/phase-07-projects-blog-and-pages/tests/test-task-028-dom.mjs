#!/usr/bin/env node
/** P07-T028 — landing scroll-spy (navigation.js) + FAQ accordion (main.js) in JSDOM. */
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
const html = execFileSync(join(root, 'temp/tools/php'), [join(here, 'build-arya-fixture.php')], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
const dom = new JSDOM(html, { url: 'https://example.test/arya/', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom, { document } = window;
window.matchMedia ||= () => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} });
window.scrollTo ||= () => {};
window.HTMLElement.prototype.scrollIntoView ||= () => {};
// Capture the scroll-spy observer so section visibility can be simulated.
let spyCallback = null;
const observed = [];
window.IntersectionObserver = class {
  constructor(cb) { if (!spyCallback) spyCallback = cb; this.cb = cb; }
  observe(el) { observed.push(el); }
  unobserve() {} disconnect() {}
};
window.eval(readFileSync(join(root, 'erfansanat/assets/js/navigation.js'), 'utf8'));
window.eval(readFileSync(join(root, 'erfansanat/assets/js/main.js'), 'utf8'));
document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));

const links = [...document.querySelectorAll('[data-landing-link="true"]')];
check('AC-02: منوی لندینگ با ۵ لینک حاضر است', links.length === 5, `got ${links.length}`);
const targetsOk = links.every(a => document.getElementById(a.getAttribute('href').slice(1)));
check('SC-01: هر لینک منو سکشن متناظر دارد', targetsOk);
check('SC-01: scroll-spy سکشن‌های لینک‌شده را observe می‌کند', observed.some(el => el.id === 'specs') && observed.some(el => el.id === 'faq'));
const link = id => links.find(a => a.getAttribute('href') === `#${id}`);
spyCallback([{ isIntersecting: true, target: document.getElementById('specs') }]);
check('SC-01: ورود به مشخصات، لینک آن را active می‌کند', link('specs').classList.contains('is-active') && link('specs').getAttribute('aria-current') === 'true');
spyCallback([{ isIntersecting: true, target: document.getElementById('cta') }]);
check('SC-01: active به لینک CTA منتقل می‌شود', link('cta').classList.contains('is-active') && !link('specs').classList.contains('is-active') && !link('specs').hasAttribute('aria-current'));

const body = i => document.querySelector(`[data-faq-body="${i}"]`);
check('FAQ: آیتم اول باز، دوم بسته است', !body(0).classList.contains('hidden') && body(1).classList.contains('hidden'));
document.querySelector('[data-faq-toggle="1"]').click();
check('FAQ: کلیک پرسش دوم پاسخ آن را باز می‌کند', !body(1).classList.contains('hidden'));
document.querySelector('[data-faq-toggle="1"]').click();
check('FAQ: کلیک دوباره پاسخ را می‌بندد', body(1).classList.contains('hidden'));
console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
window.close();
process.exitCode = fail ? 1 : 0;
