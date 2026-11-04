// Execute the protected frontend bundle against either the static reference or
// the full PHP-WASM mock render from Task 5.19. No production JS is rewritten.
// Usage: node tools/harness/jscheck.mjs [--mock | html-file] [main-js-file]
import fs from 'node:fs';
import path from 'node:path';
import { createHash } from 'node:crypto';
import { fileURLToPath } from 'node:url';
import { JSDOM, VirtualConsole } from 'jsdom';
import { runPhp } from './fetch.mjs';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const args = process.argv.slice(2);
const useMock = args[0] === '--mock';
const htmlArg = useMock ? '' : (args[0] || 'reference/static-mirror/index.html');
const jsArg = useMock ? (args[1] || 'theme/assets/js/main.js') : (args[1] || 'theme/assets/js/main.js');
const jsPath = path.resolve(root, jsArg);
let html;
let htmlLabel;
let phpTestOutput = '';
let mockQueryReport = null;
const sha256 = (value) => createHash('sha256').update(value).digest('hex');

if (useMock) {
  const requestPath = path.join(root, 'tools/harness/jscheck-export.request');
  const marker = '<<<ES_HTML_BASE64>>>';
  const endMarker = '<<<END_ES_HTML>>>';
  fs.writeFileSync(requestPath, 'export homepage HTML for jsdom');
  try {
    const result = await runPhp(path.join(root, 'temp/phase-05-homepage-and-sections/tests/test-task-19.php'));
    phpTestOutput = result.out;
    const start = result.out.indexOf(marker);
    const end = result.out.indexOf(endMarker, start + marker.length);
    if (result.code !== 0 || start < 0 || end < 0) {
      throw new Error(`Task 5.19 mock render export failed (exit ${result.code}).\n${result.err}\n${result.out.slice(-1500)}`);
    }
    html = Buffer.from(result.out.slice(start + marker.length, end), 'base64').toString('utf8');
    htmlLabel = 'Task 5.19 PHP-WASM mock homepage render';
    const queryReportPath = path.join(root, 'tools/harness/home-query-report.json');
    if (fs.existsSync(queryReportPath)) {
      mockQueryReport = JSON.parse(fs.readFileSync(queryReportPath, 'utf8'));
    }
    if (!mockQueryReport || mockQueryReport.html_sha256 !== sha256(html)) {
      throw new Error('PHP-WASM query report does not match the exported homepage HTML.');
    }
  } finally {
    if (fs.existsSync(requestPath)) fs.unlinkSync(requestPath);
  }
} else {
  const htmlPath = path.resolve(root, htmlArg);
  html = fs.readFileSync(htmlPath, 'utf8');
  htmlLabel = path.relative(root, htmlPath);
}

const source = fs.readFileSync(jsPath, 'utf8');
const errors = [];
const virtualConsole = new VirtualConsole();
virtualConsole.on('jsdomError', (error) => errors.push(`jsdom: ${error.message}`));
const dom = new JSDOM(html, {
  url: 'https://example.test/',
  runScripts: 'outside-only',
  pretendToBeVisual: true,
  virtualConsole,
});
const { window } = dom;
const interactionChecks = {};
window.matchMedia = (query) => ({
  matches: false,
  media: query,
  onchange: null,
  addListener() {},
  removeListener() {},
  addEventListener() {},
  removeEventListener() {},
  dispatchEvent() { return false; },
});
window.IntersectionObserver = class {
  constructor(callback) { this.callback = callback; }
  observe(target) { this.callback([{ target, isIntersecting: true, intersectionRatio: 1 }], this); }
  unobserve() {}
  disconnect() {}
};
window.ResizeObserver = class { observe() {} unobserve() {} disconnect() {} };
window.scrollTo = () => {};
window.HTMLElement.prototype.scrollIntoView = function () {};
window.requestAnimationFrame = (callback) => window.setTimeout(() => callback(window.performance.now()), 0);
window.cancelAnimationFrame = (id) => window.clearTimeout(id);
window.HTMLMediaElement.prototype.play = function () { return Promise.resolve(); };
window.HTMLMediaElement.prototype.pause = function () {};
window.addEventListener('error', (event) => errors.push(`window: ${event.message || event.error || 'runtime error'}`));
window.addEventListener('unhandledrejection', (event) => errors.push(`promise: ${event.reason}`));

try {
  window.eval(source);
  window.document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));

  const serviceRow = window.document.querySelector('[data-service-row]');
  const serviceTitle = window.document.querySelector('[data-service-preview-title="true"]');
  if (serviceRow) serviceRow.dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  interactionChecks.services = Boolean(serviceRow && serviceTitle && serviceTitle.textContent === serviceRow.getAttribute('data-service-title'));

  const tunnelTab = [...window.document.querySelectorAll('[data-home2-port-tab]')]
    .find((tab) => tab.getAttribute('data-home2-port-tab') === 'tunnel');
  if (tunnelTab) tunnelTab.dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  const tunnelPanel = window.document.querySelector('[data-home2-port-panel="tunnel"]');
  interactionChecks.gallery = Boolean(tunnelTab?.getAttribute('aria-selected') === 'true' && tunnelPanel?.classList.contains('grid') && !tunnelPanel?.classList.contains('hidden'));

  const next = window.document.querySelector('[data-home2-history-next="true"]');
  if (next) next.dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  const historyBtn0 = window.document.querySelector('[data-home2-history-btn="0"]');
  const historyBtn1 = window.document.querySelector('[data-home2-history-btn="1"]');
  const historyPanel0 = window.document.querySelector('[data-home2-history-panel="0"]');
  const historyPanel1 = window.document.querySelector('[data-home2-history-panel="1"]');
  interactionChecks.historyNext = Boolean(historyBtn1?.getAttribute('aria-selected') === 'true' && historyPanel1?.classList.contains('grid') && !historyPanel1?.classList.contains('hidden'));

  const previous = window.document.querySelector('[data-home2-history-prev="true"]');
  if (previous) previous.dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  interactionChecks.historyPrevious = Boolean(historyBtn0?.getAttribute('aria-selected') === 'true' && historyPanel0?.classList.contains('grid') && !historyPanel0?.classList.contains('hidden'));

  const historyStage = window.document.querySelector('[data-history-stage="true"]') || window.document.querySelector('[data-history-scroll-track="true"]');
  if (historyStage) historyStage.getBoundingClientRect = () => ({ top: 100, bottom: 500, left: 0, right: 800, width: 800, height: 400 });
  const originalNow = window.Date.now;
  const wheelTime = originalNow() + 1000;
  window.Date.now = () => wheelTime;
  const wheel = new window.WheelEvent('wheel', { deltaY: 100, bubbles: true, cancelable: true });
  window.dispatchEvent(wheel);
  window.Date.now = originalNow;
  interactionChecks.historyWheel = Boolean(historyBtn1?.getAttribute('aria-selected') === 'true' && historyPanel1?.classList.contains('grid') && wheel.defaultPrevented);

  for (const [name, passed] of Object.entries(interactionChecks)) if (!passed) errors.push(`interaction failed: ${name}`);
} catch (error) {
  errors.push(`execute: ${error?.stack || error}`);
}

await new Promise((resolve) => window.setTimeout(resolve, 10));
if (useMock && window.document.querySelectorAll('section').length < 15) {
  errors.push(`expected >=15 homepage sections, found ${window.document.querySelectorAll('section').length}`);
}
const sectionSimilarities = [];
if (useMock) {
  const referenceDom = new JSDOM(fs.readFileSync(path.join(root, 'reference/static-mirror/index.html'), 'utf8'));
  const baselineDom = new JSDOM(html);
  const referenceSections = [...referenceDom.window.document.querySelectorAll('section')];
  const actualSections = [...baselineDom.window.document.querySelectorAll('section')];
  const collapseDynamicGalleryCards = (section) => {
    for (const panel of section.querySelectorAll('[data-home2-port-panel]')) {
      const links = [...panel.querySelectorAll('a')];
      const cards = links.filter((link) => !links.some((candidate) => candidate !== link && candidate.contains(link)));
      for (const duplicate of cards.slice(1)) duplicate.remove();
    }
  };
  // Card cardinality is deliberately dynamic and is tested independently in Task 5.4.
  referenceSections.forEach(collapseDynamicGalleryCards);
  actualSections.forEach(collapseDynamicGalleryCards);
  const signature = (element) => [element, ...element.querySelectorAll('*')].map((node) => {
    const classes = [...node.classList].sort().join(' ');
    const attrs = ['id', 'role', 'aria-label', 'aria-hidden', 'aria-expanded']
      .filter((name) => node.hasAttribute(name)).map((name) => `${name}:${node.getAttribute(name)}`).sort().join(';');
    return `${node.tagName.toLowerCase()}|${classes ? `class:${classes}` : ''}${attrs ? `;${attrs}` : ''}`;
  }).sort();
  const similarity = (expected, actual) => {
    const left = signature(expected); const right = signature(actual);
    const counts = new Map(); for (const item of right) counts.set(item, (counts.get(item) || 0) + 1);
    let matched = 0;
    for (const item of left) if (counts.get(item) > 0) { matched++; counts.set(item, counts.get(item) - 1); }
    return Number((100 * matched / Math.max(left.length, right.length, 1)).toFixed(2));
  };
  for (let i = 0; i < Math.min(referenceSections.length, actualSections.length); i++) {
    sectionSimilarities.push(similarity(referenceSections[i], actualSections[i]));
  }
  referenceDom.window.close();
  baselineDom.window.close();
}
const phpMatch = useMock ? phpTestOutput.match(/PASS\s*=\s*(\d+)\s+FAIL\s*=\s*(\d+)\s+PENDING\s*=\s*(\d+)/) : null;
const phpMockAssertions = phpMatch ? { pass: Number(phpMatch[1]), fail: Number(phpMatch[2]), pending: Number(phpMatch[3]) } : null;
if (useMock && ( !phpMockAssertions || phpMockAssertions.fail !== 0 || phpMockAssertions.pending !== 0 )) {
  errors.push('Task 5.19 PHP-WASM integration checks did not all pass.');
}
const summary = {
  html: htmlLabel,
  script: path.relative(root, jsPath),
  bytes: html.length,
  homeSections: window.document.querySelectorAll('section').length,
  htmlSha256: sha256(html),
  mainJsSha256: sha256(source),
  phpMockAssertions,
  queryReport: mockQueryReport,
  sectionSimilarities,
  interactionChecks,
  errors,
};
if (useMock) {
  summary.generatedAt = new Date().toISOString();
  fs.writeFileSync(path.join(root, 'tools/harness/jscheck-report.json'), JSON.stringify(summary, null, 2));
}
console.log(JSON.stringify(summary, null, 2));
dom.window.close();
if (errors.length) process.exit(1);
process.exit(0);
