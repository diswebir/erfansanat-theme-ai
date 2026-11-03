# -*- coding: utf-8 -*-
"""Phase 5 — atomic tasks: homepage sections (one task per section) + queries + cards."""

def sec(n, slug, title, hooks, extras=None, size='M', extra_steps=None, extra_pass=None, deps=None):
    steps = [
        f"برش دقیق مارک‌آپ سکشن از `index.html` (بازه بایتی از `temp/_scan/sections.json` → سکشن #{n}) با حفظ تمام کلاس‌ها.",
        f"انتقال متون/تصاویر/لینک‌ها به Schema سکشن در `Registry` (کلید `{slug}`).",
        f"حفظ قرارداد JS: {hooks}",
        "حذف `custom=\"...\"` و اتریبیوت‌های بازمانده React؛ تبدیل مسیر تصویر به `assets/img/...`.",
        "پشتیبانی از حالت «داده خالی» → رندر بلوک پیش‌فرض یا حذف کامل بدون خطا.",
    ] + (extra_steps or [])
    passes = [
        "مقایسه ساختاری DOM با استاتیک ≥ ۹۹٪",
        "هیچ متن هاردکدی در فایل سکشن نمانده باشد (همه از Schema)",
        "تغییر محتوا از داشبورد در خروجی اعمال شود",
        "در حالت خالی، خطا/Warning نداشته باشد",
    ] + (extra_pass or [])
    return dict(id=f'5.{n + 1}', size=size, deps=deps or ['2.11','2.13'],
      title=title, desc=f'تبدیل سکشن «{title}» از `index.html` به `template-parts/home/section-{slug}.php` با اتصال کامل به Section Manager و بدون تغییر در `main.js`.',
      files=[f'template-parts/home/section-{slug}.php', f'inc/Sections/Schema/home/{slug}.php'],
      steps=steps, pass_=passes,
      asserts=['diff ساختاری سکشن', 'اسکن هاردکد', 'اثر داشبورد', 'حالت خالی'])

PHASE = dict(
    num=5, slug='homepage-and-sections',
    title='صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار',
    goal='هر سکشن صفحه اصلی یک تسک مستقل؛ اتصال کامل به WP_Query و کارت‌های استاندارد؛ صفر محتوای هاردکد.',
    tasks=[

dict(id='5.1', size='M', deps=['2.11','2.13'],
  title='`front-page.php` + حلقه رندر ۱۵ سکشن',
  desc='ساخت `front-page.php` که فقط از `Renderer` سکشن‌ها استفاده می‌کند (بدون هیچ مارک‌آپ سکشن داخل خودش)، به‌همراه انتخاب واریانت هیرو از آپشن (طبق G1: پیش‌فرض `home1`).',
  files=['front-page.php','inc/Sections/Renderer.php','inc/Sections/Schema/home.php'],
  steps=[
   '`front-page.php` = `get_header()` + `Renderer::render(\'home\')` + `get_footer()`.',
   'آپشن `home_hero_variant` با مقادیر `home1`/`home2`/`home3` (پیش‌فرض `home1`).',
   'فهرست ۱۵ کلید سکشن در Registry به ترتیب دقیق استاتیک.',
   'پشتیبانی از `page` واقعی صفحه اصلی (fallback به محتوای برگه در صورت خالی بودن سکشن‌ها).',
  ],
  pass_=[
   '`front-page.php` هیچ مارک‌آپ سکشن نداشته باشد (تست grep)',
   '۱۵ سکشن در ترتیب صحیح رندر شوند',
   'تغییر `home_hero_variant` به `home2` سکشن هیرو متفاوتی رندر کند',
   'بدون سکشن فعال، صفحه خطا ندهد',
  ],
  asserts=['بدون مارک‌آپ مستقیم', 'ترتیب ۱۵ سکشن', 'سوییچ واریانت', 'حالت خالی']),

sec(1, 'hero', 'هیرو واریانت home1 (اسلایدر صحنه + ویدیو + نوار دسته‌بندی)',
    '`data-home1-scene-img`, `data-home1-scene-btn`, `data-home1-overlay-item`, `data-home1-inline-video`, `data-home1-play-trigger`, `data-home1-video-close/mute/controls`, `data-home1-bottom-tab`, `data-home1-bottom-fillet`, `data-home1-categories-grid`, `data-home1-scene-title/cta/link`',
    size='L',
    extra_steps=[
      'تولید صحنه‌ها از Repeater داشبورد (تصویر، عنوان، لینک، CTA، is_video) با ایندکس صحیح `data-home1-scene-img="0..N"`.',
      'ویدیو از `assets/video/hero-video.mp4` یا مقدار آپشن (پیش‌فرض G-A: فایل قالب).',
      'دسته‌بندی‌های پایین هیرو از `product_cat` واقعی (نه ۵ آیتم hardcode) با fallback استاتیک.',
    ],
    extra_pass=[
      'تعداد صحنه‌ها و ایندکس‌ها با `main.js` سازگار باشد (۰-based پیوسته)',
      'ویدیو با پوستر و `preload="metadata"` رندر شود',
      'در نبود ویدیو، دکمه play رندر نشود',
    ]),

sec(2, 'projects-intro', 'معرفی پروژه‌های نورپردازی شهری',
    'هیچ هوک JS (سکشن استاتیک با ۳ کارت)', size='S'),

sec(3, 'gallery', 'گالری نمونه‌کارها (تب اسلایدر نمونه‌کارها)',
    '`data-home2-port-tab`, `data-home2-port-panel`', size='L',
    extra_steps=[
      'تب‌ها از آخرین پروژه‌ها (`projects`) با تصویر شاخص و لینک single.',
      'حداکثر ۶ نمونه‌کار (قابل تنظیم از تب آرشیو).',
    ],
    extra_pass=['تعداد تب‌ها با تعداد پروژه‌ها برابر باشد', 'لینک هر نمونه‌کار به single-projects صحیح باشد']),

sec(4, 'about', 'درباره + شمارنده‌های آماری',
    '`data-counter-target`, `data-counter-suffix`', size='M',
    extra_steps=['اعداد از آپشن (Repeater آماری) با fallback اعداد استاتیک.', 'تعداد پروژه‌ها می‌تواند از `wp_count_posts(\'projects\')` بیاید (toggle).'],
    extra_pass=['شمارنده با مقدار واقعی هم‌خوان باشد', 'در نبود آپشن، اعداد استاتیک نمایش داده شود']),

sec(5, 'about-establishment', 'درباره تاسیس شرکت (بلوک متنی + تصویر)',
    'هیچ هوک JS', size='S'),

sec(6, 'timeline', 'تاریخچه شرکت (۱۳۸۰ تا ۱۴۰۵) با اسکرول تعاملی',
    '`data-home2-history-btn/panel/prev/next`, `data-history-scroll-track`, `data-history-total`, `data-history-stage`, `data-history-step-label`, `data-history-progress-bar`',
    size='L',
    extra_steps=['گره‌های تاریخچه از Repeater (سال، عنوان، توضیح، تصویر).', '`data-history-total` باید با تعداد گره‌ها برابر باشد.'],
    extra_pass=['`data-history-total` = تعداد واقعی گره‌ها', 'اسکرول/دکمه‌های قبل-بعد با JS موجود کار کنند']),

sec(7, 'products', 'محصولات منتخب (۴ کارت دسته)',
    'هیچ هوک JS', size='M',
    extra_steps=['کارت‌ها از `product_cat` با تصویر `thumbnail` ترم (term meta تسک ۳.۱۵).', 'لینک هر کارت به آرشیو دسته.'],
    extra_pass=['در نبود تصویر ترم، placeholder استاتیک نمایش داده شود', 'کوئری اضافه ایجاد نشود (کش transient)']),

sec(8, 'catalog', 'بنر دانلود کاتالوگ جامع',
    'هیچ هوک JS', size='S',
    extra_steps=['فایل کاتالوگ از Media Library (آپشن)؛ پیش‌فرض بدون فایل → دکمه مخفی.'],
    extra_pass=['بدون فایل، دکمه دانلود رندر نشود', 'لینک دانلود با `esc_url` و `target` امن']),

sec(9, 'services', 'خدمات تخصصی (ردیف‌های تعاملی)',
    '`data-service-row`, `data-service-title`, `data-service-desc-text`, `data-service-img`, `data-service-counter`, `data-service-indicator`, `data-service-num`, `data-service-heading`, `data-service-row-icon`',
    size='L',
    extra_steps=['ردیف‌های خدمات از Repeater (۵ خدمت: میدان، خیابان، پل، المان، ساختمان).', 'لینک هر ردیف به صفحه خدمت (`/services/{slug}/`).'],
    extra_pass=['`data-service-counter` با تعداد ردیف‌ها هم‌خوان باشد', 'تعامل hover/ کلیک با JS موجود کار کند']),

sec(10, 'quality', 'استانداردهای کیفیت و ماندگاری (۴ کارت)',
    'هیچ هوک JS', size='M'),

sec(11, 'support', 'پشتیبانی فنی و گارانتی (نقشه/ویژگی‌ها)',
    'هیچ هوک JS', size='M'),

sec(12, 'clients', 'مشتریان و سازمان‌های همکار',
    'هیچ هوک JS', size='S',
    extra_steps=['لوگوی مشتریان از Repeater Media (بدون کوئری).'],
    extra_pass=['با لیست خالی، سکشن حذف شود (نه باکس خالی)']),

sec(13, 'blog-preview', 'ویترین مقالات (۴ کارت)',
    'هیچ هوک JS', size='M',
    extra_steps=['۴ مقاله آخر با تصویر شاخص، دسته نخست، تاریخ شمسی.'],
    extra_pass=['تاریخ با WP-Parsidate/جلالی صحیح باشد', 'در نبود مقاله، سکشن حذف شود']),

sec(14, 'contact-cta', 'CTA تماس/استعلام قیمت',
    'هیچ هوک JS', size='S',
    extra_steps=['دکمه‌ها به `/contact/` و `tel:` از آپشن.'],
    extra_pass=['شماره تلفن از داشبورد خوانده شود (نه هاردکد)']),

sec(15, 'map-contact', 'نقشه موقعیت کارخانه + اطلاعات تماس',
    'هیچ هوک JS', size='M',
    extra_steps=['نقشه: iframe/تصویر استاتیک از آپشن (بدون اسکریپت خارجی در حالت پیش‌فرض).', 'اطلاعات تماس از `es_option`.'],
    extra_pass=['در نبود نقشه، بلوک تماس سالم بماند', 'هیچ اسکریپت third-party ناخواسته لود نشود']),

sec(16, 'why-ese', 'چرا عرفان صنعت (تمایزها و گواهینامه‌ها)',
    'هیچ هوک JS', size='M',
    extra_steps=['۴ تمایز اصلی از Repeater (عنوان، آیکون SVG، توضیح).', 'نشان‌های ISO/GS1 از آپشن با رندر شرطی.'],
    extra_pass=['در نبود آیتم، سکشن حذف شود', 'آیکون‌ها فقط از لیست مجاز SVG انتخاب شوند']),

dict(id='5.18', size='L', deps=['5.8','5.14'],
  title='کارت‌های استاندارد: `card-product`, `card-project`, `card-post`',
  desc='سه فایل کارت مشترک با **دقیقاً** همان مارک‌آپ کارت‌های استاتیک و پارامترهای ورودی (`$args`) — مصرف‌کننده در صفحه اصلی، آرشیوها، مرتبط‌ها و جستجو.',
  files=['template-parts/cards/card-product.php','template-parts/cards/card-project.php','template-parts/cards/card-post.php'],
  steps=[
   'استخراج مارک‌آپ کارت از `products.html` (`data-product-item`) و `projects.html`/`blog.html`.',
   'پارامترها: `post_id`/`term_id`, `show_excerpt`, `size`, `badge`, `class`.',
   'پر کردن `data-product-*` از داده واقعی برای فیلتر سمت کلاینت.',
   'بدون `h2` تکراری در صفحه (سلسله‌مراتب عنوان).',
  ],
  pass_=[
   'کارت محصول خروجی برابر نمونه استاتیک (کلاس‌ها)',
   'همه `data-product-*` پر شوند',
   'کارت در ۳ مصرف‌کننده بدون خطا رندر شود',
   'هیچ کارت بدون تصویر، خطا ندهد (placeholder)',
  ],
  asserts=['انطباق کارت', 'پر بودن هوک‌ها', 'سه مصرف‌کننده', 'حالت بدون تصویر']),

dict(id='5.19', size='L', deps=['5.18'],
  title='لایه کوئری بهینه صفحه اصلی (`HomeQueries`)',
  desc='تمام کوئری‌های صفحه اصلی در یک کلاس با کش transient، `no_found_rows`, `update_post_meta_cache=false` برای کارت‌ها، و invalidation هوشمند روی `save_post`/`edited_term`.',
  files=['inc/Query/HomeQueries.php'],
  steps=[
   'کش با کلید نسخه‌دار (`es_home_v{ES_THEME_VERSION}`).',
   'Invalidation روی `save_post`, `deleted_post`, `edited_term`, `set_object_terms`.',
   'حداکثر ۶ کوئری مستقل برای کل صفحه.',
   'پشتیبانی از حالت بدون کش (query string یا ثابت).',
  ],
  pass_=[
   '≤ ۳۰ کوئری کل صفحه اصلی (با شمارش `$wpdb->num_queries`)',
   'بار دوم رندر، کوئری‌های محتوایی از ۶ کمتر شود (کش)',
   'افزودن یک پروژه جدید، کش را باطل کند',
   'با `update_post_meta_cache=true` غلط، داده کارت‌ها صحیح بماند',
  ],
  asserts=['شمارش کوئری', 'اثر کش', 'invalidation', 'صحت داده']),

dict(id='5.20', size='M', deps=['5.1'],
  title='واریانت‌های آماده `home2` و `home3` (غیرفعال، بدون حذف)',
  desc='طبق G1: مارک‌آپ دو واریانت دیگر هیرو به‌صورت فایل مستقل نگه داشته شود و از داشبورد قابل فعال‌سازی باشد؛ هیچ‌کدام در حالت پیش‌فرض رندر نمی‌شوند.',
  files=['template-parts/home/hero-variants/hero-home2.php','template-parts/home/hero-variants/hero-home3.php'],
  steps=[
   'استخراج مارک‌آپ واریانت‌ها از `index.html` (بلوک‌های `data-home2-*` و بخش‌های هیرو جایگزین).',
   'اتصال به همان Schema/Manger؛ فقط از طریق آپشن انتخاب شوند.',
   'مستندسازی تفاوت‌ها در `temp/STATIC_TEMPLATE_MAP.md`.',
  ],
  pass_=[
   'در حالت پیش‌فرض هیچ بایتی از این دو فایل در خروجی نباشد',
   'فعال‌سازی از داشبورد باعث رندر آن واریانت شود',
   'هیچ هوک JS و کلاسی حذف نشده باشد (تست وجود در فایل)',
  ],
  asserts=['عدم رندر پیش‌فرض', 'فعال‌سازی', 'کامل بودن مارک‌آپ']),

dict(id='5.21', size='S', deps=['5.2','5.3','5.4','5.5','5.6','5.7','5.8','5.9','5.10','5.11','5.12','5.13','5.14','5.15','5.16','5.17','5.18','5.19','5.20'],
  title='گیت فاز ۵: تست رندر و کوئری صفحه اصلی',
  desc='اجرای کل تست‌ها + مقایسه سکشن‌به‌سکشن با استاتیک + اجرای `main.js` روی خروجی (jsdom) برای اطمینان از عدم خطای JS.',
  files=['temp/phase-05-homepage-and-sections/tests/run-phase-05-tests.sh','tools/harness/jscheck.mjs'],
  steps=[
   'رندر `/` و استخراج هر سکشن برای diff.',
   'اجرای `main.js` در jsdom و جمع‌آوری خطاها.',
   'شمارش کوئری و ثبت در لاگ.',
  ],
  pass_=[
   '۱۵ سکشن با انطباق ≥ ۹۹٪',
   'صفر خطای JS در jsdom',
   '≤ ۳۰ کوئری',
   '۱۰۰٪ تست‌های تسک PASS',
  ],
  asserts=['انطباق ۱۵ سکشن', 'خطای JS', 'کوئری']),
])
