#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Generate temp/phase-XX-*/{tasks,tests} scaffolding for the 8 phases (24 tasks)."""
import os, textwrap

ROOT = 'temp'

PHASES = [
 dict(num=1, slug='core-and-assets', title='زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک',
  goal='اسکلت قالب، معماری OOP بدون Composer، DeBloat و مدیر دارایی‌های شرطی.',
  tasks=[
   dict(id='1.1', title='اسکلت پایه قالب و معماری شیءگرا (OOP)',
    desc='ساخت `style.css` (هدر قالب)، `functions.php` سبک، `index.php`، Autoloader دستی در `inc/Autoloader.php` و Namespace `ErfanSanat\\Theme`. راه‌اندازی `Theme` به‌عنوان Kernel با الگوی Singleton و ثبت `add_theme_support`های لازم.',
    files=['style.css','functions.php','index.php','inc/Autoloader.php','inc/Theme.php','inc/Contracts/ModuleInterface.php'],
    steps=[
     'هدر `style.css` با فیلدهای استاندارد وردپرس (Text Domain: `erfansanat`)',
     'Autoloader PSR-4 دستی: `ErfanSanat\\Theme\\Foo\\Bar` → `inc/Foo/Bar.php` (بدون Composer)',
     'Kernel: ثبت هوک‌ها، `after_setup_theme` برای `title-tag`, `post-thumbnails`, `html5`, `custom-logo`, `woocommerce`, `wc-product-gallery-zoom/slider/lightbox`',
     '`add_theme_support(\'align-wide\')` فقط اگر با CSS استاتیک تعارض نداشت',
     '`load_theme_textdomain` با پوشه `languages/`',
     'ثبت منوها در `inc/Setup/Menus.php` (بدون Walker در این تسک)',
    ],
    pass_=[
     'فعال‌سازی قالب بدون هیچ Notice/Warning در `WP_DEBUG=true`',
     '`php -l` روی همه فایل‌های PHP بدون خطا',
     '`get_theme_support(\'title-tag\')`, `post-thumbnails`, `html5`, `woocommerce` همگی true',
     'کلاس‌ها با Autoloader بارگذاری می‌شوند (بدون `require` دستی در functions.php)',
     '`wp_get_theme()->get(\'TextDomain\') === \'erfansanat\'`',
    ],
    asserts=['لینت همه فایل‌های PHP', 'وجود add_theme_supportهای الزامی در سورس', 'بارگذاری کلاس Kernel از طریق Autoloader', 'نبود Notice در بوت']),

   dict(id='1.2', title='ماژول DeBloat و پاکسازی سرصفحه',
    desc='حذف کامل CSS/JS گوتنبرگ و بلاک‌ها، ایموجی، oEmbed، `wp-embed.js`، و متاتگ‌های اضافی `wp_head` برای رسیدن به بودجه عملکرد.',
    files=['inc/Performance/DeBloat.php'],
    steps=[
     'حذف `wp-block-library`, `wp-block-library-theme`, `global-styles`, `classic-theme-styles`',
     'حذف اکشن‌های ایموجی (`print_emoji_*`, `wp_emoji_*`) و فیلترهای `the_content` مرتبط',
     'حذف `wp_oembed_add_discovery_links`, `wp_oembed_add_host_js`, `rest_output_link_wp_head`',
     'حذف `wp_generator`, `rsd_link`, `wlwmanifest_link`, `shortlink`',
     'غیرفعال‌سازی `dashicons` در فرانت (فقط برای کاربر وارد‌شده)',
     'حذف `wp-block-library` از `wp_enqueue_scripts` با اولویت ۱۰۰',
    ],
    pass_=[
     'هیچ فایل `wp-includes/css/dist/block-library/*` در سورس HTML خروجی نباشد',
     'هیچ `wp-emoji`/`wp-embed` در سورس نباشد',
     'تعداد `<link rel=stylesheet>` فقط ۲ عدد (styles.css + افزونه‌های لازم)',
     '`global-styles` و `classic-theme-styles` حذف شده باشند',
     'صفحه ادمین و ویرایشگر بدون خطا کار کند (DeBloat فقط فرانت)',
    ],
    asserts=['شبیه‌سازی wp_enqueue_scripts و بررسی نبود handleها', 'بررسی خروجی wp_head برای الگوهای ممنوع', 'تست ایزوله‌بودن ادمین']),
   dict(id='1.3', title='انتقال Assets و مدیر Enqueue شرطی',
    desc='انتقال `assets/css/styles.css`, `assets/js/main.js`, ۳ فونت Vazirmatn، ۲۲ تصویر و لوگو به ساختار `assets/` قالب + کلاس `AssetManager` با نسخه‌گذاری `filemtime` و Enqueue شرطی بر اساس `STATIC_TEMPLATE_MAP.md`.',
    files=['assets/css/styles.css','assets/js/main.js','assets/fonts/*','assets/img/**','assets/video/hero-video.mp4','inc/Assets/AssetManager.php','inc/Assets/Manifest.php'],
    steps=[
     'کپی بایت‌به‌بایت فایل‌های CSS/JS/فونت (بدون تغییر محتوا)',
     'جابه‌جایی تصاویر به `assets/img/` و نگه‌داشتن مسیرهای `url()` در CSS بدون شکستگی',
     '`Manifest.php` = آرایه نگاشت «صفحه → دارایی‌های لازم»',
     '`register/enqueue` با `filemtime()` به‌عنوان ورژن',
     '`preload` فونت arabic + تصویر LCP هیرو (فقط صفحه اصلی)',
     '`defer` برای `main.js` (همان رفتار استاتیک)',
    ],
    pass_=[
     'همه دارایی‌ها HTTP 200 (مسیرهای نسبی صحیح)',
     'هیچ `url(...)` شکسته‌ای در CSS نباشد (بررسی خودکار مسیر فونت‌ها)',
     'JS/CSS در همه صفحات یک‌بار و بدون تکرار لود شود',
     'صفحه‌ای که هیرو ندارد، `hero` را preload نکند',
     'نسخه‌گذاری با filemtime کار کند (تغییر mtime → تغییر query string)',
    ],
    asserts=['مقایسه md5 فایل مبدأ و مقصد (بدون تغییر)', 'حل مسیر فونت‌ها از داخل CSS', 'شبیه‌سازی enqueue در سه صفحه و شمارش assets', 'بررسی preload شرطی']),
  ]),

 dict(num=2, slug='custom-admin-dashboard', title='فریم‌ورک داشبورد اختصاصی ادمین و Section Builder',
  goal='پنل مدیریت اختصاصی RTL، آپشن واحد، و موتور مدیریت سکشن‌ها با Drag&Drop.',
  tasks=[
   dict(id='2.1', title='پنل «داشبورد اختصاصی عرفان صنعت» و لایه آپشن',
    desc='صفحه ادمین مدرن و تب‌بندی‌شده با ذخیره AJAX، Nonce و Capability (`manage_options`). ذخیره در آپشن واحد `erfansanat_theme_options` و دسترسی از طریق `es_option($key,$default)` با کش استاتیک.',
    files=['inc/Admin/AdminMenu.php','inc/Admin/ThemeOptions.php','inc/Admin/Fields/FieldRenderer.php','inc/Helpers/Options.php','assets/admin/admin.css','assets/admin/admin.js'],
    steps=[
     'ثبت منوی سطح‌بالا با آیکون اختصاصی + زیرمنوها',
     'Schema تب‌ها (هدر، فوتر، تماس، شبکه‌های اجتماعی، برند/رنگ، اسکریپت‌ها، آرشیوها، منوها)',
     '`FieldRenderer`: text, textarea, url, email, color, media, toggle, repeater, select',
     'AJAX save با `check_ajax_referer` + `current_user_can`',
     '`es_option()` با کش استاتیک و fallback دقیقاً برابر محتوای استاتیک',
     'صفحه‌بندی «برند و رنگ» فقط متغیرهای CSS مجاز را تغییر دهد (بدون دست‌زدن به ساختار)',
    ],
    pass_=[
     'درخواست بدون Nonce یا بدون Capability رد شود (403)',
     'ذخیره در یک ردیف آپشن (`erfansanat_theme_options`) انجام شود',
     'مقدار پیش‌فرض هیچ تفاوتی با خروجی استاتیک ایجاد نکند',
     'تمام فیلدها با `sanitize_*` مناسب پاکسازی و با `esc_*` خروجی داده شوند',
     '`es_option()` در ۱۰۰ فراخوانی فقط ۱ بار `get_option` بزند (کش)',
    ],
    asserts=['تست منفی Nonce/Capability', 'تست رفت‌وبرگشت ذخیره فیلدها', 'شمارش فراخوانی get_option', 'تطابق پیش‌فرض با استاتیک']),
   dict(id='2.2', title='تب‌های سراسری: هدر، فوتر، تماس، شبکه‌های اجتماعی، برند، اسکریپت‌ها، آرشیوها',
    desc='پر کردن تب‌های داشبورد با مقادیر پیش‌فرض استخراج‌شده از میرور (نوار اعلان، لوگو، تلفن‌ها، آدرس، شبکه‌ها، متن کپی‌رایت، اسکریپت هدر/فوتر) و اتصال آنی به فرانت.',
    files=['inc/Admin/Tabs/*.php','inc/Frontend/HeaderData.php','inc/Frontend/FooterData.php'],
    steps=[
     'استخراج تمام متون هدر/فوتر از `index.html` به‌عنوان Default',
     'اتصال فیلدها به `header.php`/`footer.php` (تسک ۴.۱ مصرف‌کننده است)',
     'مدیریت نوار اعلان (روشن/خاموش، متن، لینک)',
     'مدیریت شبکه‌های اجتماعی با آیکون‌های inline موجود',
     'اسکریپت سفارشی هدر/فوتر با خروجی escape‌شده',
    ],
    pass_=[
     'تغییر هر فیلد در ادمین بلافاصله در فرانت دیده شود (بدون کش منقضی)',
     'هیچ متن هاردکدی از هدر/فوتر باقی نماند',
     'خروجی اسکریپت سفارشی فقط برای `manage_options` نمایش داده شود یا مطابق سیاست امنیتی',
     'مقادیر خالی/None باعث حذف المان (نه رندر خالی) شوند',
    ],
    asserts=['اسکن سورس برای متن‌های هاردکد هدر/فوتر', 'تست جایگزینی مقدار', 'تست حذف مقدار']),
   dict(id='2.3', title='موتور مدیریت سکشن‌ها (Section Manager)',
    desc='موتور سکشن برای برگه‌ها: فعال/غیرفعال، ترتیب Drag&Drop، و ویرایش تمام متون/تصاویر/دکمه‌ها/Repeaterها؛ ذخیره در متای برگه و رندر پویا در فرانت.',
    files=['inc/Admin/SectionManager.php','inc/Admin/Fields/Repeater.php','inc/Sections/Registry.php','inc/Sections/Renderer.php','assets/admin/sortable.js'],
    steps=[
     'ثبت Schema سکشن‌ها برای صفحه اصلی (۱۵ سکشن) و برگه‌های ثابت',
     'ذخیره ساختار در `_es_sections` (ترتیب + فعال + داده)',
     'Drag&Drop با vanilla JS (بدون jQuery UI)',
     'Renderer با `get_template_part` و پشتیبانی از fallback استاتیک',
    ],
    pass_=[
     'تغییر ترتیب در ادمین، ترتیب خروجی فرانت را تغییر دهد',
     'غیرفعال‌سازی سکشن، آن را از خروجی حذف کند (بدون رندر خالی)',
     'Repeaterها با فرمت سازگار JetEngine (`{"item-0":{...}}`) ذخیره شوند',
     'Media picker فیلدها فقط attachment ID معتبر بپذیرد',
     'هیچ سکشنی در Schema بدون فیلد قابل ویرایش نباشد',
    ],
    asserts=['تست ذخیره/بازیابی ترتیب', 'تست خاموش‌کردن سکشن', 'تست Repeater format', 'تست sanitize فیلدهای media']),
  ]),

 dict(num=3, slug='cpt-and-legacy-meta', title='معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده',
  goal='ثبت Native تایپ‌ها/تکسونومی‌ها و خواندن/نوشتن بی‌نقص همه کلیدهای متای JetEngine.',
  tasks=[
   dict(id='3.1', title='ثبت `projects` + `project_type` + `city` و اصلاح Rewriteها',
    desc='ثبت دقیق CPT و تکسونومی‌ها طبق DATA_BLUEPRINT + ستون‌های ادمین (`same_key_hrefl`, `number2-viewc`) و فیلترهای دراپ‌داون شهر/نوع پروژه.',
    files=['inc/Content/PostTypes.php','inc/Content/Taxonomies.php','inc/Admin/ListTables.php','inc/Content/Rewrites.php'],
    steps=[
     '`register_post_type(\'projects\')` با تنظیمات دقیق بلوپرینت',
     '`register_taxonomy(\'project_type\'|\'city\')`',
     'بازنویسی `product_cat` به `product_cat` (نه product-category) با `with_front=true`',
     '`product_tag` slug=`product-tag`, `with_front=false`',
     'ستون‌های لیست ادمین + فیلترها + قابل مرتب‌سازی',
     'Flush rewrite فقط هنگام فعال‌سازی/تغییر نسخه',
    ],
    pass_=[
     '`get_post_type_archive_link(\'projects\')` = `/projects/`',
     '`get_term_link` برای `city` = `/city/{slug}/` و `project_type` = `/project_type/{slug}/`',
     'دسته محصول = `/product_cat/{slug}/`',
     'بدون flush در هر بار بارگذاری (فقط on activation)',
     'ستون‌ها و فیلترها در لیست ادمین پروژه‌ها ظاهر شوند',
    ],
    asserts=['تست URL تولیدی', 'تست ثبت تایپ/تکسونومی', 'تست نبود flush_rewrite_rules در هوک روزمره', 'تست ستون‌های ادمین']),
   dict(id='3.2', title='متاباکس محصولات + لایه نرمال‌سازی متا',
    desc='متاباکس تب‌بندی‌شده برای همه کلیدهای متای محصول و Repeaterها با پشتیبانی از آرایه سریالایز و JSON؛ نوشتن با حفظ فرمت قبلی.',
    files=['inc/Admin/Metaboxes/ProductMeta.php','inc/Meta/MetaRepository.php','inc/Meta/RepeaterCodec.php'],
    steps=[
     'تب‌ها: شناسه‌های قانونی / عناوین و فروش / فایل و گالری / روابط / ویژگی‌ها / کاربردها / FAQ',
     '`RepeaterCodec`: decode/encode هم برای serialize و هم JSON',
     'Auto-sync `galerry_produ` ↔ `_product_image_gallery`',
     'Quick Edit برای `gs1_id..product_id`, `zireonvan`, `english_titl`, `true_sale`',
     'Media picker برای `product-catalog-file` (ذخیره URL) و گالری (CSV IDs)',
     'Relations: انتخابگر پروژه‌ها و ترم‌های `project_type`',
    ],
    pass_=[
     'خواندن مقادیر `{\"item-0\":{...}}` سریالایز همان نتیجه JSON را بدهد',
     'ذخیره مجدد، ساختار قبلی را خراب نکند (round-trip byte-stable در سطح داده)',
     'تمام ۱۴ کلید و ۴ Repeater خوانده/نوشته شوند',
     '`true_sale` فقط `true`/`false` بپذیرد',
     'Nonce و capability روی هر متاباکس اعمال شود',
    ],
    asserts=['تست round-trip همه کلیدها', 'تست هر دو فرمت Repeater', 'تست sync گالری', 'تست sanitize و capability']),
   dict(id='3.3', title='متاباکس پروژه‌ها، FAQ دسته‌ها، Hreflang و شمارنده بازدید',
    desc='متاباکس پروژه با ۵ تصویر گالری (با املای دقیق `foure-image`)، تاریخ تکمیل، محصولات مرتبط؛ متاباکس FAQ تکسونومی `product_cat` با Media؛ متاباکس Hreflang؛ شمارنده `number2-viewc`.',
    files=['inc/Admin/Metaboxes/ProjectMeta.php','inc/Admin/Metaboxes/TermMeta.php','inc/Admin/Metaboxes/Hreflang.php','inc/Meta/Views.php'],
    steps=[
     'گالری پروژه با همگام‌سازی دوطرفه بین فیلد مدرن و `second-image..six-image`',
     '`finish-pro-time` با اعتبارسنجی الگوی تاریخ جلالی',
     '`mortabet_mahsole_2` انتخابگر چندگانه محصول',
     'Term meta: `thumbnail` (media) و `faq` (repeater) برای `product_cat`',
     'Hreflang برای post/page/product/projects + همه تکسونومی‌ها + ستون `same_key_hrefl`',
     '`number2-viewc` با fallback خواندن `number-viewc` و ضدتکرار در یک session',
    ],
    pass_=[
     'همه ۷ کلید پروژه و ۴ کلید Hreflang و ۲ کلید تکسونومی تست شوند',
     'گالری پروژه با هیچ‌یک از کلیدهای قدیمی گم نشود',
     'تاریخ نامعتبر رد شود',
     'افزایش بازدید بدون تأثیر بر زمان پاسخ (<5ms) و بدون شمارش ربات‌ها',
    ],
    asserts=['CRUD کامل متاها', 'تست گالری دوطرفه', 'تست هنجاری تاریخ', 'تست شمارنده بازدید']),
  ]),

 dict(num=4, slug='header-footer-navigation', title='هدر، مگامنو، منوی موبایل و فوتر',
  goal='یک هدر/فوتر مشترک با حفظ ۱۰۰٪ DOM + Walkers اختصاصی و مگامنوی قابل تنظیم از Appearance.',
  tasks=[
   dict(id='4.1', title='`header.php` و `footer.php` از روی میرور',
    desc='برش هدر/فوتر مشترک (۱۲۸ فایل یکسان) به همراه Hreflang در `<head>`, `wp_head`/`wp_body_open`/`wp_footer`, و اتصال به Theme Options.',
    files=['header.php','footer.php','template-parts/header/topbar.php','template-parts/header/nav.php','template-parts/header/mobile-menu.php','inc/Frontend/Head.php'],
    steps=[
     'استخراج هدر از `index.html` و پارامتری کردن لینک‌ها با `home_url()`',
     'جایگزینی لینک‌های ثابت با `get_permalink`/`get_term_link`',
     'Hreflang از متای `hrefmeta_*` + پیش‌فرض سایت',
     'حذف `custom="0.03"` باقی‌مانده از React (تبدیل به `data-*` در صورت نیاز JS)',
     'فوتر با همان ساختار و داده‌های تماس از داشبورد',
    ],
    pass_=[
     'DOM خروجی ≥ ۹۹٪ منطبق بر استاتیک (تست مقایسه ساختاری)',
     'وجود `wp_head`, `body_class`, `wp_body_open`, `wp_footer`',
     'همه لینک‌های داخلی با `home_url` ساخته شوند (بدون `.html`)',
     'بدون `custom=` در خروجی',
     'هیچ کلاس Tailwind حذف/اضافه نشود',
    ],
    asserts=['diff ساختاری DOM', 'تست وجود هوک‌ها', 'تست مطلق‌بودن لینک‌ها', 'تست نبود custom=']),
   dict(id='4.2', title='Nav Walker مگامنو + مدیریت از Appearance → Menus',
    desc='Walker اختصاصی که مارک‌آپ مگامنو (تب محصولات، تب نوع/شهر پروژه، ستون‌های لینک) را از فهرست‌های وردپرس تولید کند. تصمیم Gate 0: **ساختار منو، آیتم‌ها و قابلیت‌های مگامنو (نوع پنل، ستون، آیکون، برچسب، تب) همه در Appearance → Menus** مدیریت می‌شوند؛ **فقط تنظیمات نمایشی سطح‌بالا** (روشن/خاموش مگامنو، حالت hover/click، ارتفاع هدر، چسبندگی) در داشبورد اختصاصی قالب است.',
    files=['inc/Nav/MegaMenuWalker.php','inc/Nav/Fields.php','template-parts/header/mega-products.php','template-parts/header/mega-projects.php','inc/Nav/MenuLocations.php'],
    steps=[
     'ثبت `main-mega-menu`, `mobile-menu`, `tolidat-sherkat`, `landing`',
     '`wp_nav_menu_item_custom_fields` برای فیلدهای آیتم (نوع مگامنو، ستون، آیکون، برچسب، تصویر، تب)',
     'Walker تولیدکننده‌ی دقیق `<div data-mega-prod-panel>` و …',
     'حالت فعال (`aria-current`) مطابق تفاوت‌های مشاهده‌شده در میرور',
     'تنظیمات نمایشی (روشن/خاموش، hover/click، چسبندگی) از داشبورد؛ ساختار و آیتم‌ها و فیلدهای مگامنو فقط از Appearance → Menus',
    ],
    pass_=[
     'خروجی Walker با مارک‌آپ استاتیک منطبق باشد (هش ساختاری)',
     'فیلدهای مگامنو دقیقاً در صفحه Appearance → Menus (هوک `wp_nav_menu_item_custom_fields`) رندر و ذخیره شوند و هیچ صفحه مدیریت منوی جداگانه‌ای در داشبورد ساخته نشود',
     'منوی موبایل off-canvas با JS موجود باز/بسته شود',
     'در نبود منو، fallback دقیقاً مارک‌آپ استاتیک باشد',
    ],
    asserts=['تست Walker با منوی نمونه', 'تست ذخیره فیلد آیتم منو', 'تست fallback', 'تست aria-current']),
   dict(id='4.3', title='جستجوی گلوبال هدر و سبد خرید هدر',
    desc='اتصال مودال جستجو (`data-global-search-*`) به کوئری واقعی محصولات/پروژه‌ها و شمارنده سبد خرید هدر به WooCommerce.',
    files=['template-parts/header/search-modal.php','inc/Search/LiveSearch.php','inc/Woo/HeaderCart.php'],
    steps=[
     'رندر داده اولیه جستجو از سرور (بدون کوئری اضافه)',
     'Endpoint ایجکس امن برای جستجوی زنده',
     'شمارنده سبد از `WC()->cart` با Fragment',
     'حالت خالی/لودینگ با همان کلاس‌های استاتیک',
    ],
    pass_=[
     'جستجو بدون رفرش نتیجه بدهد (<۲۰۰ms)',
     'خروجی escape شده و کوئری با `$wpdb->prepare`/`WP_Query` امن باشد',
     'شمارنده سبد با افزودن محصول به‌روز شود',
     'در نبود ووکامرس، هدر بدون خطا رندر شود',
    ],
    asserts=['تست endpoint', 'تست escaping', 'تست fragment سبد', 'تست نبود WooCommerce']),
  ]),

 dict(num=5, slug='homepage-and-sections', title='صفحه اصلی و کامپوننت‌های ماژولار',
  goal='۱۵ سکشن صفحه اصلی به‌صورت فایل‌های مستقل + کارت‌های استاندارد + صفر محتوای هاردکد.',
  tasks=[
   dict(id='5.1', title='برش ۱۵ سکشن صفحه اصلی به `template-parts/home/`',
    desc='هر سکشن یک فایل، با رندر پویا بر اساس Section Manager و حفظ کامل مارک‌آپ؛ فقط واریانت هیروی فعال (home1) رندر می‌شود، دو واریانت دیگر به‌صورت فایل آماده و از داشبورد قابل فعال‌سازی‌اند.',
    files=['front-page.php','template-parts/home/section-hero.php','section-projects.php','section-gallery.php','section-about.php','section-timeline.php','section-products.php','section-catalog.php','section-services.php','section-quality.php','section-support.php','section-clients.php','section-blog.php','section-cta.php','section-map.php','template-parts/home/hero-variants/hero-home2.php','hero-home3.php'],
    steps=[
     'استخراج هر سکشن با ابزار برش (offsets دقیق از `temp/_scan/sections.json`)',
     'پارامتری‌کردن متون/تصاویر/لینک‌ها از Theme Options و Section Schema',
     'حفظ `data-home1-*`, `data-home2-*` و همه کلاس‌ها',
     'انتخاب واریانت هیرو از آپشن (`es_option(\'home_hero_variant\')`)',
     'حذف `custom=...` و `style`های تولیدی React در صورت بی‌اثر بودن',
    ],
    pass_=[
     'رندر پیش‌فرض با استاتیک منطبق باشد (تست ساختاری سکشن‌به‌سکشن)',
     'تغییر ترتیب/خاموش‌کردن سکشن از داشبورد کار کند',
     'هیچ متن فارسی هاردکدی در فایل‌های سکشن نباشد (به‌جز fallback پیش‌فرض در Registry)',
     'تمام ۱۵ سکشن موجود باشند',
    ],
    asserts=['مقایسه سکشن‌به‌سکشن', 'تست ترتیب', 'اسکن متن هاردکد', 'شمارش ۱۵ فایل']),
   dict(id='5.2', title='اتصال سکشن‌های داینامیک به WP_Query و کارت‌های استاندارد',
    desc='دسته‌های `product_cat`، محصولات منتخب، آخرین پروژه‌ها، آخرین مقالات، آمار و مشتریان با کوئری بهینه و کارت‌های `template-parts/cards/*`.',
    files=['template-parts/cards/card-product.php','card-project.php','card-post.php','inc/Query/HomeQueries.php'],
    steps=[
     '`WP_Query` با `no_found_rows`, `update_post_meta_cache=false`, `fields` مناسب',
     'Transient cache با invalidation روی `save_post`',
     'کارت‌ها با همان مارک‌آپ کارت‌های میرور',
     'دسته‌ها از `get_terms` با `thumbnail` و شمارش',
     'آمار/شمارنده‌ها از آپشن (قابل ویرایش)',
    ],
    pass_=[
     'صفر محتوای هاردکد (فقط fallback پیش‌فرض)',
     '≤ ۳۰ کوئری در صفحه اصلی',
     'کارت‌ها با ۳ فایل استاندارد رندر شوند',
     'با خالی بودن یک CPT، سکشن حذف/خالی رندر نشود',
    ],
    asserts=['شمارش کوئری', 'تست کارت‌ها', 'تست حالت خالی', 'تست cache invalidation']),
   dict(id='5.3', title='تعاملات صفحه اصلی (اسلایدر هیرو، تایم‌لاین، تب‌ها، شمارنده)',
    desc='اطمینان از کارکرد تمام ماژول‌های `main.js` روی مارک‌آپ PHP-تولیدشده (بدون تغییر JS) و افزودن داده اولیه صحیح.',
    files=['assets/js/main.js (بدون تغییر)','template-parts/home/*','inc/Frontend/InlineData.php'],
    steps=[
     'بررسی `data-home1-scene-*` و تزریق تعداد صحیح صحنه‌ها',
     'تب‌های پورت‌فولیو (`data-home2-port-*`) و تایم‌لاین (`data-home2-history-*`)',
     'شمارنده‌ها (`data-counter-target`)',
     'مودال ویدیو/لایت‌باکس',
    ],
    pass_=[
     'هیچ خطای JS در کنسول نباشد',
     'همه تعاملات استاتیک در نسخه PHP کار کنند',
     'بدون تغییر در `main.js` (md5 یکسان)',
    ],
    asserts=['تست md5 main.js', 'تست وجود هوک‌های لازم', 'بررسی نبود خطای JS با jsdom (در صورت امکان)']),
  ]),

 dict(num=6, slug='woocommerce-and-products', title='قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه',
  goal='Override کامل قالب‌های ووکامرس با همان UI استاتیک و اتصال همه متاها.',
  tasks=[
   dict(id='6.1', title='`single-product.php` و اتصال کامل متاها',
    desc='هیرو، گالری (`galerry_produ` + گالری ووکامرس)، زیرعنوان، عنوان انگلیسی، شناسه‌های قانونی، اطلاعیه پیش از خرید، کاتالوگ، جدول ۱۸ ویژگی، ویژگی‌ها/کاربردهای Repeater، پروژه‌های مرتبط، FAQ، نظرات.',
    files=['woocommerce/single-product.php','woocommerce/content-single-product.php','template-parts/product/hero.php','gallery.php','tabs.php','specs.php','legal-ids.php','related-projects.php','faq.php','inc/Woo/ProductData.php'],
    steps=[
     'خواندن `true_sale` برای حالت خرید/استعلام',
     'گالری با fallback زنجیره‌ای `galerry_produ` → `_product_image_gallery` → `gallery-filed` → `image-2..6`',
     'جدول ویژگی‌ها از `pa_*` (۱۸ مورد) با ترتیب ثابت',
     'Repeaterها با `RepeaterCodec`',
     'فرم افزودن به سبد استاندارد ووکامرس (متغیر/ساده)',
     'نظرات با `comments_template`',
    ],
    pass_=[
     'محصول `true_sale=true` دکمه/فرم خرید داشته باشد',
     'محصول `true_sale=false` دکمه استعلام + لینک تماس داشته باشد',
     'سکشن خالی رندر نشود',
     'همه متاها با املای دقیق خوانده شوند',
     'بدون خطای PHP با محصول بدون گالری/بدون ویژگی',
    ],
    asserts=['تست دو حالت فروش', 'تست fallback گالری', 'تست جدول ویژگی', 'تست محصول بدون داده']),
   dict(id='6.2', title='آرشیوها: `archive-product.php`, `taxonomy-product_cat.php`, `taxonomy-product_tag.php`',
    desc='آرشیو با همان گرید/فیلتر سمت کلاینت، FAQ اختصاصی دسته، توضیحات دسته و صفحه‌بندی سئو-دوست.',
    files=['woocommerce/archive-product.php','woocommerce/taxonomy-product_cat.php','woocommerce/taxonomy-product_tag.php','template-parts/archive/product-card.php','template-parts/archive/filters.php','inc/Woo/ArchiveQuery.php'],
    steps=[
     'تولید کارت‌ها با `data-product-*` از داده واقعی',
     'صفحه‌بندی سرور + لینک‌های سئو',
     'FAQ دسته از term meta `faq`',
     'دراوور فیلتر (`data-archive-drawer`)',
    ],
    pass_=[
     '`/shop/` و `/product_cat/{slug}/` رندر صحیح با کارت‌های منطبق',
     'صفحه > ۱ بدون خطا و با `rel=next/prev`',
     '`data-product-*` همه پر شوند',
     'دسته بدون محصول حالت خالی داشته باشد',
    ],
    asserts=['تست آرشیو', 'تست صفحه‌بندی', 'تست داده‌هوک‌ها', 'تست حالت خالی']),
   dict(id='6.3', title='سبد خرید، پرداخت و حساب کاربری با UI استاتیک',
    desc='Override قالب‌های cart/checkout/my-account با مارک‌آپ استاتیک، سازگار با درگاه بانکی و افزونه‌های WPC/Swatches.',
    files=['woocommerce/cart/cart.php','woocommerce/cart/cart-empty.php','woocommerce/checkout/form-checkout.php','woocommerce/myaccount/my-account.php','inc/Woo/CheckoutCompat.php'],
    steps=[
     'حفظ هوک‌های استاندارد (`woocommerce_before_cart_table` و …)',
     'جایگزینی JS سبد استاتیک با رویدادهای ووکامرس یا رندر سرور',
     'سازگاری با Variation Swatches و WPC Quantity',
     'پشتیبانی از RTL و `IRT`',
    ],
    pass_=[
     'چرخه خرید کامل (افزودن → سبد → پرداخت → سفارش) بدون خطا',
     'درگاه بانکی قالب را نشکند (لایه‌های JS ووکامرس سالم)',
     'هیچ کد راست‌چین/چپ‌چین یا ارز اشتباه نباشد',
    ],
    asserts=['تست رندر قالب‌ها', 'تست وجود هوک‌ها', 'تست نبود اسکریپت تکراری', 'تست سازگاری افزونه‌ها']),
  ]),

 dict(num=7, slug='projects-blog-and-pages', title='پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها',
  goal='تک‌پروژه، آرشیو نوع/شهر، وبلاگ، تک‌نوشته، ۴۰۴ و قالب‌های برگه.',
  tasks=[
   dict(id='7.1', title='`single-projects.php` و آرشیوهای `project_type`/`city`',
    desc='گالری ۵ تصویره، تاریخ تکمیل، شهر/نوع، تب‌های جزئیات، محصولات استفاده‌شده و آرشیوهای فیلترشده.',
    files=['single-projects.php','archive-projects.php','taxonomy-project_type.php','taxonomy-city.php','template-parts/project/*.php'],
    steps=[
     'گالری از `second-image..six-image` (فقط تصاویر موجود)',
     '`finish-pro-time` با نمایش تاریخ شمسی',
     '`mortabet_mahsole_2` → کارت محصول',
     'آرشیو با فیلتر نوع/شهر و شمارنده',
    ],
    pass_=[
     'رندر صحیح تک‌پروژه و هر دو آرشیو',
     'پروژه بدون گالری خطا ندهد',
     'لینک‌های تاکسونومی درست باشند',
    ],
    asserts=['تست رندر', 'تست گالری جزئی', 'تست لینک تاکسونومی']),
   dict(id='7.2', title='وبلاگ، تک‌نوشته، جستجو و ۴۰۴',
    desc='`home.php`, `archive.php`, `category.php`, `single.php` (TOC، اشتراک‌گذاری، بازدید `number2-viewc`، مرتبط‌ها، دیدگاه‌ها)، `search.php`, `404.php`.',
    files=['home.php','archive.php','category.php','single.php','search.php','404.php','template-parts/blog/*.php'],
    steps=[
     'آرشیو با فیلتر دسته و جستجو (`data-blog-*`)',
     'TOC خودکار از سرتیترهای محتوا',
     'افزایش بازدید با `number2-viewc`',
     'مرتبط‌ها بر اساس دسته',
    ],
    pass_=[
     'تمام صفحات محتوایی رندر شوند',
     'شمارنده بازدید افزایش یابد (با fallback `number-viewc`)',
     'جستجو نتایج صحیح بدهد',
     '۴۰۴ استایل‌شده و با کد وضعیت صحیح',
    ],
    asserts=['تست آرشیو/تک‌نوشته', 'تست شمارنده', 'تست جستجو', 'تست ۴۰۴']),
   dict(id='7.3', title='قالب‌های برگه اختصاصی',
    desc='درباره ما، تماس، مجوزها، FAQ، شرایط گارانتی، استخدام (+فرم) با ویرایش کامل از داشبورد؛ لندینگ‌های `arya`/`smartelement` فعلاً مسکوت (تصمیم Gate 0).',
    files=['page-templates/template-about.php','template-contact.php','template-licenses.php','template-faq.php','template-refund.php','template-recruitment.php'],
    steps=[
     'انتقال هر برگه به Section Schema',
     'فرم تماس/استخدام با Nonce و Honeypot',
     'آکاردئون FAQ (`data-faq-*`)',
     'تب‌های استخدام (`initRecruitmentTabs`)',
    ],
    pass_=[
     'تمام متون/تصاویر/تب‌ها از داشبورد قابل ویرایش باشند',
     'فرم‌ها امن و با پیام موفقیت درست',
     'هیچ متن هاردکدی باقی نماند',
    ],
    asserts=['تست قالب برگه‌ها', 'تست فرم', 'اسکن هاردکد']),
  ]),

 dict(num=8, slug='ajax-filters-and-audit', title='جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی',
  goal='موتور جستجو/فیلتر ایجکس، فرم‌ساز اختصاصی، و ممیزی سئو/امنیت/عملکرد.',
  tasks=[
   dict(id='8.1', title='جستجوی زنده و فیلترهای هوشمند (جایگزین JetSearch/JetSmartFilters)',
    desc='فیلتر محصولات بر اساس متن/دسته/قیمت/وضعیت فروش/`pa_*` + مرتب‌سازی؛ فیلتر پروژه بر اساس نوع/شهر با شمارنده.',
    files=['inc/Ajax/SearchEndpoint.php','inc/Ajax/FilterEndpoint.php','assets/js/filters.js','template-parts/archive/filters.php'],
    steps=[
     'Endpoint با Nonce، rate-limit سبک و `WP_Query` امن',
     'پارامترهای فیلتر whitelist شده',
     'فرagment HTML کارت‌ها',
     'سازگاری با SEO (لینک‌های قابل crawl برای فیلترهای اصلی)',
    ],
    pass_=[
     'پاسخ < ۲۰۰ms در محیط تست',
     'مقاوم در برابر SQLi/XSS (تست payload)',
     'بدون رفرش صفحه',
     'کارکرد بدون جاوااسکریپت (fallback فرم GET)',
    ],
    asserts=['تست زمان پاسخ', 'تست payloadهای امنیتی', 'تست fallback بدون JS']),
   dict(id='8.2', title='سیستم فرم‌های اختصاصی + ذخیره‌سازی و ایمیل',
    desc='فرم تماس، استعلام قیمت (پاپ‌آپ)، استخدام، بازخورد مشتری با ذخیره در جدول اختصاصی و ایمیل اطلاع‌رسانی.',
    files=['inc/Forms/FormHandler.php','inc/Forms/FormRepository.php','inc/Admin/FormsList.php'],
    steps=[
     'جدول `{$wpdb->prefix}es_forms` با migration نسخه‌دار',
     'Nonce + Honeypot + محدودیت نرخ',
     'ایمیل با `wp_mail` و قالب فارسی',
     'لیست و جزئیات در ادمین + نشانگر خوانده‌نشده',
    ],
    pass_=[
     'ثبت امن از فرانت',
     'لیست پیام‌ها در منوی اختصاصی',
     'ارسال ایمیل (mock)',
     'رد درخواست‌های بدون Nonce',
    ],
    asserts=['تست ثبت فرم', 'تست لیست ادمین', 'تست ایمیل mock', 'تست امنیت']),
   dict(id='8.3', title='ممیزی نهایی سئو/عملکرد و E2E',
    desc='SEOPress، Hreflang، JSON-LD، escaping، ریسپانسیو و اجرای مجدد همه تست‌ها.',
    files=['temp/phase-08-ajax-filters-and-audit/tests/run-all-phases-e2e.sh'],
    steps=[
     'اسکن escaping (`esc_html`/`esc_attr`/`wp_kses`*) در همه قالب‌ها',
     'بررسی JSON-LD مطابق استاتیک',
     'بررسی Hreflang',
     'اجرای E2E همه فازها',
    ],
    pass_=[
     '۱۰۰٪ تست‌های فاز ۱–۸ پاس',
     'صفر Warning در `WP_DEBUG=true`',
     'بدون خطای 404 داخلی',
     'آماده استقرار',
    ],
    asserts=['E2E کامل', 'lint امنیتی', 'تست JSON-LD', 'تست Hreflang']),
  ]),
]

def task_md(phase, t):
    lines = []
    A = lines.append
    A(f"# Task {t['id']} — {t['title']}\n")
    A(f"**فاز:** {phase['num']} — {phase['title']}  ")
    A(f"**وضعیت:** `[PENDING]`  ")
    A(f"**وابستگی:** " + ('—' if t['id'].endswith('.1') else f"Task {str(float(t['id'])-0.1)[:3]}") + "\n")
    A("## هدف\n")
    A(t['desc'] + "\n")
    A("## فایل‌های هدف\n")
    for f in t['files']:
        A(f"- `{f}`")
    A("\n## مراحل اجرا (چرخه ۶ گامی)\n")
    A("| گام | اقدام |")
    A("|---|---|")
    A("| `[READ MEMORY]` | خواندن `temp/PROJECT_MEMORY.md` و `temp/STATIC_TEMPLATE_MAP.md` و همین فایل |")
    A("| `[THINK & PLAN]` | تحلیل مارک‌آپ استاتیک مربوطه و طرح اتصال به وردپرس |")
    A("| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` |")
    A("| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM |")
    A("| `[RUN TEST]` | اجرا و رفع خطا تا ۱۰۰٪ شروط |")
    A("| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md` + کامیت |")
    A("\n## جزئیات پیاده‌سازی\n")
    for i, s in enumerate(t['steps'], 1):
        A(f"{i}. {s}")
    A("\n## شروط پاس شدن (Pass Conditions)\n")
    for p in t['pass_']:
        A(f"- [ ] {p}")
    A("\n## تست‌های خودکار\n")
    for a in t['asserts']:
        A(f"- {a}")
    A(f"\n> اسکریپت: `temp/phase-{phase['num']:02d}-{phase['slug']}/tests/test-task-{t['id'].split('.')[1]}.php`\n")
    return "\n".join(lines)

def php_str(s: str) -> str:
    """Single-quoted PHP string literal, UTF-8 preserved."""
    return "'" + s.replace("\\", "\\\\").replace("'", "\\'") + "'"

def test_php(phase, t):
    n = t['id'].split('.')[1]
    checks = "\n".join(
        "pending( " + php_str(a) + " );" for a in t['asserts'])
    return f"""<?php
/**
 * Test for Task {t['id']} — {t['title']}
 * Phase {phase['num']}: {phase['title']}
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-{phase['num']:02d}-{phase['slug']}/tests/test-task-{n}.php
 * هر Assertion باید PASS شود تا تسک پاس شود.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task {t['id']}: {t['title']} ==\\n";

{checks}

summary();
"""

def run_phase_sh(phase):
    n = phase['num']
    tests = "\n".join(
        f'run_test "Task {t["id"]}" "test-task-{t["id"].split(".")[1]}.php"'
        for t in phase['tasks'])
    return f"""#!/usr/bin/env bash
# Phase {n} test suite — {phase['title']}
set -uo pipefail
cd "$(dirname "$0")/.."         # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE {n} ##########"
{tests}

phase_summary "Phase {n}"
"""

def main():
    os.makedirs(f'{ROOT}/tests', exist_ok=True)
    for ph in PHASES:
        d = f"{ROOT}/phase-{ph['num']:02d}-{ph['slug']}"
        os.makedirs(f'{d}/tasks', exist_ok=True)
        os.makedirs(f'{d}/tests', exist_ok=True)
        with open(f'{d}/README.md', 'w', encoding='utf-8') as f:
            f.write(f"# Phase {ph['num']} — {ph['title']}\n\n**هدف:** {ph['goal']}\n\n"
                    f"**تسک‌ها:** {', '.join(t['id'] for t in ph['tasks'])}\n\n"
                    f"**اجرای تست‌های فاز:**\n```bash\nbash {d}/tests/run-phase-{ph['num']:02d}-tests.sh\n```\n")
        for t in ph['tasks']:
            n = t['id'].split('.')[1].zfill(2)
            with open(f"{d}/tasks/task-{n}.md", 'w', encoding='utf-8') as f:
                f.write(task_md(ph, t))
            with open(f"{d}/tests/test-task-{n}.php", 'w', encoding='utf-8') as f:
                f.write(test_php(ph, t))
        p = f"{d}/tests/run-phase-{ph['num']:02d}-tests.sh"
        with open(p, 'w', encoding='utf-8') as f:
            f.write(run_phase_sh(ph))
        os.chmod(p, 0o755)
    print("generated", sum(len(p['tasks']) for p in PHASES), "tasks in", len(PHASES), "phases")

main()
