<?php
/**
 * Test 6.3 — تب‌های محصول + جدول pa_* + شناسه‌های قانونی (بایت‌دقیق مقابل میرور) + تب‌های خالی
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-03.php
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
echo "== Test 6.3: single-product specs tabs ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

/* ---------- فیکسچر: محصول والواشر مطابق میرور ---------- */
$TITLE = 'والواشر RGB و تک‌رنگ صنعتی (IP68)';
$GLOBALS['__es_stub_titles'] = [900 => $TITLE];
$GLOBALS['__es_stub_comment_counts'] = [900 => 3];
$GLOBALS['__wp_post_meta'] = [
    900 => [
        '_product_attributes' => [
            'pa_led'      => ['label' => 'نوع LED', 'is_taxonomy' => 0, 'value' => 'پیکسل RGB پرقدرت فول‌کالر / پاور LED'],
            'pa_color'    => ['label' => 'تنوع رنگ', 'is_taxonomy' => 0, 'value' => 'بیش از ۱۶ میلیون رنگ و تک‌رنگ'],
            'pa_energy'   => ['label' => 'مصرف انرژی', 'is_taxonomy' => 0, 'value' => '۳۶ وات در هر متر'],
            'pa_body'     => ['label' => 'بدنه', 'is_taxonomy' => 0, 'value' => 'آلومینیوم اکسترود صنعتی + شیشه سکوریت'],
            'pa_ip'       => ['label' => 'درجه حفاظت', 'is_taxonomy' => 1],
            'pa_warranty' => ['label' => 'گارانتی', 'is_taxonomy' => 0, 'value' => '۲۴ ماه گارانتی + ۵ سال خدمات پس از فروش'],
            'pa_power'    => ['label' => 'توان مصرفی', 'is_taxonomy' => 1],
            'pa_voltage'  => ['label' => 'ولتاژ ورودی', 'is_taxonomy' => 1],
            'pa_material' => ['label' => 'جنس بدنه', 'is_taxonomy' => 0, 'value' => 'آلومینیوم اکسترود صنعتی'],
        ],
        'product_id'    => 'ESE-WW-036',
        'gs1_id'        => '6260491807290',
        'national_code' => '2901140800601',
        'hs_id'         => '94054290',
        'isic_id'       => '2740',
        'cpc_id'        => '46531',
    ],
];
$GLOBALS['__es_stub_post_term_map'] = [
    900 => [
        'pa_ip'      => [(object) ['name' => 'IP68']],
        'pa_power'   => [(object) ['name' => '۳۶ وات']],
        'pa_voltage' => [(object) ['name' => '۲۴V DC / ۲۲۰V AC']],
    ],
];

/* ---------- ۱) رندر ---------- */
ob_start();
es_get_template('template-parts/single-product/specs-tabs.php', ['pid' => 900]);
$render = ob_get_clean();

check(str_contains($render, '<section id="product-details" data-prod-tabs-container="true"'), 'کانتینر: section#product-details + data-prod-tabs-container');
check(str_contains($render, 'data-prod-tab-btn="specs"') && str_contains($render, 'data-prod-tab-icon="true"'), 'دکمه specs + آیکون');
check(substr_count($render, 'data-prod-tab-btn=') === 1, 'فقط تب specs رندر شد (بدون desc/reviews خالی)');
check(str_contains($render, 'bg-emerald-800 text-white shadow-sm'), 'تب اول = active (کلاس میرور)');
check(str_contains($render, 'data-prod-tab-panel="specs"'), 'پنل specs حاضر');
check(substr_count($render, 'جدول کامل مشخصات فنی و مهندسی') === 1 && str_contains($render, $TITLE), 'هدر جدول + عنوان محصول');

/* ---------- ۲) بایت‌دقیق (محدوده پنل specs — desc در ۶.۴ و reviews در ۶.۶) ---------- */
$mirror = (string) file_get_contents($root . '/uploads/static-mirror/product/wall-washer.html');
$sec = strpos($mirror, '<section id="product-details"');
$sp_panel = strpos($mirror, '<div data-prod-tab-panel="specs"', $sec);
$sp_end = strpos($mirror, '<div data-prod-tab-panel="reviews"', $sec);
check($sec > 0 && $sp_end > $sp_panel && $sp_panel > $sec, 'مرجع: برش سکشن یافت شد');
$ref = substr($mirror, $sp_panel, $sp_end - $sp_panel) . '</div></div></section>';
$cut = strpos($render, '<div data-prod-tab-panel="specs"');
check($cut > 0 && substr($render, $cut) === $ref, 'بایت‌دقیق: پنل specs رندر = برش میرور (۹ ردیف + ۶ شناسه)');

/* ---------- ۳) اتریبیوت خالی در جدول نمی‌آید ---------- */
$GLOBALS['__es_stub_titles'][901] = 'محصول کم‌مشخصه';
$GLOBALS['__wp_post_meta'][901] = [
    '_product_attributes' => [
        'pa_empty_tax' => ['label' => 'بدون ترم', 'is_taxonomy' => 1],
        'pa_empty_val' => ['label' => 'مقدار خالی', 'is_taxonomy' => 0, 'value' => '  '],
        'pa_ok'        => ['label' => 'موجود', 'is_taxonomy' => 0, 'value' => 'مقدار تست'],
    ],
];
ob_start();
es_get_template('template-parts/single-product/specs-tabs.php', ['pid' => 901]);
$r2 = ob_get_clean();
check(substr_count($r2, 'class="grid grid-cols-1 gap-1 px-5 py-4') === 1, 'اتریبیوت خالی (ترم و مقدار) از جدول حذف شد — فقط یک ردیف');
check(str_contains($r2, '<div class="text-xs font-bold text-slate-500">موجود</div>'), 'ردیف مقدار-دار با لیبل ثبت‌شده');

/* ---------- ۴) بدون هیچ محتوا → خروجی خالی ---------- */
$GLOBALS['__es_stub_titles'][902] = 'محصول تهی';
$GLOBALS['__wp_post_meta'][902] = [];
ob_start();
es_get_template('template-parts/single-product/specs-tabs.php', ['pid' => 902]);
$r3 = ob_get_clean();
check($r3 === '', 'بدون attrs/شناسه/دیدگاه → کل سکشن خالی (early return)');

/* ---------- ۵) شناسه ناقص: فقط مقدار-دارها ---------- */
$GLOBALS['__es_stub_titles'][903] = 'شناسه ناقص';
$GLOBALS['__wp_post_meta'][903] = ['gs1_id' => '6260000000000'];
ob_start();
es_get_template('template-parts/single-product/specs-tabs.php', ['pid' => 903]);
$r4 = ob_get_clean();
check(substr_count($r4, '<div class="rounded-xl border border-emerald-100 bg-white p-3.5">') === 1, 'فقط یک شناسه مقدار-دار (کارت GS1)');
check(str_contains($r4, 'text-emerald-800') && str_contains($r4, '6260000000000'), 'کارت GS1 با کلاس امرالد میرور');

/* ---------- ۶) فیلتر es_product_specs_args — تب reviews فعال ---------- */
$GLOBALS['__es_stub_comment_counts'][904] = 7;
$GLOBALS['__es_stub_titles'][904] = 'با دیدگاه';
$GLOBALS['__wp_post_meta'][904] = [];
add_filter('es_product_specs_args', static function (array $data, int $pid): array {
    if ($pid === 904) {
        $data['has_reviews'] = true;
    }
    return $data;
}, 10, 2);
ob_start();
es_get_template('template-parts/single-product/specs-tabs.php', ['pid' => 904]);
$r5 = ob_get_clean();
check(str_contains($r5, 'data-prod-tab-btn="reviews"'), 'فیلتر: تب reviews رندر شد');
check(str_contains($r5, 'نظرات کاربران (۷)'), 'شمارنده دیدگاه فارسی (۷)');
check(str_contains($r5, 'lucide-message-circle h-4 w-4 text-white'), 'آیکون تب فعال سفید');
check(!str_contains($r5, 'data-prod-tab-panel="specs"'), 'بدون specs → پنل specs غایب (بستن سکشن مستقیم)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
