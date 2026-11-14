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


## 2026-11-07 — P07-T014 اشتراک‌گذاری تک‌نوشته (20)
- `template-parts/post/share.php`: ردیف مرجع برچسب/اشتراک + دکمه `data-share-btn` عین مرجع (main.js دست‌نخورده)، چهار لینک شبکه rawurlencode شده با target/rel امن، فیلد readonly `data-share-url` برای کپی دستی، حذف کامل در نوشته رمزدار. single.php ردیف قدیمی را به part سپرد (وابستگی ثبت‌شده).
- تست **14 PHP + 6 DOM = 20/0**؛ رگرسیون T012=14، T013=13. اصلاح تست: esc_url آمپرساند را `&#038;` می‌کند؛ DOM تلگرام باید permalink واقعی WP را مقایسه کند نه URL پنجره JSDOM.


## 2026-11-07 — P07-T015 سخت‌سازی شمارنده بازدید (17)
- ViewCounter (ساخته P03-T024) اکنون ضدبات واقعی است: `is_bot()` با لیست فیلترپذیر `es_view_counter_bots` (UA خالی=بات)، رد پیش‌نویس/پیش‌نمایش/ادمین/AJAX، رد کاربر واردشده دارای edit_post، رد نوشته رمزدار قفل‌شده، و dedupe کوکی `es_recent_views` (پنجره ۳۰ دقیقه، سقف ۵۰ شناسه). کلید اصلی همان `number2-viewc` و افزایش اتمیک حفظ شد.
- تست **17/0**؛ رگرسیون P03-T024=14 (قرارداد خواندن/fallback/اتمیک دست‌نخورده).


## 2026-11-07 — P07-T016 مقالات مرتبط (13)
- `template-parts/post/related.php`: رتبه‌بندی هم‌پوشانی دسته+برچسب (تاریخ جدیدتر در تساوی)، حذف نوشته جاری/پیش‌نویس/رمزدار، تعداد args+فیلتر `es_related_posts_count`، کوئری فیلترپذیر. fallback جدیدترین فقط وقتی نوشته هیچ دسته/برچسبی ندارد؛ در غیر این صورت حالت خالی=بدون سکشن (AC-03؛ اصلاح حین تست).
- کارت استاندارد `card-post` گونه `compact` گرفت (وابستگی P05-T004 ثبت شد) با DOM مرجع h-44؛ single.php سکشن inline را به part سپرد.
- تست **13/0**؛ رگرسیون T012=14، T013=13، T014=14، T011=16، P05-T004=18.


## 2026-11-07 — P07-T017 دیدگاه‌های وبلاگ (14)
- `comments.php` + `template-parts/post/comments.php`: لیست/فرم کامل هسته (wp_list_comments/comment_form با همه هوک‌ها)، callback طراحی `es_comment_callback` در Helpers (وابستگی ثبت‌شده)، پاسخ تودرتو با walker/script هسته، پیام moderation، حالت بسته و رمزدار. فیلدهای author/email/url فقط کلاس گرفتند (حذف نشدند). وایرینگ `es_single_post_comments`→comments_template در functions.php.
- تست **14/0**؛ رگرسیون T012=14، T013=13+6 DOM، P01-T004=9. اصلاحات: در style=div هسته کلاس children نمی‌سازد (depth-2+ترتیب+data-belowelement تست شد)؛ کلاس‌های تکراری callback حذف شد؛ تست TOC теперь فقط داخل nav خودش لنگرها را می‌سنجد تا لینک #respond دیدگاه تداخل نکند.


## 2026-11-07 — P07-T018 صفحه جستجو (14)
- `search.php`: کوئری ثانویه publish-only/بدون‌رمز روی post+product+projects (فیلترپذیر)، بج نوع `data-result-type`، کارت فشرده استاندارد برای مقاله و کارت مرجع برای محصول/پروژه، صفحه‌بندی سروری paginate_links با حفظ s، حالت خالی و حالت عبارت‌خالی، escape کامل (تست XSS). گونه compact کارت اکنون data-search-result را هم می‌پذیرد.
- تست **14/0** (product CPT با shim P06 رجیستر شد—الگوی رسمی هارنس)؛ رگرسیون T016=13، T011=16، P05-T004=18.


## 2026-11-07 — P07-T019 صفحه 404 (12)
- `404.php`: بدون دستکاری وضعیت/ریدایرکت، جستجوی واقعی GET، لینک‌های خانه/فروشگاه (wc یا Menus)/پروژه‌ها/وبلاگ/تماس همگی از URLهای واقعی، بدون نتیجه ساختگی. تست **12/0**؛ رگرسیون P04-T012=16.


## 2026-11-07 — P07-T020 برگه درباره ما (10)
- `page-templates/about.php` کنترلر Renderer با context=about. وابستگی‌های ثبت‌شده: Schema about_sections بازنویسی شد (hero/intro/iso/principles/history با فیلدهای کامل)؛ ۵ پارت sections/about ساخته شد؛ نگاشت قالب→context در functions.php روی فیلتر `erfansanat_sections_context` (برای about/contact/arya).
- CTA تماس فقط از فوتر گلوبال (بدون تکرار). تست **10/0**؛ رگرسیون P02-T018=10، P05-T001=14. نکته تست: locate_template هسته require_once است؛ در یک پروسه فقط اولین رندر کامل فوتر دارد—تست CTA روی رندر اول اصلاح شد.


## 2026-11-07 — P07-T021 برگه تماس (10)
- کنترلر Renderer context=contact؛ Schema contact به hero/form بازطراحی شد (info داخل form؛ fallback به FooterTab::contact، override هر فیلد، map فقط http(s)، فرم بدون action/method/name + یادداشت امن تا فاز ۸). تست **10/0**؛ رگرسیون T020=10.


## 2026-11-07 — P07-T022 برگه مجوزها و افتخارات (13)
- `page-templates/licenses.php` با DOM مرجع (هیرو + نوار ۴ نشان + گرید کارت‌ها + باند ستاد). گواهینامه‌ها Repeater رسانه‌ای در تب جدید `pages` تنظیمات (`licenses_items` با زیرفیلد image رسانه‌ای + alt/category/code/issuer/year). `PagesTab::licenses()` ترتیب ذخیره‌شده را حفظ می‌کند و در حالت خالی ۶ کارت مرجع را برمی‌گرداند.
- لایت‌باکس اختصاصی `assets/js/certificates.js` (قلاب‌های data-cert-*، باز/بسته/قبلی/بعدی/Escape، alt روی تصویر بزرگ) فقط در همین برگه enqueue می‌شود؛ main.js دست‌نخورده. CTA تماس فقط از فوتر.
- وابستگی ثبت‌شده: Options/Schema.php (فیلدهای licenses + تب pages) و functions.php (boot PagesTab + enqueue شرطی). تست **13/0**؛ رگرسیون P02-T007=18، P02-T018=10، T020=10، T021=10.
- نکته: بازنشانی snapshot رخ داد؛ با fetch + reset --mixed به a28c48d بازیابی شد و harness (node_modules/temp/wp) دوباره نصب + تم فعال + rewrite flush شد.


## 2026-11-07 — P07-T023 برگه سوالات متداول (13 + 9 DOM)
- `page-templates/faq.php` با DOM مرجع (هیرو + سایدبار دسته‌ها/تماس + آکاردئون). گروه/سوال/جواب از Repeater `faq_items` (تب pages). آکاردئون روی قلاب‌های main.js (data-faq-list/toggle/body/icon؛ آیتم اول باز). جستجوی اختصاصی `assets/js/faq-search.js` (نرمال‌سازی ی/ک و ZWNJ، فیلتر data-faq-item، پیام خالی، شمارنده) فقط در همین برگه enqueue می‌شود.
- AC-03: `PagesTab::faq()` با `Store::raw()` تشخیص «هرگز تنظیم‌نشده» (پیش‌فرض مرجع) از «خالیِ صریح» ([] → بدون سکشن)؛ گارد `if($es_items)` سکشن نمی‌سازد.
- تست PHP **13/0**؛ JSDOM **9/0** (باز/بسته‌شدن واقعی + چرخش آیکون + فیلتر جستجو + پیام خالی + شمارنده). رگرسیون P02-T007=18، P02-T018=10، T022=13.


## 2026-11-07 — P07-T024 برگه شرایط گارانتی (13)
- `page-templates/warranty.php` با DOM مرجع refund_returns.html (هیرو + ۳ کارت آیکون‌دار + دوستون پوشش/استثنا + باند مراحل ۳گانه + تلفن/آدرس/لینک تماس). کارت‌ها (آیکون/عنوان/متن)، لیست‌های پوشش/استثنا و مراحل از Repeaterهای تب pages (`PagesTab::warranty()` با پیش‌فرض مرجع). آیکون کارت از فیلد icon اعتبارسنجی‌شده (نقشه SVG استاتیک، fallback shield).
- AC-02: تلفن از FooterTab::contact، لینک ثبت درخواست به /contact/. CTA تماس فقط از فوتر.
- تست **13/0**؛ رگرسیون P02-T007=18، P02-T018=10، T022=13، T023=13. نکته تست: intro هیرو هم «۲۴ ماه…» دارد؛ SC-01 با ظهور عنوان ویرایش‌شده + شمارش کارت‌ها سنجیده شد (نه حذف سراسری رشته).


## 2026-11-07 — P07-T025 برگه استخدام (14 + 8 DOM)
- `page-templates/recruitment.php`: چهار تب سازمانی (R&D/پروژه/حسابداری/IT) + ۷ کارت شغلی از Repeaterهای `recruit_tabs`/`recruit_jobs` (تب pages؛ مدل تخت چون render_subfield زیرrepeater ندارد؛ jobs با کلید tab گروه‌بندی می‌شوند؛ skills خط‌به‌خط). پیش‌فرض مرجع در PagesTab::recruitment_defaults.
- یافته مهم: مرجع `data-recruit-tab-panel` دارد ولی main.js `[data-recruit-panel]` می‌جوید → هر پنل هر دو صفت را دارد؛ کلاس‌های وضعیت دکمه مطابق قرارداد toggle main.js (bg-emerald-700/bg-slate-50). فرم #apply-form بدون action/method/name + یادداشت امن تا فاز ۸.
- تست PHP **14/0**؛ JSDOM **8/0** (سوئیچ واقعی هر ۴ تب + کلاس فعال + محتوای پنل). رگرسیون P02-T007=18، P02-T018=10، T024=13.
- بازنشانی snapshot دوباره رخ داد؛ fetch+reset --mixed به 50567f4، harness reinstall + theme activate + rewrite flush.


## 2026-11-07 — P07-T026 برگه خدمات smartelement (15)
- `page-templates/services.php`: ردیف‌های خدمت از CPT services (publish، menu_order/date؛ ۲۰تا). نشان/مزیت‌ها از متای `_es_service_badge`/`_es_service_bullets` (خط‌به‌خط؛ متباکس رسمی در T027)، توصیف از excerpt→content. تصویر شاخص با fallback. مسیر همکاری ۵ مرحله + CTA از تب pages (`services_path/services_cta_text/services_cta_url`؛ لینک خالی→تماس). id=services-list و چیپس هیرو حفظ شد. آرشیو مستقل ندارد (has_archive=false تایید شد).
- تست **15/0**؛ رگرسیون P02-T007=18، P02-T018=10، P03-T002=12، T025=14.
- **نکته محیطی مهم:** بعد از harness reinstall، ساختار پیوند یکتا خالی می‌ماند (wp rewrite structure کرش می‌کند) → P03-T002 دو fail داد.رفع مشکل: `wp eval 'update_option("permalink_structure","/%postname%/"); flush_rewrite_rules();'`. بعد از هر setup-harness، این را هم اجرا کن (علاوه بر theme activate).


## 2026-11-07 — P07-T027 تک‌خدمت (15)
- `single-services.php` (هیرو: نشان/موقعیت «خدمت N از M»/قبلی-بعدی/تصویر؛ گارد پست نامعتبر → پیام امن بدون fatal) + `template-parts/service/details.php` (id=service-details؛ اقلام شماره‌دار/نکته/محتوا + مسیر اجرا + CTA تلفن + پروژه‌های مرتبط با card-project استاندارد؛ ترم همنام project_type در اولویت).
- وابستگی ثبت‌شده: `inc/Admin/ServiceMetabox.php` جدید (nonce+capability؛ کلیدهای _es_service_badge/_bullets/_note/_steps؛ steps خطی «عنوان|توضیح» با fallback مسیر مرجع) + boot در functions.php. T026 همان کلیدها را می‌خواند.
- تست **15/0**؛ رگرسیون T026=15، P03-T002=12، P03-T023=11. یادآوری: شمارش CTA فوتر روی اولین رندر کامل پروسه (require_once).


## 2026-11-07 — P07-T028 لندینگ آریا (17 + 8 DOM) — پایان فاز ۷
- `page-templates/landing-arya.php` (Template Name: لندینگ آریا): رندر سکشن‌های context=arya via Renderer + منوی اسکرول شناور پایین صفحه — منوی اختصاصی landing via LandingWalker در اولویت؛ در نبود آن fallback خودکار از سکشن‌های فعال (id لنگر = id سکشن، data-landing-link/data-landing-menu همان قرارداد P04-T019). scroll-spy همان navigation.js موجود؛ بدون CSS/JS جدید (AC-03).
- Schema آریا گسترش یافت: specs (rows repeater label/value) + faq (items repeater question/answer) کنار hero/features/cta؛ ۵ پارت جدید `template-parts/sections/arya/section-{hero,features,specs,faq,cta}.php` با واژگان طراحی موجود (rounded-3xl/emerald card). FAQ از همان قرارداد main.js (data-faq-list/toggle/body/icon) استفاده می‌کند؛ CTA خالی‌بودن URL → contact.
- تست PHP **17/0** (ویرایش هر ۵ سکشن، جایگزینی منوی اختصاصی، حذف سکشن غیرفعال از منو، لنگرهای سالم) + JSDOM **8/0** (spyCallback شبیه‌سازی‌شده: active/aria-current روی لینک درست؛ آکاردئون FAQ با main.js اصلی). رگرسیون P02-T015=14، T017=14، T018=10، P04-T019=18.
- **سوئیت کامل فاز ۷: ALL PASS** — 28 سوئیت PHP + 9 سوئیت DOM بدون شکست.


## 2026-11-07 — P08-T001 قرارداد و امنیت AJAX (20)
- `inc/Core/Ajax.php`: رجیستری واحد endpointها — نام یکتای `es_*` (رد تکراری/نام نامعتبر)، nonce اختصاصی `es_ajax_{action}`، سیاست public/capability، whitelist ورودی تایپ‌دار (text/textarea/int/float/bool/enum/key/key_list/int_list/email/url) و پاکت JSON استاندارد. استثنا → 500 بدون افشای پیام/trace؛ WP_Error → status خودش. `config()` + inline `window.ES_AJAX` روی es-main (الگوی CartFragments). boot در functions.php.
- تست **20/0** با es_capture_ajax (فیلتر status_header + wp_die handler). رگرسیون P02-T008=13، P06-T023=21، P01-T004 سبز.
- محیط: بعد از ریست snapshot، هارنس بازسازی + theme activate + permalink_structure restored (طبق روال ثبت‌شده).


## 2026-11-07 — P08-T002 جستجوی زنده محصولات (14)
- `inc/Core/AjaxSearch.php`: اکشن عمومی `es_product_search` روی قرارداد Ajax؛ ورودی term/limit (سقف 12). موتور سه‌مرحله‌ای: WP search عنوان/متن → متا LIKE (english_titl/zireonvan/_sku) → نام product_cat؛ فقط publish + NOT IN product_visibility (وقتی تاکسونومی موجود است)؛ ردیف type=product/url=permalink واقعی/true_sale. نرمال‌سازی ي/ك + ZWNJ؛ عبارت تک‌کاراکتری کوئری نمی‌زند.
- تست **14/0** شامل SC-03 بودجه: 1.3ms (زیر 200ms). رگرسیون T001=20، P03-T005=13، P03-T014=10.


## 2026-11-07 — P08-T003 جستجوی پروژه‌ها/مقالات (13)
- `AjaxSearch::search_content` + اکشن `es_content_search`: عنوان/متن projects+post، سپس ترم‌های city/project_type؛ ردیف type=project|post با permalink واقعی؛ publish-only؛ پیام امن خالی.
- باگ واقعی رفع شد: normalize() نیم‌فاصله را به فاصله تبدیل می‌کرد و name__like روی ترم ذخیره‌شده «خرم‌آباد» (با ZWNJ) شکست می‌خورد → `term_variants()` دو شکل (نرمال + حافظ ZWNJ) را برای جستجوی ترم‌ها امتحان می‌کند؛ در product_cat هم اعمال شد.
- تست **13/0**؛ رگرسیون T002=14، T001=20، P03-T001=15.


## 2026-11-07 — P08-T004 اتصال JS مودال جستجو (12 + 10 DOM)
- `assets/js/search.js` جدید: ارتقای مودال موجود بدون دست‌زدن main.js — debounce 250ms، AbortController + شمارنده seq (پاسخ کهنه دور ریخته می‌شود)، کیبورد ArrowDown/Up/Enter/Escape با aria-current، حالت خالی امن، بازگشت به رندر سرور برای عبارت کوتاه؛ ردیف‌ها با textContent ساخته می‌شوند (بدون innerHTML داده کاربر) و data-search-item حفظ می‌شود. اگر ES_AJAX نباشد fallback فیلتر محلی main.js می‌ماند.
- `search-modal.php`: enqueue es-search با dependency es-main (ترتیب اجرای inline config). گارد __esSearchInit برای idempotent بودن (باگ واقعی: DOMContentLoaded طبیعی JSDOM + dispatch دستی → دوبار بایند؛ در پروداکشن هم اگر اسکریپت دوبار لود شود ایمن است).
- تست PHP **12/0** + JSDOM **10/0** (fetch.mock با تأخیر متفاوت: درخواست کند abort و فقط آخرین عبارت رندر؛ SC-02 کیبورد). رگرسیون T001=20، T002=14، T003=13، P04-T012=16.


## 2026-11-07 — P08-T005 QueryBuilder فیلتر محصول (15)
- `inc/Core/SmartFilters.php`: فقط WP_Query args (بدون $wpdb/SQL). whitelist تاکسونومی = product_cat + pa_*های موجود از AttributeMap؛ ترم‌ها id یا slug تمیز؛ visibility exclusion؛ قیمت NUMERIC BETWEEN (_price) با جابه‌جایی min/max؛ sale_mode online|inquiry → متای true_sale؛ orderby لیست‌سفید (latest/oldest/price_asc/desc/name/popular)؛ per_page سقف 48، page کف 1. خروجی query_products: ids/total/pages/page/args.
- تست **15/0** (ترکیبی دسته+قیمت+IP فقط هدف؛ کلید ناشناخته حذف؛ مرتب‌سازی قیمت واقعی). رگرسیون P03-T005=13، P03-T007=10، P06-T010=10.


## 2026-11-07 — P08-T006 UI فیلترهای آرشیو محصول (16 + 9 DOM)
- `template-parts/archive/product-filters.php`: چک‌باکس دسته‌ها با شمارنده + دکمه «حذف همه فیلترها» (data-product-cat-filter/data-product-filters-clear)؛ در سایدبار و drawer شل آرشیو include شد (وابستگی ثبت‌شده در تست). enqueue es-product-filters با dep es-main.
- `SmartFilters::boot()` (init 20 یا مستقیم اگر init گذشته): اکشن عمومی `es_product_filters` با ورودی‌های کامل QueryBuilder (cats/term/price/sale_mode/orderby/page/per_page + ۱۸ pa_* به‌صورت key_list)؛ پاسخ html کارت‌های مرجع (include woocommerce/content-product.php per product) + total/pages/page.
- `assets/js/product-filters.js`: state قابل اشتراک در URL (replaceState؛ es_cats/es_q/...)، restore از URL هنگام لود، debounce+abort+seq، تعویض grid.innerHTML با کارت سرور، به‌روزرسانی شمارنده/حالت خالی، رویداد es:products-filtered؛ window.ES_PRODUCT_FILTERS برای توسعه T007-T009.
- زیرساخت تست: `ajax-bridge.php` (اجرای endpoint واقعی از CLI برای JSDOM) + `build-shop-fixture.php` (فیکسچر در DB مشترک؛ پاکسازی از طریق bridge). تست PHP **16/0** + JSDOM **9/0** (fetch→bridge→endpoint واقعی). رگرسیون T005=15، T001=20، P06-T016=17، P06-T018=13.


## 2026-11-07 — P08-T007 فیلتر قیمت و حالت خرید (12 + 4 DOM)
- `SmartFilters::sanitize_price()` (اسکالر/عددی/غیرمنفی → وگرنه null=بدون شرط) و `sanitize_sale_mode()` (فقط online/inquiry؛ بقیه all) عمومی شدند؛ build_product_query از آن‌ها استفاده می‌کند (AC-01/AC-02/AC-03).
- product-filters.js: اسلایدر مرجع `data-product-price-range` (مقدار=max یعنی بی‌سقف) و رادیوهای `data-product-purchase-mode` (all/online/call→inquiry) به readControls/syncControls/listeners اضافه شدند؛ es_price/es_mode در URL؛ Clear همه را ریست می‌کند.
- DOM: ۴ چک جدید در test-task-006-dom.mjs (اسلایدر/URL/استعلامی/ریست) — مجموع 14/0 با endpoint واقعی. رگرسیون T005=15، T006=16، T001=20، P06-T016=17.
- یادآوری محیط: node_modules و temp/wp بین نوبت‌ها پاک می‌شوند → npm install + setup-harness.sh + theme activate + permalink؛ مسیر wp-cli برابر temp/wp است نه temp.


## 2026-11-07 — P08-T008 فیلترهای ویژگی‌های فنی (11 + 5 DOM)
- product-filters.php: برای هر AttributeMap::all() دارای ترم، گروه data-product-attr-group با چک‌باکس‌های data-product-attr-filter={tax} value={term_id} و شمارنده فارسی؛ pa_color سواچ از es_product_color_swatch_map؛ ترم نداشته باشد گروه رندر نمی‌شود (AC-03).
- JS: state.attrs={tax:[ids]} → body.set(tax, ids) و URL es_attr_{tax}؛ syncControls/clear/readUrl پشتیبانی کامل.
- **دام مهم:** کلید تاکسونومی‌ها با خط تیره است (pa_ip-rating، pa_input-voltage، pa_led-chip-type...) نه زیرخط — تست اول با pa_ip_rating خطای Invalid taxonomy داد و ۷ چک fail شد؛ بعد از اصلاح کلیدها 11/0.
- DOM: ۵ چک جدید (گروه IP، سواچ رنگ، فیلتر IP68، URL ویژگی، پاکسازی) — مجموع 19/0. فیکسچر shop حالا IP/ولتاژ/رنگ هم می‌گیرد. رگرسیون T007=12، T006=16، T005=15، P06-T010=10، P06-T016=17.


## 2026-11-07 — P08-T009 مرتب‌سازی/صفحه‌بندی/حذف فیلتر (14 + 5 DOM)
- JS: سورت مرجع (default/popular/rating/price-asc/price-desc) → state.orderby؛ renderPagination داخل data-pagination-root با همان data-page-btn/summary/kelasهای مرجع (active: bg-emerald-700) و prev/next؛ بعد از هر پاسخ re-render → نودهای main.js بازنویسی می‌شوند.
- SmartFilters::query_products: clamp صفحه خارج از بازه به آخرین صفحه؛ **quirk SQLite**: offset فراتر از آخرین صفحه found_rows=0 برمی‌گرداند → probe صفحه ۱ برای محاسبه total/pages.
- تست فیکسچر باید post_date متمایز بدهد (هر ۱۲ پست در یک ثانیه = ترتیب ناپایدار)؛ «#01 جدیدترین» یعنی post_date = base + (13-i)*60.
- چک ریست DOM: per_page override باید قبل از چک «۳ کارت» برگردد وگرنه ۱ کارت درست است نه باگ.
- رگرسیون T008=11، T005=15، T001=20، P06-T021=13، P06-T016=17.


## 2026-11-07 — P08-T010 فیلترهای پروژه‌ها و شمارنده (18 + 9 DOM)
- SmartFilters: PROJECT_ORDERBY (newest/popular/name)، build_project_query (AND بین type/city، IN داخل تاکسونومی، term→s عنوان)، query_projects با همان clamp/probe، project_term_counts facet-aware (شمارنده هر ترم با فیلتر تاکسونومی دیگر + جستجو، بدون فیلتر خودش)، ajax_project_filters پاسخ html کارت‌های card-project (include با args project_id) + counts.
- project-filters.php/js: الگوی product-filters؛ URL با پیشوند es_p* تا با پارامترهای شل (type/location) تلاقی نکند؛ شمارنده‌های ترم از counts پاسخ به‌روز می‌شوند.
- WP_Query «s» فقط عنوان/متن پست است نه ترم — تست اول «خمینی‌شهر» را انتظار داشت و fail شد؛ اصلاح: جستجوی عنوان + چک «شهر بدون عنوان → خالی اما سالم».
- شمار اولیه DOM: کارت پروژه data-project-card ندارد → selector عمومی children/article. رگرسیون T009=14، T005=15، T001=20، P07-T006=12، P07-T007=10، P07-T008=10، P06-T021=13.


## 2026-11-07 — P08-T011 کارایی/کش فیلتر و جستجو (12)
- SmartFilters: cache_version (option es_filters_cache_version) + cache_get/cache_set (memory + wp_cache گروه es_filters، کلید نسخه‌دار) + bust_cache روی save_post/deleted_post/set_object_terms/created|edited|deleted_term/edited_terms؛ query_products/query_projects/project_term_counts کش شدند؛ _prime_post_caches(ids,true,true) قبل از هر حلقه رندر کارت (ضد N+1؛ ۹ کارت با <40 کوئری).
- AjaxSearch: search_products/search_content با wrapper کش (کلید normalize(term)+limit) — **دام:** در namespace باید self::normalize باشد؛ normalize() تنها = fatal «critical error» و ران تست وسط راه می‌میرد.
- **آلودگی DB از ران abortشده:** ران فetal فیکسچرها را بدون cleanup رها می‌کند → ران بعدی «نام دسته» fail می‌شود (۲ ست فیکسچر T002 موازی). پاکسازی ران‌های قبلی (پست‌های حاوی T00x + ترم‌ها) سپس رگرسیون → همه سبز. قبل از ران رگرسیون، DB چک/پاک شود.
- بنچمارک ۱۰۰ محصول + ۱۰۰ پروژه: p95 هر سه endpoint زیر 200ms (SC-01 ثبت شد).


## 2026-11-07 — P08-T012 اسکیمای فرم‌ها و ذخیره ورودی‌ها (22)
- Forms/Schema.php: چهار نوع با فیلدهای واقعی مرجع (contact: name/phone/organization/message؛ quote: +product/quantity؛ recruitment: +email/background؛ feedback: +rating)؛ validate: فیلد ناشناخته حذف، الزامی → WP_Error با برچسب فارسی، ایمیل sanitize_email، tel با اجازه +/()/رقم فارسی.
- Forms/Entries.php: CPT es_form_entry (public/publicly_queryable=false، show_ui=false تا T017)؛ store → متا _es_entry_data/_es_entry_form/_es_entry_status(unread)/_es_entry_meta؛ query/mark_read/delete فقط manage_options.
- **دام namespace:** WP_Error/WP_Query بدون \ داخل namespace ErfanSanat\Theme\Forms = Fatal «critical error» و cleanup ران نمی‌رود → DB آلوده (پاکسازی پست‌های T00x لازم شد).
- QA DB هیچ کاربر ادمین ندارد (فقط مسیر deny قابل تست) → تست ادمین با wp_insert_user role=administrator می‌سازد و در teardown می‌حذف؛ wp_delete_user نیازمند wp-admin/includes/user.php.


## 2026-11-07 — P08-T013 فرم تماس با ما (18)
- template-parts/forms/contact.php: DOM مرجع + name attrs (es_cf_*) + nonce + honeypot hidden + error box؛ اسکریپت inline روی es-main (بدون فایل JS جدید): stopImmediatePropagation جلوی listener جعلی main.js را می‌گیرد، fetch به es_contact_submit، پیام خطا با textContent (AC-03).
- ContactForm::submit: honeypot پر → WP_Error بدون ذخیره؛ Schema::validate(contact) → Entries::store؛ endpoint عمومی contact_submit (boot از functions.php — وابستگی).
- **به‌روزرسانی قراردادهای قدیمی (وابستگی ثبت‌شده):** P07-T021 «بدون name/action» مال دوران stub بود؛ اکنون فرم واقعی با nonce/honeypot امن است و چک به «name + nonce + honeypot، بدون action خارجی» تغییر یافت. چک‌های سورسی P07 به forms/contact.php اشاره می‌کنند.
- es_contains پایه‌اش ES_THEME است — برای فایل‌های temp/ مسیر absolute بده (dirname(__DIR__,2)).
- wp_add_inline_script در خروجی پارت ظاهر نمی‌شود (به اسکریپت es-main می‌چسبد) → چک‌های سورسی نه رندری.


## 2026-11-07 — P08-T014 فرم استعلام قیمت (22)
- QuoteForm::resolve_content: الگوی ^\d{1,20}$ + get_post_status publish + نوع درست؛ لینک فقط برای محتوای منتشرشده.
- **دام منطقی:** دو resolve پشت هم (product سپس project) — دومی اولی را بازنویسی می‌کرد و استعلام محصول بدون لینک می‌شد؛ fix: فقط اگر اولی خالی بود دومی جایگزین شود.
- purchase.php: CTA استعلامی از <a href=checkout> به <button data-quote-open="product" data-quote-id> تغییر کرد (متن مرجع حفظ؛ P06-T006=23 سبز) + مودال quote فقط در حالت !es_is_online (متغیر درست es_is_online است نه es_mode).
- مودال: open با closest('[data-quote-open]') + پر کردن es_qf_product_id/es_qf_product؛ Escape/backdrop بستن؛ success/error با کلاس‌های مرجع.


## 2026-11-07 — P08-T015 فرم استخدام (27)
- RecruitmentForm: whitelist واحدها (rnd/project/accounting/it با برچسب فارسی از tabs صفحه) و تجربه؛ رزومه: پسوند allowlist + سقف 2MB + نام تصادفی token- در uploads/es-resumes + دفاع دوم بررسی پسوند روی دیسک؛ خطای فایل → حذف entry نیمه‌کاره.
- **دام دوباره:** Entries::store دوباره Schema::validate اجرا می‌کند و فیلدهای خارج از اسکیما را حذف می‌کند → unit اول ذخیره نمی‌شد؛ حل: unit/experience به اسکیمای recruitment اضافه شدند (whitelist در فرم می‌ماند).
- **دام تست:** submit با «عنصر خام $_FILES» کار می‌کند نه wrapper — تست اول wrapper می‌داد و handle_resume name='es_rf_resume' می‌دید.
- کلاس‌های hidden فرم، شمارش class="hidden" در چک قدیمی صفحه را می‌شکند؛ چک P07-T025 با data-recruit-panel= جدید شد (تو در تو بودن substring ها را در نظر بگیر).


## 2026-11-07 — P08-T016 فرم نظرات مشتریان (20)
- FeedbackForm: نرمال‌سازی ارقام فارسی/عربی → بازه ۱..۵ (خارج بازه WP_Error بدون ذخیره)؛ متن با اسکیمای feedback؛ وضعیت بررسی در متای _es_feedback_status = pending پیش‌فرض، approve() برای ادمین (دسترسی در T017).
- فرم مستقل بدون الحاق به سکشن مرجع (بازخورد در mirror نبود) — بخش قابل استفاده مجدد.


## 2026-11-07 — P08-T017 پنل مدیریت ورودی‌ها (17)
- FormEntries زیرمنوی پیشخوان (ThemeOptions::SLUG) با CAP manage_options: لیست فیلترشدنی (نوع فرم/وضعیت/از-تا تاریخ)، رندر کاملاً escape، اکشن‌های read/unread/approve/delete با admin-post + nonce + هدایت امن.
- فیلتر تاریخ با get_post_timestamp روی نتیجه Entries::query پیاده شد تا فایل T012 (هدف نبودِ T017) دست‌نخورده بماند.
- FeedbackForm::approve برای نوع feedback در اکشن‌ها صدا زده می‌شود — وضعیت بررسی و خوانده‌شدن دو چیز جدا هستند.
- **محیط:** QA DB از نوبت قبلی بدون کاربر ID 1 مانده بود (P02-T003 با wp_set_current_user(1) رد می‌شد) → admin با SQL مستقیم در ID 1 بازسازی شد. node_modules و temp/wp هر دو بین نوبت‌ها پاک شده بودند → npm install + setup-harness + theme activate + permalinks قبل از رگرسیون‌های php-wasm لازم است.
- تست php-wasm با `node` اجرا می‌شود نه `php` (php-cli.mjs یک اسکریپت ESM است)؛ `php temp/tools/php-cli.mjs` بی‌صدا خروجی سورس می‌دهد.
- تست namespace سراسری: کلاس‌های theme را حتماً use کن (FeedbackForm بدون use → fatal «کلاس پیدا نشد» وسط تست).
- wp_insert_user برای نام کاربری تکراری fatal نمی‌دهد بلکه WP_Error می‌دهد → قبل از ساخت، مانده‌های run قبلی را get_user_by('login') و پاک کن.


## 2026-11-07 — P08-T018 ایمیل، Honeypot و Rate-limit (28)
- SpamGuard: سه دفاع مشترک — honeypot_ok (خالی=مجاز)، nonce_ok (Ajax::dispatch از این مسیر می‌رود — وابستگی ثبت‌شده)، enforce_rate/hit per-IP+scope با transient (سقف ۱۰ در ۶۰۰ ثانیه، پنجره ثابت از اولین برخورد) + reset_all برای ادمین/تست.
- Mailer: گیرنده option «es_form_recipient» → filter «es_form_recipient» → admin_email؛ send_entry بعد از store در هر ۴ فرم صدا زده می‌شود — **خطای ایمیل هرگز ورودی را از دست نمی‌دهد (SC-03)** و try/catch روی wp_mail.
- **دام Schema::labels() وجود ندارد** — متد واقعی Schema::types() است (همان نگاشت نوع→برچسب فارسی)؛ Mailer اول labels صدا زد و fatal شد.
- **دام reset_all:** DELETE خام روی wp_options کش options/wp_cache را پاک نمی‌کند و get_transient مقدار کهنه برمی‌گرداند → باید option_name ها را SELECT و با delete_transient حذف کرد (کش + timeout با هم).
- **دام pre_wp_mail:** callback دو آرگومانی است ($pre, $atts) → add_filter با accepted_args=2؛ $atts['to'] رشته/آرایه بسته به نسخه — با (array) نرمال کن.
- ترتیب تست: delete_option گیرنده را قبل از submit نگذار (capture پس از fallback به admin_email خراب می‌شود).
- وابستگی ثبت‌شده: ۴ فرم honeypot را از SpamGuard می‌گیرند + enforce_rate/hit/Mailer؛ تست‌های T013..T016 یک خط SpamGuard::reset_all() بعد از functions.php گرفتند.


## 2026-11-07 — P08-T019 ممیزی SEO و Schema (20)
- Core/Seo.php: canonical تم فقط در شکاف‌های هسته (front در حالت posts و آرشیو ترم) — singular/صفحه پیشخوان استاتیک را rel_canonical() خود هسته می‌پوشاند و تم نباید دوباره چاپ کند (AC-01). با پلاگین فعال: تم ساکت + remove_action('wp_head','rel_canonical',10) در زمان رندر (اولویت ۲ قبل از ۱۰) تا head مالک پلاگین شود (SC-02).
- JSON-LD سه‌گانه با سازنده‌های خالص (organization/article/product_schema) + گارد one-shot per-type و reset_printed برای پروسه‌های چندرندر؛ خروجی wp_json_encode با JSON_UNESCAPED_UNICODE.
- **دام WP نسخه QA:** get_post_modified_datetime وجود ندارد (get_post_datetime هست) → get_post_time/get_post_modified_time با DATE_W3C.
- **امولیشن query برای تست head:** new WP_Query + جایگزینی $GLOBALS['wp_query'] + ست $GLOBALS['post'] از $q->posts[0] — بدون آن get_the_title/get_post_type خالی می‌مانند و JSON-LD چاپ نمی‌شود.
- **دام wp_insert_term با نام تکراری:** WP_Error برگردانده و access ['term_id'] فوراً fatal می‌دهد → همیشه اول get_term_by و idempotent بساز؛ ران قطع‌شده ترم یتیم می‌گذارد (پاکسازی موفق self-clean است).
- wp_die داخل هوک wp_head = exit 255 «critical error» نه Throwable — shutdown_function/error_get_last منبع را نشان داد (Seo.php:204).
- P04-T017=23 سبز — hreflang مالکش Core\Hreflang ماند و Seo فقط audit_hreflang کمکی دارد.


## 2026-11-07 — P08-T020 ممیزی کارایی و Escaping (12)
- **باگ واقعی پیدا و اصلاح شد (وابستگی ثبت‌شده):** topbar.php:36 `preg_match('#^(https?://|//|#|/)#')` — `#` داخل الگو دلیلیمتر را می‌بست و «Unknown modifier '|'» می‌داد (۳ warning در هر رندر) → `\#`. رندر صفحه اصلی/تک‌مقاله/آرشیو اکنون صفر warning.
- قاعده اسکن escape: `echo $var;` خام فقط با علامت phpcs:ignore مجاز (خروجی داخلی escape‌شده مثل Menus::html)؛ تِرنری با هر دو شاخه literal امن ساختاری است و regex `[^;?]*;` آن را فلگ نمی‌کند — اسکن روی خط‌ها نه کل فایل.
- بودجه‌ها: استایل‌های صف‌شده ≤8 (واقعی 6)، اسکریپت ≤6 (واقعی 3)، کوئری صفحه اصلی ≤250 (واقعی 35)، تک‌مقاله ≤150 (واقعی 6)؛ emoji با DeBloat حذف.
- **SAVEQUERIES باید قبل از es_load_wp تعریف شود** تا wpdb کوئری‌ها را بشمارد؛ بدون آن num_queries صفر برمی‌گردد.
- چک asset داخلی: روی src های registered صف‌شده‌ها نه regex کل head (لینک‌های api.w.org/oembed مجازند و asset نیستند).
- رندر صفحات با swap کوئری + include قالب واقعی (get_header/get_footer) — همین مسیر warning های topbar را لو داد؛ ممیزی کارایی باید قالب واقعی را اجرا کند نه فقط head.


## 2026-11-07 — P08-T021 ممیزی ریسپانسیو و دسترس‌پذیری (13)
- اسکن‌های سورسی: عرض ثابت `w-[≥1000px]` ممنوع (max-w مجاز — جمع‌شونده است)، همه img با alt (44/44)، فرم‌ها label کامل (قاعده: labels+aria ≥ inputs−hidden−submit)، آکاردئون‌ها button+aria-expanded، Escape در main.js/certificates/مودال استعلام، onclick فقط روی button/input/a.
- بریک‌پوینت‌های Tailwind 4 در styles.css به شکل `@media (width>=40rem/48rem/64rem)` هستند (نه max-width) — شمارش @media با grep خطی گمراه‌کننده است (فایل minified تک‌خطی).
- **دام get_header():** include دوباره قالب کامل ناممکن است — load_template با require_once و سند HTML یک‌بار در هر ریکوئست ساخته می‌شود؛ اسنپ‌شات determinism را باید روی Renderer::render (بخش حساس به viewport) گرفت نه دو بار include کل صفحه.
- مودال‌های search/mobile-menu نقش dialog ندارند اما aria-label/aria-hidden دارند — قاعده audit: aria-label یا aria-modal یا role=dialog برای هر مودال/دراور.
- P05-T021=21، P08-T019=20، P08-T020=12 همه سبز — رندر front-page در ممیزی‌ها با swap کوئری + Seo::reset_printed انجام می‌شود.


## 2026-11-07 — P08-T022 Dry-run مهاجرت روی استیجینگ (20)
- staging-migration-dry-run.sh: درخت استیجینگ scratch با symlink به سورس + DB واقعیِ کپی‌شده — هیچ نوشتنی روی لایو (SC-01)؛ بک‌آپ staging-backup-<stamp>.sqlite + sha256 (AC-01)؛ شمارش پست/ترم/۳۷ کلید legacy قبل/بعد (AC-02)؛ کلیدها با املای تاریخی (english_titl/galerry_produ/foure-image/usage_countr/product-feature2_copy) بدون rename (AC-03)؛ گزارش markdown با drift=0 (SC-02).
- **cp -a کل temp/wp (149MB) در بات ۶۰۰ ثانیه timeout شد** → الگوی symlink + کپی فقط DB؛ اجرا ۱۵ ثانیه.
- **php در PATH نسخه wasm است و shell_exec/proc_open ندارد** (SPAWN_UNSUPPORTED) → تست، جریان dry-run را in-process بازتولید می‌کند (کپی DB → بک‌آپ → شمارش با PDO خام روی کپی → UPDATE idempotent → مقایسه → گزارش)؛ اسکریپت .sh جداگانه با bash هارنس اجرا و تایید شد.
- مسیر ROOT اسکریپت باید دو سطح بالا باشد (temp/tools/../..) و mkdir دیتابیس کپی فراموش نشود.
- شمارش کلیدها ۳۷ است نه ۳۶ (image-2..image-6 پنج‌تاست) — شمارش دستی فارسی را با count() تأیید کن.
- رگرسیون‌ها: P03-T002=12، P03-T012=16، P07-T026=15، P08-T012=22 — LegacyMeta و CPT دست‌نخورده.


## 2026-11-07 — P08-T023 ترتیب غیرفعال‌سازی و Rollback (17)
- DEACTIVATION_RUNBOOK.md: ۶ مرحله ثابت (بک‌آپ سه‌گانه → فعال‌سازی قالب → بررسی ۲۰۰ → Elementor → Jet* به ترتیب وابستگی jet-engine اول → بررسی مجدد + dry-run ممیزی)؛ سه نقطه rollback (wp db export + uploads + git tag pre-deactivation)؛ فقط Deactivate، هرگز uninstall/Delete Data؛ متاهای JetEngine همان postmeta استانداردند و LegacyMeta فرمت را حفظ می‌کند.
- تمرین rollback در تست: کپی scratch → حذف jet*/elementor از active_plugins با PDO → تطبیق شمارش ۱۱ کلید legacy قبل/بعد → بازگردانی از بک‌آپ → رندر خانه/تک‌مقاله/آرشیو بدون warning (معادل ۲۰۰؛ افزونه‌ها در QA اصلاً نصب نبوده‌اند و قالب مستقل رندر می‌کند).
- **دام چک hash لایو:** چک sha256 باید فقط جریان scratch را بپوشاند — نوشته‌های fixture لایو (wp_insert_post) بعد از چک بیایند وگرنه hash تغییر می‌کند.
- front-page عنوان صفحه را در خروجی ندارد (سکشن‌ها) — چک محتوایی روی طول خروجی، نه strpos عنوان.
- get_header() require_once است: فقط اولین رندرِ هر پروسه header کامل دارد (سازگار با چک‌های محتوایی، نه ساختاری).


## 2026-11-07 — P08-T024 E2E نهایی و بسته انتشار (15)
- **e2e نهایی: 2808 چک، 0 رد، exit 0** — `bash temp/run-all-phases-e2e.sh` (report: temp/e2e-report.md، state: temp/e2e-state.txt).
- سه رگرسیون محیطی که e2e لو داد و اصلاح شد (وابستگی ثبت‌شده):
  1. **P01-T012:** package-theme.sh قدیمی گم شده بود (temp/tools هرگز commit نمی‌شود) → اسکریپت جدید T024 قرارداد `temp/dist/erfansanat.zip` را برقرار کرد. **دام SIGPIPE:** `unzip -l | grep -q` با pipefail و exit 141 unzip → پیپ‌لاین رد می‌شود؛ خروجی را اول در متغیر بگیر.
  2. **P05-T013:** شمارش «دقیقاً ۴ محصول» به DB وابسته بود (فیکسچرهای P08 مزاحم) → انتظار پویا با همین معیارهای کوئری سکشن + حالت خالی با فیلتر دسته ناموجود.
  3. **P05-T003:** `wp_insert_term(...)['term_id']` روی ترم مانده از ران fatal شده fatal می‌داد و خودش را ماندگار می‌کرد (cleanup هیچ‌وقت نمی‌رسید) → helper idempotent es_t003_term + تعمیر کاربردهای $var['term_id'] بعد از تبدیل به int.
- **چرخه گزارش/تست:** T024 اگر گزارش e2e را validate کند و e2e گزارش را آخر بنویسد، حلقه ناهمسو می‌شود → e2e بعد از هر فاز state می‌نویسد و T024 وضعیت «فازهای ۰۱..۰۷ اجرای جاری» را چک می‌کند (≥7 و ≤8 فاز، 0 رد) + گزارش کامل فقط به‌عنوان آرتیفکت تکمیلی.
- **get_header() require_once است** — رندر دو باره قالب کامل ناممکن؛ اسنپ‌شات روی Renderer::render.
- package: `temp/dist/erfansanat.zip` (249 فایل، 13MB، فقط erfansanat/)؛ **نصب تمیز اثبات شد**: کپی درخت + حذف دیتابیس (دقت: `rm database/*` فایل‌های نقطه‌دار را حذف نمی‌کند — کل پوشه!) → wp core install → unzip zip → theme activate → rewrite → locate_template(front-page)=found.
- **برای e2e کامل پیش‌نیازها:** npm install + setup-harness + theme activate + permalinks + (node_modules برای DOM/wasm).

## 2026-11-14 — P08-T025 سخت‌سازی پس از ممیزی نهایی (18)
- **ممیزی «اگر موردی مونده»:** PLAN/INDEX بدون وضعیت ناتمام؛ صفر TODO/FIXME در تم؛ php -l و node --check کل تم بدون خطا (با PATH هارنس).
- **ممیزی حلقه‌ها نتیجه مثبت داد:** فقط 3 فایل bare `the_post()` دارند (index.php، single-projects.php، woocommerce/single-product.php = main-loop مجاز)؛ کارت‌ها با ID صریح؛ SmartFilters setup/reset متوازن داخل ob → **بدون حفره آلودگی global**؛ قرارداد با تست T025 قفل شد.
- **دو دام جدید ثبت شد:**
  1. `wp rewrite structure` در php-wasm fatal proc_open می‌دهد (قبلاً "benign" بود) → در verify-clean-install با `wp option update permalink_structure` + `wp eval 'flush_rewrite_rules();'` جایگزین شد.
  2. تست باید با `ob_end_flush(); es_summary();` تمام شود — `ob_end_clean()` خروجی RESULT را می‌بلعد و exit 1 بی‌صدای می‌دهد (تست T025 اولین اجرا).
- **اسکریپت‌های مستقل باید php را خودشان resolve کنند** (`command -v php || $ROOT/temp/tools/php`) — PATH بین نوبت‌ها پایدار نیست.
- **SC-02 حالا خودکار است:** verify-clean-install.sh → کپی تازه هارنس، پاک‌سازی کل پوشه database (دام نقطه‌دار)، core install، unzip از زیپ انتشار، activate، رندر front-page (190,269 بایت روی DB خالی)، CLEAN INSTALL OK / exit 0 (~18s).
- **پاریتی zip ↔ git = 212 فایل** (لیست unzip شامل ورودی‌های پوشه است: 249 = 212 فایل + 37 پوشه).
- e2e بعد از T025: **2826 چک / 0 رد / exit 0** (P08: 408 → 426).

## 2026-11-14 — merge نهایی
- PR #7 (`main` ← `arena/7378effd-erfansanat-theme-ai`) با تأیید کاربر و merge commit ادغام شد — کل پروژه (8 فاز، 176 تسک اتمیک + سخت‌سازی T025) وارد main شد.
- برنچ session حذف نشد (Arena آن را ردیابی می‌کند)؛ کارهای آینده از main منشعب می‌شوند.
