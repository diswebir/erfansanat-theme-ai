<?php
/**
 * Test 6.4 — پنل desc (features/feature2/notice/usages) + FAQ + کاتالوگ — بایت‌دقیق مقابل میرور
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-04.php
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
echo "== Test 6.4: single-product features/usages/faq/catalog ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

/* ---------- استخراج فیکسچر از خود میرور (ZWNJ تضمینی) ---------- */
$mirror = (string) file_get_contents($root . '/uploads/static-mirror/product/wall-washer.html');
$dp = strpos($mirror, '<div data-prod-tab-panel="desc"');
$sp = strpos($mirror, '<div data-prod-tab-panel="specs"');
$DESC_REF = substr($mirror, $dp, $sp - $dp);
$TITLE = 'والواشر RGB و تک‌رنگ صنعتی (IP68)';
// محتوا
$c0 = strpos($DESC_REF, '<div class="mt-4 space-y-4');
$c1 = strpos($DESC_REF, '</div>', strpos($DESC_REF, '</p>', $c0)) + 6;
$CONTENT = substr($DESC_REF, $c0, $c1 - $c0);
// چهار ویژگی
preg_match_all('/<span>([^<]+)<\/span><\/div><div class="flex items-start gap-2\.5 rounded-xl/', $DESC_REF, $fm);
$FEATURES = $fm[1]; // ۴ ویژگی (آخرین بدون دنباله — جداگانه)
preg_match('/<span>پوشش محافظ پیشرفته IP68<\/span>/', $DESC_REF, $last);
$FEATURES = ['ابعاد سفارشی بر اساس پروژه', 'پخش نور یکنواخت با لنز اپتیکی', 'مصرف بهینه انرژی', 'پوشش محافظ پیشرفته IP68'];
// متن اطلاعیه
preg_match('/text-amber-900\/90">([^<]+)<\/p>/', $DESC_REF, $nm);
$NOTICE_TEXT = $nm[1];
// چیپ‌ها و ترم‌ها
preg_match_all('/<span class="rounded-xl border border-emerald-200\/80 bg-white px-3\.5 py-2 text-xs font-bold text-emerald-950 shadow-2xs">([^<]+)<\/span>/', $DESC_REF, $cm);
$CHIPS = $cm[1];
preg_match_all('/<a href="\.\.\/project_type\/([^"]+)"[^>]*><span>([^<]+)<\/span>/', $DESC_REF, $lm);
$TERM_SLUGS = array_map(static fn(string $x): string => preg_replace('/\.html$/', '', $x), $lm[1]);
$TERM_NAMES = $lm[2];
// FAQ
preg_match('/<h2 class="text-lg font-extrabold text-slate-900">سوالات متداول درباره ([^<]+)<\/h2>/', $mirror, $fh);
$fs = strpos($mirror, '<section class="mx-auto max-w-4xl px-5 pb-14 lg:px-8">');
$fe = strpos($mirror, '</section>', $fs) + 10;
$FAQ_REF = substr($mirror, $fs, $fe - $fs);
$QA_PAIRS = [];
foreach (array_slice(explode('<div class="py-3.5 first:pt-0 last:pb-0">', $FAQ_REF), 1) as $faq_chunk) {
    if (preg_match('/<span class="text-sm font-extrabold text-slate-800">([^<]+)<\/span>/', $faq_chunk, $qq)
        && preg_match('/<p class="text-\[13\.5px\] leading-7 text-slate-600">([^<]+)<\/p>/', $faq_chunk, $aa)) {
        $QA_PAIRS[] = ['q' => $qq[1], 'a' => $aa[1]];
    }
}
check(count($QA_PAIRS) === 4, 'استخراج فیکسچر: ۴ جفت سوال/پاسخ از میرور');

/* ---------- فیکسچر محصول 900 ---------- */
$GLOBALS['__es_stub_titles'] = [900 => $TITLE, 905 => $TITLE];
$GLOBALS['__es_stub_posts'] = [(object) ['ID' => 900, 'post_content' => $CONTENT]];
$rep = static function (array $rows): string {
    $out = [];
    foreach ($rows as $i => $r) { $out['item-' . $i] = $r; }
    return json_encode($out, JSON_UNESCAPED_UNICODE);
};
$GLOBALS['__wp_post_meta'] = [
    900 => [
        'english_titl' => 'Industrial RGB & Mono LED Wall Washer IP68',
        'product-feature' => $rep([
            ['add-new-feature' => $FEATURES[0]], ['add-new-feature' => $FEATURES[1]],
            ['add-new-feature' => $FEATURES[2]], ['add-new-feature' => $FEATURES[3]],
        ]),
        'before-sale-c' => $NOTICE_TEXT,
        'usage_product' => $rep([['add-new-usage' => $CHIPS[0]], ['add-new-usage' => $CHIPS[1]], ['add-new-usage' => $CHIPS[2]]]),
        'usage_countr' => ['item-0' => ['id' => 31], 'item-1' => ['id' => 32], 'item-2' => ['id' => 33]],
        'faq' => $rep(array_map(static fn(array $p): array => ['name_question' => $p['q'], 'answer_question' => $p['a']], $QA_PAIRS)),
    ],
];
$GLOBALS['__es_stub_terms'] = [
    31 => (object) ['term_id' => 31, 'name' => $TERM_NAMES[0], 'slug' => $TERM_SLUGS[0], 'taxonomy' => 'project_type'],
    32 => (object) ['term_id' => 32, 'name' => $TERM_NAMES[1], 'slug' => $TERM_SLUGS[1], 'taxonomy' => 'project_type'],
    33 => (object) ['term_id' => 33, 'name' => $TERM_NAMES[2], 'slug' => $TERM_SLUGS[2], 'taxonomy' => 'project_type'],
];

/* ---------- ۱) بایت‌دقیق: پنل desc کامل ---------- */
ob_start();
es_get_template('template-parts/single-product/features.php', ['pid' => 900]);
$desc = ob_get_clean();
foreach ($TERM_SLUGS as $ts) {
    $DESC_REF = str_replace('../project_type/' . $ts . '.html', 'https://test.example/project_type/' . $ts . '/', $DESC_REF);
}
check($desc === $DESC_REF, 'بایت‌دقیق: پنل desc = برش میرور (معرفی/ویژگی‌ها/اطلاعیه/کاربردها/لینک ترم‌ها)');

/* ---------- ۲) بایت‌دقیق: FAQ ---------- */
ob_start();
es_get_template('template-parts/single-product/faq.php', ['pid' => 900]);
$faq = ob_get_clean();
check($faq === $FAQ_REF, 'بایت‌دقیق: سکشن FAQ = برش میرور (آکاردئون data-faq-*)');
check(str_contains($faq, 'bg-emerald-800 text-white rotate-45') && str_contains($faq, 'data-faq-body="0" class="block pt-2 pb-1"'), 'FAQ: آیتم اول باز، آیکون چرخیده');

/* ---------- ۳) ادغام usages بدون تکرار ---------- */
$GLOBALS['__wp_post_meta'][900]['usage_product'] = $rep([
    ['add-new-usage' => $TERM_NAMES[0]],         // همنام ترم ۳۱ → باید حذف شود
    ['add-new-usage' => 'کاربرد جدید تستی'],     // جدید → چیپ می‌شود
]);
ob_start();
es_get_template('template-parts/single-product/usages.php', ['pid' => 900]);
$usg = ob_get_clean();
check(substr_count($usg, 'rounded-xl border border-emerald-200/80 bg-white px-3.5 py-2 text-xs font-bold') === 1, 'ادغام: چیپ همنام ترم حذف شد (فقط «کاربرد جدید تستی»)');
check(substr_count($usg, '<a href=') === 3, 'سه لینک ترم سرجایشان');
unset($GLOBALS['__wp_post_meta'][900]['usage_product'], $GLOBALS['__wp_post_meta'][900]['usage_countr']);
ob_start();
es_get_template('template-parts/single-product/usages.php', ['pid' => 900]);
$usg_empty = ob_get_clean();
check($usg_empty === '', 'بدون چیپ/ترم → کاربردها مخفی');
$GLOBALS['__wp_post_meta'][900]['usage_product'] = $rep([['add-new-usage' => $CHIPS[0]], ['add-new-usage' => $CHIPS[1]], ['add-new-usage' => $CHIPS[2]]]);
$GLOBALS['__wp_post_meta'][900]['usage_countr'] = ['item-0' => ['id' => 31], 'item-1' => ['id' => 32], 'item-2' => ['id' => 33]];

/* ---------- ۴) notice تنها: مخفی/نمایان ---------- */
$GLOBALS['__wp_post_meta'][910] = ['before-sale-c' => 'نکته تستی قبل از خرید'];
$GLOBALS['__es_stub_titles'][910] = 'تستی';
ob_start();
es_get_template('template-parts/single-product/notice.php', ['pid' => 910]);
$n1 = ob_get_clean();
check(str_contains($n1, 'text-amber-900/90') && str_contains($n1, 'نکته تستی قبل از خرید'), 'اطلاعیه مقدار-دار → جعبه آمبر میرور');
$GLOBALS['__wp_post_meta'][911] = [];
ob_start();
es_get_template('template-parts/single-product/notice.php', ['pid' => 911]);
check(ob_get_clean() === '', 'before-sale-c خالی → مخفی');

/* ---------- ۵) feature2 دو بخشی (الگوی خواهر میرور) ---------- */
$GLOBALS['__wp_post_meta'][912] = [
    'product-feature2_copy' => $rep([
        ['add-new-feature' => 'گارانتی', 'vaule_of_variable' => '۲۴ ماهه'],
        ['add-new-feature' => 'خدمات', 'vaule_of_variable' => '۵ ساله'],
    ]),
];
$GLOBALS['__es_stub_titles'][912] = 'تستی f2';
ob_start();
es_get_template('template-parts/single-product/features.php', ['pid' => 912]);
$f2 = ob_get_clean();
check(str_contains($f2, 'مشخصات کلیدی در یک نگاه') && str_contains($f2, '۲۴ ماهه') && str_contains($f2, '۵ ساله'), 'feature2: دو جفت عنوان/مقدار رندر شد');
check(str_contains($f2, 'text-[11px] font-bold text-slate-500">گارانتی</div>'), 'feature2: عنوان با استایل لیبل میرور');

/* ---------- ۶) خالی کامل → پنل رندر نشود ---------- */
$GLOBALS['__wp_post_meta'][913] = [];
$GLOBALS['__es_stub_titles'][913] = 'خالی';
ob_start();
es_get_template('template-parts/single-product/features.php', ['pid' => 913]);
check(ob_get_clean() === '', 'بدون content/features/f2/notice → پنل desc مخفی');

/* ---------- ۷) کاتالوگ: تب docs فقط با فایل ---------- */
$GLOBALS['__es_stub_attachment_urls'] = [55 => 'https://test.example/wp-content/uploads/catalog-wall-washer.pdf'];
$GLOBALS['__wp_post_meta'][905] = ['product-catalog-file' => '55', 'gs1_id' => '6260491807290'];
ob_start();
es_get_template('template-parts/single-product/specs-tabs.php', ['pid' => 905]);
$with_docs = ob_get_clean();
check(str_contains($with_docs, 'data-prod-tab-btn="docs"'), 'کاتالوگ: دکمه تب docs رندر شد');
check(str_contains($with_docs, 'href="https://test.example/wp-content/uploads/catalog-wall-washer.pdf" target="_blank" rel="noopener"'), 'کاتالوگ: لینک فایل با target=_blank');
check(str_contains($with_docs, 'data-prod-tab-panel="docs"') && str_contains($with_docs, 'فایل‌های فنی، کاتالوگ و نقشه‌های اجرایی'), 'کاتالوگ: پنل docs رندر شد');
check(str_contains($with_docs, 'کاتالوگ فنی ' . $TITLE), 'کاتالوگ: عنوان کارت از نام محصول');
$GLOBALS['__wp_post_meta'][914] = ['gs1_id' => '6260000000000'];
$GLOBALS['__es_stub_titles'][914] = 'بدون فایل';
ob_start();
es_get_template('template-parts/single-product/specs-tabs.php', ['pid' => 914]);
$no_docs = ob_get_clean();
check(!str_contains($no_docs, 'data-prod-tab-btn="docs"'), 'بدون فایل کاتالوگ → تب docs غایب');

/* ---------- ۸) فیلتر es_product_features_args ---------- */
add_filter('es_product_features_args', static function (array $data, int $pid): array {
    if ($pid === 915) {
        $data['features'] = ['ویژگی فیلترشده'];
        $data['content'] = '';
        $data['notice'] = '';
    }
    return $data;
}, 10, 2);
$GLOBALS['__wp_post_meta'][915] = [];
$GLOBALS['__es_stub_titles'][915] = 'فیلتری';
ob_start();
es_get_template('template-parts/single-product/features.php', ['pid' => 915]);
$filtered = ob_get_clean();
check(str_contains($filtered, 'ویژگی فیلترشده') && !str_contains($filtered, 'فضای کاربردها'), 'فیلتر es_product_features_args: فقط ویژگی تزریقی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
