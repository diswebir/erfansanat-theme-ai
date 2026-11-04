<?php
/**
 * Test 4.3 — بلوک هدر (header-top.php): برش میرور + جایگزینی داینامیک لوگو/CTA/badge/نوار اعلان
 * اجرا: /home/user/tools/php -f temp/phase-04-header-footer-navigation/tests/test-task-03.php
 */
declare(strict_types=1);

error_reporting(E_ALL);
$failures = [];
$passes   = 0;
function check(bool $cond, string $label): void {
    global $failures, $passes;
    if ($cond) { $passes++; echo "  PASS  $label\n"; }
    else       { $failures[] = $label; echo "  FAIL  $label\n"; }
}

$root = dirname(ES_SCRIPT_PATH, 4);
echo "== Test 4.3: بلوک هدر ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

es_option_reset_cache();
$GLOBALS['__wp_options'] = [];
$GLOBALS['__es_stub_blogname'] = 'عرفان صنعت اصفهان';

// ── رندر پیش‌فرض (بدون آپشن/لوگو) ──
ob_start();
es_get_template('template-parts/header/header-top.php');
$out = (string) ob_get_clean();

check(str_contains($out, '<header data-navbar-header="true"'), 'تگ header با data-navbar-header');
check(str_contains($out, 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)'), 'نوار بالایی — برند میرور');
check(substr_count($out, 'licenses-and-honors') === 1 || str_contains($out, 'مجوزها و افتخارات'), 'لینک‌های نوار بالا');
check(str_contains($out, 'href="' . es_page_url('licenses-and-honors') . '"'), 'لینک نوار بالا → es_page_url');
$html_free = preg_replace('/<div data-search-modal="true".*?<\/header>/s', '</header>', $out);
check(!str_contains((string) $html_free, '.html"'), 'هیچ لینک .html خارج از اسکلت مودال جستجو (پرشدن = فاز ۸)');
check(str_contains($out, 'placeholder میرور ESE') === false && str_contains($out, '>ESE</div>'), 'fallback لوگوی متنی ESE');
check(str_contains($out, 'عرفان صنعت اصفهان'), 'نام برند لوگو');
check(str_contains($out, 'data-search-modal-open="true"'), 'دکمه جستجو (hook)');
check(str_contains($out, 'data-cart-modal-open="true"'), 'دکمه سبد (hook)');
check(str_contains($out, 'data-header-cart-badge="true"'), 'badge سبد (hook)');
check(str_contains($out, '>۲</span>'), 'badge پیش‌فرض میرور (۲)');
check(str_contains($out, 'استعلام قیمت'), 'متن CTA پیش‌فرض میرور');
check(str_contains($out, 'data-mobile-toggle="true"'), 'دکمه منوی موبایل');
check(str_contains($out, 'data-search-modal="true"') && str_contains($out, 'data-global-search-input="true"'), 'مودال جستجو + اینپوت');
check(str_contains($out, 'data-global-search-products="true"') && str_contains($out, 'data-global-search-projects="true"'), 'کانتینرهای نتایج جستجو');
check(!str_contains($out, 'data-announcement-bar'), 'نوار اعلان پیش‌فرض خاموش');

// ── MegaMenu داخل nav ──
check(str_contains($out, '<nav aria-label="منوی اصلی"'), 'nav دسکتاپ میرور');

// ── حالت: آپشن CTA + نوار اعلان روشن ──
$GLOBALS['__wp_options']['erfansanat_theme_options'] = [
    'header' => [
        'cta_text'    => 'درخواست مشاوره',
        'cta_url'     => 'https://erfansanat.com/contact/?consult=1',
        'ann_enabled' => 1,
        'ann_text'    => 'ارسال رایگان برای پروژه‌های شهری',
        'ann_url'     => 'https://erfansanat.com/contact/',
        'ann_bg'      => '#065f46',
    ],
];
es_option_reset_cache();
ob_start();
es_get_template('template-parts/header/header-top.php');
$out2 = (string) ob_get_clean();
check(str_contains($out2, 'درخواست مشاوره'), 'متن CTA از آپشن');
check(str_contains($out2, 'data-announcement-bar="true"'), 'نوار اعلان فعال');
check(str_contains($out2, 'ارسال رایگان برای پروژه‌های شهری'), 'متن نوار اعلان');
check(str_contains($out2, 'bg-[#065f46]'), 'رنگ نوار اعلان از آپشن');

// ── لوگوی سفارشی ──
$GLOBALS['__wp_theme_mods']['custom_logo'] = 55;
$GLOBALS['__es_stub_attachment_urls'][55] = 'http://example.org/wp-content/uploads/logo.png';
ob_start();
es_get_template('template-parts/header/header-top.php');
$out3 = (string) ob_get_clean();
check(str_contains($out3, '<img src="http://example.org/wp-content/uploads/logo.png"'), 'لوگوی custom-logo');
check(!str_contains($out3, '>ESE</div>'), 'fallback متنی در حالت لوگو حذف');
unset($GLOBALS['__wp_theme_mods']['custom_logo']);

// ── badge ووکامرس ──
$GLOBALS['__es_stub_wc'] = new class {
    public function get_cart_contents_count(): int { return 12; }
};
ob_start();
echo es_cart_badge();
check((string) ob_get_clean() === '۱۲', 'badge سبد = تعداد WC با ارقام فارسی');
unset($GLOBALS['__es_stub_wc']);

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
