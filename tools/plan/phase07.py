# -*- coding: utf-8 -*-
"""Phase 7 — atomic tasks: projects, blog, service pages, static pages."""

PHASE = dict(
    num=7, slug='projects-blog-and-pages',
    title='صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی',
    goal='تک‌پروژه، آرشیو نوع/شهر، وبلاگ/تک‌نوشته/جستجو/۴۰۴، صفحه خدمات و ۶ برگه ثابت — همه با داده داینامیک.',
    tasks=[

dict(id='7.1', size='L', deps=['3.1','3.2','3.3','5.18'],
  title='`single-projects.php`: هیرو + گالری + شناسنامه',
  desc='صفحه تک‌پروژه: breadcrumb، عنوان، شهر/نوع، تاریخ تکمیل (`finish-pro-time`)، گالری ۵ تصویره (`second-image`..`six-image`) و شناسنامه اجرایی.',
  files=['single-projects.php','template-parts/project/hero.php','template-parts/project/gallery.php','template-parts/project/facts.php'],
  steps=[
   'Breadcrumb: خانه › پروژه‌ها › نوع › عنوان.',
   'گالری فقط با تصاویر موجود (۱ تا ۵) بدون خانه خالی.',
   'نمایش `city`/`project_type` با `get_the_terms`.',
   'تاریخ تکمیل با تبدیل شمسی صحیح (WP-Parsidate یا helper).',
  ],
  pass_=[
   'پروژه با ۵ تصویر و پروژه بدون گالری هر دو رندر شوند',
   'تاریخ نامعتبر باعث خطا نشود (fallback مخفی)',
   'شهر/نوع با لینک آرشیو صحیح',
   'h1 یکتا',
  ],
  asserts=['دو حالت گالری', 'تاریخ', 'لینک تاکسونومی', 'h1']),

dict(id='7.2', size='L', deps=['7.1'],
  title='تب‌های جزئیات پروژه + فرآیند اجرا',
  desc='تب‌های `#project-details` (`data-prod-tab-*`) شامل «درباره پروژه»، «شناسنامه کامل» (جدول متادیتا) و «فرآیند مهندسی/تولید/اجرا» با محتوای قابل ویرایش.',
  files=['template-parts/project/tabs.php'],
  steps=[
   'محتوا از `the_content` + Repeater فرآیند (افزودن به متاباکس پروژه در ۳.۱۴ به‌عنوان فیلد جدید با کلید `es-process`).',
   'حفظ هوک‌های تب موجود.',
  ],
  pass_=[
   'تب‌ها با JS موجود سوییچ شوند',
   'در نبود فرآیند، تب سوم رندر نشود',
   'محتوای اصلی با `the_content` (فیلترهای افزونه‌ها اعمال شود)',
  ],
  asserts=['سوییچ تب', 'حذف تب خالی', 'the_content']),

dict(id='7.3', size='M', deps=['7.1','5.18'],
  title='محصولات استفاده‌شده در پروژه (`mortabet_mahsole_2`)',
  desc='سکشن `#used-products` با کارت محصولات مرتبط شده به پروژه، به ترتیب ذخیره‌شده و با حذف محصولات ناموجود.',
  files=['template-parts/project/used-products.php'],
  steps=[
   '`WP_Query` روی `post__in` با `orderby => post__in`.',
   'کارت با `card-product` و `data-product-*` کامل.',
  ],
  pass_=[
   'ترتیب ذخیره‌شده حفظ شود',
   'بدون محصول، سکشن حذف شود',
   'ID حذف‌شده نادیده گرفته شود',
  ],
  asserts=['ترتیب', 'حذف خالی', 'ID حذف‌شده']),

dict(id='7.4', size='M', deps=['5.18'],
  title='پروژه‌های شاخص مرتبط (سایر پروژه‌های هم‌نوع)',
  desc='بلوک «سایر پروژه‌های شاخص» با پروژه‌های هم‌نوع/هم‌شهر و کارت استاندارد؛ حذف در حالت خالی.',
  files=['template-parts/project/related.php'],
  steps=[
   'ترجیح: هم‌نوع → هم‌شهر → آخرین پروژه‌ها.',
   'حداکثر ۳ کارت، بدون پروژه جاری.',
  ],
  pass_=[
   'پروژه جاری در لیست نباشد',
   'ترتیب اولویت اعمال شود',
   'در نبود پروژه دیگر، سکشن حذف شود',
  ],
  asserts=['عدم نمایش جاری', 'اولویت', 'حذف خالی']),

dict(id='7.5', size='L', deps=['7.1'],
  title='آرشیو پروژه‌ها (`archive-projects.php`)',
  desc='آرشیو با hero، گرید پروژه‌ها، دراوور فیلتر، جستجوی متنی و مرتب‌سازی — همه با همان هوک‌های `data-project-*` و شمارنده واقعی.',
  files=['archive-projects.php','template-parts/archive/project-grid.php','template-parts/archive/project-hero.php'],
  steps=[
   'کارت پروژه با `data-project-type/location/views/rating/search-text` از داده واقعی.',
   '`data-project-count` از `$wp_query->found_posts`.',
   'صفحه‌بندی با `paginate_links`.',
  ],
  pass_=[
   '`/projects/` رندر صحیح با ۱۲+ کارت',
   'شمارنده با `found_posts` برابر',
   'جستجو/فیلتر سمت کلاینت با JS موجود کار کند',
   'صفحه ۲ بدون خطا',
  ],
  asserts=['رندر', 'شمارنده', 'فیلتر JS', 'صفحه‌بندی']),

dict(id='7.6', size='M', deps=['3.2'],
  title='آرشیو نوع پروژه (`taxonomy-project_type.php`)',
  desc='آرشیو نوع پروژه با hero اختصاصی (نام/توضیح/تصویر ترم) و همان گرید؛ شامل شمارنده پروژه‌های هر نوع.',
  files=['taxonomy-project_type.php','template-parts/archive/term-hero.php'],
  steps=[
   'تیتر و توضیح از `term_description()` و نام ترم.',
   'تصویر هیرو از term meta (اختیاری، رندر شرطی).',
  ],
  pass_=[
   'هر ۸ نوع پروژه رندر شوند',
   'ترم خالی، حالت «پروژه‌ای یافت نشد» داشته باشد',
   '`term_description` با `wp_kses_post`',
  ],
  asserts=['رندر انواع', 'حالت خالی', 'kses']),

dict(id='7.7', size='M', deps=['3.3'],
  title='آرشیو شهر (`taxonomy-city.php`)',
  desc='آرشیو شهر با hero و گرید پروژه‌های همان شهر و لینک متقابل به نوع پروژه.',
  files=['taxonomy-city.php'],
  steps=[
   'استفاده مجدد از `term-hero.php` و `project-grid.php`.',
   'نمایش «تعداد پروژه‌های این شهر».',
  ],
  pass_=[
   'هر ۱۰ شهر رندر شوند',
   'لینک cross-reference به type معتبر باشد',
  ],
  asserts=['رندر شهرها', 'لینک متقابل']),

dict(id='7.8', size='M', deps=['2.11','5.18'],
  title='آرشیو/فهرست خدمات (`services.html` → برگه «خدمات»)',
  desc='صفحه فهرست خدمات (۴ سکشن) با ردیف‌های خدمات و لینک به صفحات جزئی؛ داده از Repeater داشبورد (چون CPT خدمات وجود ندارد).',
  files=['page-templates/template-services.php','template-parts/services/hero.php','template-parts/services/list.php'],
  steps=[
   'Schema سکشن برای این برگه در Registry.',
   '۵ خدمت به‌عنوان Repeater با slug/link/icon/image.',
  ],
  pass_=[
   '۵ خدمت با لینک صحیح نمایش داده شوند',
   'متن‌ها از داشبورد قابل ویرایش باشند',
   'بدون Repeater، سکشن حذف شود',
  ],
  asserts=['پنج خدمت', 'ویرایش‌پذیری', 'حذف خالی']),

dict(id='7.9', size='M', deps=['7.8'],
  title='صفحه جزئیات خدمت (۵ صفحه: میدان، خیابان، پل، المان، ساختمان)',
  desc='قالب برگه فرزند «خدمات» با hero، شرح، پروژه‌های مرتبط همان نوع و CTA استعلام — محتوا از برگه وردپرس (نه هاردکد).',
  files=['page-templates/template-service-detail.php','template-parts/services/detail-hero.php','template-parts/services/related-projects.php'],
  steps=[
   'انتخاب خودکار قالب برای فرزندان برگه «خدمات».',
   'پروژه‌های مرتبط بر اساس نگاشت slug خدمت → `project_type`.',
  ],
  pass_=[
   'هر ۵ صفحه رندر شوند',
   'نگاشت خدمت→نوع پروژه کار کند',
   'در نبود پروژه، سکشن حذف شود',
  ],
  asserts=['رندر پنج صفحه', 'نگاشت', 'حذف خالی']),

dict(id='7.10', size='L', deps=['5.18'],
  title='آرشیو وبلاگ (`home.php`) با فیلتر دسته و جستجو',
  desc='صفحه `blog` با hero، فیلتر دسته (`data-blog-cat-btn`)، جستجو (`data-blog-search`)، گرید کارت‌ها، شمارنده (`data-blog-count`) و صفحه‌بندی.',
  files=['home.php','template-parts/blog/hero.php','template-parts/blog/grid.php','template-parts/blog/filters.php'],
  steps=[
   'کارت با `data-blog-item`, `data-blog-category`, `data-blog-search-text`.',
   'دسته‌ها از `get_categories()` با شمارش.',
   'صفحه‌بندی سئو-دوست.',
  ],
  pass_=[
   '`/blog/` رندر صحیح با ۷+ مقاله',
   'فیلتر و جستجوی کلاینت با JS موجود کار کند',
   'شمارنده با تعداد واقعی',
  ],
  asserts=['رندر', 'فیلتر JS', 'شمارنده']),

dict(id='7.11', size='M', deps=['7.10'],
  title='آرشیو دسته نوشته (`category.php`) + برچسب/نویسنده',
  desc='آرشیو دسته با hero اختصاصی و همان گرید؛ به‌همراه `tag.php`، `author.php` و `archive.php` عمومی.',
  files=['category.php','tag.php','author.php','archive.php'],
  steps=[
   'استفاده مجدد از `grid.php`.',
   'Hero با نام/توضیح دسته.',
  ],
  pass_=[
   'دسته `magazine` و `news-and-events` رندر شوند',
   'آرشیو نویسنده/برچسب بدون خطا',
   '۴۰۴ برای ترم ناموجود',
  ],
  asserts=['رندر دسته‌ها', 'آرشیوهای دیگر', '۴۰۴']),

dict(id='7.12', size='L', deps=['7.10','3.17'],
  title='تک‌نوشته (`single.php`): محتوا + TOC + اشتراک‌گذاری',
  desc='تک‌نوشته با hero، جدول محتوا از `h2/h3`، دکمه‌های اشتراک‌گذاری (`data-share-btn`)، تاریخ شمسی، زمان مطالعه و افزایش بازدید.',
  files=['single.php','template-parts/blog/single-hero.php','template-parts/blog/toc.php','template-parts/blog/share.php','inc/Blog/TableOfContents.php'],
  steps=[
   'TOC از محتوای فیلترشده (`apply_filters(\'the_content\')`) با `DOMDocument` و افزودن `id` به سرتیترها.',
   'دکمه‌های اشتراک با `data-share-label` (کپی لینک/توییتر/واتساپ/تلگرام) بدون کتابخانه.',
   'زمان مطالعه با شمارش کلمات فارسی.',
  ],
  pass_=[
   'TOC لینک‌های صحیح به سرتیترها بدهد',
   'اشتراک‌گذاری بدون خطا',
   'افزایش بازدید ثبت شود',
   'محتوای خالی خطا ندهد',
  ],
  asserts=['TOC', 'اشتراک', 'بازدید', 'حالت خالی']),

dict(id='7.13', size='L', deps=['7.12'],
  title='نظرات تک‌نوشته + مقالات مرتبط',
  desc='فرم و لیست نظرات با همان مارک‌آپ و بلوک «مطالب مرتبط» (هم‌دسته) با کارت استاندارد.',
  files=['comments.php','template-parts/blog/related.php','template-parts/blog/comments.php'],
  steps=[
   '`wp_list_comments` با callback اختصاصی و مارک‌آپ استاتیک.',
   'فرم نظر با `comment_form()` و آرگومان‌های استایل.',
   'مرتبط‌ها حداکثر ۳، هم‌دسته.',
  ],
  pass_=[
   'ثبت نظر تستی موفق باشد',
   'Reply تودرتو کار کند',
   'مرتبط‌ها بدون نوشته جاری',
  ],
  asserts=['ثبت نظر', 'تودرتو', 'مرتبط']),

dict(id='7.14', size='M', deps=['5.18'],
  title='نتایج جستجو (`search.php`)',
  desc='صفحه جستجو با تفکیک نتایج (محصول/پروژه/مقاله)، پیام «چیزی یافت نشد» و بازاستفاده از کارت‌ها.',
  files=['search.php','template-parts/search/results.php'],
  steps=[
   'جستجوی `s` با نمایش نوع هر نتیجه.',
   'شمارش و صفحه‌بندی.',
  ],
  pass_=[
   'جستجوی موجود، نتایج مرتبط بدهد',
   'جستجوی خالی، پیام مناسب بدهد',
   'بدون کوئری سنگین (≤ ۲۰ نتیجه در صفحه)',
  ],
  asserts=['نتایج', 'حالت خالی', 'صفحه‌بندی']),

dict(id='7.15', size='M', deps=['4.2','4.3'],
  title='صفحه ۴۰۴ اختصاصی',
  desc='۴۰۴ با همان زبان بصری، پیشنهاد مسیرها (فروشگاه، پروژه‌ها، وبلاگ، تماس) و کد وضعیت صحیح — بدون طراحی جدید.',
  files=['404.php'],
  steps=[
   'استفاده از کلاس‌های موجود + دکمه‌های CTA.',
   'لاگ ۴۰۴ برای ادمین (اتصال به ابزار ۳.۵).',
  ],
  pass_=[
   'کد وضعیت ۴۰۴ برگردد',
   'لینک‌های پیشنهادی معتبر',
   'بدون خطا',
  ],
  asserts=['کد وضعیت', 'لینک‌ها']),

dict(id='7.16', size='M', deps=['2.11'],
  title='برگه «درباره ما» (`template-about.php`)',
  desc='۵ سکشن (هیرو، معرفی، گواهینامه، اصول، CTA) با Schema و ویرایش کامل از داشبورد.',
  files=['page-templates/template-about.php','inc/Sections/Schema/about.php'],
  steps=[
   'ثبت Schema ۵ سکشن با فیلدهای متنی/تصویری.',
   'انتخاب خودکار قالب برای برگه اسلاگ `about` (و `درباره-ما`).',
  ],
  pass_=[
   'رندر برابر استاتیک',
   'همه متن‌ها از داشبورد قابل تغییر',
   'اسلاگ فارسی `درباره-ما` هم قالب را بگیرد',
  ],
  asserts=['رندر', 'ویرایش‌پذیری', 'اسلاگ فارسی']),

dict(id='7.17', size='M', deps=['2.11'],
  title='برگه «تماس با ما» (`template-contact.php`) + فرم',
  desc='هیرو تماس، فرم مشاوره (`data-contact-form`) با Nonce/Honeypot (منطق در ۸.۵)، اطلاعات تماس، نقشه.',
  files=['page-templates/template-contact.php','inc/Sections/Schema/contact.php'],
  steps=[
   'مارک‌آپ فرم استاتیک با `name`های مشخص و nonce.',
   'اطلاعات تماس از `es_option`.',
  ],
  pass_=[
   'فرم بدون JS هم قابل ارسال باشد (`action` معتبر)',
   'Nonce در فرم موجود باشد',
   'اطلاعات تماس از داشبورد',
  ],
  asserts=['فرم بدون JS', 'nonce', 'داده‌های تماس']),

dict(id='7.18', size='M', deps=['2.11'],
  title='برگه «مجوزها و افتخارات» (`template-licenses.php`)',
  desc='۳ سکشن (هیرو، گواهینامه‌ها، CTA) با گالری گواهینامه‌ها از Media و متن‌های داینامیک.',
  files=['page-templates/template-licenses.php','inc/Sections/Schema/licenses.php'],
  steps=[
   'Repeater گواهینامه (عنوان، تصویر، توضیح، سال).',
   'لایت‌باکس با هوک‌های موجود `data-gallery-*`.',
  ],
  pass_=[
   'گالری با JS موجود کار کند',
   'بدون گواهینامه، سکشن حذف شود',
  ],
  asserts=['گالری', 'حذف خالی']),

dict(id='7.19', size='L', deps=['2.11'],
  title='برگه «سوالات متداول» (`template-faq.php`)',
  desc='FAQ صفحه‌ای با گروه‌بندی موضوعی، آکاردئون (`data-faq-*`) و Schema.org `FAQPage` برای سئو.',
  files=['page-templates/template-faq.php','inc/Sections/Schema/faq.php','inc/SEO/FaqSchema.php'],
  steps=[
   'Repeater گروه‌ها (`گروه`, `سؤال/پاسخ`).',
   'تولید JSON-LD `FAQPage` از همان داده (بدون تکرار میانی).',
  ],
  pass_=[
   'آکاردئون با JS موجود کار کند',
   'JSON-LD معتبر و برابر محتوای صفحه',
   'بدون سؤال، سکشن حذف شود',
  ],
  asserts=['آکاردئون', 'JSON-LD', 'حذف خالی']),

dict(id='7.20', size='M', deps=['2.11'],
  title='برگه «شرایط گارانتی» (`template-refund.php`)',
  desc='۳ سکشن سیاست‌نامه با ساختار تیتر/بند و ویرایش کامل از داشبورد (بدون نیاز به ویرایشگر متن).',
  files=['page-templates/template-refund.php','inc/Sections/Schema/refund.php'],
  steps=[
   'Repeater بندها (عنوان، متن) با `wp_kses_post`.',
   'فهرست شماره‌دار با همان استایل استاتیک.',
  ],
  pass_=[
   'همه بندها ویرایش‌پذیر باشند',
   'HTML مجاز محدود شود (`wp_kses_post`)',
  ],
  asserts=['ویرایش‌پذیری', 'kses']),

dict(id='7.21', size='L', deps=['2.11'],
  title='برگه «فرصت‌های شغلی» (`template-recruitment.php`)',
  desc='برگه استخدام با تب‌های واحدها (`initRecruitmentTabs`), شرح موقعیت و فرم ارسال رزومه (Nonce + Honeypot، منطق در ۸.۵).',
  files=['page-templates/template-recruitment.php','inc/Sections/Schema/recruitment.php'],
  steps=[
   'Repeater موقعیت‌ها (واحد، عنوان، شرح، شرایط، لینک درخواست).',
   'تب‌ها با `data-*` موجود.',
   'فرم با فیلد رزومه (فقط PDF، حداکثر ۵MB) — پردازش در ۸.۵.',
  ],
  pass_=[
   'تب‌ها با JS موجود کار کنند',
   'فرم بدون JS قابل ارسال باشد',
   'محدودیت نوع/حجم فایل در فرانت و بک‌اند اعمال شود',
  ],
  asserts=['تب‌ها', 'فرم بدون JS', 'محدودیت فایل']),

dict(id='7.22', size='M', deps=['7.16','7.17','7.18','7.19','7.20','7.21'],
  title='`page.php` عمومی + نگاشت خودکار قالب‌های برگه',
  desc='قالب برگه عمومی (برای برگه‌های بدون قالب اختصاصی) با مارک‌آپ سازگار، و نگاشت خودکار اسلاگ→قالب برای برگه‌های ثابت (شامل اسلاگ‌های فارسی).',
  files=['page.php','inc/Setup/TemplateRouter.php'],
  steps=[
   '`template_include` filter با نگاشت اسلاگ‌ها (`cart`, `checkout`, `my-account`, `shop`, `blog`, `about`, `contact`, `faq`, …).',
   '`page.php` ساده با `the_content` و breadcrumb.',
  ],
  pass_=[
   'برگه ناشناس با `page.php` رندر شود',
   'هر ۶ برگه ثابت قالب اختصاصی بگیرند',
   'اسلاگ فارسی «درباره-ما»/«تماس-با-ما» هم نگاشت شوند',
  ],
  asserts=['برگه ناشناس', 'نگاشت شش برگه', 'اسلاگ فارسی']),

dict(id='7.23', size='S', deps=['7.1','7.2','7.3','7.4','7.5','7.6','7.7','7.8','7.9','7.10','7.11','7.12','7.13','7.14','7.15','7.16','7.17','7.18','7.19','7.20','7.21','7.22'],
  title='گیت فاز ۷: رندر همه قالب‌ها + جستجوی ۴۰۴',
  desc='اجرای کل تست‌ها، رندر هر ۲۲ قالب با داده seed، و بررسی HTTP 200 برای همه URLهای میرور (۱۲۸ مسیر).',
  files=['temp/phase-07-projects-blog-and-pages/tests/run-phase-07-tests.sh','tools/harness/url-audit.php'],
  steps=[
   'مقایسه فهرست URLهای میرور با URLهای قالب.',
   'گزارش هر مسیر با کد وضعیت.',
  ],
  pass_=[
   '۱۰۰٪ تست‌ها PASS',
   'همه مسیرهای حیاتی ۲۰۰ (بدون ۴۰۴ ناخواسته)',
   'صفر Warning',
  ],
  asserts=['اجرای فاز', 'ممیزی URL', 'بدون Warning']),
])
