# QA_REFLECTION_LOG — لاگ تفکر، تست و خوداصلاحی

> هر تسک: تحلیل → تست → باگ‌ها → خوداصلاحی → نتیجه. جدیدترین بالا.

---

## 2026-11-03 — P01-T001 (هدر قالب، style.css و حداقل index.php)
- **READ MEMORY:** برنامه اتمیک ۱۷۵ تسکی و دروازه شروع Phase 1 خوانده شد؛ کاربر تایید کرد.
- **THINK & PLAN:** هدف فقط متادیتای پوسته بود؛ اما تست WP نشان داد پوسته کلاسیک بدون `index.php` خطای `theme_no_index` می‌دهد و در فهرست پوسته‌ها نمی‌آید.
- **WRITE TEST:** تست واقعی نوشته شد: کشف پوسته، نبود خطای اعتبارسنجی، هدرهای معتبر، فقط-هدر بودن style.css، فعال‌سازی و بازگردانی پوسته قبلی.
- **IMPLEMENT:** `erfansanat/style.css` با هدر معتبر و `erfansanat/index.php` حداقلی ساخته شد. CSS استاتیک کپی نشد.
- **RUN TEST:** `php -l` روی index.php و تست بدون خطا؛ تست تسک = **12 passed, 0 failed, 0 skipped/pending**. `wp theme list` قالب `erfansanat` را نشان می‌دهد؛ پوسته فعال QA به حالت قبل برگشت.
- **SELF-CORRECTION:** دامنه P01-T001 به‌صورت شفاف اصلاح شد تا حداقل index.php را شامل شود؛ fallback کامل همچنان P01-T005 است. برنامه/تسک/حافظه به‌روزرسانی شدند.
- **UPDATE MEMORY:** وضعیت = P01-T001 PASSED، گام بعد P01-T002.

---

## 2026-11-03 — Stage 1 بازطراحی اتمیک (درخواست کاربر)
- **درخواست کاربر:** تسک‌های هر فاز کم بودند؛ باید اتمیک‌تر، ریزتر، با جزئیات و جامع‌تر شوند.
- **تحلیل:** ۲۲ تسک قبلی چندین مسئولیت مستقل را ادغام کرده بودند (مثلاً 6.1 کل تک‌محصول، 7.3 همه برگه‌ها). این با قانون Atomic Task-by-Task و تست‌پذیری مستقل سازگار نبود.
- **اقدام:** برنامه به **۱۷۵ تسک** شکسته شد: P1=12، P2=18، P3=24، P4=20، P5=21، P6=28، P7=28، P8=24. هر تسک شناسه پایدار `Pxx-Tyyy`، وابستگی قبلی، فایل هدف محدود، منبع استاتیک، کلیدهای داده، شروط پذیرش `AC` و سناریوی تست `SC` دارد.
- **نگاشت:** ۲۲ تسک قبلی حذف/بایگانی معنایی شدند و نگاشت کامل در `LEGACY_TASK_MAP.md` ثبت شد؛ هیچ قابلیت قبلی گم نشد. قابلیت‌های پوشش‌داده‌نشده قبلی (migration dry-run، rollback، rate-limit فرم، mini-cart AJAX، proforma، parity/a11y، بسته انتشار) به تسک مستقل اضافه شدند.
- **اصلاح تست:** `es_pending` و `es_summary(strict)` اضافه شد؛ سناریوی رفتاری خودکارنشده دیگر نمی‌تواند silent pass بدهد. runner فاز در صورت FAIL/PENDING exit 1 می‌دهد. فعال‌سازی قالب در runner بدون `|| true` انجام می‌شود.
- **اعتبارسنجی:** جنریتور وابستگی‌ها را assert می‌کند (وابستگی رو به جلو/گم‌شده ممنوع). `php -l` روی ۱۷۶ فایل PHP = بدون خطا. تعداد task/test = ۱۷۵/۱۷۵. فایل‌های دوب رقمی قبلی = ۰. اجرای نمونه P03-T015 = FAIL target + PENDING scenarios (قرمزِ مورد انتظار).
- **نتیجه:** برنامه اتمیک آماده؛ همچنان هیچ کد قالبی نوشته نشده و در انتظار تایید کاربر برای P01-T001 هستیم.

---

## 2026-11-03 — Stage 1 (ساخت درخت فازها)
- **تحلیل:** پاسخ‌های کاربر اعمال شد (D1..D8). نکته کلیدی D3: اسلاگ ترم‌های لایو فارسی است → قالب‌ها slug-agnostic؛ تست‌ها باید با ترم فارسی (`اصفهان`) هم کار کنند.
- **تصمیم معماری D8:** سورس قالب در `erfansanat/`؛ mu-plugin محیط تست ریشه ریپو را theme directory می‌کند (تست شد: `wp eval` نشان داد ثبت شده).
- **باگ/محدودیت کشف‌شده:** دانلود zip بیلدشده WooCommerce از release گیت‌هاب → ریدایرکت به `objects.githubusercontent.com` (مسدود) → TLS EOF. **خوداصلاحی:** فاز ۶ با shim سازگاری ووکامرس تست می‌شود (تایید کاربر D7)؛ نهایی‌سازی روی استیجینگ.
- **اقدام:** ساخت ۸ پوشه فاز × (tasks + tests) + bootstrap تست + run-phase-tests.sh. اسکلت تست‌ها الان **قرمز (TDD)** هستند چون هنوز قالبی ساخته نشده — این مورد انتظار است.
- **باگ ۱ (خوداصلاحی):** جنریتور مسیر ریشه را اشتباه محاسبه می‌کرد و فایل‌ها را در `temp/temp/` می‌ساخت → اصلاح `ROOT` (سه سطح بالا) و حذف پوشه اشتباه؛ بازتولید در مسیر صحیح.
- **باگ ۲ (خوداصلاحی — مهم):** چک‌های وابسته به WP روی قالب پیش‌فرض (twentytwentyfive) اجرا می‌شدند و `post-thumbnails`/`html5` را **به‌اشتباه PASS** می‌کردند → حالا فقط وقتی قالب خودمان فعال باشد اجرا می‌شوند (`es_activate_theme()`) وگرنه SKIP. runner هم قالب را در پروسه جدا pre-activate می‌کند تا `after_setup_theme` درست اجرا شود.
- **تست اجراشده:** `php -l` روی هر ۲۳ فایل PHP = بدون خطا. اجرای task 1.1 = `0 passed, 6 failed, 5 skipped` (قرمزِ مورد انتظار). exit code runner = 1 (صحیح).
- **نتیجه:** درخت آماده؛ در انتظار تایید کاربر برای Phase 1.

---

## 2026-11-03 — Stage 0 (Bootstrapping)
- **کشف:** فایل پیوست `erfansanat-static-mirror.zip.png` در واقع ZIP بود (magic bytes `PK`) → استخراج به `static-mirror/` (۱۲۸ HTML).
- **محدودیت شبکه:** apt/wordpress.org مسدود → PHP از `@php-wasm/node` (نصب موفق: 8.4.27). `proc_open` در WASM نیست → هشدار بی‌ضرر wp-cli `--info`.
- **باگ میرور:** `<title>undefined</title>` در `category/magazine.html` و `category/news-and-events.html` → در وردپرس از title-tag/SEOPress استفاده می‌شود (الگو نیست).
- **کشف ساختاری:** ۴۰ فایل Alias (فقط canonical متفاوت) → پیشنهاد 301 (تایید شد D3). هدر/فوتر تماماً data-attribute (بدون id).
- **نتیجه:** هارنس سالم (`wp core install` + `wp eval` موفق)؛ ۴ فایل حافظه ساخته شد.

---

## 2026-11-03 — Phase 1 تکمیل شد (P01-T001..T012)
- **READ MEMORY:** PROJECT_MEMORY و تسک‌های فاز ۱ خوانده شد. کاربر مجوز داد همه تسک‌های فاز ۱ بدون توقف بین تسک‌ها کامل شوند.
- **THINK & PLAN:** ترتیب وابستگی رعایت شد؛ چون `functions.php` به Autoloader/Theme/DeBloat/AssetManager نیاز دارد، ماژول‌ها قبل از تست bootstrap ساخته شدند. Assetها عیناً از mirror کپی شدند (hash یکسان).
- **WRITE TEST + IMPLEMENT:** برای هر تسک تست اختصاصی واقعی نوشته شد (بدون PENDING). فایل‌های هدف: `functions.php`, `inc/Constants.php`, `inc/Autoloader.php`, `inc/Core/Theme.php`, `inc/Core/DeBloat.php`, `inc/Core/AssetManager.php`, `index.php`, `assets/` و `temp/tools/package-theme.sh`.
- **RUN TEST / SELF-CORRECT:**
  - محیط کار در ابتدای ادامه به snapshot قدیمی برگشته بود؛ با `git fetch`/`reset --hard origin/arena/...` به `adcc499` بازیابی شد و هارنس بازسازی شد.
  - `run-phase-tests.sh` مسیر ROOT را اشتباه محاسبه می‌کرد (`../..` به‌جای `../../..`) → اصلاح شد و جنریتور هم اصلاح شد تا بازتولید سالم بماند.
  - PHP-WASM امکان `shell_exec`/HTTP ندارد → تست HTTP 200 با اسکریپت Python (`check_assets_http.py`) انجام و گزارش JSON به تست‌های PHP داده می‌شود.
  - `set_current_screen('dashboard')` در WP-CLI باعث توقف می‌شد → تست admin با `define('WP_ADMIN', true)` شبیه‌سازی شد.
  - `functools.partial` قابل subclass نبود → handler سرور HTTP اصلاح شد.
- **نتیجه تست‌ها:** T001=12، T002=12، T003=6، T004=9، T005=7، T006=6، T007=11، T008=11، T009=7، T010=8، T011=13، T012=9؛ مجموعاً **111 چک پاس، ۰ fail، ۰ pending**. `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh` → `PHASE RESULT: PASS`.
- **Asset/Package:** `check-assets-http.sh` → 27/27 HTTP 200 و تطابق اندازه. `package-theme.sh` → `temp/dist/erfansanat.zip` فقط شامل `erfansanat/` (بدون static-mirror/temp/node_modules). تست T012 زیپ را extract و پوسته بسته‌بندی‌شده را فعال/برگرداند.
- **UPDATE MEMORY + Git:** وضعیت فاز ۱ PASSED؛ گام بعدی Phase 2 / P02-T001 در انتظار تایید کاربر. کامیت‌ها روی برنچ `arena/7378effd-erfansanat-theme-ai`.

### خوداصلاحی تکمیلی فاز ۱ (پایداری تست‌ها)
- **باگ ۳ (مهم):** تست T012 پوسته بسته‌بندی‌شده را در `temp/dist/package-test` استخراج و فعال می‌کرد؛ `switch_theme` باعث می‌شد `stylesheet_root`/`template_root` روی مسیر موقت بمانند و در اجرای بعدی `wp_get_theme('erfansanat')` خطای `theme_not_found` بدهد. **اصلاح:** استخراج در مسیر یکتا، تغییر نام به `erfansanat-packaged`، بازگرداندن صریح `stylesheet_root`/`template_root`، پاک‌سازی کامل مسیر موقت. تست‌های T001/T004 نیز rootها را صریح برمی‌گردانند.
- **نتیجه:** runner فاز ۱ دو بار متوالی `PHASE RESULT: PASS`؛ هیچ leftover package-test؛ ریشه پوسته فعال سالم.

---

## 2026-11-03 — Phase 2 تکمیل شد (P02-T001..T018)
- **READ MEMORY:** PROJECT_MEMORY + تسک‌های فاز ۲ خوانده شد. کاربر دو بار «ادامه بده» داد → اجرای پیوسته تا پایان فاز بدون توقف بین تسک‌ها؛ گیت پایان فاز قبل از فاز ۳ باقی است.
- **THINK & PLAN:** Schema منبع واحد حقیقت آپشن‌ها؛ Store با رد کلید ناشناخته/بازگشت به پیش‌فرض در خرابی؛ Sanitizer per-type با خطای فیلدی (بدون abort دسته‌ای)؛ Section Builder با متای نسخه‌دار و رندر کش‌شونده.
- **WRITE TEST + IMPLEMENT:** برای هر ۱۸ تسک تست واقعی نوشته شد. فایل‌های هدف: `inc/Options/{Schema,Store,Sanitizer}.php`, `inc/Options/Tabs/{Header,MegaMenu,Footer,Brand,Archive}Tab.php`, `inc/Admin/{ThemeOptions,FieldRenderer,SectionMetabox}.php`, `inc/Sections/{Schema,SectionManager,Renderer}.php`, `assets/admin/{options,media-field,repeater,sections}.js`, `functions.php`.
- **RUN TEST / SELF-CORRECT:**
  - **Regression #2 workspace:** ابتدای ادامه، `.git` به `8eddaff` برگشته و پوشه‌های gitignore (`temp/wp`, `node_modules`, `bin`) حذف شده بودند؛ کامیت‌های T001–T013 فقط local بودند و از دست رفتند. **بازیابی:** `git fetch` + `git reset --mixed origin/arena/...` (نه hard/clean — کار کامیت‌نشده T014 حفظ شد)، بازسازی ۱۳ کامیت از فایل‌های سالم (guardهای `class_exists` فایل‌های مشترک را backward-safe می‌کردند)، push، بازسازی هارنس. **قاعده دائمی: بعد از هر کامیت بلافاصله push.**
  - **باگ AJAX در WASM:** `wp_send_json` وقتی `DOING_AJAX` تعریف نشده باشد `die` می‌کند (تست را می‌کشت). **اصلاح:** الگوی T008 — `define('DOING_AJAX',true)` + فیلتر `status_header` برای گرفتن کد + throw در `wp_die_handler` + ob برای گرفتن JSON. تست T016 با همین الگو بازنویسی شد.
  - **AC-01 سخت‌گیرانه:** `save_section_fields` ابتدا فیلدهای موجود را نگه می‌داشت (کلید خارج از schema باقی می‌ماند). **اصلاح:** بازسازی `$clean` فقط از کلیدهای schema؛ مقدار خالی فیلد را حذف نمی‌کند؛ فیلد ارسال‌نشده حفظ می‌شود؛ media/link/icon نامعتبر مقدار خوب قبلی را بازنویسی نمی‌کند.
  - **Renderer:** `locate_template` + fallback امن → template-part ناموجود fatal نمی‌دهد؛ کش per-page/context با invalidation روی `erfansanat_sections_updated`.
- **نتیجه تست‌ها:** T001=16، T002=10، T003=9، T004=24، T005=30، T006=19، T007=18، T008=13، T009=24، T010=11، T011=7، T012=10، T013=10، T014=9، T015=14، T016=12، T017=14، T018=10 → **۲۶۰ چک پاس، ۰ fail، ۰ pending**. `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh` → `PHASE RESULT: ALL PASS`. گارد activate در runner مانند فاز ۱ سخت‌گیرانه شد (`is-active` + `--allow-root`).
- **UPDATE MEMORY + Git:** وضعیت فاز ۲ PASSED؛ گام بعدی Phase 3 در انتظار تایید کاربر. همه کامیت‌ها روی برنچ `arena/7378effd-erfansanat-theme-ai` و بلافاصله push شدند.

---

## 2026-11-03 — Phase 3 تکمیل شد (P03-T001..T024)
- **READ MEMORY:** PROJECT_MEMORY + ۲۴ تسک فاز ۳ + DATA_BLUEPRINT خوانده شد. کاربر «ادامه بده و برو فاز بعدی رو کامل کن» داد → اجرای پیوسته تا پایان فاز.
- **THINK & PLAN:** CPT/تکسونومی بومی با guard ثبت تکراری؛ rewrite ووکامرس فقط با فیلتر args (نه ثبت مجدد)؛ codec مشترک LegacyMeta زیربنای همه متاباکس‌ها؛ ریدایرکت‌ها با نگاشت فیلترپذیر برای اسلاگ فارسی لایو (D3).
- **WRITE TEST + IMPLEMENT:** برای هر ۲۴ تسک تست واقعی نوشته شد. فایل‌های هدف: `inc/Core/{PostTypes,Taxonomies,Rewrites,AttributeMap,LegacyRedirects,LegacyMeta,ViewCounter}.php`, `inc/Admin/{ProjectColumns,ProductMetaboxes,ProductQuickEdit,ProjectMetaboxes,TermMetaboxes,HreflangMetabox}.php`, `functions.php`.
- **RUN TEST / SELF-CORRECT:**
  - **Regression #3 workspace:** میانه فاز، `.git` به `8eddaff` برگشت و پوشه‌های gitignore حذف شدند؛ چون همه کامیت‌های قبلی push شده بودند، چیزی از دست نرفت. **بازیابی:** `git fetch` + `git reset --mixed origin/arena/...` (کار کامیت‌نشده T016 حفظ شد) + `setup-harness.sh` + فعال‌سازی قالب + permalink. قاعده push-بعد-از-هر-کامیت دوباره نجات‌بخش بود.
  - **باگ slashing (مهم):** `update_metadata` هسته `wp_unslash` می‌کند → بک‌اسلش‌های `\u` در JSON حذف می‌شدند (ترم‌متای فارسی خراب می‌شد). **اصلاح:** `wp_slash($value)` قبل از update_metadata در LegacyMeta. این کشف برای همه متاباکس‌های بعدی حیاتی بود.
  - **باگ hierarchical rewrite:** product_cat باید `rewrite['hierarchical']=true` هم داشته باشد تا URL تودرتو حفظ شود (SC-02 T005).
  - **باگ ایزولاسیون تست (چندبار):** هوک‌های `save_post_*`/`created_*` هنگام ساخت پست/ترم جدید در تست، `$_POST` باقی‌مانده را ذخیره می‌کردند. **اصلاح:** پاک‌سازی `$_POST` قبل از ساخت fixture جدید در تست‌های T015/T020/T022.
  - **نقل‌قول dropdown:** `wp_dropdown_categories` با نقل‌قول تکی `name='city'` رندر می‌کند → تست با regex نقل‌قول‌agnostic اصلاح شد.
- **نتیجه تست‌ها:** T001=15، T002=12، T003=15، T004=14، T005=13، T006=10، T007=10، T008=14، T009=12، T010=12، T011=12، T012=16، T013=10، T014=10، T015=16، T016=10، T017=13، T018=11، T019=13، T020=16، T021=10، T022=12، T023=11، T024=14 → **۳۰۱ چک پاس، ۰ fail، ۰ pending**. `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh` → `PHASE RESULT: ALL PASS`. گارد activate سخت‌گیرانه شد.
- **UPDATE MEMORY + Git:** وضعیت فاز ۳ PASSED؛ گام بعدی Phase 4 در انتظار تایید کاربر. همه کامیت‌ها روی برنچ `arena/7378effd-erfansanat-theme-ai` و بلافاصله push شدند.

---

## 2026-11-04 — Phase 4 تکمیل شد (P04-T001..T020)
- **READ MEMORY:** PROJECT_MEMORY + ۲۰ تسک فاز ۴ + DATA_BLUEPRINT + DOM مرجع خوانده شد. کاربر «ادامه بده» داد → اجرای پیوسته تا پایان فاز؛ گیت پایان فاز قبل از فاز ۵ باقی است.
- **THINK & PLAN:** منوها با fallback واقعی (بدون داده ساختگی)؛ واکرهای NavWalkers هم‌DOM با مرجع تا main.js دست‌نخورده بماند؛ مگامنوها taxonomy-driven با آپشن‌های تب‌بندی؛ CTA/فوتر/هدر تماماً option-driven؛ hreflang استاندارد با تعویق به افزونه‌ها.
- **WRITE TEST + IMPLEMENT:** برای هر ۲۰ تسک تست واقعی نوشته شد. فایل‌های هدف: `inc/Core/{Menus,NavWalkers,Hreflang}.php`, `header.php`, `footer.php`, `template-parts/header/{topbar,brand-row,mega-products,mega-projects,mega-corporate,mobile-menu,search-modal,cart-modal}.php`, `template-parts/global/{footer-info,cta-contact}.php`, `assets/js/navigation.js`, `inc/Options/Schema.php` + `Tabs/FooterTab.php` (وابستگی‌های ثبت‌شده)، `functions.php`.
- **RUN TEST / SELF-CORRECT:**
  - **باگ WP 7.1 Walker:** `Walker::display_element` حالا `$this->has_children` را روی خود واکر ست می‌کند؛ واکرها باید همین را بخوانند (نه args->has_children به‌تنهایی). در T005 کشف و در همه واکرها اعمال شد.
  - **محدودیت هارنس (T015):** فراخوانی دوم `get_header()`/`get_footer()` در یک درخواست، بافر خروجی را خالی می‌کند (wp_footer دوباره). تست‌های رندر چندحالته به رندر واحد + چک لایه داده یا `get_template_part` مستقیم تغییر کردند.
  - **main.js دست‌نخورده:** Escape/focus برای کشوی موبایل در main.js نبود → `navigation.js` کمکی (فقط افزایشی). هرگز main.js ویرایش نشد.
  - **Python f-string + بک‌اسلش PHP:** ساخت قالب با f-string خطای SyntaxError می‌داد → الگوی raw-string + `.replace('__TOKEN__',…)` برای تولید PHP (T014/T016).
  - **اتریبیوت‌های بی‌اثر مرجع:** `custom` (Framer) در main.js مصرف نمی‌شود و در هیچ part نگه داشته نشد؛ `grid-Floor` در styles.css نیست ولی برای parity DOM در footer ماند.
  - **انحراف ثبت‌شده T013:** دکمه نمونه‌ساز `data-cart-modal-restore` با لینک واقعی فروشگاه جایگزین شد (داده واقعی فقط).
  - **وابستگی‌های ثبت‌شده:** T008/T016 کلیدهای Schema را گسترش دادند (الگوی امن — P02-T001 فقط سازگاری کلید را چک می‌کند نه تعداد)؛ T018 کلاس hover را به fallback تولیدات اضافه کرد؛ T017/T019 boot در functions.php.
- **نتیجه تست‌ها:** T001=14، T002=15، T003=9، T004=15، T005=13، T006=10، T007=14، T008=11، T009=16، T010=12، T011=11، T012=16، T013=18، T014=11، T015=14، T016=22، T017=23، T018=15، T019=18، T020=33 → **۳۱۰ چک پاس، ۰ fail، ۰ pending**. `bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh` → `PHASE RESULT: ALL PASS`. گارد activate سخت‌گیرانه شد. رگرسیون کامل فازهای ۱–۳: ۱۱۱+۲۶۰+۳۰۱ پاس. **تجمعی: ۹۸۲ چک.**
- **UPDATE MEMORY + Git:** وضعیت فاز ۴ PASSED؛ گام بعدی Phase 5 در انتظار تایید کاربر. همه کامیت‌ها روی برنچ `arena/7378effd-erfansanat-theme-ai` و بلافاصله push شدند (آخرین: `6ecbe7d`).

---

## 2026-11-04 — Phase 5 تکمیل شد (P05-T001..T021)
- **READ MEMORY:** PROJECT_MEMORY + ۲۱ تسک فاز ۵ + Schema سکشن‌ها + DOM مرجع + هوک‌های main.js خوانده شد. کاربر «ادامه بده» داد → اجرای پیوسته تا پایان فاز؛ گیت پایان فاز قبل از فاز ۶ باقی است.
- **THINK & PLAN:** ۱۳ سکشن صفحه اصلی با ترتیب ثابت (hero, project-types, featured-projects, about, localization, history, products, catalog, services, standards, support, clients, blog-preview) از طریق `Sections\Renderer`؛ هر سکشن یک part در `template-parts/sections/home/section-{type}.php` (blog-preview → section-blog.php). کارت‌های استاندارد (T002–T004) بازاستفاده شدند. main.js دست‌نخورده؛ همه هوک‌های data-* مرجع تولید شدند. repeater خالی → پیش‌فرض‌های فیلترپذیر (عمدی، تا سکشن هرگز ناپدید نشود).
- **WRITE TEST + IMPLEMENT:** برای هر ۲۱ تسک تست واقعی نوشته شد. فایل‌های هدف: `front-page.php`, `template-parts/cards/card-{project,product,post}.php`, `template-parts/sections/home/section-*.php` (۱۳ سکشن), `inc/Options/Tabs/HomeTab.php`, `inc/Sections/Schema.php` (وابستگی‌های ثبت‌شده). اسکریپت‌های ساخت `temp/phase-05-homepage-and-sections/build-*.py` + مراجع استخراجی `ref-section-*.html` + `ref-*-svg.json`.
- **RUN TEST / SELF-CORRECT:**
  - **رگرسیون workspace #5:**.snapshot .git را به کامیت پایه `8eddaff` برگرداند و node_modules هارنس را حذف کرد. بازیابی: `git fetch` + `git reset --mixed origin/<branch>` (هرگز --hard/clean) + `bash temp/tools/setup-harness.sh` + فعال‌سازی قالب + `/%postname%/` + flush درون‌فرآیندی (`wp eval 'flush_rewrite_rules(true);'` چون `wp rewrite flush` در WASM با proc_open شکست می‌خورد). همه کارهای کامیت‌شده دست‌نخورده ماندند.
  - **تله ارقام فارسی (چندبار):** literalهای فارسی دست‌نویس اشتباه از آب درآمدند (`۱۳۰`→`۱۳۸۰` T011، `۱۴۵`→`۱۴۰۵` T012، شمارنده `۱ / ` ناقص T015، regex کلاس‌کاراکتر ناقص T016، تلفن `۰۲۱-…` غلط T017). راه‌حل قطعی: ساخت رشته فارسی از ASCII با `chr(0x6F0+int(c))` در پایتون و assert حذف old/حضور new بعد از هر replace.
  - **data-product-category نام است نه slug:** کارت محصول (T003) از `wp_list_pluck(...,'name')` استفاده می‌کند → assertion تست T013 با نام دسته اصلاح شد.
  - **tel:tel: مرجع:** باگ href دوتایی در بخش پشتیبانی مرجع → به `tel:` نرمال‌شده (فقط رقم/+) اصلاح شد (SC-02 T017). چک ایستا به‌جای منفی، مثبت شد (`'tel:' . $es_tel`) چون docblock literal tel:tel: داشت.
  - **get_footer() در docblock:** شمارش `get_footer()` در front-page.php شامل اشاره docblock هم می‌شد → تست T020 به `get_footer();` (با نقطه‌ویرگول) تغییر کرد.
  - **پست نمونه نصب تازه:** پس از بازسازی هارنس، پست پیش‌فرض «سلام دنیا» با تاریخ روز، شمارش کارت‌های وبلاگ را خراب می‌کرد → تست T019 ابتدا همه postها را پاک می‌کند (clean slate).
  - **بودجه کوئری (T021):** رندر کامل خانه با داده خالی=۱۱ کوئری، با ۸ محصول/۶ پروژه/۵ خدمت/۳ مقاله=۱۸ کوئری (WP_Query کش متا/ترم را prime می‌کند؛ بدون N+1). بودجه گارد ≤۶۰ تعیین شد.
  - **انحراف‌های ثبت‌شده:** T013 کارت‌های یکنواخت استاندارد به‌جای featured-first مرجع؛ badge کارت‌های standards و url مشتری‌ها کلید اضافهٔ فقط-پیش‌فرض (Schema ندارد)؛ باکس فرآیند ۵ گام standards ایستا با محتوای فیلترپذیر؛ `custom` (Framer) حذف؛ زیرعنوان/باکس‌های آمار تاریخچه که در Schema نیستند حذف.
- **نتیجه تست‌ها:** T001=14، T002=24، T003=31، T004=18، T005=20، T006=16، T007=23، T008=17، T009=15، T010=20، T011=14، T012=15، T013=18، T014=15، T015=23، T016=24، T017=20، T018=21، T019=19، T020=22، T021=21 → **۴۱۰ چک پاس، ۰ fail، ۰ pending**. `bash temp/phase-05-homepage-and-sections/tests/run-phase-tests.sh` → `PHASE RESULT: ALL PASS`. رگرسیون P02-T018 Renderer بعد از هر part = ۱۰/۱۰. رگرسیون کامل فازهای ۱–۴: ۱۱۱+۲۶۰+۳۰۱+۳۱۰ پاس. **تجمعی: ۱۳۹۲ چک.**
- **UPDATE MEMORY + Git:** وضعیت فاز ۵ PASSED؛ گام بعدی Phase 6 در انتظار تایید کاربر. همه کامیت‌ها روی برنچ `arena/7378effd-erfansanat-theme-ai` و بلافاصله push شدند (T013 `b663c65`، T014 `39ec4ab`، T015 `0b253b3`، T016 `69595f6`، T017 `c4201b9`، T018 `b43b08f`، T019 `a633a58`، T020 `96c4a61`، T021 در کامیت پایانی فاز).

---

## 2026-11-04 — Phase 6 (P06-T001..T007) + درخواست وزیرمتن پنل
- **READ MEMORY:** MASTER_PLAN فاز ۶ (۲۸ تسک) + DATA_BLUEPRINT + main.js (initGalleries/initProductDetailAndFaqs/initPurchaseJourney/optGroups/PDP stepper) + مراجع product/*.html خوانده شد. کاربر فاز ۶ را تأیید کرد («اوکیه ادامه بده») و سپس «ادامه بده و موارد باقی‌مانده را کامل کن» + «ادغام کامل فونت وزیرمتن در پنل پیشخوان قالب».
- **T001 shim (32):** ووکامرس واقعی در سندباکس نصب‌شدنی نیست (release asset → هاست مسدود). shim فقط-QA با گارد `defined('WC_VERSION') || class_exists('WooCommerce')`. باگ خودتحریکی: `function_exists('WC')` بعد از تعریف WC() خود shim true می‌شد → حذف شد. SC-02 با `eval('class WooCommerce {}')`.
- **T002 shell (27):** single-product.php + content-single-product.php؛ ترتیب breadcrumb→h1→gallery→summary→tabs؛ هوک‌های استاندارد + hook-or-fallback؛ ناموجود/نامعتبر امن. خطای ساخت: `var_export` (PHP) در اسکریپت Python → helper phpstr. تست مستقیم content template (نه get_header/get_footer که یک‌بار در هر درخواست هستند).
- **T003 gallery (18):** تصویر شاخص + `ProductMetaboxes::get_gallery` (ترتیب fallback galerry_produ→_product_image_gallery→gallery-filed→image-2..6)؛ تصویر حذف‌شده رندر نمی‌شود؛ لایت‌باکس کامل با هوک‌های initGalleries. وایرینگ در functions.php.
- **T004 summary (17):** نام/زیرعنوان/english_titl در بلوک عنوان hero (نام در کارت خلاصه نیست — طبق مرجع). shell به summary.php واگذار شد (وابستگی ثبت‌شده). زیرعنوان خالی DOM اضافه نمی‌کند؛ escape کامل.
- **T005 legal-ids (19):** شش شناسه (product_id/gs1_id/national_code/hs_id/isic_id/cpc_id) با برچسب فارسی در پنل specs؛ صفر پیشرو حفظ (رشته)؛ خالی=بدون کارت؛ product_id fallback به SKU.
- **T006 purchase (23):** کارت خلاصه؛ true_sale=true+موجود+قیمت‌دار → دکمه سبد data-pdp-add-to-cart با data-product-*؛ استعلامی → CTA پیش‌فاکتور؛ ناموجود/بدون‌قیمت → پیام درست. هوک‌های داخلی mid/after. **باگ ارقام فارسی:** literal دست‌نویس قیمت در تست یک صفر کم داشت → تولید برنامه‌ای با chr(0xDB).chr(0xB0+d). **رگرسیون T002:** تست هوک summary باید مکانیزم را ایزوله کند (remove_all_actions قبل از marker) چون T006 هوک دائمی اضافه کرد.
- **T007 swatches/quantity (43 = 31 PHP + 12 JSDOM):** سواچ pa_color/pa_size با data-opt-*؛ stepper data-pdp-qty-* با min/max/step؛ `Core\ProductQuantity::rules/validate` (رد below_minimum/above_maximum/invalid_step/invalid_quantity). فرم متغیر واقعی `variable.php` با هوک‌های canonical + data-product_variations؛ وقتی WC_VERSION هست جایگزین fallback می‌شود. guard حداکثر `product-quantity.js` فقط بدون Woo (main.js دست‌نخورده). **تست DOM:** JSDOM روی main.js واقعی؛ نیاز به dispatch دستی DOMContentLoaded چون readyState=loading بود. jsdom به temp/tools اضافه شد (setup-harness npm install).
- **نتیجه فاز ۶ تاکنون:** T001=32, T002=27, T003=18, T004=17, T005=19, T006=23, T007=43 → **۱۷۹ چک پاس، ۰ fail/pending** (اسکلت‌های T008+ هنوز pending). تجمعی: **۱۵۷۱**.
- **گام بعد:** P02-T019 وزیرمتن پنل پیشخوان (الحاقی) سپس P06-T008..T028.

## 2026-11-04 — P02-T019 وزیرمتن پنل پیشخوان (27)
- درخواست کاربر: ادغام کامل فونت وزیرمتن در پنل پیشخوان قالب. `Admin\Typography` (enqueue اولویت ۵ + admin_body_class) فقط صفحات قالب: داشبورد، ویرایشگر product/projects/page/post/services، ترم‌های product_cat/product_tag/product_brand/project_type/city. `admin/font.css`: سه subset self-hosted با unicode-range صحیح (U+0600-06FF به‌جای U+6?? خراب مرجع)، وزن 100–900، font-display:swap. آیکون‌فونت‌ها dashicons/media حفظ؛ فیلد کد monospace. options.css @font-face تکراری حذف و به handle فونت وابسته شد. **باگ تست:** کامنت options.css.literal '@font-face' داشت → چک strpos مثبت می‌شد؛ کامنت اصلاح شد. رگرسیون کامل فاز ۲ (۱۹ تست) ALL PASS. تجمعی: **۱۵۹۸**.

## ADMIN-PRO — ارتقای حرفه‌ای داشبورد ادمین (درخواست کاربر، 2026-11-04)
- Commit 97d24cc. کاربر از فاز ۷ به داشبورد ادمین تغییر مسیر داد.
- **باگ رفع‌شده:** فیلد `link` هنگام ذخیره فقط URL را ذخیره می‌کرد و label/target از دست می‌رفت (فرانت‌اند topbar این دو را از آرایه می‌خواند). اصلاح: FieldRenderer گروه `data-es-link-key` + options.js `collectLinkGroups` → ذخیره به‌صورت {url,label,target}. تست JSDOM payload واقعی AJAX را تأیید می‌کند.
- **کامل‌تر:** تب «پیشخوان» (DashboardOverview) به‌عنوان تب اول: کارت‌های آمار محتوا (ارقام فارسی)، دسترسی سریع، جدول وضعیت سیستم. Schema::tabs() prepend شد تا T004 سازگار بماند.
- **فونت فارسی کامل:** رفع هم‌پوشانی unicode-range (ZWNJ U+200C-200E فقط subset عربی)، font-smoothing/optimizeLegibility، پوشش پیشخوان/جدول وضعیت/chrome ادمین، اعداد tabular.
- **UX:** هشدار تغییرات ذخیره‌نشده (beforeunload + dirty).
- تست: test-admin-dashboard-pro.php (25) + test-admin-dashboard-pro-dom.mjs (7). کل فاز ۲: 287 سبز / 0 شکست. پیش‌نمایش خودکفا: preview/admin-dashboard.html (temp/build-admin-preview.php).


## 2026-11-07 — P07-T009 آرشیو اصلی وبلاگ (23)
- `home.php` + `blog-shell.php` + `blog-hero.php` از مرجع blog.html؛ کوئری فقط post/publish و بدون محدودیت مصنوعی برای فیلتر کلاینتی؛ منتخب سردبیر از sticky واقعی با fallback جدیدترین منتشرشده.
- تست PHP **16/0**، تست main.js/JSDOM **7/0**: صفحه اول ۴ کارت، صفحه دوم ۳ کارت، قبلی/بعدی، جستجوی عنوان و حالت خالی. مسیر واقعی `/blog/` با تنظیم صفحه نوشته‌ها و بازنشانی rewrite بررسی شد.
- خوداصلاحی تست: پس از تغییر permalink_structure باید `$wp_rewrite->init()` قبل از flush اجرا شود؛ PATH_INFO/PHP_SELF نیز باید شبیه درخواست وب مقداردهی شوند. مشکل محصول نبود.
- رگرسیون وابستگی‌ها: P05-T004=18، P02-T014=12، P07-T006=12 PHP+7 DOM، P07-T002 DOM=10؛ همگی سبز. runner فاز اکنون تست‌های `*-dom.mjs` را نیز اجرا می‌کند.


## 2026-11-07 — P07-T010 آرشیو دسته/عمومی وبلاگ (12)
- `category.php` و `archive.php` به shell مشترک T009 متصل شدند؛ عنوان/توضیح ترم واقعی، لینک دسته واقعی، آرشیو تاریخ/نویسنده ایمن و فقط محتوای منتشرشده. باگ `undefined` عنوان mirror تکرار نشد.
- وابستگی ثبت‌شده روی T009: blog-hero/blog-shell accepting term/context/description/toolbar. در آرشیو scoped، دسته‌ها لینک ناوبری واقعی‌اند (نه فیلتر کلاینتی که دسته خارج از کوئری را خالی نشان دهد). هیرو/کلاس‌ها حفظ شدند.
- تست اختصاصی **12/0**؛ رگرسیون T009=16 PHP+7 DOM و P05-T004=18. باگ هارنس در تست کارت P05: ساخت ترم ثابت با wp_insert_term در اجرای مجدد WP_Error می‌داد؛ به es_insert_term با slug یکتا مهاجرت کرد (فقط فایل تست).


## 2026-11-07 — P07-T011 کارت مقاله/فیلتر/تاریخ شمسی (26)
- کارت مشترک اکنون پوشش `data-blog-item` را در حالت آرشیو می‌سازد؛ `data-search-item` فقط برای نتایج جستجوی سراسری فعال می‌شود تا مودال سراسری کارت‌های صفحه را پنهان نکند. کلاس/ساختار کارت مرجع حفظ شد.
- `blog-filter.php` مشترک هیرو/تولبار؛ خانه دکمه کلاینتی، آرشیو scoped لینک واقعی دسته. متن جستجو شامل عنوان/خلاصه/دسته/برچسب و گونه‌های ی/ک عربی، نیم‌فاصله و ارقام فارسی/لاتین است.
- `Core\Calendar`: تبدیل واقعی میلادی→جلالی با تاریخ محلی سایت؛ اگر WP-Parsidate فعال باشد خروجی `get_the_date()` حفظ می‌شود (بدون تبدیل دوباره). تاریخ‌های مرجع/نوروز/اسفند کبیسه تست شدند.
- تست اختصاصی **16 PHP+10 DOM=26/0**. رگرسیون T009=16+7، T010=12، P05-T004=18، P05-T005=20. تست کارت P05 برای قرارداد جدید تاریخ به‌روزرسانی شد.
- اصلاح‌های حین تست: انتظار تاریخ/املاي فارسی در خود تست‌ها؛ نام دسته fixture دقیقاً «اخبار و رویداد ها»؛ سلکتور صفحه‌بندی `data-page-prev/next`؛ polyfillهای JSDOM مانند T009؛ عنوان بازنشانی طبق main.js «آخرین مقالات» است.


## 2026-11-07 — P07-T012 Shell تک‌نوشته (14)
- `single.php` با hero/breadcrumb/meta/image، main/article/aside مرجع، `the_content()` کامل (بلوک/شورت‌کد)، فرم رمز هسته برای نوشته محافظت‌شده، سایدبار تماس واقعی و مقالات مرتبط واقعی ساخته شد. تاریخ از Calendar T011.
- وابستگی‌های ثبت‌شده برای ادامه: اکشن‌های `es_single_post_sidebar_top` (T013/T014)، `es_single_post_content_after` (T015)، `es_single_post_comments` (T017). مرتبط فعلاً fallback هم‌دسته/جدیدترین در خود single.php است و T016 آن را تخصصی می‌کند.
- تست اختصاصی **14/0**؛ رگرسیون P04-T002=15، P03-T024=14، P07-T011=16+10. اصلاح تست: عبارت مرتبط ساده شد؛ حالت نامعتبر با `$post=null` و شناسه ناموجود واقعی آزمایش شد.


## 2026-11-07 — P07-T013 TOC تک‌نوشته (19)
- `template-parts/post/toc.php` با مرحله prepare پیش از `the_content()` و فیلتر_priority_20: فقط h2/h3 معتبر، شناسه یکتا (حفظ id موجود، رفع تکرار)، کلاس `scroll-mt-24`، لینک لنگر واقعی و کارت مرجع. بدون هدینگ/رمزدار خروجی خالی است.
- وابستگی ثبت‌شده T012: فراخوانی prepare در `single.php`؛ رندر از اکشن `es_single_post_sidebar_top`.
- تست اختصاصی **13 PHP+6 DOM=19/0**؛ رگرسیون T012=14. DOM کلیک لنگر به‌صورت عدم preventDefault + هدف واقعی بررسی شد (JSDOM ناوبری قطعه را اجرا نمی‌کند).
- یادداشت محیط: ابتدای این نوبت checkout محلی به پایه اولیه برگشته بود و harness نبود؛ با fetch + mixed reset به `9349150` بازیابی شد (بدون hard/clean) و setup-harness دوباره اجرا شد.
