"use strict";

const assert = require("node:assert/strict");
const fs = require("node:fs");
const path = require("node:path");
process.env.AWS_EXECUTION_ENV = process.env.AWS_EXECUTION_ENV || "AWS_Lambda_nodejs22.x";
const chromiumPackage = require("@sparticuz/chromium");
const chromium = chromiumPackage.default;
const puppeteer = require("puppeteer-core");

const root = path.resolve(__dirname, "../../..");
const html = `<!doctype html>
<html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>فرم تماس</title></head>
<body class="bg-slate-50">
  <main class="mx-auto max-w-3xl px-4 py-8">
    <section class="rounded-3xl border border-emerald-100 bg-white p-5 sm:p-8">
      <h1 class="text-xl font-black">فرم تماس</h1>
      <form action="/wp-admin/admin-ajax.php" method="post" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2" data-ese-submission-form="true" data-form-type="contact">
        <input type="hidden" name="action" value="ese_submit_form"><input type="hidden" name="form_type" value="contact"><input type="hidden" name="form_nonce" value="browser-form-nonce">
        <div><label for="browser-name">نام *</label><input id="browser-name" name="name" required value="Browser QA" class="w-full rounded-xl border p-3"></div>
        <div><label for="browser-phone">تلفن *</label><input id="browser-phone" name="phone" required value="09123456789" class="w-full rounded-xl border p-3"></div>
        <div class="sm:col-span-2"><label for="browser-message">پیام *</label><textarea id="browser-message" name="message" required minlength="8" class="w-full rounded-xl border p-3">Please contact me about a project.</textarea></div>
        <input name="website" value="" tabindex="-1" autocomplete="off" class="sr-only" aria-hidden="true">
        <div class="sm:col-span-2"><button type="submit" class="rounded-xl bg-emerald-800 px-6 py-3 text-white">ارسال</button></div>
      </form>
      <div class="hidden mt-4 rounded-xl bg-emerald-50 p-4" role="status" aria-live="polite" data-ese-form-success="true">ثبت شد<button type="button" data-ese-form-reset="true">ارسال دوباره</button></div>
      <p class="hidden mt-4 rounded-xl bg-red-50 p-4" role="alert" data-ese-form-error="true"></p>
    </section>
  </main>
</body></html>`;

let browser;
(async () => {
  await chromiumPackage.inflate(path.join(root, "node_modules/@sparticuz/chromium/bin/al2023.tar.br"));
  const executablePath = await chromium.executablePath();
  browser = await puppeteer.launch({
    args: chromium.args,
    defaultViewport: { width: 390, height: 844, deviceScaleFactor: 1 },
    executablePath,
    headless: true,
  });
  const page = await browser.newPage();
  const pageErrors = [];
  page.on("pageerror", (error) => pageErrors.push(error.message));
  await page.setContent(html, { waitUntil: "domcontentloaded" });
  await page.addStyleTag({ path: path.join(root, "assets/css/styles.css") });
  await page.evaluate(() => {
    window.eseAjax = { ajaxUrl: "https://example.test/wp-admin/admin-ajax.php", nonce: "public-nonce" };
    window.__eseFailNext = false;
    window.fetch = async (url, options) => {
      window.__eseRequest = { url, method: options.method, body: options.body };
      if (window.__eseFailNext) {
        window.__eseFailNext = false;
        return { ok: true, json: async () => ({ success: false, data: { message: "private server data" } }) };
      }
      return { ok: true, json: async () => ({ success: true, data: { message: "درخواست با موفقیت ثبت شد." } }) };
    };
  });
  await page.addScriptTag({ path: path.join(root, "assets/js/main.js") });
  await page.addScriptTag({ path: require.resolve("axe-core") });
  const mobileLayout = await page.evaluate(() => ({
    width: window.innerWidth,
    scrollWidth: document.documentElement.scrollWidth,
    columns: getComputedStyle(document.querySelector("form")).gridTemplateColumns.split(" ").length,
    labels: [...document.querySelectorAll("form input[name], form textarea[name]")].every((field) =>
      field.type === "hidden" || field.name === "website" || !!document.querySelector(`label[for="${field.id}"]`)
    ),
  }));
  assert.equal(mobileLayout.width, 390);
  assert.ok(mobileLayout.scrollWidth <= mobileLayout.width, `mobile layout overflows: ${JSON.stringify(mobileLayout)}`);
  assert.equal(mobileLayout.columns, 1, "mobile form should use one column");
  assert.equal(mobileLayout.labels, true, "visible controls should have associated labels");
  const formA11y = await page.evaluate(async () => {
    const result = await axe.run(document, { runOnly: { type: "tag", values: ["wcag2a", "wcag2aa", "wcag21a", "wcag21aa"] } });
    return result.violations.map(({ id, impact, nodes }) => ({ id, impact, targets: nodes.map((node) => node.target) }));
  });
  assert.deepEqual(formA11y, [], `form WCAG accessibility violations: ${JSON.stringify(formA11y)}`);

  await page.locator("form button[type=submit]").click();
  await page.waitForFunction(() => document.querySelector("form").classList.contains("hidden"));
  const savedState = await page.evaluate(() => ({
    success: !document.querySelector('[data-ese-form-success="true"]').classList.contains("hidden"),
    request: window.__eseRequest,
  }));
  assert.equal(savedState.success, true);
  assert.equal(savedState.request.url, "https://example.test/wp-admin/admin-ajax.php");
  assert.equal(savedState.request.method, "POST");
  const requestBody = new URLSearchParams(savedState.request.body);
  assert.equal(requestBody.get("action"), "ese_submit_form");
  assert.equal(requestBody.get("form_type"), "contact");
  assert.equal(requestBody.get("form_nonce"), "browser-form-nonce");
  assert.equal(requestBody.get("nonce"), "public-nonce");

  await page.locator('[data-ese-form-reset="true"]').click();
  assert.equal(await page.$eval("form", (form) => form.classList.contains("hidden")), false);
  await page.evaluate(() => { window.__eseFailNext = true; });
  await page.locator("form button[type=submit]").click();
  await page.waitForFunction(() => !document.querySelector('[data-ese-form-error="true"]').classList.contains("hidden"));
  const errorText = await page.$eval('[data-ese-form-error="true"]', (element) => element.textContent);
  assert.match(errorText, /دوباره تلاش کنید/);
  assert.doesNotMatch(errorText, /private server data/);

  await page.setViewport({ width: 1280, height: 900, deviceScaleFactor: 1 });
  const desktopLayout = await page.evaluate(() => ({
    width: window.innerWidth,
    scrollWidth: document.documentElement.scrollWidth,
    columns: getComputedStyle(document.querySelector("form")).gridTemplateColumns.split(" ").length,
  }));
  assert.equal(desktopLayout.width, 1280);
  assert.ok(desktopLayout.scrollWidth <= desktopLayout.width, `desktop layout overflows: ${JSON.stringify(desktopLayout)}`);
  assert.equal(desktopLayout.columns, 2, "desktop form should use two columns");

  const adminFontCss = fs.readFileSync(path.join(root, "assets/css/admin-vazirmatn.css"), "utf8").replace(
    /url\([\"']\.\.\/fonts\/([^\"']+)[\"']\)/g,
    (_match, fontFile) => `url(data:font/woff2;base64,${fs.readFileSync(path.join(root, "assets/fonts", fontFile)).toString("base64")})`
  );
  await page.setContent('<!doctype html><html lang="fa" dir="rtl"><head><title>پیشخوان آزمایشی</title></head><body class="wp-admin"><main id="admin-copy">پنل پیشخوان فارسی</main><span id="admin-icon" class="dashicons">\uF111</span><div class="editor-styles-wrapper"><p id="editor-copy">متن ویرایشگر فارسی</p></div></body></html>');
  await page.addStyleTag({ content: '.dashicons { font-family: dashicons; }' });
  await page.addStyleTag({ content: adminFontCss });
  await page.addScriptTag({ path: require.resolve("axe-core") });
  const adminTypography = await page.evaluate(async () => {
    await document.fonts.load('400 16px Vazirmatn', 'پنل فارسی');
    return {
      adminFamily: getComputedStyle(document.querySelector('#admin-copy')).fontFamily,
      editorFamily: getComputedStyle(document.querySelector('#editor-copy')).fontFamily,
      iconFamily: getComputedStyle(document.querySelector('#admin-icon')).fontFamily,
      fontLoaded: document.fonts.check('400 16px Vazirmatn', 'پنل فارسی'),
    };
  });
  assert.match(adminTypography.adminFamily, /Vazirmatn/);
  assert.match(adminTypography.editorFamily, /Vazirmatn/);
  assert.equal(adminTypography.iconFamily, "dashicons", "admin font must preserve WordPress icon fonts");
  assert.equal(adminTypography.fontLoaded, true, "local Vazirmatn font should load Persian glyphs");
  const adminA11y = await page.evaluate(async () => {
    const result = await axe.run(document, { runOnly: { type: "tag", values: ["wcag2a", "wcag2aa", "wcag21a", "wcag21aa"] } });
    return result.violations.map(({ id, impact, nodes }) => ({ id, impact, targets: nodes.map((node) => node.target) }));
  });
  assert.deepEqual(adminA11y, [], `admin/editor WCAG accessibility violations: ${JSON.stringify(adminA11y)}`);

  const dashboardHtml = `<!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>پیشخوان</title></head><body class="wp-admin"><main id="ese-admin-overview"><div class="postbox-header"><h2>نمای کلی عرفان صنعت</h2></div><div class="inside"><section class="ese-dashboard-overview" dir="rtl" lang="fa"><header class="ese-dashboard-hero"><div class="ese-dashboard-hero-copy"><p class="ese-dashboard-eyebrow">مدیریت وب‌سایت</p><h2>خوش آمدید، مدیر سایت</h2><p>وضعیت محتوا و دسترسی‌های پرکاربرد</p></div><span class="ese-dashboard-brand" aria-hidden="true">ES</span></header><div class="ese-dashboard-stats" aria-label="آمار وب‌سایت"><article class="ese-dashboard-stat"><span class="ese-dashboard-stat-icon dashicons dashicons-products" aria-hidden="true"></span><div><p>محصولات منتشرشده</p><strong>۱۲</strong><small>مدیریت کاتالوگ فروشگاه</small></div></article><article class="ese-dashboard-stat"><span class="ese-dashboard-stat-icon dashicons dashicons-portfolio" aria-hidden="true"></span><div><p>پروژه‌های منتشرشده</p><strong>۸</strong><small>نمونه‌کارهای سایت</small></div></article><article class="ese-dashboard-stat"><span class="ese-dashboard-stat-icon dashicons dashicons-email-alt" aria-hidden="true"></span><div><p>درخواست‌های دریافتی</p><strong>۴</strong><small>صندوق درخواست‌ها</small></div></article></div><div class="ese-dashboard-workspace"><section class="ese-dashboard-panel" aria-labelledby="recent-title"><div class="ese-dashboard-section-title"><h3 id="recent-title">محتوای تازه</h3><p>آخرین موارد قابل ویرایش</p></div><ul class="ese-dashboard-recent-list"><li><span class="dashicons dashicons-portfolio" aria-hidden="true"></span><span class="ese-dashboard-recent-copy"><a href="#project">پروژهٔ آزمایشی</a><small>پروژه · ۱۴۰۵/۰۸/۱۳</small></span></li></ul></section><section class="ese-dashboard-panel" aria-labelledby="inbox-title"><div class="ese-dashboard-section-title"><h3 id="inbox-title">درخواست‌های جدید</h3><a class="ese-dashboard-panel-link" href="#inbox">مشاهده صندوق</a></div><ul class="ese-dashboard-inbox-list"><li><a href="#request"><strong>درخواست آزمایشی</strong><small>۱۴۰۵/۰۸/۱۳</small></a><span class="ese-dashboard-new-badge">جدید</span></li></ul></section></div><div class="ese-dashboard-actions"><div class="ese-dashboard-section-title"><h3>دسترسی سریع</h3><p>مسیرهای اصلی مدیریت سایت</p></div><nav class="ese-dashboard-action-grid" aria-label="دسترسی‌های سریع مدیریت"><a class="ese-dashboard-action" href="#settings"><span class="dashicons dashicons-admin-customizer" aria-hidden="true"></span><span><strong>تنظیمات قالب</strong><small>هویت و ظاهر سایت</small></span><span class="ese-dashboard-action-arrow" aria-hidden="true">←</span></a><a class="ese-dashboard-action" href="#inbox"><span class="dashicons dashicons-email-alt" aria-hidden="true"></span><span><strong>صندوق درخواست‌ها</strong><small>پیگیری پیام‌ها</small></span><span class="ese-dashboard-action-arrow" aria-hidden="true">←</span></a></nav></div><footer class="ese-dashboard-footer"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span><p>تغییرات صفحات را پیش‌نمایش و سپس منتشر کنید.</p></footer></section></div></main></body></html>`;
  await page.setViewport({ width: 390, height: 844, deviceScaleFactor: 1 });
  await page.setContent(dashboardHtml, { waitUntil: "domcontentloaded" });
  await page.addStyleTag({ path: path.join(root, "assets/css/admin-dashboard.css") });
  await page.addStyleTag({ content: adminFontCss });
  await page.addScriptTag({ path: require.resolve("axe-core") });
  const mobileDashboard = await page.evaluate(() => ({
    width: window.innerWidth,
    scrollWidth: document.documentElement.scrollWidth,
    statsColumns: getComputedStyle(document.querySelector('.ese-dashboard-stats')).gridTemplateColumns.split(' ').length,
    actionColumns: getComputedStyle(document.querySelector('.ese-dashboard-action-grid')).gridTemplateColumns.split(' ').length,
    workspaceColumns: getComputedStyle(document.querySelector('.ese-dashboard-workspace')).gridTemplateColumns.split(' ').length,
  }));
  assert.ok(mobileDashboard.scrollWidth <= mobileDashboard.width, `mobile dashboard overflows: ${JSON.stringify(mobileDashboard)}`);
  assert.equal(mobileDashboard.statsColumns, 1, "mobile dashboard stats should stack");
  assert.equal(mobileDashboard.actionColumns, 1, "mobile dashboard actions should stack");
  assert.equal(mobileDashboard.workspaceColumns, 1, "mobile dashboard panels should stack");
  const dashboardA11y = await page.evaluate(async () => {
    const result = await axe.run(document, { runOnly: { type: "tag", values: ["wcag2a", "wcag2aa", "wcag21a", "wcag21aa"] } });
    return result.violations.map(({ id, impact, nodes }) => ({ id, impact, targets: nodes.map((node) => node.target) }));
  });
  assert.deepEqual(dashboardA11y, [], `dashboard WCAG accessibility violations: ${JSON.stringify(dashboardA11y)}`);
  await page.setViewport({ width: 1280, height: 900, deviceScaleFactor: 1 });
  const desktopDashboard = await page.evaluate(() => ({
    width: window.innerWidth,
    scrollWidth: document.documentElement.scrollWidth,
    statsColumns: getComputedStyle(document.querySelector('.ese-dashboard-stats')).gridTemplateColumns.split(' ').length,
    actionColumns: getComputedStyle(document.querySelector('.ese-dashboard-action-grid')).gridTemplateColumns.split(' ').length,
    workspaceColumns: getComputedStyle(document.querySelector('.ese-dashboard-workspace')).gridTemplateColumns.split(' ').length,
  }));
  assert.ok(desktopDashboard.scrollWidth <= desktopDashboard.width, `desktop dashboard overflows: ${JSON.stringify(desktopDashboard)}`);
  assert.equal(desktopDashboard.statsColumns, 3, "desktop dashboard stats should use three columns");
  assert.equal(desktopDashboard.actionColumns, 2, "desktop dashboard actions should use two columns");
  assert.equal(desktopDashboard.workspaceColumns, 2, "desktop dashboard panels should use two columns");
  await page.$eval('#ese-admin-overview', (widget) => { widget.style.width = '600px'; });
  const narrowDashboardWidget = await page.evaluate(() => ({
    statsColumns: getComputedStyle(document.querySelector('.ese-dashboard-stats')).gridTemplateColumns.split(' ').length,
    workspaceColumns: getComputedStyle(document.querySelector('.ese-dashboard-workspace')).gridTemplateColumns.split(' ').length,
  }));
  assert.equal(narrowDashboardWidget.statsColumns, 2, "narrow dashboard widget should reflow stats independently of viewport width");
  assert.equal(narrowDashboardWidget.workspaceColumns, 1, "narrow dashboard widget should stack content panels independently of viewport width");

  assert.deepEqual(pageErrors, [], `browser runtime errors: ${pageErrors.join(" | ")}`);
  await browser.close();
  console.log("PASS Chromium E2E: responsive public forms and admin dashboard, Vazirmatn admin/editor typography, icon preservation, WCAG 2A/AA axe checks, and internal-error concealment");
})().catch(async (error) => {
  console.error(error.stack || error);
  if (browser) await browser.close();
  process.exitCode = 1;
});
