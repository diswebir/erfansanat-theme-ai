/**
 * ADMIN-PRO DOM test — real options.js: link fields save as {url,label,target}
 * (regression for the data-loss bug) + unsaved-changes guard.
 * Run: node temp/phase-02-custom-admin-dashboard/tests/test-admin-dashboard-pro-dom.mjs
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
const fixture = execFileSync(php, [path.join(here, 'build-admin-pro-fixture.php')], { cwd: root, encoding: 'utf8' });
const optionsJs = readFileSync(path.join(root, 'erfansanat/assets/admin/options.js'), 'utf8');

const dom = new JSDOM(fixture, { url: 'http://localhost/wp-admin/admin.php?page=erfansanat-dashboard', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom;

// Capture the AJAX save payload.
let captured = null;
window.ES_OPTIONS = { ajaxUrl: '/wp-admin/admin-ajax.php', nonce: 'testnonce', action: 'es_save_options' };
window.fetch = (url, opts) => {
  captured = opts && opts.body ? String(opts.body) : null;
  return Promise.resolve({
    status: 200,
    json: () => Promise.resolve({ success: true, data: { message: 'تغییرات ذخیره شد.' } })
  });
};

window.eval(optionsJs);

const doc = window.document;
const dashboard = doc.querySelector('[data-es-dashboard]');
const group = dashboard.querySelector('[data-es-link-key="footer_link"]');
check('فیلد لینک با گروه رندر شد', !!group);

// Fill the link fields.
group.querySelector('[data-es-link-url]').value = 'https://new.example/page';
group.querySelector('[data-es-link-label]').value = 'لینک تازه';
group.querySelector('[data-es-link-target]').checked = true;

// Dirty guard: an input event marks the form dirty and beforeunload is prevented.
group.querySelector('[data-es-link-url]').dispatchEvent(new window.Event('input', { bubbles: true }));
const beforeUnload = new window.Event('beforeunload', { bubbles: true, cancelable: true });
window.dispatchEvent(beforeUnload);
check('هشدار تغییرات ذخیره‌نشده فعال است', beforeUnload.defaultPrevented === true);

// Save and inspect the collected payload.
dashboard.querySelector('[data-es-save]').click();

setTimeout(() => {
  let values = {};
  try {
    const params = new URLSearchParams(captured || '');
    values = JSON.parse(params.get('values') || '{}');
  } catch (e) { /* captured stays null */ }

  const link = values.footer_link;
  check('ذخیره: لینک به‌صورت آبجکت جمع شد', !!link && typeof link === 'object' && !Array.isArray(link));
  check('باگ لینک رفع شد: url حفظ شد', link && link.url === 'https://new.example/page');
  check('باگ لینک رفع شد: label حفظ شد', link && link.label === 'لینک تازه');
  check('باگ لینک رفع شد: target=_blank حفظ شد', link && link.target === '_blank');
  check('فیلد متنی معمولی همچنان ذخیره می‌شود', values.brand_name === 'عرفان صنعت');

  console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
  process.exit(fail ? 1 : 0);
}, 30);
