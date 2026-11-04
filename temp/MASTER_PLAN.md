# MASTER PLAN — نقشه راه اتمیک فازها و تسک‌ها (بازطراحی ۲۰۲۶)

> **کلید وضعیت‌ها:** `[PENDING]` در انتظار | `[IN_PROGRESS]` در حال اجرا | `[TESTING]` در حال تست | `[PASSED]` پاس‌شده

> **قانون اتمیک (تصمیم کارفرما):** هر تسک = یک کامپوننت/یک قابلیت مستقل، با فایل تست جدا، شروط پاس مستقل و کامیت جدا. فاز ۱ فریز است و دست نخورده باقی می‌ماند.

---
## خلاصه وضعیت کلان

| فاز | عنوان | پوشه | تعداد تسک | وضعیت |
|---|---|---|---:|---|
| STAGE 0/1 | تجهیز هارنس، اسکن استاتیک، معماری اولیه | `temp/` | — | `[PASSED]` |
| **Phase 1** | زیرساخت هسته، Autoloader، DeBloat و Assets | `temp/phase-01-core-and-assets` | ۳ | `[PASSED]` (فریز — `91/91`) |
| **Phase 2** | مهندسی فریم‌ورک داشبورد اختصاصی ادمین و SectionManager | `temp/phase-02-custom-admin-dashboard` | 15 | `[PASSED]` |
| **Phase 3** | معماری داده‌ها، CPT، تکسونومی‌ها و متاباکس‌های دیتابیس زنده | `temp/phase-03-cpt-and-legacy-meta` | 17 | `[PASSED]` |
| **Phase 4** | تبدیل هدر، مگامنو، منوی موبایل، مودال‌ها و فوتر استاتیک | `temp/phase-04-header-footer-navigation` | 11 | `[PASSED]` |
| **Phase 5** | تبدیل صفحه اصلی (۱۵ سکشن) و کارت‌های استاندارد | `temp/phase-05-homepage-and-sections` | 20 | `[PASSED]` |
| **Phase 6** | تبدیل قالب‌های ووکامرس، تک‌محصول، آرشیو و سبد/پرداخت | `temp/phase-06-woocommerce-and-products` | 20 | `[PASSED]` |
| **Phase 7** | پروژه‌ها، وبلاگ، برگه‌های عمومی، خدمات و لندینگ آریا | `temp/phase-07-projects-blog-and-pages` | 20 | `[PENDING]` |
| **Phase 8** | جستجو و فیلتر Native، فرم‌ها، سئو و ممیزی نهایی | `temp/phase-08-ajax-filters-and-audit` | 17 | `[PENDING]` |

**جمع کل تسک‌های اجرایی: 123 تسک** (۳ تسک پاس‌شده فاز ۱ + 120 تسک اتمیک فازهای ۲ تا ۸)


---

### Phase 2: مهندسی فریم‌ورک داشبورد اختصاصی ادمین و SectionManager (`temp/phase-02-custom-admin-dashboard`) — 15 تسک

- `[PASSED]` **Task 2.1 (`task-01.md` / `test-task-01.php`):** ثبت منوی ادمین و پوسته صفحه داشبورد
- `[PASSED]` **Task 2.2 (`task-02.md` / `test-task-02.php`):** مخزن آپشن واحد و رجیستری مقادیر پیش‌فرض
- `[PASSED]` **Task 2.3 (`task-03.md` / `test-task-03.php`):** تابع es_option() و کش درون‌حافظه‌ای
- `[PASSED]` **Task 2.4 (`task-04.md` / `test-task-04.php`):** دارایی‌های ادمین (CSS/JS) و Enqueue مشروط
- `[PASSED]` **Task 2.5 (`task-05.md` / `test-task-05.php`):** ذخیره‌سازی AJAX امن تنظیمات
- `[PASSED]` **Task 2.6 (`task-06.md` / `test-task-06.php`):** بازنشانی (Reset) و خروجی/ورودی JSON تنظیمات
- `[PASSED]` **Task 2.7 (`task-07.md` / `test-task-07.php`):** موتور رندر فیلدها — فیلدهای پایه
- `[PASSED]` **Task 2.8 (`task-08.md` / `test-task-08.php`):** موتور رندر فیلدها — مدیا، لینک، آیکون و Repeater
- `[PASSED]` **Task 2.9 (`task-09.md` / `test-task-09.php`):** تب‌های هدر، نوار اعلان و لوگو
- `[PASSED]` **Task 2.10 (`task-10.md` / `test-task-10.php`):** تب‌های فوتر، تماس و شبکه‌های اجتماعی
- `[PASSED]` **Task 2.11 (`task-11.md` / `test-task-11.php`):** تب‌های مگامنو، رنگ برند و اسکریپت‌های سفارشی
- `[PASSED]` **Task 2.12 (`task-12.md` / `test-task-12.php`):** تب تنظیمات آرشیوها و رجیستری فیلترها (شامل ۱۸ pa_*)
- `[PASSED]` **Task 2.13 (`task-13.md` / `test-task-13.php`):** مدل داده SectionManager و Storage
- `[PASSED]` **Task 2.14 (`task-14.md` / `test-task-14.php`):** رابط کاربری SectionManager (Drag & Drop)
- `[PASSED]` **Task 2.15 (`task-15.md` / `test-task-15.php`):** API رندر فرانت‌اند سکشن‌ها
- `[PASSED]` **Phase 2 Suite (`run-phase-02-tests.sh`):** هر ۱۵ تست پاس + رگرسیون فاز ۱ سبز.

---

### Phase 3: معماری داده‌ها، CPT، تکسونومی‌ها و متاباکس‌های دیتابیس زنده (`temp/phase-03-cpt-and-legacy-meta`) — 17 تسک

- `[PASSED]` **Task 3.1 (`task-01.md` / `test-task-01.php`):** ثبت CPT پروژه‌ها (projects)
- `[PASSED]` **Task 3.2 (`task-02.md` / `test-task-02.php`):** ثبت تکسونومی‌های project_type و city
- `[PASSED]` **Task 3.3 (`task-03.md` / `test-task-03.php`):** ثبت تکسونومی product_brand
- `[PASSED]` **Task 3.4 (`task-04.md` / `test-task-04.php`):** حفاظت از Rewriteهای product_cat و product_tag
- `[PASSED]` **Task 3.5 (`task-05.md` / `test-task-05.php`):** ریدایرکت ۳۰۱ برگه /project/ به /projects/
- `[PASSED]` **Task 3.6 (`task-06.md` / `test-task-06.php`):** ستون‌ها و فیلترهای ادمین پروژه‌ها
- `[PASSED]` **Task 3.7 (`task-07.md` / `test-task-07.php`):** موتور خواندن/نوشتن متای Legacy و Repeater جت‌انجین
- `[PASSED]` **Task 3.8 (`task-08.md` / `test-task-08.php`):** اسکلت متاباکس محصول و ذخیره امن
- `[PASSED]` **Task 3.9 (`task-09.md` / `test-task-09.php`):** فیلدهای شناسه‌های صنعتی محصول + Quick Edit
- `[PASSED]` **Task 3.10 (`task-10.md` / `test-task-10.php`):** فیلدهای عنوان، وضعیت فروش و فایل‌های محصول
- `[PASSED]` **Task 3.11 (`task-11.md` / `test-task-11.php`):** روابط محصول: پروژه‌ها و کاربردها
- `[PASSED]` **Task 3.12 (`task-12.md` / `test-task-12.php`):** Repeaterهای محصول (۴ گروه)
- `[PASSED]` **Task 3.13 (`task-13.md` / `test-task-13.php`):** متاباکس پروژه: گالری ۵ تصویر و شناسنامه
- `[PASSED]` **Task 3.14 (`task-14.md` / `test-task-14.php`):** متاباکس پروژه: محصولات استفاده‌شده
- `[PASSED]` **Task 3.15 (`task-15.md` / `test-task-15.php`):** متاباکس ترم product_cat: تصویر و FAQ
- `[PASSED]` **Task 3.16 (`task-16.md` / `test-task-16.php`):** متاباکس Hreflang برای پست‌ها و ترم‌ها
- `[PASSED]` **Task 3.17 (`task-17.md` / `test-task-17.php`):** سرویس شمارنده بازدید نوشته‌ها
- `[PENDING]` **Phase 3 Suite (`run-phase-03-tests.sh`):** اجرای همه 17 تست این فاز + گزارش و توقف برای تایید کارفرما (فاز به فاز).

---

### Phase 4: تبدیل هدر، مگامنو، منوی موبایل، مودال‌ها و فوتر استاتیک (`temp/phase-04-header-footer-navigation`) — 11 تسک

- `[PASSED]` **Task 4.1 (`task-01.md` / `test-task-01.php`):** پوسته header.php و باز کردن Wrapperها
- `[PASSED]` **Task 4.2 (`task-02.md` / `test-task-02.php`):** هدر دسکتاپ: لوگو، برند و اکشن‌ها
- `[PASSED]` **Task 4.3 (`task-03.md` / `test-task-03.php`):** Walker منوی اصلی و اسکلت مگامنو
- `[PASSED]` **Task 4.4 (`task-04.md` / `test-task-04.php`):** پنل مگامنوی محصولات (تب‌دار)
- `[PASSED]` **Task 4.5 (`task-05.md` / `test-task-05.php`):** پنل مگامنوی پروژه‌ها و پنل شرکتی
- `[PASSED]` **Task 4.6 (`task-06.md` / `test-task-06.php`):** دراور منوی موبایل
- `[PASSED]` **Task 4.7 (`task-07.md` / `test-task-07.php`):** مودال جستجوی سراسری (فقط Markup)
- `[PASSED]` **Task 4.8 (`task-08.md` / `test-task-08.php`):** مودال سبد خرید (فقط Markup + حالت خالی)
- `[PASSED]` **Task 4.9 (`task-09.md` / `test-task-09.php`):** فوتر اصلی: معرفی، ستون‌ها و تماس
- `[PASSED]` **Task 4.10 (`task-10.md` / `test-task-10.php`):** نوار پایانی فوتر، Scroll-Top و بستن Wrapperها
- `[PASSED]` **Task 4.11 (`task-11.md` / `test-task-11.php`):** تگ‌های Hreflang در head و ممیزی تطابق DOM پوسته
- `[PASSED]` **Phase 4 Suite (`run-phase-04-tests.sh`):** اجرای همه 11 تست این فاز + گزارش و توقف برای تایید کارفرما (فاز به فاز).

---

### Phase 5: تبدیل صفحه اصلی (۱۵ سکشن) و کارت‌های استاندارد (`temp/phase-05-homepage-and-sections`) — 20 تسک

- `[PASSED]` **Task 5.1 (`task-01.md` / `test-task-01.php`):** front-page.php و حلقه SectionManager
- `[PASSED]` **Task 5.2 (`task-02.md` / `test-task-02.php`):** کارت استاندارد محصول
- `[PASSED]` **Task 5.3 (`task-03.md` / `test-task-03.php`):** کارت استاندارد پروژه
- `[PASSED]` **Task 5.4 (`task-04.md` / `test-task-04.php`):** کارت استاندارد مقاله
- `[PASSED]` **Task 5.5 (`task-05.md` / `test-task-05.php`):** سکشن ۰۱ — هیرو ۵ صحنه‌ای و ویدیو
- `[PASSED]` **Task 5.6 (`task-06.md` / `test-task-06.php`):** سکشن ۰۲ — معرفی پروژه‌های نورپردازی شهری
- `[PASSED]` **Task 5.7 (`task-07.md` / `test-task-07.php`):** سکشن ۰۳ — ویترین پروژه‌های شاخص
- `[PASSED]` **Task 5.8 (`task-08.md` / `test-task-08.php`):** سکشن ۰۴ — درباره و شمارنده‌های آماری
- `[PASSED]` **Task 5.9 (`task-09.md` / `test-task-09.php`):** سکشن ۰۵ — بنر مرجع بومی‌سازی
- `[PASSED]` **Task 5.10 (`task-10.md` / `test-task-10.php`):** سکشن ۰۶ — تایم‌لاین تاریخچه
- `[PASSED]` **Task 5.11 (`task-11.md` / `test-task-11.php`):** سکشن ۰۷ — منتخب محصولات
- `[PASSED]` **Task 5.12 (`task-12.md` / `test-task-12.php`):** سکشن ۰۸ — بنر دانلود کاتالوگ جامع
- `[PASSED]` **Task 5.13 (`task-13.md` / `test-task-13.php`):** سکشن ۰۹ — خدمات تب‌دار
- `[PASSED]` **Task 5.14 (`task-14.md` / `test-task-14.php`):** سکشن ۱۰ — استانداردهای کیفیت
- `[PASSED]` **Task 5.15 (`task-15.md` / `test-task-15.php`):** سکشن ۱۱ — بنر پشتیبانی و گارانتی
- `[PASSED]` **Task 5.16 (`task-16.md` / `test-task-16.php`):** سکشن ۱۲ — مشتریان و سازمان‌های همکار
- `[PASSED]` **Task 5.17 (`task-17.md` / `test-task-17.php`):** سکشن ۱۳ — آخرین مقالات
- `[PASSED]` **Task 5.18 (`task-18.md` / `test-task-18.php`):** سکشن ۱۴ — CTA مشاوره و استعلام
- `[PASSED]` **Task 5.19 (`task-19.md` / `test-task-19.php`):** سکشن ۱۵ — کارت نشانی و تماس کارخانه
- `[PASSED]` **Task 5.20 (`task-20.md` / `test-task-20.php`):** ممیزی یکپارچه صفحه اصلی
- `[PASSED]` **Phase 5 Suite (`run-phase-05-tests.sh`):** اجرای همه 20 تست این فاز + گزارش و توقف برای تایید کارفرما (فاز به فاز).

---

### Phase 6: تبدیل قالب‌های ووکامرس، تک‌محصول، آرشیو و سبد/پرداخت (`temp/phase-06-woocommerce-and-products`) — 20 تسک

- `[PASSED]` **Task 6.1 (`task-01.md` / `test-task-01.php`):** یکپارچه‌سازی ووکامرس و Wrapper قالب
- `[PASSED]` **Task 6.2 (`task-02.md` / `test-task-02.php`):** تک‌محصول: Breadcrumb و هدر محصول
- `[PASSED]` **Task 6.3 (`task-03.md` / `test-task-03.php`):** تک‌محصول: گالری و لایت‌باکس
- `[PASSED]` **Task 6.4 (`task-04.md` / `test-task-04.php`):** تک‌محصول: پنل خرید آنلاین (true_sale=true)
- `[PASSED]` **Task 6.5 (`task-05.md` / `test-task-05.php`):** تک‌محصول: پنل استعلام (true_sale=false)
- `[PASSED]` **Task 6.6 (`task-06.md` / `test-task-06.php`):** تک‌محصول: انتخابگر متغیر و Swatches
- `[PASSED]` **Task 6.7 (`task-07.md` / `test-task-07.php`):** تک‌محصول: اسلات‌های مشروط اطلاعیه و کاتالوگ
- `[PASSED]` **Task 6.8 (`task-08.md` / `test-task-08.php`):** تک‌محصول: تب معرفی، ویژگی‌ها و کاربردها
- `[PASSED]` **Task 6.9 (`task-09.md` / `test-task-09.php`):** تک‌محصول: تب مشخصات فنی و شناسه‌ها
- `[PASSED]` **Task 6.10 (`task-10.md` / `test-task-10.php`):** تک‌محصول: ویژگی‌های کلیدی دوبخشی
- `[PASSED]` **Task 6.11 (`task-11.md` / `test-task-11.php`):** تک‌محصول: پروژه‌های مرتبط
- `[PASSED]` **Task 6.12 (`task-12.md` / `test-task-12.php`):** تک‌محصول: FAQ و دیدگاه‌ها
- `[PASSED]` **Task 6.13 (`task-13.md` / `test-task-13.php`):** تک‌محصول: محصولات مشابه
- `[PASSED]` **Task 6.14 (`task-14.md` / `test-task-14.php`):** آرشیو فروشگاه: Layout، گرید و صفحه‌بندی
- `[PASSED]` **Task 6.15 (`task-15.md` / `test-task-15.php`):** آرشیو product_cat: هدر سئو و FAQ ترم
- `[PASSED]` **Task 6.16 (`task-16.md` / `test-task-16.php`):** آرشیو product_tag و product_brand
- `[PASSED]` **Task 6.17 (`task-17.md` / `test-task-17.php`):** صفحه سبد خرید (cart)
- `[PASSED]` **Task 6.18 (`task-18.md` / `test-task-18.php`):** مینی‌کارت مودال و WC Fragments
- `[PASSED]` **Task 6.19 (`task-19.md` / `test-task-19.php`):** صفحه تسویه‌حساب (checkout)
- `[PASSED]` **Task 6.20 (`task-20.md` / `test-task-20.php`):** حساب کاربری و ورود (fl-panel / voorodak)
- `[PASSED]` **Phase 6 Suite (`run-phase-06-tests.sh`):** اجرای همه 20 تست این فاز + گزارش و توقف برای تایید کارفرما (فاز به فاز).

---

### Phase 7: پروژه‌ها، وبلاگ، برگه‌های عمومی، خدمات و لندینگ آریا (`temp/phase-07-projects-blog-and-pages`) — 20 تسک

- `[PASSED]` **Task 7.1 (`task-01.md` / `test-task-01.php`):** تک‌پروژه: هدر و Breadcrumb
- `[PASSED]` **Task 7.2 (`task-02.md` / `test-task-02.php`):** تک‌پروژه: گالری و کارت شناسنامه
- `[PASSED]` **Task 7.3 (`task-03.md` / `test-task-03.php`):** تک‌پروژه: محتوا، محصولات استفاده‌شده و پروژه‌های مرتبط
- `[PASSED]` **Task 7.4 (`task-04.md` / `test-task-04.php`):** آرشیو پروژه‌ها
- `[PASSED]` **Task 7.5 (`task-05.md` / `test-task-05.php`):** آرشیو project_type و city
- `[PASSED]` **Task 7.6 (`task-06.md` / `test-task-06.php`):** آرشیو وبلاگ و دسته‌بندی مقالات
- `[PASSED]` **Task 7.7 (`task-07.md` / `test-task-07.php`):** تک‌نوشته: هدر، متا و شمارنده بازدید
- `[PASSED]` **Task 7.8 (`task-08.md` / `test-task-08.php`):** تک‌نوشته: فهرست مطالب خودکار (TOC)
- `[PASSED]` **Task 7.9 (`task-09.md` / `test-task-09.php`):** تک‌نوشته: اشتراک‌گذاری، مرتبط‌ها و دیدگاه‌ها
- `[PASSED]` **Task 7.10 (`task-10.md` / `test-task-10.php`):** قالب جستجو (search.php)
- `[PASSED]` **Task 7.11 (`task-11.md` / `test-task-11.php`):** قالب 404
- `[PASSED]` **Task 7.12 (`task-12.md` / `test-task-12.php`):** قالب درباره ما
- `[PASSED]` **Task 7.13 (`task-13.md` / `test-task-13.php`):** قالب تماس با ما
- `[PASSED]` **Task 7.14 (`task-14.md` / `test-task-14.php`):** قالب مجوزها و افتخارات
- `[PASSED]` **Task 7.15 (`task-15.md` / `test-task-15.php`):** قالب سوالات متداول
- `[PASSED]` **Task 7.16 (`task-16.md` / `test-task-16.php`):** قالب شرایط گارانتی و بازگشت
- `[PASSED]` **Task 7.17 (`task-17.md` / `test-task-17.php`):** قالب استخدام (۴ واحد)
- `[PASSED]` **Task 7.18 (`task-18.md` / `test-task-18.php`):** قالب خدمات مادر (smartelement)
- `[PASSED]` **Task 7.19 (`task-19.md` / `test-task-19.php`):** قالب خدمت تکی (برگه‌های فرزند)
- `[PASSED]` **Task 7.20 (`task-20.md` / `test-task-20.php`):** لندینگ درب بازکن هوشمند آریا
- `[PASSED]` **Phase 7 Suite (`run-phase-07-tests.sh`):** 20/20 tests passed. Regression suites Phase 1–7 all passed after installing WooCommerce 11.2.1 from the user-provided built ZIP. Stop here for Phase 7 approval; do not start Phase 8.

---

### Phase 8: جستجو و فیلتر Native، فرم‌ها، سئو و ممیزی نهایی (`temp/phase-08-ajax-filters-and-audit`) — 17 تسک

- `[PASSED]` **Task 8.1 (`task-01.md` / `test-task-01.php`):** زیرساخت AJAX قالب
- `[PASSED]` **Task 8.2 (`task-02.md` / `test-task-02.php`):** جستجوی زنده محصولات
- `[PASSED]` **Task 8.3 (`task-03.md` / `test-task-03.php`):** جستجوی زنده پروژه‌ها و مقالات
- `[PASSED]` **Task 8.4 (`task-04.md` / `test-task-04.php`):** Query Builder فیلتر محصولات
- `[PENDING]` **Task 8.5 (`task-05.md` / `test-task-05.php`):** فیلتر وضعیت خرید آنلاین (true_sale)
- `[PASSED]` **Task 8.6 (`task-06.md` / `test-task-06.php`):** فیلترهای ۱۸ ویژگی pa_*
- `[PENDING]` **Task 8.7 (`task-07.md` / `test-task-07.php`):** رندر نتایج فیلتر و صفحه‌بندی AJAX محصولات
- `[PENDING]` **Task 8.8 (`task-08.md` / `test-task-08.php`):** اتصال JS فیلتر محصولات بدون تغییر URL
- `[PENDING]` **Task 8.9 (`task-09.md` / `test-task-09.php`):** فیلتر پروژه‌ها (نوع، شهر، جستجو)
- `[PENDING]` **Task 8.10 (`task-10.md` / `test-task-10.php`):** فیلتر وبلاگ (دسته و جستجو)
- `[PENDING]` **Task 8.11 (`task-11.md` / `test-task-11.php`):** دراور فیلتر موبایل و همگام‌سازی وضعیت
- `[PENDING]` **Task 8.12 (`task-12.md` / `test-task-12.php`):** FormManager: هسته و ذخیره پیام‌ها
- `[PENDING]` **Task 8.13 (`task-13.md` / `test-task-13.php`):** فرم تماس/مشاوره و استعلام محصول
- `[PENDING]` **Task 8.14 (`task-14.md` / `test-task-14.php`):** فرم استخدام و آپلود رزومه
- `[PENDING]` **Task 8.15 (`task-15.md` / `test-task-15.php`):** فرم بازخورد مشتریان (client-feedback)
- `[PENDING]` **Task 8.16 (`task-16.md` / `test-task-16.php`):** ممیزی سئو و داده ساخت‌یافته
- `[PENDING]` **Task 8.17 (`task-17.md` / `test-task-17.php`):** ممیزی امنیت، کارایی و E2E نهایی
- `[PENDING]` **Phase 8 Suite (`run-phase-08-tests.sh`):** اجرای همه 17 تست این فاز + گزارش و توقف برای تایید کارفرما (فاز به فاز).

---

### Phase 1 (فریز — بدون تغییر)
- `[PASSED]` Task 1.1 اسکلت هسته و PSR-4 — کامیت `6b85da5`
- `[PASSED]` Task 1.2 DeBloat — کامیت `f0a04e0`
- `[PASSED]` Task 1.3 Assets و AssetManager — کامیت `45b2f36`
- `[PASSED]` سوئیت فاز ۱: `91/91 Checks PASSED`


## قواعد اجرای هر تسک (الزامی)
1. چرخه ۶ گامی TDD (`READ MEMORY → THINK & PLAN → WRITE TEST (RED) → IMPLEMENT → RUN TEST → UPDATE MEMORY + commit`).
2. هیچ تسکی بدون سبز شدن تست اختصاصی خودش `[PASSED]` نمی‌شود.
3. در پایان هر فاز: اجرای `run-phase-XX-tests.sh`، گزارش جامع فارسی و توقف تا تایید کارفرما.
4. ممنوعیت placeholder/TODO و ممنوعیت تغییر URL در فیلترها (`filterUrlMutation: false`).
