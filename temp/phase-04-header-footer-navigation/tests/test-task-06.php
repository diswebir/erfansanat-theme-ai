<?php
/**
 * Test 4.6 — فوتر: بازسازی byte-exact + سه منبع محتوایی (منو/fallback/آپشن) + wp_footer
 * اجرا: /home/user/tools/php -f temp/phase-04-header-footer-navigation/tests/test-task-06.php
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
echo "== Test 4.6: فوتر ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Nav\Footer;

$ref = (string) file_get_contents($root . '/temp/phase-04-header-footer-navigation/refs/footer-ref.html');

// ═══ ۱) بازسازی byte-exact با مقادیر میرور ═══
$R = new ReflectionClass(Footer::class);
$qli = (string) $R->getConstant('QLI');
$quick_inner = '';
preg_match_all('/<li><a href="[^"]+\.html"[^>]*>[^<]+<\/a><\/li>/', $ref, $quick_matches);
preg_match('/<div class="lg:col-span-2">.*?<ul class="space-y-2\.5 text-\[13px\] text-slate-600">(.*?)<\/ul>/s', $ref, $qm);
preg_match_all('/<li>.*?<\/li>/s', $qm[1] ?? '', $quick_items);
foreach ($quick_items[0] as $li) {
    preg_match('/href="([^"]+)"/', $li, $hm);
    preg_match('/>([^<]+)<\/a>/', $li, $lm);
    $quick_inner .= strtr($qli, ['{ES_HREF}' => $hm[1], '{ES_LABEL}' => $lm[1]]);
}
$cli = (string) $R->getConstant('CLI');
preg_match('/<div class="lg:col-span-3">.*?<ul class="space-y-2\.5 text-\[13px\] text-slate-600">(.*?)<\/ul>/s', $ref, $cm);
$cats_inner = '';
preg_match_all('/<li>.*?<\/li>/s', $cm[1] ?? '', $cat_items);
foreach ($cat_items[0] as $li) {
    preg_match('/href="([^"]+)"/', $li, $hm);
    preg_match('/>([^<]+)<\/a>/', $li, $lm);
    $cats_inner .= strtr($cli, ['{ES_HREF}' => $hm[1], '{ES_LABEL}' => $lm[1]]);
}
$recon = (string) $R->getConstant('F_OPEN')
    . (string) $R->getConstant('BRAND')
    . str_replace('{ES_QUICK_LINKS}', $quick_inner, (string) $R->getConstant('F_QUICK'))
    . str_replace('{ES_CAT_LINKS}', $cats_inner, (string) $R->getConstant('F_CATS'))
    . strtr((string) $R->getConstant('F_CONTACT'), [
        '{ES_ADDRESS}'       => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹',
        '{ES_TEL_URL}'       => 'tel:031-91091011',
        '{ES_PHONE_DISPLAY}' => '۰۳۱ - ۹۱۰۹۱۰۱۱ (۱۰ خط ویژه)',
        '{ES_SALES_URL}'     => 'tel:09137976915',
        '{ES_SALES_DISPLAY}' => 'مدیر فروش (آقای محمدعلی قربانی): ۰۹۱۳۷۹۷۶۹۱۵',
        '{ES_MAIL_URL}'      => 'mailto:info@erfansanat.com',
        '{ES_EMAIL}'         => 'info@erfansanat.com',
        '{ES_HOURS}'         => 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر',
    ])
    . str_replace('{ES_COPYRIGHT}', '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.', (string) $R->getConstant('F_BAR'));
check($recon === $ref, 'بازسازی byte-exact فوتر کامل (فرگمنت+مقادیر میرور=مرجع)');

// ═══ ۲) رندر داینامیک پیش‌فرض (بدون منو، آپشن‌های پیش‌فرض) ═══
$GLOBALS['__wp_options'] = [];
es_option_reset_cache();
$GLOBALS['__es_stub_terms'] = [
    'product_cat' => [
        (object) ['term_id' => 1, 'name' => 'پوینت لایت', 'slug' => 'point-light', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 2, 'name' => 'ریسه LED', 'slug' => 'strings', 'taxonomy' => 'product_cat', 'parent' => 0],
    ],
];
$GLOBALS['__es_stub_nav_items'] = null;

ob_start();
Footer::output();
$html = (string) ob_get_clean();

check(str_starts_with($html, '<footer class="relative overflow-hidden border-t border-emerald-100 bg-white">'), 'تگ footer میرور');
check(!str_contains($html, '{ES_'), 'بدون placeholder جامانده');
check(substr_count($html, 'href="' . home_url('/') . 'shop/"') >= 1, 'quick_links آپشن → لینک فروشگاه');
check(str_contains($html, 'پروژه‌های اجراشده'), 'quick_links — لیبل آپشن');
check(str_contains($html, 'href="' . home_url('/') . 'product_cat/point-light/"'), 'دسته‌بندی از product_cat (fallback)');
check(str_contains($html, 'href="tel:031-91091011"'), 'تلفن از contact_args');
check(str_contains($html, '۰۳۱ - ۹۱۰۹۱۰۱۱ (۱۰ خط ویژه)'), 'نمایش تلفن میرور');
check(str_contains($html, 'info@erfansanat.com'), 'ایمیل');
check(str_contains($html, 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر'), 'ساعات کاری');
check(str_contains($html, 'بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹'), 'آدرس fallback میرور');
check(str_contains($html, '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.'), 'کپی‌رایت fallback میرور');
check(str_contains($html, 'data-scroll-top="true"'), 'دکمه scroll-top (hook)');
check(str_contains($html, 'ISO 9001:2015') && str_contains($html, 'کد بین‌المللی GS1'), 'چیپ‌های گواهی ستون برند (ثابت میرور)');
check(!str_contains($html, '.html"'), 'بدون لینک .html');

// ═══ ۳) منوی وردپرس اولویت دارد ═══
$GLOBALS['__es_stub_nav_items'] = [
    (object) ['db_id' => 1, 'menu_item_parent' => 0, 'title' => 'لینک منویی فوتر', 'url' => home_url('/custom-link/'), 'classes' => []],
];
ob_start();
Footer::output();
$html2 = (string) ob_get_clean();
check(str_contains($html2, 'لینک منویی فوتر'), 'منوی footer → اولویت بر آپشن');
check(!str_contains($html2, 'پروژه‌های اجراشده'), 'آپشن در حالت منو نمایش داده نمی‌شود');

// ═══ ۴) footer.php کامل ═══
$GLOBALS['__es_stub_nav_items'] = null;
$GLOBALS['__wp_options'] = [];
es_option_reset_cache();
ob_start();
include $root . '/footer.php';
$full = (string) ob_get_clean();
check(str_contains($full, 'wp-footer-marker'), 'wp_footer اجرا شد');
check(str_ends_with(trim($full), '</body>' . "\n" . '</html>'), 'بستن body/html');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
