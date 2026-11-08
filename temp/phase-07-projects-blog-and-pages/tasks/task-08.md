# Task 7.8 — قالب برگه‌های licenses، faq، refund_returns و recruitment `[DONE — 78/78 · رگرسیون ۱۴۵۲/۰]`

## 🎯 هدف
چهار قالب برگه اختصاصی با سکشن‌های کاملاً قابل ویرایش از ادمین (Repeaterها) و فرم استخدام — پیش‌فرض عین میرور:
- licenses = هیرو + بج‌ها + گرید گواهینامه‌ها (Repeater) + باکس ستاد/دریافت مستندات + CTA تماس
- faq = هیرو + آکاردئون دسته‌بندی‌شده (Repeater گروهی با هوک‌های data-faq-*) + سایدبار + CTA تماس
- refund = هیرو + کارت‌ها + پوشش/شمول گارانتی + مراحل پشتیبانی (همه Repeater) + CTA تماس
- recruitment = هیرو + تب‌های واحدها (R&D/پروژه/حسابداری/IT از Repeater) + ۷ موقعیت شغلی + فرم همکاری (اتصال ۸.۶)

## 📤 خروجی‌ها
- `page-templates/page-{licenses,faq,refund,recruitment}.php` — شل `get_header(); es_render_sections(get_queried_object_id()); get_footer();`
- `inc/Sections/LandingSections.php` — ثبت ۴ گروه در SectionManager (فیلتر es_sections_registry)
- `template-parts/landing/section-{licenses-hero,licenses-certs,faq-hero,faq-list,refund-hero,refund-policy,recruit-hero,recruit-jobs}.php` — مارک‌آپ nowdoc بایت‌دقیق با توکن {ES_*}
- `template-parts/sections/{…}.php` — ۸ رپر (مسیر رندر SectionManager)
- پچ‌ها: `inc/Admin/SectionManager.php` (TEMPLATE_GROUPS +۴) · `inc/Theme.php` (بوت LandingSections)
- ژنراتور: `temp/gen_p78.py` (برش میرور + Tok جایگاه‌محور + assert بازسازی «رشته کامل» ×۴)
- فیکسچر: `temp/p78_expect_{licenses,faq,refund,recruitment}.html`

## ساختار گروه‌ها (ترتیب عین میرور)
| گروه | سکشن‌ها |
|---|---|
| licenses | licenses-hero · licenses-certs · **contact (reuse)** |
| faq | faq-hero · faq-list · **contact (reuse)** |
| refund | refund-hero · refund-policy · **contact (reuse)** |
| recruitment | recruit-hero · recruit-jobs (میرور CTA ندارد) |

## اسکیم فیلدها (پیش‌فرض = میرور)
- هیروها (۴): crumb/kicker/h1_pre/h1_hl/desc — پرفیکس توکن LH/FH/RH/KH؛ href ایندکس → {ES_*_HOME}
- licenses-certs: badges[title,desc]×4 · certs[cat,code,title,desc,org,period]×6 (custom دوره‌ای 0.04/0.1/0.16) · s_badge/s_h2/s_desc · s_items[item]×4 · docs_title/docs_desc/phone(+url)/products_text(+url)
- faq-list: aside_title/aside_count · cats[title]×3 · help_title/help_desc/phone(+url)/refund_text(+url) · items[cat,question,answer,open]×6 (custom دوره‌ای 0..0.15؛ ایندکس data-faq-toggle/icon/body؛ باز=rotate-45+block)
- refund-policy: cards[title,desc]×3 (custom 0/0.06/0.12) · c_title/c_sub · covered[item]×5 · x_title/x_sub · excluded[item]×4 · p_badge/p_title/phone_text(+url) · steps[title,desc]×3 (شماره es_fa_num از ایندکس) · address · cta_text(+url)
- recruit-jobs: h2/desc · tabs[key,btn,open,badge,title,desc]×4 · jobs[tab,type,exp,title,loc,skills(چندخطی→li)]×7 · فیلدهای فرم (hr_*/addr/hours/l_*/ph_*/submit/ok_*)

## ⛔ محدودیت‌ها و خط‌قرمزها
- پیش‌فرض سکشن‌ها = متن/لینک میرور؛ حالت متای خالی = رندر کامل میرور (بایت‌دقیق)
- فرم recruitment تا ۸.۶ نمایشی است (بدون action) — اتصال با فیلتر `es_recruit_form_extra`
- contact بین گروه‌ها reuse می‌شود (سومین استفاده؛ اسکیم = HomeSections::contact_schema) — id یکتا بین بقیه گروه‌ها
- JS آکاردئون/تب = اسکریپت موجود میرور (data-faq-*/data-recruit-tab-*)؛ بدون اسکریپت جدید

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [x] رندر پیش‌فرض ۴ قالب مطابق میرور — بایت‌دقیق: licenses 30761 · faq 23505 · refund 23230 · recruitment 38830 کاراکتر (temp/p78_expect_*.html)
- [x] Repeaterها از ادمین قابل ویرایش — ویرایش items faq و certs licenses اعمال شد (تست)
- [x] فرم recruitment با hookها — data-contact-form/success/reset + لنگر apply-form + فیلتر es_recruit_form_extra

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — پروب عمیق ۴ میرور (آناتومی سکشن‌ها/ران‌ها/توکن‌ها/anchorها)
- [x] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-08.php
- [x] IMPLEMENT — ژنراتور یکپارچه temp/gen_p78.py بدون پچ تدریجی
- [x] RUN TEST — 78/78 پاس + رگرسیون کامل ۱۴۵۲/۰ + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN ×۲ + Git Commit

## 📌 گزارش اجرا (2026-11-03)
- همه خروجی‌ها با `temp/gen_p78.py` (assert بازسازی بایت‌دقیق ۴/۴ سبز) تولید شدند؛ پچ‌ها: SectionManager (TEMPLATE_GROUPS ×۴)، Theme.php (بوت)، تست 2.7 (۸ گروه — به‌روزرسانی پیش‌بینی‌شده).
- **انحراف مستند از spec (میرور حاکم است):**
  - میرور licenses هیچ `<img>` ندارد → گواهینامه‌ها کاشی آیکونی ۶-ردیفه‌اند (cat/code/title/desc/org/period) نه Repeater تصویری.
  - میرور recruitment سکشن CTA تماس ندارد → گروه recruitment فقط ۲ سکشن.
  - فرم میرور input بدون name دارد → تا ۸.۶ نمایشی (مطابق الگوی ۷.۷) با هوک‌های data-contact-* و فیلتر es_recruit_form_extra.
- **باگ کلیدی مسیر:** اسلاگ مارک‌آپ هیروی recruit (recruitment-hero) با شناسه سکشن (recruit-hero) یکی نبود → es_get_template بی‌صدا تهی برمی‌گشت؛ تست بایت‌دقیق recruitment گرفت. اسلاگ‌ها یکتا/هم‌نام با id شدند.
- **اصلاح escape:** optionهای واحدهای سازمانی بدون esc_attr چاپ می‌شوند (پیش‌فرض میرور `&amp;` دارد؛ esc_attr دوبار escape می‌کرد).
- تست: `tests/test-task-08.php` — **78/78** (رجیستری ۴ گروه + const ORDER، reuse اسکیم contact، نگاشت قالب→گروه + سازگاری ۵.۱/۷.۷، پیش‌فرض گروهی، بایت‌دقیق ×۴، هوک‌های data-faq-* با ایندکس/استیت، فرم + فیلتر + بازگشت بایت‌دقیق، es_fa_num گام‌ها، ویرایش Repeater ×۲، toggle، reorder، قالب کامل ×۴ با شل، عدم نشتی متا). رگرسیون کامل: **۱۴۵۲/۰** (۷۱ فایل پیاده‌شده + ۱۲ placeholder شناخته‌شده ۷.۹/۷.۱۰/فاز ۸). php -l همه فایل‌ها سبز. ژنراتور idempotent (git diff خالی پس از rerun).
- محیط: رانر php-wasm نسخه ۹ (`tools/phpwasm/php.mjs` — مونت کامل مخزن متنی، ES_SCRIPT_PATH، شکافتن PHPExecutionFailureError، exit صریح).
