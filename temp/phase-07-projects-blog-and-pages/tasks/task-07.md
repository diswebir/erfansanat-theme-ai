# Task 7.7 — قالب برگه‌های about و contact (با SectionManager)

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[DONE — 58/58 · رگرسیون ۱۳۵۳/۰]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-07.php`

## 🎯 هدف
دو قالب برگه با سکشن‌های کاملاً قابل ویرایش از ادمین — پیش‌فرض عین میرور

## 🔗 وابستگی‌ها
- Task 2.9
- Task 8.5 (اسکلت فرم)

## 📎 مراجع (منابع برش/استخراج)
- about.html (هیرو/معرفی/آمار/تاریخچه/ویدیو home3-about-video/ارزش‌ها)
- contact.html (اطلاعات/فرم/نقشه)

## 📤 فایل‌های خروجی
- `page-templates/page-about.php`
- `page-templates/page-contact.php`
- `inc/Sections/AboutSections.php`
- `inc/Sections/ContactSections.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. page-about.php: es_render_sections با Registry سکشن‌های about (هیرو، معرفی، آمار، تایم‌لاین، ویدیو معرفی، ارزش‌ها) — schema فیلد کامل هر سکشن
2. ویدیو about با hookهای home3-* میرور (play/toggle) حفظ
3. page-contact.php: سکشن‌های (هیرو، اطلاعات تماس، فرم تماس، نقشه) — فرم متصل به سیستم فرم فاز ۸ (action ajax با nonce؛ تا فاز ۸ حالت نمایش)
4. استایل فرم contact مطابق میرور (contact-form/reset/success hookها)
5. هر دو قالب در select قالب برگه ظاهر شوند (Template Name)

## ⛔ محدودیت‌ها و خط‌قرمزها
- پیش‌فرض سکشن‌ها = متن/تصویر میرور
- فرم هنوز ارسال نمی‌کند تا 8.5 (پیام آماده‌سازی)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [x] رندر پیش‌فرض ساختاری مطابق میرور — بایت‌دقیق: about 32839 و contact 25648 کاراکتر (temp/p77_expect_*.html)
- [x] toggle/reorder ادمین اثر کند — متاباکس گروه قالب + get_sections گروهی (فیلتر es_sections_template_groups)
- [x] فرم رندر با hookها — data-contact-form/success/reset حفظ؛ فیلتر es_contact_form_extra برای اتصال ۸.۵

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-07.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit

## 📌 گزارش اجرا (2026-11-03)
- همه خروجی‌ها با `temp/gen_p77.py` (assert بازسازی ۶/۶ سبز) تولید شدند؛ پچ‌ها: SectionManager (TEMPLATE_GROUPS/group_for_post/get_sections گروهی/متاباکس گروهی)، HomeSections (استخراج contact_schema مشترک)، Theme.php (بوت)، wp-stubs (get_page_template_slug + remove_filter واقعی)، MobileWalker (رفع نشتی `<?php` داخل nowdoc باگ ۵.x → توکن {ES_LOGO_HTML}).
- **انحراف مستند از spec (میرور حاکم است — خط‌قرمز پیش‌فرض = میرور):**
  - میرور about.html سکشن «تایم‌لاین/ویدیو معرفی home3-about-video» ندارد (grep home3 = 0) → گروه about = ۵ سکشن واقعی میرور (هیرو/معرفی/ISO/ارزش‌ها + CTA contact مشترک بایت‌مساوی ایندکس).
  - میرور contact.html سکشن «نقشه» جدا ندارد (نقشه SVG فقط در index) → گروه contact = ۲ سکشن (هیرو + information/فرم در یک container میرور).
  - idهای سکشن با پیشوند page-about-* (تداخل با سکشن‌های همسایه ایندکس: about/about-intro — schema_of سراسری است).
- تست: `tests/test-task-07.php` — 58/58 (رجیستری، عدم‌تداخل اسکیم، اسکیم مشترک contact، نگاشت قالب→گروه، پیش‌فرض گروهی، متاباکس گروهی، بایت‌دقیق ×۲، هوک‌های فرم، فیلتر form_extra، ویرایش فیلد، toggle، reorder، قالب کامل ×۲ با شل، MobileWalker با/بدون لوگو، فایل‌ها). رگرسیون کل: ۱۳۵۳/۰ (۱۳ placeholder).
- فرم تا فاز ۸.۵ فقط نمایشی است (بدون action/nonce؛ پیام آماده‌سازی = نقش فیلتر es_contact_form_extra که ۸.۵ با آن اتصال می‌دهد).
