/**
 * P06-T022 DOM behaviour test — real main.js cart-page qty/remove recalc.
 * Run: node temp/phase-06-woocommerce-and-products/tests/test-task-022-dom.mjs
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
const fixture = execFileSync(php, [path.join(here, 'build-task-022-fixture.php')], { cwd: root, encoding: 'utf8' });
const mainJs = readFileSync(path.join(root, 'erfansanat/assets/js/main.js'), 'utf8');

const dom = new JSDOM(fixture, { url: 'http://localhost/cart/', runScripts: 'outside-only', pretendToBeVisual: true });
const { window } = dom;
window.matchMedia = window.matchMedia || (() => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} }));
window.IntersectionObserver = window.IntersectionObserver || class { observe() {} unobserve() {} disconnect() {} };
window.requestAnimationFrame = window.requestAnimationFrame || ((cb) => setTimeout(cb, 0));
window.scrollTo = window.scrollTo || (() => {});
window.localStorage.clear();

window.eval(mainJs);
if (window.document.readyState === 'loading') {
  window.document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));
}

const doc = window.document;
const cartPage = doc.querySelector('[data-cart-page="true"]');
const rows = Array.from(cartPage.querySelectorAll('[data-cart-item-row]'));
const FA = Array.from({ length: 10 }, (_, i) => String.fromCodePoint(0x6f0 + i)).join('');
const fa = (n) => String(n).replace(/\d/g, (d) => FA[Number(d)]);
const visibleRows = () => rows.filter((r) => !r.classList.contains('hidden'));

check('AC-02: صفحه سبد و دو ردیف', !!cartPage && rows.length === 2);
// main.js initializes qty to min (p1 min=5, p2 min=1).
check('SC-01: مقدار اولیه ردیف اول = حداقل (۵)', rows[0].getAttribute('data-current-qty') === '5');
check('SC-01: جمع ردیف اول (۵۰,۰۰ تومان)', rows[0].querySelector('[data-cart-row-total]').textContent.includes(fa('500,000')));

// Increase qty of row 1 (step 5 → 10).
rows[0].querySelector('[data-cart-qty-inc]').click();
check('SC-01: افزایش تعداد → ۱۰', rows[0].getAttribute('data-current-qty') === '10');
check('SC-01: جمع ردیف به‌روز شد (۱,۰۰۰,۰۰)', rows[0].querySelector('[data-cart-row-total]').textContent.includes(fa('1,000,000')));

// Decrease back to 5.
rows[0].querySelector('[data-cart-qty-dec]').click();
check('SC-01: کاهش تعداد → ۵', rows[0].getAttribute('data-current-qty') === '5');

// Remove row 2 → count 1, totals recalc.
rows[1].querySelector('[data-cart-remove]').click();
check('SC-01: حذف ردیف دوم آن را پنهان می‌کند', rows[1].classList.contains('hidden'));
check('SC-01: شمارنده اقلام = ۱', cartPage.querySelector('[data-cart-count="true"]').textContent === fa(1));

// Clear cart → empty state shown.
cartPage.querySelector('[data-cart-clear="true"]').click();
check('SC-01: خالی کردن سبد → همه ردیف‌ها پنهان', visibleRows().length === 0);
check('SC-01: حالت خالی نمایش داده شد', !cartPage.querySelector('[data-cart-empty="true"]').classList.contains('hidden'));

console.log(`\n  RESULT: ${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
