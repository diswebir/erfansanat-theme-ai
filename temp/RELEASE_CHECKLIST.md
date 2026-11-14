# چک‌لیست انتشار قالب عرفان‌صنعت (P08-T024)

> گزارش نهایی آمادگی استقرار + مراحل نصب. خروجی‌های قابل تکرار:
> `bash temp/run-all-phases-e2e.sh` و `bash temp/tools/package-theme.sh`.

## ۱) پوشش تست — AC-01

- آخرین اجرا (هارنس): `E2E RESULT: ALL PHASES PASS` — **۲۸۰۸ چک، ۰ رد، exit 0**.
- گزارش با جزئیات: `temp/e2e-report.md` (RESULT تک‌تک تسک‌ها) + `temp/e2e-state.txt` (نتیجه هر فاز).

| فاز | پاس | رد |
|---|---|---|
| phase-01-core-and-assets | 111 | 0 |
| phase-02-custom-admin-dashboard | 287 | 0 |
| phase-03-cpt-and-legacy-meta | 301 | 0 |
| phase-04-header-footer-navigation | 310 | 0 |
| phase-05-homepage-and-sections | 410 | 0 |
| phase-06-woocommerce-and-products | 544 | 0 |
| phase-07-projects-blog-and-pages | 437 | 0 |
| phase-08-ajax-filters-and-audit | 408 | 0 |

- معیار: ۱۰۰٪ پاس؛ skip فقط در حد «pending مجاز مستندشده» (P06-T028 تایید روی
  استیجینگ واقعی ووکامرس — محدودیت محیطی ثبت‌شده، نه نقص تست).

## ۲) ممیزی SEO و Schema (P08-T019)

- [x] دقیقاً یک `<link rel="canonical">` در head صفحات کلیدی (هسته روی singular، تم برای آرشیو ترم‌ها).
- [x] JSON-LD معتبر: Organization (خانه) / Article (تک‌مقاله) / Product با Offer (محصول، از فیلتر `es_seo_product_data`).
- [x] hreflang استاندارد بدون تداخل (مالک: `Core\Hreflang`؛ با پلاگین SEO غیرفعال می‌شود).
- [x] با پلاگین SEOPress/RankMath: تم ساکت + حذف canonical هسته (SC-02) — متای پلاگین دست‌نخورده.
- تست: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-019.php` → 20/0.

## ۳) کارایی (P08-T020)

- [x] رندر صفحه اصلی/تک‌مقاله/آرشیو بدون warning/notice.
- [x] بودجه asset: استایل ≤ ۸ (واقعی ۶)، اسکریپت ≤ ۶ (واقعی ۳)؛ همه assetهای صف‌شده داخلی.
- [x] بودجه کوئری: صفحه اصلی ≤ ۲۵۰ (واقعی ۳۵)، تک‌مقاله ≤ ۱۵۰ (واقعی ۶).
- [x] اسکن escaping: هیچ `echo $var;` بدون phpcs:ignore در کل قالب.
- تست: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-020.php` → 12/0.

## ۴) ریسپانسیو و دسترس‌پذیری (P08-T021)

- [x] قرارداد بریک‌پوینت ۶۴۰/۷۶۸/۱۰۲۴ در `styles.css` (اسنپ‌شات سه viewport 375/768/1440 از یک DOM واحد).
- [x] بدون عرض ثابت ≥۱۰۰۰px (no horizontal overflow).
- [x] تصاویر با alt (44/44)، فیلدهای فرم با label، مودال/دراور با aria، آکاردئون‌ها button+aria-expanded، Escape برای بستن.
- تست: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-021.php` → 13/0.

## ۵) مهاجرت و Rollback (P08-T022/T023)

- [x] Dry-run مهاجرت روی کپی استیجینگ: `temp/tools/staging-migration-dry-run.sh` (بک‌آپ + شمارش قبل/بعد + ۳۷ کلید legacy بدون rename + drift=0).
- [x] ران‌بوک خاموش‌کردن Elementor/Jet*: `temp/DEACTIVATION_RUNBOOK.md` (ترتیب ۶ مرحله‌ای، سه نقطه rollback، فقط Deactivate).
- [x] سناریوی rollback روی کپی تمرین و تأیید شد (متاهای legacy دست‌نخورده).
- تست‌ها: test-task-022 → 20/0، test-task-023 → 17/0.

## ۶) نصب از بسته انتشار (SC-02)

1. بسته: `bash temp/tools/package-theme.sh` → `temp/dist/erfansanat.zip` (فقط پوشه `erfansanat/` — AC-02).
2. نصب تمیز: `wp core install` روی یک دیتابیس خالی → `unzip temp/dist/erfansanat.zip -d wp-content/themes/` → `wp theme activate erfansanat`.
3. تمرین انجام‌شده در هارنس: نصب تمیز + فعال‌سازی + ثبت پیوندهای یکتا `/%postname%/` موفق؛ قالب بدون خطا فعال می‌شود.
4. بعد از فعال‌سازی: چک‌لیست صفحات کلیدی ۲۰۰ از `temp/DEACTIVATION_RUNBOOK.md` بخش ۳.

## ۷) تایید نهایی

- [ ] E2E کامل: exit 0
- [ ] زیپ فقط `erfansanat/` دارد
- [ ] گزارش آمادگی (همین فایل) به‌روز
- [ ] نقاط rollback ثبت شده‌اند
