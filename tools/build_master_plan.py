#!/usr/bin/env python3
"""Generate the approved Stage 1 task/test tree from one auditable specification."""
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[1]
TEMP = ROOT / "temp"
PHASES = [
    ("01", "core-and-assets", "هسته قالب و دارایی‌ها", [
        ("01", "اسکلت قالب، Autoloader و پشتیبانی ووکامرس", ["style.css", "functions.php", "index.php", "inc/"], ["1.1"],
         "ساختار شیءگرا با namespace `ErfanSanat\\Theme`؛ بارگذاری خودکار امن کلاس‌ها؛ بدون وابستگی به Elementor/Jet.",
         ["قالب در وردپرس آزمایشی فعال شود و هیچ Fatal/Warning منتسب به قالب ایجاد نکند.", "همه فایل‌های PHP با `php -l` پاس شوند.", "پشتیبانی title-tag، post-thumbnails، woocommerce و html5 ثبت شود.", "هیچ فایل مرجع در static-mirror تغییر نکند."],
         ["assert_true(wp_get_theme()->get('Name') !== '');", "assert_true(current_theme_supports('title-tag'));", "assert_true(current_theme_supports('post-thumbnails'));", "assert_true(current_theme_supports('woocommerce'));", "assert_true(current_theme_supports('html5'));"]),
        ("02", "پاکسازی خروجی عمومی بدون شکستن افزونه‌های ضروری", ["inc/DeBloat.php"], ["1.2"],
         "حذف هدفمند block library، global styles و emoji در فرانت‌اند؛ حفظ خروجی موردنیاز مدیریت و افزونه‌های باقی‌مانده.",
         ["در صفحه عمومی آزمایشی هیچ asset گوتنبرگ یا ایموجی بارگذاری نشود.", "وابستگی‌های ووکامرس، سواچ‌ها و افزونه ورود پیامکی حذف نشوند.", "در ویرایشگر مدیریت، قابلیت‌های لازم حفظ شوند."],
         ["assert_true(wp_style_is('wp-block-library', 'done') === false);", "assert_true(wp_style_is('global-styles', 'done') === false);", "assert_true(!has_action('wp_head', 'print_emoji_detection_script'));"]),
        ("03", "انتقال و بارگذاری شرطی Assets", ["inc/AssetManager.php", "assets/"], ["1.3"],
         "انتقال بدون تغییر محتوای CSS/JS/فونت؛ حفظ مسیرهای نسبی فونت؛ تفکیک بارگذاری صفحه‌ها و نسخه‌گذاری filemtime.",
         ["CSS/JS/فونت‌های هر نمای تبدیل‌شده HTTP 200 باشند.", "هش فایل‌های منتقل‌شده با مرجع برابر باشد یا تغییر مجاز مستند شود.", "مسیرهای url(../fonts/...) شکسته نباشند.", "نسخه هر asset برابر filemtime فایل محلی باشد.", "ویدیو روی صفحات غیرمرتبط preload نشود."],
         ["assert_true(class_exists('ErfanSanat\\Theme\\AssetManager'));", "assert_true(is_dir(get_template_directory() . '/assets/fonts'));", "assert_true(file_exists(get_template_directory() . '/assets/css/styles.css'));"]),
    ]),
    ("02", "custom-admin-dashboard", "داشبورد اختصاصی و مدیریت سکشن‌ها", [
        ("01", "زیرساخت تنظیمات و ذخیره AJAX امن", ["inc/Admin/ThemeOptions.php", "inc/helpers.php"], ["2.1"],
         "پنل RTL تب‌بندی‌شده با مدیریت یک option و کش خواندن؛ ذخیره AJAX با بررسی مجوز و nonce.",
         ["کاربر بدون manage_options نتواند بخواند یا ذخیره کند.", "nonce نامعتبر رد شود.", "تنظیمات فقط در erfansanat_theme_options ذخیره شوند.", "es_option مقدار ذخیره‌شده و fallback صحیح بدهد."],
         ["assert_true(class_exists('ErfanSanat\\Theme\\Admin\\ThemeOptions'));", "assert_true(function_exists('ErfanSanat\\Theme\\es_option') || function_exists('es_option'));"]),
        ("02", "تب‌های سراسری هدر، فوتر، برند و آرشیو", ["inc/Admin/ThemeOptions.php", "template-parts/options/"], ["2.2"],
         "فیلدهای قابل ویرایش برای لوگو، اعلان، تماس، شبکه‌ها، منو، رنگ، اسکریپت مجاز و آرشیوها؛ مقادیر اولیه از مرجع.",
         ["تغییر هر فیلد پس از ذخیره در خروجی مرتبط دیده شود.", "fallbackها با متن/رسانه مرجع مطابقت داشته باشند.", "اسکریپت سفارشی فقط برای مجوز تعیین‌شده و با سیاست امنیتی ذخیره شود.", "داده نامعتبر و کلید ناشناخته ذخیره نشود."],
         ["assert_true(is_array(get_option('erfansanat_theme_options', null)));", "assert_true(post_type_exists('page'));"]),
        ("03", "Section Manager با ترتیب، وضعیت و فیلدهای تکرارشونده", ["inc/Admin/SectionManager.php"], ["2.3"],
         "رجیستری سکشن‌ها و متادیتای هر برگه؛ جابه‌جایی قابل دسترس با صفحه‌کلید علاوه بر drag/drop؛ media/link/icon/repeater.",
         ["ترتیب رندر دقیقاً برابر ترتیب ذخیره‌شده باشد.", "سکشن غیرفعال رندر نشود.", "فیلدهای repeater بدون گم‌شدن آیتم ذخیره شوند.", "شناسه سکشن ناشناخته و داده مخرب رد شود.", "پیش‌فرض خانه تمام ۱۵ سکشن مرجع را پوشش دهد."],
         ["assert_true(class_exists('ErfanSanat\\Theme\\Admin\\SectionManager'));", "assert_true(post_type_exists('page'));"]),
    ]),
    ("03", "cpt-and-legacy-meta", "داده‌های بومی و سازگاری متای قدیمی", [
        ("01", "ثبت projects، services، تکسونومی‌ها و Rewriteها", ["inc/ContentTypes.php", "inc/Rewrites.php"], ["3.1"],
         "ثبت projects/project_type/city طبق بلوپرینت؛ services مستقل با smartelement به‌عنوان آیتم؛ حفظ product_cat و product-tag و ستون‌های ادمین.",
         ["آرشیو و تک‌پروژه و تکسونومی‌ها بدون 404 باشند.", "services و تک‌خدمت‌ها قابل مشاهده باشند؛ smartelement یکی از خدمات باشد.", "اسلاگ‌های حیاتی و with_front مطابق بلوپرینت باشند.", "aliasها فقط پس از audit مسیر فعال شوند.", "فیلتر شهر/نوع و ستون same_key_hrefl کار کند."],
         ["assert_true(post_type_exists('projects'));", "assert_true(post_type_exists('services'));", "assert_true(taxonomy_exists('project_type'));", "assert_true(taxonomy_exists('city'));", "assert_true(taxonomy_exists('product_cat'));"]),
        ("02", "متاباکس کامل محصول و Round-trip متاهای JetEngine", ["inc/Admin/ProductMetaboxes.php", "inc/Data/LegacyMeta.php"], ["3.2"],
         "پشتیبانی همه کلیدهای محصول، گالری fallback، روابط و repeaterها؛ تشخیص فرمت خام و حفظ کلیدهای دارای غلط املای تاریخی.",
         ["همه کلیدهای محصول در DATA_BLUEPRINT پوشش داده شوند.", "JSON، آرایه سریالایز‌شده و مقدار خالی round-trip شوند.", "گالری از کلیدهای قدیمی fallback بخواند.", "محصول استعلامی و قابل خرید درست تفکیک شوند.", "Quick Edit با nonce/capability و sanitization امن باشد."],
         ["assert_true(class_exists('ErfanSanat\\Theme\\Admin\\ProductMetaboxes'));", "assert_true(post_type_exists('product'));"]),
        ("03", "متای پروژه، ترم، ترجمه و بازدید", ["inc/Admin/ProjectMetaboxes.php", "inc/Admin/TermMetaboxes.php", "inc/Admin/ContentMetaboxes.php"], ["3.3"],
         "متاباکس‌های بومی برای گالری پروژه، تاریخ و روابط؛ FAQ/تصویر دسته؛ hreflang و شمارنده بازدید با fallback.",
         ["خواندن/نوشتن تمام کلیدهای پروژه و ترم پاس شود.", "foure-image و mortabet_mahsole_2 دقیقاً حفظ شوند.", "hrefmeta_fa/en/ar روی محتوا و ترم کار کند.", "number2-viewc با fallback عددی از number-viewc خوانده شود.", "داده سایر افزونه‌ها هنگام ذخیره حذف نشود."],
         ["assert_true(class_exists('ErfanSanat\\Theme\\Admin\\ProjectMetaboxes'));", "assert_true(class_exists('ErfanSanat\\Theme\\Admin\\TermMetaboxes'));"]),
    ]),
    ("04", "header-footer-navigation", "هدر، فوتر و ناوبری", [
        ("01", "برش هدر و فوتر با حفظ DOM", ["header.php", "footer.php"], ["4.1"],
         "انتقال عین ساختار مرجع و اتصال تنظیمات؛ هوک‌های استاندارد وردپرس و جلوگیری از تکرار canonical/hreflang.",
         ["کلاس‌ها، IDها و data-attributeهای ساختاری حفظ شوند.", "wp_head/wp_body_open/wp_footer فراخوانی شوند.", "لوگو، تماس و لینک‌ها از تنظیمات خوانده شوند.", "تکرار ID خانه با تصمیم مستند و مقایسه DOM حل شود."],
         ["assert_true(file_exists(get_template_directory() . '/header.php'));", "assert_true(file_exists(get_template_directory() . '/footer.php'));"]),
        ("02", "Walkerهای مگامنو، موبایل، تولیدات و لندینگ", ["inc/Navigation/", "template-parts/navigation/"], ["4.2"],
         "چهار محل منوی وردپرس با ساختار تب‌ها و زیرمنوهای مرجع؛ داده واقعی ترم‌ها و حالت صفحه‌کلید/موبایل.",
         ["چهار menu location ثبت شوند.", "مارک‌آپ walker با مرجع مقایسه شود.", "مگامنو، آکاردئون موبایل و focus/escape کار کنند.", "منوی خالی باعث Fatal یا لینک ساختگی نشود."],
         ["assert_true(has_nav_menu('main-mega-menu') || array_key_exists('main-mega-menu', get_registered_nav_menus()));", "assert_true(array_key_exists('mobile-menu', get_registered_nav_menus()));", "assert_true(array_key_exists('tolidat-sherkat', get_registered_nav_menus()));", "assert_true(array_key_exists('landing', get_registered_nav_menus()));"]),
    ]),
    ("05", "homepage-and-sections", "صفحه اصلی و کارت‌های مشترک", [
        ("01", "پانزده سکشن خانه و رندر ترتیبی", ["front-page.php", "template-parts/home/"], ["5.1"],
         "برش سکشن‌های S01 تا S15 مطابق STATIC_TEMPLATE_MAP و اتصال به SectionManager بدون طراحی مجدد.",
         ["در پیش‌فرض، ترتیب و DOM سکشن‌ها با مرجع برابر باشد.", "تغییر ترتیب و خاموش‌کردن سکشن در خروجی اعمال شود.", "هیچ سکشن مرجع حذف یا با placeholder عوض نشود."],
         ["assert_true(file_exists(get_template_directory() . '/front-page.php'));", "assert_true(count(glob(get_template_directory() . '/template-parts/home/section-*.php')) >= 15);"]),
        ("02", "کوئری‌های واقعی و کارت‌های محصول/پروژه/مقاله", ["template-parts/cards/", "inc/Queries.php"], ["5.2"],
         "اتصال دسته‌ها، محصولات منتخب، پروژه‌ها، مقالات، آمار و مشتریان به داده و تنظیمات؛ کنترل کوئری‌های خالی.",
         ["محصول/پروژه/مقاله منتشرشده آزمایشی در کارت درست دیده شود.", "قیمت و تصویر از داده واقعی خوانده شوند، نه عدد نمونه JS.", "کارت‌ها مشترک و خروجی escape شده باشد.", "تعداد کوئری‌ها و نبود N+1 ثبت و بررسی شود."],
         ["assert_true(file_exists(get_template_directory() . '/template-parts/cards/card-product.php'));", "assert_true(file_exists(get_template_directory() . '/template-parts/cards/card-project.php'));", "assert_true(file_exists(get_template_directory() . '/template-parts/cards/card-post.php'));"]),
    ]),
    ("06", "woocommerce-and-products", "محصولات و چرخه خرید", [
        ("01", "تک‌محصول، گالری، مشخصات، روابط و حالت فروش", ["single-product.php", "woocommerce/", "template-parts/product/"], ["6.1"],
         "تبدیل مرجع تک‌محصول و اتصال تمام متا/ویژگی‌ها؛ محصول ساده، متغیر، استعلامی و ناموجود؛ نظرات و محصولات مرتبط.",
         ["محصول ساده و متغیر با هوک‌های WooCommerce خریداری شوند.", "true_sale=false دکمه تماس/استعلام داشته باشد، نه افزودن به سبد.", "گالری و fallbackها درست باشند.", "۱۸ ویژگی، شناسه‌ها، کاتالوگ، FAQ و روابط رندر شوند.", "فیلد خالی سکشن خالی تولید نکند."],
         ["assert_true(function_exists('wc_get_product'));", "assert_true(file_exists(get_template_directory() . '/single-product.php'));"]),
        ("02", "آرشیو فروشگاه و دسته/برچسب با صفحه‌بندی", ["archive-product.php", "taxonomy-product_cat.php", "taxonomy-product_tag.php"], ["6.2"],
         "کارت‌های مرجع، توضیحات و FAQ ترم، صفحه‌بندی و initial filter صحیح؛ حفظ مسیرهای سئو.",
         ["shop و product_cat و product-tag پاسخ 200 بدهند.", "محصول درست در دسته و صفحه مربوطه باشد.", "FAQ/image ترم از کلیدهای موجود خوانده شود.", "صفحه‌بندی و حالت بدون نتیجه کار کند."],
         ["assert_true(file_exists(get_template_directory() . '/archive-product.php'));", "assert_true(file_exists(get_template_directory() . '/taxonomy-product_cat.php'));", "assert_true(file_exists(get_template_directory() . '/taxonomy-product_tag.php'));"]),
        ("03", "سبد، پرداخت و حساب کاربری سازگار با افزونه‌ها", ["woocommerce/cart/", "woocommerce/checkout/", "assets/css/"], ["6.3"],
         "پوسته‌دهی مرجع با حفظ shortcode/block checkout در صورت استفاده؛ سازگایی ورود پیامکی، پنل کاربری، سواچ و quantity.",
         ["افزودن/حذف/تغییر تعداد و محاسبه جمع کل صحیح باشد.", "سفارش آزمایشی با روش پرداخت تستی ثبت شود؛ رسید جعلی تولید نشود.", "حساب کاربری و ورود/خروج بدون تداخل کار کند.", "درگاه واقعی فقط در استیجینگ مجاز آزمایش شود."],
         ["assert_true(function_exists('WC'));", "assert_true(wc_get_page_id('cart') > 0);", "assert_true(wc_get_page_id('checkout') > 0);", "assert_true(wc_get_page_id('myaccount') > 0);"]),
    ]),
    ("07", "projects-blog-and-pages", "پروژه‌ها، وبلاگ، خدمات و برگه‌ها", [
        ("01", "تک‌پروژه و آرشیوهای پروژه/شهر/نوع", ["single-projects.php", "archive-projects.php", "taxonomy-project_type.php", "taxonomy-city.php"], ["7.1"],
         "گالری شش‌تصویری، تاریخ تکمیل، شهر/نوع و محصولات استفاده‌شده؛ برگزیده‌ها در برگه project جدا از آرشیو.",
         ["تمام جزئیات پروژه و روابط در fixture رندر شوند.", "آرشیو شهر و نوع فقط محتوای همان ترم را نشان دهند.", "گالری و لایت‌باکس با data-attribute مرجع کار کنند.", "برگه project و آرشیو projects تداخل نداشته باشند."],
         ["assert_true(file_exists(get_template_directory() . '/single-projects.php'));", "assert_true(file_exists(get_template_directory() . '/archive-projects.php'));", "assert_true(file_exists(get_template_directory() . '/taxonomy-city.php'));", "assert_true(file_exists(get_template_directory() . '/taxonomy-project_type.php'));"]),
        ("02", "وبلاگ، تک‌نوشته، جستجو و 404", ["home.php", "archive.php", "category.php", "single.php", "search.php", "404.php"], ["7.2"],
         "حلقه‌های واقعی، TOC، اشتراک‌گذاری، مقالات مرتبط، دیدگاه و شمارش بازدید با سیاست کش/ربات.",
         ["آرشیو، دسته و تک‌نوشته مرجع درست رندر شوند.", "number2-viewc فقط در شرایط تعیین‌شده افزایش یابد.", "جستجو نتایج واقعی و escape شده بدهد.", "404 وضعیت درست و مسیر بازیابی داشته باشد."],
         ["assert_true(file_exists(get_template_directory() . '/single.php'));", "assert_true(file_exists(get_template_directory() . '/search.php'));", "assert_true(file_exists(get_template_directory() . '/404.php'));"]),
        ("03", "برگه‌های عمومی، آرشیو خدمات و لندینگ‌ها", ["page-templates/", "archive-services.php", "single-services.php"], ["7.3"],
         "درباره/تماس/مجوزها/FAQ/استخدام/گارانتی؛ services و smartelement به‌عنوان خدمت؛ آریا با مرجع تأییدشده؛ همه سکشن‌ها قابل مدیریت.",
         ["همه برگه‌های موجود قابل ویرایش و رندر باشند.", "آرشیو services و تک‌خدمت smartelement بدون Elementor کار کنند.", "برای آریا و ورود/حساب، مرجع یا تصمیم کاربر ثبت شود.", "متن/تصویر/تب/آکاردئون از پنل قابل تغییر باشد."],
         ["assert_true(file_exists(get_template_directory() . '/archive-services.php'));", "assert_true(file_exists(get_template_directory() . '/single-services.php'));", "assert_true(file_exists(get_template_directory() . '/page-templates/recruitment.php'));"]),
    ]),
    ("08", "ajax-filters-and-audit", "جستجو، فیلتر، فرم و ممیزی نهایی", [
        ("01", "جستجوی زنده و فیلترهای بومی محصولات/پروژه‌ها", ["inc/Ajax/", "assets/js/"], ["8.1"],
         "جستجو و فیلتر دسته، قیمت، true_sale، ویژگی‌ها، شهر/نوع و sort؛ URL قابل اشتراک و پاسخ امن بدون رفرش.",
         ["پاسخ AJAX روی استیجینگ هم‌مشخصات تولید کمتر از 200ms اندازه‌گیری شود.", "پارامتر مخرب/SQLi/XSS رد یا بی‌اثر شود.", "شمارنده‌ها و pagination با فیلتر هماهنگ باشند.", "پاک‌کردن فیلتر و دکمه بازگشت مرورگر کار کند.", "محصول خصوصی/پیش‌نویس لو نرود."],
         ["assert_true(class_exists('ErfanSanat\\Theme\\Ajax\\Search'));", "assert_true(class_exists('ErfanSanat\\Theme\\Ajax\\Filters'));"]),
        ("02", "فرم‌های امن و صندوق پیام‌های مدیریتی", ["inc/Forms/", "inc/Admin/Submissions.php"], ["8.2"],
         "تماس، استعلام، استخدام و بازخورد؛ ذخیره ورودی، nonce/honeypot/rate limit، ایمیل قابل تنظیم و رزومه خصوصی.",
         ["ارسال معتبر ذخیره و ایمیل آزمایشی ثبت شود.", "nonce/capability نامعتبر و فایل خطرناک رد شود.", "رزومه از URL عمومی قابل دریافت نباشد.", "مدیر مجاز پیام‌ها را ببیند و کاربر عادی نبیند.", "سیاست حذف/نگهداری و مقصد ایمیل تأیید شده باشد."],
         ["assert_true(class_exists('ErfanSanat\\Theme\\Forms\\FormManager'));", "assert_true(class_exists('ErfanSanat\\Theme\\Admin\\Submissions'));"]),
        ("03", "ممیزی سئو، امنیت، عملکرد و E2E", ["temp/", "tests/e2e/"], ["8.3"],
         "اجرای دوباره فازها، مقایسه بصری، SEO/Schema/hreflang، RTL/موبایل، امنیت خروجی و تست افزونه‌های باقی‌مانده.",
         ["تمام تست‌های فاز 1 تا 8 پاس شوند.", "WP_DEBUG بدون خطا/Warning قالب باشد.", "تداخل نداشتن SEOPress و hreflang و schema اثبات شود.", "گزارش عملکرد واقعی و محدودیت‌ها موجود باشد.", "هیچ ادعای آمادگی تولید بدون آزمون استیجینگ و backup ثبت نشود."],
         ["assert_true(file_exists(ABSPATH . 'wp-content/themes/' . get_template() . '/functions.php'));", "assert_true(defined('WP_DEBUG') && WP_DEBUG);"]),
    ]),
]

BOOTSTRAP = r'''<?php
declare(strict_types=1);
$root = dirname(__DIR__, 3);
$cache = getenv('ERFANSANAT_TOOL_CACHE') ?: getenv('HOME') . '/.cache/erfansanat-tools';
require_once $cache . '/phpunit-vendor/autoload.php';
$wp = getenv('ERFANSANAT_WP_ROOT') ?: $root . '/.local/wordpress';
if (!is_file($wp . '/wp-load.php')) {
    throw new RuntimeException('Local WordPress is not installed. Run tools/setup-wordpress.sh first.');
}
require_once $wp . '/wp-load.php';
'''

TEST = r'''<?php
declare(strict_types=1);
/** Task __TASK__: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class Task__CLASS__Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
__ASSERTIONS__
    }
}
'''


def assertion(line):
    if line.startswith('assert_true('):
        expression = line[len('assert_true('):-2]
        return '        $this->assertTrue(' + expression + ');'
    raise ValueError(line)


def main():
    plan = ["# نقشه اصلی اجرای قالب عرفان صنعت", "",
            "> Stage 1: برنامه و قرارداد تست آماده است؛ اجرای تبدیل قالب هنوز آغاز نشده است.",
            "> مرجع تغییرناپذیر: `static-mirror/`؛ داده و کلیدها: `DATA_BLUEPRINT.md`؛ وضعیت زنده: `PROJECT_MEMORY.md`.", "",
            "## تصمیم‌های قطعی کاربر", "",
            "- ظاهر/DOM از آرشیو؛ داده واقعی سایت مقدم بر محتوای نمایشی است.",
            "- مسیرهای بلوپرینت canonical؛ redirect مسیرهای همسان فقط پس از audit استیجینگ.",
            "- CPT مستقل `services`؛ `smartelement` یکی از خدمات آن است.",
            "- فعلاً وردپرس/ووکامرس محلی با داده ساختگی؛ بدون اتصال یا تغییر سایت زنده.", "",
            "## قاعده اجرا", "",
            "هر تسک به‌ترتیب وابستگی‌ها: خواندن حافظه ← برنامه فنی کوتاه ← تست ← پیاده‌سازی ← اجرای تست/اصلاح ← به‌روزرسانی حافظه و commit. پس از هر تسک یا فاز طبق دستور کاربر تأیید گرفته شود. تست‌های اولیه قرارداد حداقلی‌اند و هنگام اجرای همان تسک با حالت‌های مرزی ذکرشده تکمیل می‌شوند؛ پاس‌شدن آن‌ها به‌تنهایی جای تست مرورگر/استیجینگ را نمی‌گیرد.", "",
            "| فاز | تسک | شرح | وابستگی | وضعیت |", "|---|---|---|---|---|"]
    qa = ["# گزارش تصمیم‌ها، آزمون و اصلاحات", "",
          "این گزارش تصمیم‌های فنی قابل ممیزی و شواهد آزمون را نگه می‌دارد؛ زنجیره فکر خصوصی ثبت نمی‌شود.", "",
          "## 2026-11-03 — Stage 0", "",
          "- آرشیو ۱۲۸ صفحه‌ای استخراج و با بایت‌های ZIP مقایسه شد؛ ۸ تست کشف و ۲ تست ابزار پاس شدند.",
          "- ترتیب متن DOM و نسخه runtime PHP/PHPCS در ابزار کشف اصلاح شد؛ جزئیات در STAGE0_TEST_RESULTS.md.",
          "- کاربر مرجع ظاهر، تقدم داده زنده، canonical پس از audit، CPT خدمات و محیط تست محلی را تعیین کرد.", "",
          "## آزمون‌های در انتظار اجرای فازها", ""]
    count = 0
    previous_phase_last = None
    for number, slug, title, tasks in PHASES:
        directory = TEMP / f"phase-{number}-{slug}"
        (directory / "tasks").mkdir(parents=True, exist_ok=True)
        (directory / "tests").mkdir(parents=True, exist_ok=True)
        (directory / "tests/bootstrap.php").write_text(BOOTSTRAP)
        phase_id = int(number)
        for task_no, task_title, files, specs, summary, conditions, checks in tasks:
            count += 1
            task_id = f"{phase_id}.{int(task_no)}"
            dependencies = [f"{phase_id}.{int(task_no)-1}"] if task_no != '01' else ([previous_phase_last] if previous_phase_last else ["محیط آزمایشی"])
            task = [f"# Task {task_id}: {task_title}", "", "[PENDING]", "",
                    "## هدف و برنامه فنی", summary, "",
                    "## فایل‌های هدف", *[f"- `{file}`" for file in files], "",
                    "## وابستگی‌ها", *[f"- {dependency}" for dependency in dependencies], "",
                    "## مرجع و داده", "- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.",
                    "- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.", "",
                    "## شروط پاس شدن", *[f"- [ ] {condition}" for condition in conditions], "",
                    "## آزمون", f"- `temp/phase-{number}-{slug}/tests/test-task-{task_no}.php`",
                    "- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.",
                    "- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.", "",
                    "## خروج از تسک", "به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.", ""]
            (directory / "tasks" / f"task-{task_no}.md").write_text("\n".join(task))
            test = TEST.replace('__TASK__', task_id).replace('__CLASS__', f"P{number}T{task_no}").replace('__ASSERTIONS__', '\n'.join(assertion(x) for x in checks))
            (directory / "tests" / f"test-task-{task_no}.php").write_text(test)
            plan.append(f"| {phase_id} | {task_id} | {task_title} | {', '.join(dependencies)} | [PENDING] |")
            qa.append(f"- Task {task_id}: [PENDING] — هنوز اجرا نشده است.")
        previous_phase_last = f"{phase_id}.{len(tasks)}"
        runner = '#!/usr/bin/env bash\nset -euo pipefail\nROOT="$(cd "$(dirname "$0")/../../.." && pwd)"\ncd "$ROOT"\nfor test in "temp/phase-' + number + '-' + slug + '/tests"/test-task-*.php; do\n  tools/phpunit --do-not-cache-result --no-progress --colors=never "$test"\ndone\n'
        runner_path = directory / "tests" / f"run-phase-{number}-tests.sh"
        runner_path.write_text(runner)
        runner_path.chmod(0o755)
    plan += ["", f"مجموع: **۸ فاز و {count} تسک اتمیک**. هیچ تسک پیاده‌سازی در Stage 1 پاس‌شده علامت نمی‌خورد.", "",
             "## دروازه‌های باقی‌مانده", "",
             "- پیش از فاز مربوط: مرجع آریا/ورود و مهاجرت برگه smartelement به CPT خدمات تعیین تکلیف شود.",
             "- پیش از فرم‌ها: مقصد ایمیل، مجوز دسترسی و مدت نگهداری رزومه تعیین شود.",
             "- پیش از استقرار: خروجی واقعی JetEngine، URLهای زنده، افزونه‌های همکار و پرداخت در استیجینگ بررسی شوند.", ""]
    (TEMP / "MASTER_PLAN.md").write_text("\n".join(plan))
    (TEMP / "QA_REFLECTION_LOG.md").write_text("\n".join(qa) + "\n")
    print(f"Generated 8 phases, {count} task specifications, {count} PHP test contracts and 8 phase runners.")


if __name__ == '__main__':
    main()
