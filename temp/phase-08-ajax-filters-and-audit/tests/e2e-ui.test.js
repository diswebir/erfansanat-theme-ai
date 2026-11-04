"use strict";

const assert = require("node:assert/strict");
const fs = require("node:fs");
const path = require("node:path");
const { JSDOM, VirtualConsole } = require("jsdom");

const root = path.resolve(__dirname, "../../..");
const script = fs.readFileSync(path.join(root, "assets/js/main.js"), "utf8");
const formTypes = ["contact", "quote", "recruitment", "client_feedback"];
const formMarkup = formTypes.map((type, index) => `
  <section>
    <form action="https://example.test/wp-admin/admin-ajax.php" method="post" data-ese-submission-form="true" data-form-type="${type}">
      <input type="hidden" name="action" value="ese_submit_form">
      <input type="hidden" name="form_type" value="${type}">
      <input type="hidden" name="form_nonce" value="form-nonce-${index}">
      <input name="name" value="QA Applicant ${index}">
      <input name="phone" value="09123456789">
      <textarea name="message">A valid fixture message for this form.</textarea>
      <input name="website" value="">
      <button type="submit">Submit</button>
    </form>
    <div class="hidden" role="status" aria-live="polite" data-ese-form-success="true">Saved<button type="button" data-ese-form-reset="true">Reset</button></div>
    <p class="hidden" role="alert" data-ese-form-error="true"></p>
  </section>`).join("");
const archiveMarkup = `
  <section data-projects-archive="true" data-initial-type="همه" data-initial-location="همه">
    <nav aria-label="نوع پروژه"><a href="/projects/">همه پروژه‌ها</a><a href="/project_type/urban/"><span>Urban Lighting</span></a></nav>
    <nav aria-label="شهر پروژه"><a href="/projects/">همه شهرها</a><a href="/city/tehran/"><span>Tehran</span></a></nav>
    <input data-project-search="true" value="">
    <select data-project-sort="true"><option value="newest">Newest</option><option value="popular">Popular</option></select>
    <span data-project-count="true">0</span>
    <div data-project-grid="true"></div><div class="hidden" data-project-empty="true"></div>
    <div class="woocommerce-pagination-fallback"></div>
    <div class="hidden" data-project-server-pagination="true"></div>
    <div class="hidden" data-project-ajax-pagination="true"></div>
    <span data-project-ajax-status="true"></span>
    <button type="button" data-project-filter-clear="true">Clear</button>
  </section>
  <section data-products-archive="true" data-product-archive-category="fixture-category" data-product-archive-tag="">
    <input data-product-search="true" value="">
    <label><input type="radio" data-product-purchase-mode="all" checked></label>
    <label><input type="radio" data-product-purchase-mode="online"></label>
    <input type="range" data-product-price-range="true" value="3000000">
    <span data-product-price-label="true"></span>
    <label><input type="checkbox" data-product-ip-filter="IP68"></label>
    <label><input type="checkbox" data-product-voltage-filter="24V DC"></label>
    <select data-product-sort="true"><option value="menu_order">Default</option><option value="price">Price</option></select>
    <span data-product-count="true">0</span>
    <span data-product-active-purch-badge="true" class="hidden"></span>
    <div data-product-grid="true"></div><div data-product-empty="true" class="hidden"></div>
    <div class="woocommerce-pagination-fallback"></div><div data-pagination-root="true" data-per-page="9"></div>
    <span data-product-ajax-status="true"></span>
    <button type="button" data-product-category-filter="led-lights">LED</button>
    <button type="button" data-product-filter-clear="true">Clear</button>
  </section>`;

const virtualConsole = new VirtualConsole();
const browserErrors = [];
virtualConsole.on("jsdomError", (error) => browserErrors.push(error.message));
const dom = new JSDOM(`<!doctype html><html><body>${formMarkup}${archiveMarkup}</body></html>`, {
  url: "https://example.test/contact/",
  runScripts: "outside-only",
  pretendToBeVisual: true,
  virtualConsole,
});
const { window } = dom;
window.HTMLElement.prototype.scrollIntoView = function () {};
window.eseAjax = { ajaxUrl: "https://example.test/wp-admin/admin-ajax.php", nonce: "public-nonce" };
const requests = [];
let failNext = false;
window.fetch = (url, options) => {
  const body = new URLSearchParams(options.body);
  requests.push({ url, method: options.method, body });
  if (failNext) {
    failNext = false;
    return Promise.resolve({
      ok: true,
      json: () => Promise.resolve({ success: false, data: { message: "private server detail" } }),
    });
  }
  const action = body.get("action");
  const data = action === "ese_filter_products" || action === "ese_filter_projects"
    ? { html: "<article>AJAX fixture</article>", total: 2, pages: 2, page: Number(body.get("page") || 1) }
    : { message: "درخواست ثبت شد." };
  return Promise.resolve({
    ok: true,
    json: () => Promise.resolve({ success: true, data }),
  });
};
const tick = (ms = 10) => new Promise((resolve) => setTimeout(resolve, ms));
const send = (form) => form.dispatchEvent(new window.Event("submit", { bubbles: true, cancelable: true }));

(async () => {
  await tick();
  assert.equal(window.document.readyState, "complete");
  window.eval(script);
  await tick();
  const forms = [...window.document.querySelectorAll('[data-ese-submission-form="true"]')];
  assert.equal(forms.length, 4, "all four form categories should be wired");
  const productArchive = window.document.querySelector('[data-products-archive="true"]');
  const projectArchive = window.document.querySelector('[data-projects-archive="true"]');
  const actionRequests = (action) => requests.filter((request) => request.body.get("action") === action);
  assert.ok(actionRequests("ese_filter_products").length >= 1, `product archive should request initial results; readyState=${window.document.readyState}; errors=${browserErrors.join(" | ")}`);
  assert.ok(actionRequests("ese_filter_projects").length >= 1, "project archive should request initial results");
  assert.equal(productArchive.querySelector('[data-product-count="true"]').textContent, "۲");
  assert.equal(projectArchive.querySelector('[data-project-count="true"]').textContent, "۲");

  for (const [index, form] of forms.entries()) {
    send(form);
    await tick();
    const section = form.parentElement;
    assert.equal(form.classList.contains("hidden"), true, `${form.dataset.formType} should hide only after server success`);
    assert.equal(section.querySelector('[data-ese-form-success="true"]').classList.contains("hidden"), false, `${form.dataset.formType} success should be announced`);
    assert.equal(section.querySelector('[data-ese-form-error="true"]').classList.contains("hidden"), true, "error state should remain hidden on success");
    const request = actionRequests("ese_submit_form")[index];
    assert.equal(request.url, window.eseAjax.ajaxUrl);
    assert.equal(request.method, "POST");
    assert.equal(request.body.get("action"), "ese_submit_form");
    assert.equal(request.body.get("form_type"), form.dataset.formType);
    assert.equal(request.body.get("form_nonce"), `form-nonce-${index}`);
    assert.equal(request.body.get("nonce"), "public-nonce");
    assert.equal(request.body.get("name"), `QA Applicant ${index}`);
    assert.equal(request.body.getAll("action").length, 1, "form action should not be duplicated in the request");
  }

  const productCategory = productArchive.querySelector('[data-product-category-filter="led-lights"]');
  productCategory.dispatchEvent(new window.MouseEvent("click", { bubbles: true, cancelable: true }));
  await tick();
  assert.equal(actionRequests("ese_filter_products").at(-1).body.get("category"), "led-lights", "category clicks should submit the selected slug without a page load");
  const onlineRadio = productArchive.querySelector('[data-product-purchase-mode="online"]');
  onlineRadio.checked = true;
  onlineRadio.dispatchEvent(new window.Event("change", { bubbles: true }));
  await tick();
  assert.equal(actionRequests("ese_filter_products").at(-1).body.get("sale"), "online", "true_sale purchase mode should be sent");
  const sortControl = productArchive.querySelector('[data-product-sort="true"]');
  sortControl.value = "price";
  sortControl.dispatchEvent(new window.Event("change", { bubbles: true }));
  await tick();
  assert.equal(actionRequests("ese_filter_products").at(-1).body.get("orderby"), "price", "product sort should be sent");
  productArchive.querySelector('[data-product-filter-clear="true"]').click();
  await tick();
  const clearProductRequest = actionRequests("ese_filter_products").at(-1).body;
  assert.equal(clearProductRequest.get("category"), "fixture-category", "clear should preserve the archive's base category");
  assert.equal(clearProductRequest.get("sale"), "all");
  assert.equal(clearProductRequest.get("orderby"), "menu_order");

  const typeLink = projectArchive.querySelector('nav[aria-label="نوع پروژه"] a[href="/project_type/urban/"]');
  typeLink.dispatchEvent(new window.MouseEvent("click", { bubbles: true, cancelable: true }));
  await tick();
  assert.equal(actionRequests("ese_filter_projects").at(-1).body.get("type"), "Urban Lighting", "project-type selection should request results without navigation");
  const cityLink = projectArchive.querySelector('nav[aria-label="شهر پروژه"] a[href="/city/tehran/"]');
  cityLink.dispatchEvent(new window.MouseEvent("click", { bubbles: true, cancelable: true }));
  await tick();
  assert.equal(actionRequests("ese_filter_projects").at(-1).body.get("city"), "Tehran");
  const projectSearch = projectArchive.querySelector('[data-project-search="true"]');
  projectSearch.value = "Bridge";
  projectSearch.dispatchEvent(new window.Event("input", { bubbles: true }));
  await tick(280);
  assert.equal(actionRequests("ese_filter_projects").at(-1).body.get("query"), "Bridge", "project live search should be debounced and sent");
  projectArchive.querySelector('[data-project-filter-clear="true"]').click();
  await tick();
  const clearProjectRequest = actionRequests("ese_filter_projects").at(-1).body;
  assert.equal(clearProjectRequest.get("type"), "همه");
  assert.equal(clearProjectRequest.get("city"), "همه");
  assert.equal(clearProjectRequest.get("query"), "");
  const projectPages = projectArchive.querySelector('[data-project-ajax-pagination="true"]');
  projectPages.querySelector('[data-ajax-page="2"]').click();
  await tick();
  assert.equal(actionRequests("ese_filter_projects").at(-1).body.get("page"), "2", "AJAX project pagination should submit the selected page");

  const contact = forms[0];
  contact.parentElement.querySelector('[data-ese-form-reset="true"]').click();
  assert.equal(contact.classList.contains("hidden"), false, "reset should restore the form");
  assert.equal(contact.parentElement.querySelector('[data-ese-form-success="true"]').classList.contains("hidden"), true, "reset should hide success state");

  failNext = true;
  send(contact);
  await tick();
  const error = contact.parentElement.querySelector('[data-ese-form-error="true"]');
  assert.equal(contact.classList.contains("hidden"), false, "failed response must not discard user input");
  assert.equal(error.classList.contains("hidden"), false, "failed response should be announced accessibly");
  assert.match(error.textContent, /دوباره تلاش کنید/);
  assert.doesNotMatch(error.textContent, /private server detail/, "server internals must not reach the visitor");
  assert.equal(browserErrors.length, 0, `browser runtime errors: ${browserErrors.join(" | ")}`);

  window.close();
  console.log("PASS DOM E2E: contact, quote, recruitment, and client-feedback submission, nonce payload, success/error/reset states, and no server-detail disclosure");
})().catch((error) => {
  window.close();
  console.error(error.stack || error);
  process.exitCode = 1;
});
