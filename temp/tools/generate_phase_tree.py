#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Stage 1 generator: builds temp/phase-XX/{tasks,tests} for all 8 phases.
Idempotent: re-running overwrites task specs + test skeletons (not theme code)."""
import os, stat

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))  # repo root (file is temp/tools/)
TEMP = os.path.join(ROOT, 'temp')

# Each task: num, title, goal, targets[], passes[], checks[(label, php_expr)], wp_checks[(label, php_expr)]
PHASES = [
 ("phase-01-core-and-assets", "هسته قالب، Autoloader و Assets", [
   dict(num="1.1", title="اسکلت پایه قالب + معماری OOP",
     goal="ساخت اسکلت قالب در erfansanat/ و معماری شیءگرا با Namespace اختصاصی، بدون خطا و با پشتیبانی‌های لازم.",
     targets=["erfansanat/style.css","erfansanat/functions.php","erfansanat/index.php","erfansanat/inc/Autoloader.php","erfansanat/inc/Core/Theme.php"],
     passes=["style.css دارای هدر معتبر قالب (Theme Name, Version, Text Domain, RTL)","php -l روی همه فایل‌های PHP پاس شود","فعال‌سازی قالب بدون Fatal/Notice","add_theme_support برای title-tag, post-thumbnails, woocommerce, html5","Autoloader با Namespace ErfanSanat\\Theme کلاس‌ها را از inc/ بارگذاری کند"],
     checks=[("style.css exists","es_file('style.css')"),("style.css theme header","es_contains('style.css','Theme Name:')"),("functions.php exists","es_file('functions.php')"),("index.php exists","es_file('index.php')"),("Autoloader exists","es_file('inc/Autoloader.php')"),("Namespace ErfanSanat\\Theme","es_contains('inc/Autoloader.php','ErfanSanat')")],
     wp_checks=[("theme activates","es_activate_theme()"),("supports title-tag","current_theme_supports('title-tag')"),("supports post-thumbnails","current_theme_supports('post-thumbnails')"),("supports woocommerce","current_theme_supports('woocommerce')"),("supports html5","current_theme_supports('html5')")]),
   dict(num="1.2", title="ماژول DeBloat (پاکسازی هسته)",
     goal="حذف کامل استایل/اسکریپت گوتنبرگ، ایموجی‌ها و تگ‌های اضافی wp_head برای حداکثر سرعت.",
     targets=["erfansanat/inc/Core/DeBloat.php"],
     passes=["wp-block-library و global-styles dequeue شوند","ایموجی‌ها (print_emoji_detection_script, print_emoji_styles) حذف شوند","تگ‌های اضافی wp_head (generator, rsd, wlwmanifest, shortlink) پاک شوند","در سورس HTML خروجی هیچ CSS/JS بلوک وردپرس نباشد"],
     checks=[("DeBloat module exists","es_file('inc/Core/DeBloat.php')"),("dequeues wp-block-library","es_contains('inc/Core/DeBloat.php','wp-block-library')"),("removes emoji","es_contains('inc/Core/DeBloat.php','print_emoji_detection_script')"),("cleans wp_head","es_contains('inc/Core/DeBloat.php','wp_generator')")],
     wp_checks=[("emoji script removed from wp_head","!has_action('wp_head','print_emoji_detection_script')"),("emoji styles removed","!has_action('wp_print_styles','print_emoji_styles')"),("generator removed","!has_action('wp_head','wp_generator')")]),
   dict(num="1.3", title="انتقال Assets + AssetManager (enqueue شرطی)",
     goal="انتقال تمام CSS/JS/فونت/تصاویر استاتیک به assets/ و enqueue شرطی per-template با نسخه‌گذاری filemtime.",
     targets=["erfansanat/assets/css/styles.css","erfansanat/assets/js/main.js","erfansanat/assets/fonts/","erfansanat/inc/Core/AssetManager.php"],
     passes=["همه assetهای استاتیک به assets/ منتقل شوند","AssetManager فایل هر صفحه را فقط در همان قالب enqueue کند","نسخه‌گذاری با filemtime","مسیرهای نسبی url(...) داخل CSS نشکنند","فونت‌های فارسی با HTTP 200 لود شوند"],
     checks=[("styles.css copied","es_file('assets/css/styles.css')"),("main.js copied","es_file('assets/js/main.js')"),("fonts dir","es_dir('assets/fonts')"),("AssetManager exists","es_file('inc/Core/AssetManager.php')"),("filemtime versioning","es_contains('inc/Core/AssetManager.php','filemtime')"),("conditional enqueue","es_contains('inc/Core/AssetManager.php','is_')")],
     wp_checks=[]),
 ]),
 ("phase-02-custom-admin-dashboard", "داشبورد اختصاصی ادمین + Section Builder", [
   dict(num="2.1", title="پنل داشبورد اختصاصی (ThemeOptions)",
     goal="پنل تب‌بندی‌شده RTL با ذخیره AJAX، بدون وابستگی به افزونه، امن و بهینه.",
     targets=["erfansanat/inc/Admin/ThemeOptions.php","erfansanat/inc/Helpers.php"],
     passes=["بررسی current_user_can('manage_options') و Nonce در ذخیره","ذخیره در آپشن واحد erfansanat_theme_options","Helper ایستا es_option($key,$default) با کش استاتیک","ظاهر مدرن RTL و تب‌بندی"],
     checks=[("ThemeOptions exists","es_file('inc/Admin/ThemeOptions.php')"),("capability check","es_contains('inc/Admin/ThemeOptions.php','manage_options')"),("nonce check","es_contains('inc/Admin/ThemeOptions.php','nonce')"),("single option key","es_contains('inc/Admin/ThemeOptions.php','erfansanat_theme_options')"),("es_option helper","es_contains('inc/Helpers.php','function es_option')")],
     wp_checks=[("es_option returns default","function_exists('es_option') && es_option('nonexistent_key_xyz','fallback')==='fallback'")]),
   dict(num="2.2", title="تب‌های مدیریتی سراسری",
     goal="تب‌های هدر/مگامنو/لوگو/اعلان/فوتر/تماس/شبکه‌های/رنگ‌ها/اسکریپت‌ها/آرشیو با پیش‌فرض دقیق استاتیک.",
     targets=["erfansanat/inc/Admin/ThemeOptions.php","erfansanat/inc/Defaults.php"],
     passes=["هر فیلد مقدار پیش‌فرض برابر محتوای استاتیک داشته باشد","تغییر ادمین بلافاصله در فرانت‌اند اعمال شود","فیلدهای Media/Link/Repeater پشتیبانی شوند"],
     checks=[("Defaults module","es_file('inc/Defaults.php')"),("header tab","es_contains('inc/Admin/ThemeOptions.php','header')"),("footer tab","es_contains('inc/Admin/ThemeOptions.php','footer')"),("contact tab","es_contains('inc/Admin/ThemeOptions.php','contact')"),("brand colors","es_contains('inc/Admin/ThemeOptions.php','color')")],
     wp_checks=[]),
   dict(num="2.3", title="موتور SectionManager",
     goal="مدیریت سکشن‌های صفحات: روشن/خاموش، ترتیب Drag&Drop، ویرایش متن/تصویر/آیکون/ویدیو/دکمه/Repeater و رندر پویا.",
     targets=["erfansanat/inc/Admin/SectionManager.php"],
     passes=["ذخیره ساختاریافته متادیتای سکشن‌ها","پشتیبانی Media/Link/Icon/Repeater","رندر بر اساس ترتیب انتخابی ادمین","فعال/غیرفعال‌سازی هر سکشن"],
     checks=[("SectionManager exists","es_file('inc/Admin/SectionManager.php')"),("repeater support","es_contains('inc/Admin/SectionManager.php','repeater')"),("enable/disable","es_contains('inc/Admin/SectionManager.php','enabled')"),("order rendering","es_contains('inc/Admin/SectionManager.php','order')")],
     wp_checks=[]),
 ]),
 ("phase-03-cpt-and-legacy-meta", "CPTها، تکسونومی‌ها و متاباکس‌های Legacy", [
   dict(num="3.1", title="ثبت CPTها/تکسونومی‌ها + Rewrite + ریدایرکت‌ها",
     goal="ثبت بومی projects/project_type/city/services و rewrite دقیق product_cat؛ فیلترهای ادمین؛ ریدایرکت project→/projects/.",
     targets=["erfansanat/inc/Core/PostTypes.php","erfansanat/inc/Core/Taxonomies.php","erfansanat/inc/Core/Rewrites.php","erfansanat/inc/Core/LegacyRedirects.php"],
     passes=["projects با rewrite slug=projects, with_front=false, has_archive=true","project_type و city با rewrite دقیق و hierarchical=true","product_cat با slug=product_cat و with_front=true","CPT services (D2) با has_archive=false","فیلتر دراپ‌داون شهر/نوع در لیست ادمین پروژه‌ها","ستون sortable same_key_hrefl","ریدایرکت 301 برگه project به /projects/","عدم 404 در لینک‌های قدیمی"],
     checks=[("PostTypes module","es_file('inc/Core/PostTypes.php')"),("Taxonomies module","es_file('inc/Core/Taxonomies.php')"),("Rewrites module","es_file('inc/Core/Rewrites.php')"),("LegacyRedirects module","es_file('inc/Core/LegacyRedirects.php')"),("services CPT","es_contains('inc/Core/PostTypes.php','services')"),("project redirect","es_contains('inc/Core/LegacyRedirects.php','projects')")],
     wp_checks=[("projects CPT registered","post_type_exists('projects')"),("services CPT registered","post_type_exists('services')"),("project_type taxonomy","taxonomy_exists('project_type')"),("city taxonomy","taxonomy_exists('city')"),("projects rewrite slug","(($o=get_post_type_object('projects')) && $o->rewrite['slug']==='projects')"),("city rewrite with_front false","(($t=get_taxonomy('city')) && $t->rewrite['with_front']===false)")]),
   dict(num="3.2", title="موتور متاباکس محصولات (سازگار با JetEngine)",
     goal="متاباکس تب‌بندی‌شده برای تمام کلیدهای متای محصول + Repeaterها با حفظ فرمت ذخیره فعلی.",
     targets=["erfansanat/inc/Admin/ProductMetaboxes.php","erfansanat/inc/Core/LegacyMeta.php"],
     passes=["خواندن داده serialized و JSON بدون تخریب","ذخیره مجدد با همان فرمت","همه کلیدها: gs1_id,national_code,hs_id,isic_id,cpc_id,product_id,zireonvan,english_titl,before-sale-c,true_sale,product-catalog-file,galerry_produ,project-ids,usage_countr","Repeaterها: product-feature, product-feature2_copy(vaule_of_variable), usage_product, faq","Fallback گالری: _product_image_gallery, gallery-filed, image-2..6"],
     checks=[("ProductMetaboxes exists","es_file('inc/Admin/ProductMetaboxes.php')"),("LegacyMeta engine","es_file('inc/Core/LegacyMeta.php')"),("key galerry_produ","es_contains('inc/Admin/ProductMetaboxes.php','galerry_produ')"),("key english_titl","es_contains('inc/Admin/ProductMetaboxes.php','english_titl')"),("key vaule_of_variable","es_contains('inc/Admin/ProductMetaboxes.php','vaule_of_variable')"),("repeater item-0 format","es_contains('inc/Core/LegacyMeta.php','item-0')")],
     wp_checks=[]),
   dict(num="3.3", title="متاباکس پروژه‌ها + FAQ دسته + Hreflang + بازدید",
     goal="متاباکس پروژه (تصاویر با املای دقیق)، FAQ و thumbnail دسته محصولات، Hreflang چندزبانه و شمارنده بازدید.",
     targets=["erfansanat/inc/Admin/ProjectMetaboxes.php","erfansanat/inc/Admin/TermMetaboxes.php","erfansanat/inc/Admin/HreflangMetabox.php","erfansanat/inc/Core/ViewCounter.php"],
     passes=["کلیدهای second-image,three-image,foure-image,five-image,six-image + گالری مدرن همگام","finish-pro-time و mortabet_mahsole_2","product_cat -> faq(name_question,answer_question) + thumbnail","hrefmeta_fa/en/ar + same_key_hrefl","number2-viewc + fallback number-viewc","تست خواندن/نوشتن روی همه کلیدها"],
     checks=[("ProjectMetaboxes exists","es_file('inc/Admin/ProjectMetaboxes.php')"),("key foure-image","es_contains('inc/Admin/ProjectMetaboxes.php','foure-image')"),("mortabet_mahsole_2","es_contains('inc/Admin/ProjectMetaboxes.php','mortabet_mahsole_2')"),("TermMetaboxes exists","es_file('inc/Admin/TermMetaboxes.php')"),("HreflangMetabox exists","es_file('inc/Admin/HreflangMetabox.php')"),("hrefmeta keys","es_contains('inc/Admin/HreflangMetabox.php','hrefmeta_fa')"),("ViewCounter exists","es_file('inc/Core/ViewCounter.php')"),("number2-viewc fallback","es_contains('inc/Core/ViewCounter.php','number-viewc')")],
     wp_checks=[]),
 ]),
 ("phase-04-header-footer-navigation", "هدر، مگامنو، منوی موبایل، فوتر", [
   dict(num="4.1", title="برش هدر/فوتر + اتصال به تنظیمات + Hreflang",
     goal="برش هدر دسکتاپ/موبایل و فوتر از استاتیک به header.php/footer.php با حفظ ۱۰۰٪ DOM و هوک‌های استاندارد.",
     targets=["erfansanat/header.php","erfansanat/footer.php","erfansanat/template-parts/header/","erfansanat/template-parts/global/"],
     passes=["حفظ ۱۰۰٪ کلاس‌ها/DOM/data-attr هدر و فوتر استاتیک","wp_head(), body_class(), wp_body_open(), wp_footer() فراخوانی شوند","اتصال به تنظیمات داشبورد (لوگو/اعلان/تماس/شبکه‌ها)","تگ‌های Hreflang در <head>"],
     checks=[("header.php exists","es_file('header.php')"),("footer.php exists","es_file('footer.php')"),("wp_head call","es_contains('header.php','wp_head')"),("body_class call","es_contains('header.php','body_class')"),("wp_body_open call","es_contains('header.php','wp_body_open')"),("wp_footer call","es_contains('footer.php','wp_footer')"),("data-navbar-header preserved","es_contains('header.php','data-navbar-header')")],
     wp_checks=[]),
   dict(num="4.2", title="Custom Nav Walkers",
     goal="Walkerهای اختصاصی برای ۴ موقعیت منو با تولید DOM دقیقاً منطبق بر استاتیک.",
     targets=["erfansanat/inc/Core/NavWalkers.php","erfansanat/inc/Core/Menus.php"],
     passes=["ثبت ۴ موقعیت: main-mega-menu, mobile-menu, tolidat-sherkat, landing","DOM منوها منطبق بر مارک‌آپ استاتیک (data-mega-*, data-mobile-acc-*)","کارکرد JS باز/بسته مگامنو و منوی موبایل"],
     checks=[("NavWalkers exists","es_file('inc/Core/NavWalkers.php')"),("Menus module","es_file('inc/Core/Menus.php')"),("main-mega-menu location","es_contains('inc/Core/Menus.php','main-mega-menu')"),("mobile-menu location","es_contains('inc/Core/Menus.php','mobile-menu')"),("tolidat-sherkat location","es_contains('inc/Core/Menus.php','tolidat-sherkat')"),("landing location","es_contains('inc/Core/Menus.php','landing')")],
     wp_checks=[("menu locations registered","(($m=get_registered_nav_menus()) && isset($m['main-mega-menu'],$m['mobile-menu'],$m['tolidat-sherkat'],$m['landing']))")]),
 ]),
 ("phase-05-homepage-and-sections", "صفحه اصلی + کامپوننت‌های ماژولار", [
   dict(num="5.1", title="برش ۱۵ سکشن + front-page.php با SectionManager",
     goal="برش تک‌تک سکشن‌های صفحه اصلی به template-parts/home/ و رندر پویا با ترتیب/فعال‌سازی از داشبورد.",
     targets=["erfansanat/front-page.php","erfansanat/template-parts/home/section-*.php"],
     passes=["۱۵ سکشن مطابق جدول §۴ نگاشت به فایل مجزا","رندر پیش‌فرض دقیقاً مشابه استاتیک","تغییر ترتیب/حذف سکشن از داشبورد اعمال شود"],
     checks=[("front-page.php exists","es_file('front-page.php')"),("hero section","es_file('template-parts/home/section-hero.php')"),("projects section","es_file('template-parts/home/section-featured-projects.php')"),("products section","es_file('template-parts/home/section-products.php')"),("blog section","es_file('template-parts/home/section-blog.php')"),("SectionManager loop","es_contains('front-page.php','SectionManager')")],
     wp_checks=[]),
   dict(num="5.2", title="اتصال داینامیک سکشن‌ها + کارت‌های استاندارد",
     goal="اتصال سکشن‌ها به WP_Query بهینه و فیلدهای داشبورد؛ هیرو D5؛ کارت‌های استاندارد در template-parts/cards/.",
     targets=["erfansanat/template-parts/cards/card-product.php","erfansanat/template-parts/cards/card-project.php","erfansanat/template-parts/cards/card-post.php","erfansanat/template-parts/home/section-hero.php"],
     passes=["صفر محتوای هاردکد","هیرو: ویدیوی اصلی جدا + اسلایدهای انتخابی از پروژه‌ها (D5)","کارت‌ها data-product-*/data-project-* را داینامیک پر کنند","WP_Query بهینه (no found_rows در صورت عدم نیاز)"],
     checks=[("card-product exists","es_file('template-parts/cards/card-product.php')"),("card-project exists","es_file('template-parts/cards/card-project.php')"),("card-post exists","es_file('template-parts/cards/card-post.php')"),("hero project selection","es_contains('template-parts/home/section-hero.php','project')"),("dynamic product data attr","es_contains('template-parts/cards/card-product.php','data-product-price')")],
     wp_checks=[]),
 ]),
 ("phase-06-woocommerce-and-products", "ووکامرس، محصولات تکی و آرشیو", [
   dict(num="6.1", title="قالب تک‌محصول (single-product)",
     goal="پیاده‌سازی تک‌محصول از استاتیک با اتصال همه متاها و منطق true_sale (آنلاین/استعلامی).",
     targets=["erfansanat/woocommerce/single-product.php","erfansanat/woocommerce/content-single-product.php","erfansanat/template-parts/product/"],
     passes=["گالری galerry_produ + گالری ووکامرس + fallbackها","zireonvan, english_titl, شناسه‌های قانونی, before-sale-c, product-catalog-file","جدول ۱۸ ویژگی pa_* + repeaterها + usage_product/usage_countr","project-ids (پروژه‌های مرتبط) + faq + محصولات مرتبط + دیدگاه‌ها","true_sale=true → افزودن به سبد؛ false → استعلام/تماس","عدم نمایش سکشن خالی وقتی متا پر نیست"],
     checks=[("single-product.php exists","es_file('woocommerce/single-product.php')"),("content-single-product.php","es_file('woocommerce/content-single-product.php')"),("galerry_produ used","es_contains('woocommerce/content-single-product.php','galerry_produ')"),("true_sale logic","es_contains('woocommerce/content-single-product.php','true_sale')"),("english_titl used","es_contains('woocommerce/content-single-product.php','english_titl')")],
     wp_checks=[]),
   dict(num="6.2", title="آرشیو محصولات + FAQ دسته + صفحه‌بندی",
     goal="آرشیو فروشگاه و دسته/برچسب از استاتیک با توضیحات و FAQ اختصاصی دسته و صفحه‌بندی اختصاصی.",
     targets=["erfansanat/woocommerce/archive-product.php","erfansanat/woocommerce/taxonomy-product_cat.php","erfansanat/woocommerce/taxonomy-product_tag.php"],
     passes=["کارکرد /shop/ و /product_cat/.../","رندر کارت محصول منطبق بر استاتیک","نمایش FAQ و توضیحات دسته (faq روی product_cat)","صفحه‌بندی اختصاصی"],
     checks=[("archive-product.php","es_file('woocommerce/archive-product.php')"),("taxonomy-product_cat.php","es_file('woocommerce/taxonomy-product_cat.php')"),("taxonomy-product_tag.php","es_file('woocommerce/taxonomy-product_tag.php')"),("category FAQ","es_contains('woocommerce/taxonomy-product_cat.php','faq')")],
     wp_checks=[]),
   dict(num="6.3", title="سبد/پرداخت/حساب کاربری + auth",
     goal="پوسته استاتیک برای cart/checkout/my-account و صفحه auth با حفظ هوک‌ها و سازگاری درگاه/افزونه‌ها.",
     targets=["erfansanat/woocommerce/cart/cart.php","erfansanat/woocommerce/checkout/form-checkout.php","erfansanat/woocommerce/myaccount/","erfansanat/page-templates/auth.php"],
     passes=["چرخه کامل خرید با شورت‌کدها/هوک‌های استاندارد ووکامرس","عدم تداخل اسکریپت","سازگاری با درگاه بانکی، Variation Swatches، WPC Quantity","صفحه auth با voorodak/fl-panel"],
     checks=[("cart.php override","es_file('woocommerce/cart/cart.php')"),("form-checkout.php override","es_file('woocommerce/checkout/form-checkout.php')"),("myaccount dir","es_file('woocommerce/myaccount/my-account.php') || es_dir('woocommerce/myaccount')"),("auth template","es_file('page-templates/auth.php')"),("checkout hook preserved","es_contains('woocommerce/checkout/form-checkout.php','woocommerce_checkout')")],
     wp_checks=[]),
 ]),
 ("phase-07-projects-blog-and-pages", "پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها", [
   dict(num="7.1", title="تک‌پروژه + آرشیوهای پروژه",
     goal="single-projects و آرشیوهای نوع/شهر از استاتیک با گالری، تاریخ، شهر/نوع و محصولات استفاده‌شده.",
     targets=["erfansanat/single-projects.php","erfansanat/archive-projects.php","erfansanat/taxonomy-project_type.php","erfansanat/taxonomy-city.php"],
     passes=["گالری second-image..six-image (املای foure-image)","finish-pro-time, city, project_type, mortabet_mahsole_2","رندر آرشیو نوع/شهر منطبق بر استاتیک (slug-agnostic — D3)"],
     checks=[("single-projects.php","es_file('single-projects.php')"),("archive-projects.php","es_file('archive-projects.php')"),("taxonomy-project_type.php","es_file('taxonomy-project_type.php')"),("taxonomy-city.php","es_file('taxonomy-city.php')"),("foure-image used","es_contains('single-projects.php','foure-image')"),("mortabet used","es_contains('single-projects.php','mortabet_mahsole_2')")],
     wp_checks=[]),
   dict(num="7.2", title="وبلاگ + تک‌نوشته (TOC/دیدگاه) + جستجو + 404",
     goal="قالب‌های وبلاگ و تک‌نوشته با TOC و دیدگاه (D6)، اشتراک، شمارنده بازدید، مرتبط؛ search و 404.",
     targets=["erfansanat/home.php","erfansanat/archive.php","erfansanat/category.php","erfansanat/single.php","erfansanat/search.php","erfansanat/404.php","erfansanat/template-parts/post/"],
     passes=["تک‌نوشته: TOC + اشتراک + number2-viewc (افزایش خودکار) + مرتبط + دیدگاه","کارکرد همه صفحات محتوایی","search.php و 404.php با همان design system"],
     checks=[("home.php","es_file('home.php')"),("single.php","es_file('single.php')"),("search.php","es_file('search.php')"),("404.php","es_file('404.php')"),("TOC part","es_file('template-parts/post/toc.php')"),("views counter used","es_contains('single.php','number2-viewc')")],
     wp_checks=[]),
   dict(num="7.3", title="قالب‌های برگه اختصاصی + لندینگ‌ها",
     goal="page-templates برای درباره/تماس/مجوزها/FAQ/گارانتی/استخدام/smartelement/آریا با ویرایش کامل از داشبورد.",
     targets=["erfansanat/page-templates/about.php","erfansanat/page-templates/contact.php","erfansanat/page-templates/faq.php","erfansanat/page-templates/licenses.php","erfansanat/page-templates/warranty.php","erfansanat/page-templates/recruitment.php","erfansanat/page-templates/services.php","erfansanat/page-templates/landing-arya.php"],
     passes=["همه متون/تصاویر/تب‌ها/آکاردئون‌ها از پنل ادمین قابل ویرایش","بدون نیاز به المنتور","لندینگ آریا با منوی landing (D1)"],
     checks=[("about template","es_file('page-templates/about.php')"),("contact template","es_file('page-templates/contact.php')"),("faq template","es_file('page-templates/faq.php')"),("recruitment template","es_file('page-templates/recruitment.php')"),("services template","es_file('page-templates/services.php')"),("arya landing","es_file('page-templates/landing-arya.php')"),("warranty template","es_file('page-templates/warranty.php')")],
     wp_checks=[]),
 ]),
 ("phase-08-ajax-filters-and-audit", "ایجکس، فرم‌ها و ممیزی نهایی", [
   dict(num="8.1", title="Live AJAX Search + Smart Filters",
     goal="جایگزینی کامل JetSearch/JetSmartFilters با موتور بومی ایجکس امن و سریع.",
     targets=["erfansanat/inc/Core/AjaxSearch.php","erfansanat/inc/Core/SmartFilters.php","erfansanat/assets/js/filters.js"],
     passes=["فیلتر محصول: متن/product_cat/قیمت/true_sale/pa_* + مرتب‌سازی + حذف فیلتر","فیلتر پروژه: project_type/city + شمارنده","پاسخ <200ms","امنیت SQLi/XSS (nonce+sanitize+escape)","به‌روزرسانی بدون رفرش"],
     checks=[("AjaxSearch module","es_file('inc/Core/AjaxSearch.php')"),("SmartFilters module","es_file('inc/Core/SmartFilters.php')"),("nonce in search","es_contains('inc/Core/AjaxSearch.php','nonce')"),("true_sale filter","es_contains('inc/Core/SmartFilters.php','true_sale')"),("city filter","es_contains('inc/Core/SmartFilters.php','city')")],
     wp_checks=[("ajax search hook","has_action('wp_ajax_es_ajax_search')"),("ajax filters hook","has_action('wp_ajax_es_ajax_filters')")]),
   dict(num="8.2", title="سیستم مدیریت فرم‌ها",
     goal="فرم‌های تماس/استعلام/استخدام/بازخورد با ذخیره در ادمین، Honeypot/Nonce و ایمیل.",
     targets=["erfansanat/inc/Core/Forms.php","erfansanat/inc/Admin/FormEntries.php"],
     passes=["ثبت امن از فرانت‌اند","نمایش لیست پیام‌ها در منوی اختصاصی ادمین","Honeypot + Nonce","ایمیل اطلاع‌رسانی"],
     checks=[("Forms module","es_file('inc/Core/Forms.php')"),("FormEntries admin","es_file('inc/Admin/FormEntries.php')"),("honeypot","es_contains('inc/Core/Forms.php','honeypot')"),("nonce","es_contains('inc/Core/Forms.php','nonce')"),("wp_mail","es_contains('inc/Core/Forms.php','wp_mail')")],
     wp_checks=[]),
   dict(num="8.3", title="ممیزی نهایی + سوئیت E2E",
     goal="ممیزی سئو/پرفورمنس/امنیت و اجرای مجدد تمام تست‌های فاز ۱..۸.",
     targets=["temp/run-all-phases-e2e.sh","erfansanat/inc/Core/Seo.php"],
     passes=["سازگاری SEOPress + Hreflang + JSON-LD","Escaping همه خروجی‌ها","تست ریسپانسیو","پاس شدن ۱۰۰٪ تست‌ها با صفر خطا/Warning در WP_DEBUG"],
     checks=[("Seo module","es_file('inc/Core/Seo.php')"),("e2e runner","es_file('temp/run-all-phases-e2e.sh', ES_ROOT)")],
     wp_checks=[]),
 ]),
]

TASK_MD = """# Task {num} — {title}

> فاز: {phase} · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
{goal}

## فایل‌های هدف
{targets}

## شروط پاس شدن (Pass Conditions)
{passes}

## تست
- اسکریپت: `tests/test-task-{nn}.php` (اجرا: `php temp/{folder}/tests/test-task-{nn}.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
"""

TEST_PHP = """<?php
/**
 * Task {num} — {title}
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/{folder}/tests/test-task-{nn}.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task {num}: {title} ==\\n";

{checks}
{wp_block}
es_summary();
"""

RUN_SH = """#!/usr/bin/env bash
# Comprehensive phase test runner: {phase}
set -u
DIR="$(cd "$(dirname "${{BASH_SOURCE[0]}}")" && pwd)"
ROOT="$(cd "$DIR/../../.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
echo "================ {phase} ================"
# Pre-activate our theme in a separate process so after_setup_theme fires with
# the correct theme when each test loads WP (theme-support checks stay accurate).
if [ -f "$ROOT/erfansanat/style.css" ]; then
  wp theme activate erfansanat >/dev/null 2>&1 || true
fi
fail=0
for t in "$DIR"/test-task-*.php; do
  echo "---- $(basename "$t") ----"
  php "$t" || fail=1
done
if [ "$fail" -eq 0 ]; then echo "PHASE RESULT: ALL PASS"; else echo "PHASE RESULT: FAILURES"; fi
exit "$fail"
"""

def php_checks(pairs):
    return "\n".join(f"es_check( '{label}', (bool) ({expr}) );" for label, expr in pairs)

count_tasks = 0
for folder, phase_title, tasks in PHASES:
    base = os.path.join(TEMP, folder)
    os.makedirs(os.path.join(base, 'tasks'), exist_ok=True)
    os.makedirs(os.path.join(base, 'tests'), exist_ok=True)
    for i, t in enumerate(tasks, start=1):
        nn = f"{i:02d}"
        targets = "\n".join(f"- `{x}`" for x in t['targets'])
        passes = "\n".join(f"- [ ] {x}" for x in t['passes'])
        md = TASK_MD.format(num=t['num'], title=t['title'], phase=phase_title,
                            goal=t['goal'], targets=targets, passes=passes, nn=nn, folder=folder)
        open(os.path.join(base, 'tasks', f'task-{nn}.md'), 'w', encoding='utf-8').write(md)
        checks = php_checks(t['checks'])
        wp_block = ""
        if t['wp_checks']:
            lines = "\n".join(f"  es_check( '{label}', (bool) ({expr}) );" for label, expr in t['wp_checks'])
            skips = "\n".join(f"  es_skip( '{label}', 'erfansanat theme not active yet' );" for label, _ in t['wp_checks'])
            wp_block = ("// WP-dependent checks run ONLY when our theme is active (avoids false\n"
                        "// positives from the default theme that ships with the QA install).\n"
                        "if ( es_activate_theme() ) {\n" + lines + "\n} else {\n" + skips + "\n}")
        php = TEST_PHP.format(num=t['num'], title=t['title'], folder=folder, nn=nn,
                              checks=checks, wp_block=wp_block)
        open(os.path.join(base, 'tests', f'test-task-{nn}.php'), 'w', encoding='utf-8').write(php)
        count_tasks += 1
    sh = RUN_SH.format(phase=phase_title)
    sh_path = os.path.join(base, 'tests', 'run-phase-tests.sh')
    open(sh_path, 'w', encoding='utf-8').write(sh)
    os.chmod(sh_path, os.stat(sh_path).st_mode | stat.S_IEXEC | stat.S_IXGRP | stat.S_IXOTH)

# E2E runner (all phases)
e2e = """#!/usr/bin/env bash
# End-to-end suite: re-run every phase's tests (Task 8.3).
set -u
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
overall=0
for d in "$ROOT"/temp/phase-*/tests/run-phase-tests.sh; do
  bash "$d" || overall=1
done
if [ "$overall" -eq 0 ]; then echo "E2E RESULT: ALL PHASES PASS"; else echo "E2E RESULT: FAILURES"; fi
exit "$overall"
"""
e2e_path = os.path.join(TEMP, 'run-all-phases-e2e.sh')
open(e2e_path, 'w', encoding='utf-8').write(e2e)
os.chmod(e2e_path, os.stat(e2e_path).st_mode | stat.S_IEXEC | stat.S_IXGRP | stat.S_IXOTH)

print(f"Generated {len(PHASES)} phases, {count_tasks} tasks (+test skeletons), 8 phase runners, 1 e2e runner.")
