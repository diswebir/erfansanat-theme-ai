# نقشه راه ارشد پروژه

> وضعیت فعلی: Phases 1–7 تکمیل و regression شده‌اند؛ Tasks 7.1–7.3 نیز پاس شده‌اند. Phase 8 هنوز شروع نشده و به مجوز مستقل نیاز دارد.

## گیت‌های ورودی و تصمیمات

- [PASSED] Stage 0: ابزارها/میرور اسکن شد؛ اسناد پایه ایجاد شدند.
- [PASSED] پاسخ‌های کاربر: مقایسه/ادغام دو مسیر محصول؛ مدل خدمات به‌صورت CPT؛ سکشن‌ها همگی قابل ویرایش؛ فیلترهای AJAX.
- [PASSED] Task 1.1 پیاده‌سازی و در WordPress 7.2-beta3 نصب موقت با SQLite فعال و بارگذاری شد؛ contract test و PHP lint پاس شدند؛ WP_DEBUG log خالی بود.
- [PASSED] کاربر مجوز Task 1.2 را داد؛ DeBloat پیاده‌سازی و تست شد.
- [PASSED] کاربر مجوز Task 1.3 را داد؛ assets منتقل و HTTP test اجرا شد.
- [PASSED] کاربر اجرای کامل Phase 2 را مجاز کرد؛ Tasks 2.1–2.3 پیاده‌سازی و تست شدند.
- [PASSED] کاربر پس از تکمیل Phase 2 دستور «ادامه بده» داد؛ Phase 3 مجاز شد.
- [PASSED] کاربر پس از تکمیل Phase 3 مجدداً «ادامه بده» گفت؛ Phase 4 مجاز شد؛ Task 4.1 اجرا شد.
- [PASSED] کاربر پس از Task 4.1 برای Task 4.2 نیز «اوکیه ادامه بده» گفت؛ پیاده‌سازی مگامنو/منوهای موبایل مجاز شد.
- [PASSED] کاربر Phase 5 را مجاز کرد و پس از Task 5.1 اجرای Task 5.2 را تأیید کرد؛ هر دو تسک پاس شدند.
- [PASSED] کاربر اجرای Task 6.1 را صریحاً مجاز کرد؛ سپس تکمیل remainder Phase 6 (Tasks 6.2 و 6.3) را نیز مجاز کرد. هر سه تسک Phase 6 تکمیل شدند.
- [PASSED] کاربر ورود به Phase 7 را مجاز کرد؛ Tasks 7.1–7.3 به‌ترتیب پیاده‌سازی و integration/regression شدند. Phase 8 خارج از محدوده این مجوز است.

## فازها و تسک‌ها

### Phase 1 — زیرساخت هسته قالب، Autoloader و انتقال Assets استاتیک (`phase-01-core-and-assets`)

- [PASSED] **1.1 اسکلت قالب و معماری OOP** — شروط پذیرش در `phase-01-core-and-assets/tasks/task-01.md`; تست در `phase-01-core-and-assets/tests/test-task-01.php`.
- [PASSED] **1.2 پاک‌سازی منابع پیش‌فرض وردپرس** — شروط پذیرش در `phase-01-core-and-assets/tasks/task-02.md`; تست در `phase-01-core-and-assets/tests/test-task-02.php`.
- [PASSED] **1.3 انتقال Assets و enqueue شرطی** — شروط پذیرش در `phase-01-core-and-assets/tasks/task-03.md`; تست در `phase-01-core-and-assets/tests/test-task-03.php`.
- تست یکپارچه: `phase-01-core-and-assets/tests/run-phase-tests.sh`

### Phase 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder (`phase-02-custom-admin-dashboard`)

- [PASSED] **2.1 پنل تنظیمات قالب** — شروط پذیرش در `phase-02-custom-admin-dashboard/tasks/task-01.md`; AJAX امن، helper کش‌دار، پنل RTL و integration test پاس.
- [PASSED] **2.2 تب‌ها و تنظیمات سراسری** — defaults منطبق با میرور برای header/footer/contact/social، palette CSS variables، scripts admin-only و archive defaults؛ integration test پاس.
- [PASSED] **2.3 مدیریت سکشن‌ها و Section Builder** — post meta امن، nonce، کنترل متن/رسانه/link/icon/video/repeater، ترتیب/visibility و fallback؛ integration test پاس.
- تست یکپارچه: `phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`

### Phase 3 — CPTها، Taxonomyها و سازگاری متادیتای legacy (`phase-03-cpt-and-legacy-meta`)

- [PASSED] **3.1 ثبت CPT و taxonomyها** — projects/services، taxonomies، legacy .html service routes، WooCommerce rewrite filters و admin project filtering/column؛ integration test پاس.
- [PASSED] **3.2 متاباکس محصولات و پشتیبانی از ساختار JetEngine** — کلیدهای legacy دقیق، JSON/serialization، fallback گالری، repeater و nonce/capability؛ integration test پاس.
- [PASSED] **3.3 متاباکس پروژه، term meta، hreflang و views** — فیلدهای legacy، گالری همگام، FAQ/thumbnail، hreflang پست/ترم، fallback بازدید و nonce/capability؛ integration test پاس.
- تست یکپارچه: `phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`

### Phase 4 — هدر، فوتر و ناوبری (`phase-04-header-footer-navigation`)

- [PASSED] **4.1 برش header/footer** — ساختار desktop/mobile/search/cart/footer از میرور، option-driven branding/topbar/announcement/footer، URLهای WP، hreflang legacy با guard ضدتکرار SEOPress؛ integration test پاس.
- [PASSED] **4.2 مگامنو و منوهای موبایل** — چهار موقعیت منو و custom walkerهای desktop/mobile/product/landing؛ selectorهای JS، تب‌های محصول/پروژه، off-canvas، ادغام با header/footer و fallback میرور؛ integration test پاس.
- تست یکپارچه: `phase-04-header-footer-navigation/tests/run-phase-tests.sh`

### Phase 5 — خانه و اجزای ماژولار (`phase-05-homepage-and-sections`)

- [PASSED] **5.1 تبدیل سکشن‌های خانه** — ۱۵ سکشن میرور به template partهای جدا منتقل شدند؛ ریشه‌های section، DOM/classes/IDs/data attributes و ترتیب/visibility از طریق SectionManager حفظ و آزموده می‌شوند. تست: `phase-05-homepage-and-sections/tests/test-task-01.php`.
- [PASSED] **5.2 اتصال داده‌های داینامیک خانه** — اسلایدر/دسته‌بندی، محصولات، پروژه‌ها، مقالات، آمار و مشتریان به داده‌های WordPress/WooCommerce و Section Builder متصل شدند؛ queryها محدود/کش‌دارند و کارت‌های مشترک استفاده می‌شوند. تست: `phase-05-homepage-and-sections/tests/test-task-02.php`.
- تست یکپارچه: `phase-05-homepage-and-sections/tests/run-phase-tests.sh`

### Phase 6 — قالب‌های WooCommerce و محصول (`phase-06-woocommerce-and-products`)

- [PASSED] **6.1 صفحه تک‌محصول** — خرید/استعلام بر پایهٔ `true_sale`، گالری legacy/Woo، ۱۸ ویژگی `pa_*`، relations، FAQ، reviews و related products؛ integration test پاس شد.
- [PASSED] **6.2 آرشیو فروشگاه و دسته محصول** — قالب‌های shop/category/tag، کارت‌ها و فیلترهای سازگار با فاز ۸، metadata دسته، pagination و empty states؛ integration test پاس شد.
- [PASSED] **6.3 Cart، checkout و my-account** — صفحات بومی از طریق محتوای وردپرس/شورت‌کدها حفظ شدند؛ بدون override چرخه سفارش WooCommerce، استایل RTL/responsive و جلوگیری از دخالت سبد localStorage قالب در صفحات تراکنشی؛ integration test پاس شد.
- تست یکپارچه: `phase-06-woocommerce-and-products/tests/run-phase-tests.sh`

### Phase 7 — پروژه‌ها، بلاگ، برگه‌ها و landingها (`phase-07-projects-blog-and-pages`)

- [PASSED] **7.1 قالب‌های پروژه و taxonomy** — جزئیات، گالری، محصولات مرتبط، آرشیوهای CPT/taxonomy، فیلترهای query، pagination و empty/escaping states؛ WordPress integration test پاس.
- [PASSED] **7.2 وبلاگ، جستجو و 404** — archive/category/single/search/404، TOC، اشتراک‌گذاری، related posts/comments و view counting امن؛ WordPress integration test پاس.
- [PASSED] **7.3 برگه‌های ویژه و landingها** — هشت template، SectionManager schemas/renderers، repeaterها، aliasهای فارسی و محتوای native/shortcode؛ استخدام data selectors به JS موجود وصل است؛ WordPress integration test پاس.
- Phase 7 runner (Tasks 7.1–7.3) و regression فازهای 1–6 پاس؛ WP_DEBUG log خالی. فرم‌های اختصاصی و submission handling به Phase 8 واگذار شده‌اند.
- تست یکپارچه: `phase-07-projects-blog-and-pages/tests/run-phase-tests.sh`

### Phase 8 — جستجوی AJAX، فیلترها، فرم‌ها و ممیزی (`phase-08-ajax-filters-and-audit`)

- [PENDING] **8.1 جستجوی زنده و فیلترهای AJAX** — شروط پذیرش در `phase-08-ajax-filters-and-audit/tasks/task-01.md`; تست در `phase-08-ajax-filters-and-audit/tests/test-task-01.php`.
- [PENDING] **8.2 فرم‌های اختصاصی** — شروط پذیرش در `phase-08-ajax-filters-and-audit/tasks/task-02.md`; تست در `phase-08-ajax-filters-and-audit/tests/test-task-02.php`.
- [PENDING] **8.3 ممیزی نهایی و E2E** — شروط پذیرش در `phase-08-ajax-filters-and-audit/tasks/task-03.md`; تست در `phase-08-ajax-filters-and-audit/tests/test-task-03.php`.
- تست یکپارچه: `phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`

## محیط تست محلی

- راهکار فعال‌سازی WordPress بدون PHP/MySQL native: `temp/setup-wordpress-test.sh`؛ محیط WordPress/SQLite در `/tmp` و PHP WASM.

## پایان پروژه

- [PENDING] اجرای تمام تست‌های فازها، E2E، ممیزی امنیت/SEO/performance و آمادگی استقرار.
- نگاشت canonical محصولات مطابق بلوپرینت زنده حفظ می‌شود؛ دادهٔ `product/` و `products/` پیش از انتخاب markup نهایی مقایسه خواهد شد.
- خدمات به‌عنوان CPT مستقل بنا به تصمیم کاربر؛ جزئیات slug/rewrite باید با URLهای زنده reconcile شود.
