#!/usr/bin/env node
/** P07-T013 — TOC anchors resolve to real rendered headings in the full single DOM. */
import { execFileSync } from 'node:child_process';
import { createRequire } from 'node:module';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const root = join(here, '../../..');
const require = createRequire(join(root, 'temp/tools/package.json'));
const { JSDOM } = require('jsdom');
let pass = 0, fail = 0;
const check = (name, ok, detail = '') => { ok ? pass++ : fail++; console.log(`  [${ok ? 'PASS' : 'FAIL'}] ${name}${ok ? '' : ' — ' + detail}`); };
const build = (mode) => execFileSync(join(root, 'temp/tools/php'), [join(here, 'build-single-fixture.php'), mode], { cwd: root, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
const open = (html) => { const dom = new JSDOM(html, { url: 'https://example.test/blog/post/', runScripts: 'outside-only' }); return dom; };

const dom = open(build('headings'));
const { document, window } = dom.window ? { document: dom.window.document, window: dom.window } : {};
const toc = document.querySelector('nav[aria-label="سرفصل‌های مقاله"]');
const links = toc ? [...toc.querySelectorAll('a[href^="#"]')] : [];
check('AC-01: چهار هدینگ معتبر h2/h3 فهرست شدند', links.length === 4, String(links.length));
const ids = links.map((a) => a.getAttribute('href').slice(1));
check('AC-02: هر لنگر هدف یکتا در DOM دارد', new Set(ids).size === ids.length && ids.every((id) => document.getElementById(id)?.matches('h2,h3')));
if (links[0]) {
  let prevented = false;
  links[0].addEventListener('click', (e) => { prevented = e.defaultPrevented; }, { capture: true });
  links[0].dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  check('SC-01: کلیک لنگر جلوگیری نمی‌شود و هدف واقعی دارد', !prevented && links[0].getAttribute('href') === '#' + ids[0] && document.getElementById(ids[0])?.tagName === 'H2');
} else { check('SC-01: کلیک لنگر جلوگیری نمی‌شود و هدف واقعی دارد', false); }
check('h3 با تورفتگی نمایش داده می‌شود', links.some((a) => a.classList.contains('pr-3') && document.getElementById(a.getAttribute('href').slice(1))?.tagName === 'H3'));
dom.window.close();

const none = open(build('none'));
check('AC-03/SC-02: مقاله بدون هدینگ TOC خالی نمی‌دهد', !none.window.document.querySelector('nav[aria-label="سرفصل‌های مقاله"]') && !none.window.document.body.textContent.includes('سرفصل‌های مقاله'));
none.window.close();

const protectedDom = open(build('protected'));
check('نوشته رمزدار TOC ندارد و فرم رمز دارد', !protectedDom.window.document.querySelector('nav[aria-label="سرفصل‌های مقاله"]') && !!protectedDom.window.document.querySelector('.post-password-form'));
protectedDom.window.close();

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exitCode = fail ? 1 : 0;
