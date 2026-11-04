# Phase 8.3 — Final compatibility, security, and release audit

Status: **complete — automated regression and browser checks passed on 2026-11-04.** This verifies the disposable harness and implementation contracts; it is not a production deployment or staging sign-off.

## Environment and evidence

- Disposable WordPress 7.2-beta4 / build 65250 + SQLite via PHP WASM. The generated test configuration sets `WP_DEBUG=true` and writes to the ignored test workspace.
- `temp/run-all-phases-e2e.sh` is the repeatable gate for all eight phase runners, the jsdom UI E2E, bundled headless Chromium E2E, PHP/JavaScript/shell syntax checks, whitespace checks, and an empty `WP_DEBUG` log.
- The Chromium binary and its Linux runtime libraries are supplied by the locked `@sparticuz/chromium` dev dependency and unpacked under `/tmp` by the E2E script; no system-installed browser is required.
- The integrated full runner passed in approximately 150 seconds. All phase suites passed, including Task 8.2 integration checks. No PHP warning, notice, or fatal was logged with `WP_DEBUG=true`.
- The real Chromium E2E exercised 390px and 1280px viewports, layout overflow/grid behavior, form labels, AJAX payload/nonces, success/error/reset states, and private-error non-disclosure. The API is mocked in the browser test; this is not a live WordPress HTTP/browser test or pixel/screenshot review.
- Phase 8.1 fixture endpoint timing: maximum **3.00 ms** in the latest complete regression run (an earlier full run measured 2.00 ms) and **4.00 ms** in a focused post-run repeat, excluding browser/network round-trip. This is a regression fixture measurement, not a production-server benchmark.

## Compatibility and SEO review

- Theme-owned legacy `hreflang` output escapes URLs and is suppressed when SEOPress is detected, avoiding duplicate alternates. Phase 3/4 integration tests cover post/term output and the SEOPress guard.
- WordPress canonical routes, existing product/project metadata, native post content, and WooCommerce transaction templates remain owned by WordPress/WooCommerce rather than duplicated by form submission code.
- The theme does not add a competing JSON-LD/schema generator. The disposable harness does not install the production SEO/WooCommerce plugin stack, so rendered schema and canonical output must still be checked on staging with the actual plugins enabled.

## Security, accessibility, responsiveness, and performance

- Public forms require POST + form nonce, bounded allowlisted schemas, server-side validation/sanitization, honeypot, and a short hashed-IP rate limit. They store private records; inbox/status/settings actions require `manage_options` and nonce checks. Mail transport failures do not expose recipient or message details to visitors.
- Publicly rendered data is escaped. Integration tests cover SQL/XSS probes, invalid nonce/method, invalid URLs/ratings, status allowlists, and filter query bounds.
- Form controls have explicit labels and required constraints, status/error live regions, and keyboard-operable submit/reset controls. Browser E2E confirms the rendered labels and responsive 390px/1280px layout does not horizontally overflow and uses the expected desktop columns. axe-core reports zero WCAG 2.0/2.1 A/AA violations in the test form and synthetic admin/editor typography fixtures. This automated check does not replace a real screen-reader/assistive-technology audit; pixel-level visual review is also not represented as completed.
- The local Vazirmatn WOFF2 subsets are enqueued throughout WordPress admin, theme controls, and classic/block editor content; icon-font classes are excluded from the inherited font override. The Phase 2 integration test verifies all three local font assets, the admin enqueue hook, and editor-style support. Chromium E2E loads the actual local font data, verifies Persian glyph rendering and computed font-family on admin/editor content, and confirms Dashicons retain their icon font.
- The fixture latency above gives a local regression check only. Production p95 latency, cache behavior, email deliverability, and plugin-specific SEO/schema output require staging measurements.

## Rollout and rollback

### Rollout
1. Back up the database and theme directory; deploy the changes to staging first.
2. Configure SMTP/`wp_mail`, then set/verify the inbox notification address and enablement in **درخواست‌های سایت**. Confirm a test message reaches the inbox and record whether notification is marked sent.
3. Purge page/object/CDN caches after deployment. Pages containing nonces should not be served stale beyond the nonce lifetime; if full-page caching is required, bypass these form pages or provide a nonce refresh strategy.
4. Submit one contact, quote, recruitment, and product-feedback fixture; confirm private inbox/status transitions and logged-out submissions. Verify product reviews, checkout, SEOPress canonical/schema output, and hreflang on the actual staging plugin stack.
5. Keep an administrator account able to access the inbox. Review retention/backup policy for private application and contact data; CVs are accepted as HTTP(S) resume links only (no file uploads are stored).

### Rollback
1. Redeploy the previous stable theme version or revert the form-submission changes if public submissions need to be disabled. Revert the audit runner/docs separately only if that QA tooling should also be removed.
2. Preserve/export `ese_submission` records before any code removal. Reverting theme code does not delete database rows; do not purge them as part of rollback.
3. Clear caches and re-test primary page, WooCommerce checkout/account, canonical URLs, and navigation. If only mail delivery fails, disable email notifications in inbox settings while keeping inbox storage available.

## Final run results

- **PASS** all eight phase runners (Phases 1–8).
- **PASS** Task 8.2 validation, honeypot, nonce, rate limit, private inbox/status controls, optional email notification, escaping, and form placement integration tests.
- **PASS** jsdom and bundled headless Chromium E2E.
- **PASS** PHP syntax audit over theme/test PHP files, JavaScript and shell syntax, `git diff --check`, and empty `WP_DEBUG` log.
- **PASS** final audit contract; latest Phase 8.1 fixture maximum 3.00 ms excluding network (focused repeat maximum 4.00 ms).

No production deployment, production performance benchmark, live-mail delivery, plugin-rendered schema validation, assistive-technology audit, or screenshot-based visual review is claimed by this run.
