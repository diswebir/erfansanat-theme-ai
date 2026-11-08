<?php
/**
 * Test 7.8 — قالب برگه‌های licenses / faq / refund / recruitment با SectionManager
 * اجرا: /home/user/tools/php -f temp/phase-07-projects-blog-and-pages/tests/test-task-08.php
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
echo "== Test 7.8: page-{licenses,faq,refund,recruitment} + LandingSections ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Sections\LandingSections;
use ErfanSanat\Theme\Sections\HomeSections;
use ErfanSanat\Theme\Admin\SectionManager;

do_action('init');

$GLOBALS['__es_stub_is_front_page'] = false;
$GLOBALS['__wp_post_meta'] = [];

// شناسه‌های برگه‌های فرضی + نگاشت قالب
$P = ['licenses' => 9101, 'faq' => 9102, 'refund' => 9103, 'recruitment' => 9104];
foreach ($P as $pid) {
    $GLOBALS['__es_stub_template_slug'][$pid] = 'page-templates/page-' . array_search($pid, $P, true) . '.php';
}

// ═══ ۱) رجیستری: ۴ گروه جدید با ترتیب میرور ═══
$registry = apply_filters('es_sections_registry', []);
$expected = [
    'licenses'    => ['licenses-hero', 'licenses-certs', 'contact'],
    'faq'         => ['faq-hero', 'faq-list', 'contact'],
    'refund'      => ['refund-hero', 'refund-policy', 'contact'],
    'recruitment' => ['recruit-hero', 'recruit-jobs'],
];
foreach ($expected as $g => $ids) {
    $got = array_map(static fn($i): string => (string) ($i['id'] ?? ''), (array) ($registry[$g] ?? []));
    check($got === $ids, "گروه {$g}: " . count($ids) . " سکشن به ترتیب میرور");
    $const = 'ORDER_' . strtoupper($g);
    check($got === constant(LandingSections::class . '::' . $const), "گروه {$g}: مطابق const {$const}");
}
foreach (['licenses-hero', 'licenses-certs', 'faq-hero', 'faq-list', 'refund-hero', 'refund-policy', 'recruit-hero', 'recruit-jobs'] as $sid) {
    check(SectionManager::schema_of($sid) !== [], "اسکیم {$sid} ثبت و پر است");
}

// ═══ ۲) reuse مشترک contact (سومین استفاده پس از home و about) ═══
$lic_contact = SectionManager::sections_of('licenses')['contact']['schema'] ?? null;
check(is_array($lic_contact) && serialize($lic_contact) === serialize(HomeSections::contact_schema()), 'سکشن contact گروه licenses = HomeSections::contact_schema()');
check(serialize(SectionManager::schema_of('contact')) === serialize(HomeSections::contact_schema()), 'schema_of(contact) همچنان اسکیم مشترک (اولین ثبت = home)');
foreach (['faq', 'refund'] as $g) {
    $c = SectionManager::sections_of($g)['contact']['schema'] ?? null;
    check(is_array($c) && serialize($c) === serialize(HomeSections::contact_schema()), "سکشن contact گروه {$g} = اسکیم مشترک");
}
check(!isset(SectionManager::sections_of('recruitment')['contact']), 'recruitment بدون سکشن contact (میرور CTA ندارد)');

// ═══ ۳) نگاشت قالب برگه → گروه + سازگاری ۵.۱ ═══
check(SectionManager::group_for_post(9101) === 'licenses', 'group_for_post: قالب licenses → گروه licenses');
check(SectionManager::group_for_post(9102) === 'faq', 'group_for_post: قالب faq → گروه faq');
check(SectionManager::group_for_post(9103) === 'refund', 'group_for_post: قالب refund → گروه refund');
check(SectionManager::group_for_post(9104) === 'recruitment', 'group_for_post: قالب recruitment → گروه recruitment');
check(SectionManager::group_for_post(9003) === 'page', 'group_for_post: بدون قالب → گروه page (پیش‌فرض)');
check(count(SectionManager::sections_of('page')) === 15, 'sections_of(page) = ۱۵ سکشن صفحه اصلی (سازگاری ۵.۱)');
check(count(SectionManager::sections_of('about')) === 5 && count(SectionManager::sections_of('contact')) === 2, 'گروه‌های about/contact دست‌نخورده (۷.۷)');
check(SectionManager::schema_of('licenses-hero') !== SectionManager::schema_of('faq-hero'), 'schema_of بین هیروهای ۴ گروه قاطی نمی‌کند');

// ═══ ۴) پیش‌فرض get_sections هر گروه ═══
foreach ($P as $g => $pid) {
    $rows = SectionManager::get_sections($pid);
    check(array_column($rows, 'id') === $expected[$g], "برگه {$g}: پیش‌فرض سکشن‌های گروه خودش");
}

// ═══ ۵) رندر بایت‌دقیق ۴ برگه (بدون متا = عین میرور) ═══
$out = [];
foreach ($P as $g => $pid) {
    ob_start();
    es_render_sections($pid);
    $out[$g] = (string) ob_get_clean();
    $exp = (string) file_get_contents($root . '/temp/p78_expect_' . $g . '.html');
    check($out[$g] === $exp, "رندر پیش‌فرض {$g} = بایت‌دقیق میرور (" . strlen($exp) . " کاراکتر)");
}

// ═══ ۶) hookهای آکاردئون faq (data-faq-*) ═══
ob_start();
es_render_sections(9102, ['only' => 'faq-list']);
$faq = (string) ob_get_clean();
check(substr_count($faq, 'data-faq-toggle="') === 6 && substr_count($faq, 'data-faq-icon="') === 6 && substr_count($faq, 'data-faq-body="') === 6, 'آکاردئون: ۳×۶ هوک data-faq-* با ایندکس ۰..۵');
check(str_contains($faq, 'data-faq-toggle="0"') && str_contains($faq, 'data-faq-toggle="5"'), 'ایندکس‌های ۰ و ۵ موجود');
check(str_contains($faq, 'rotate-45 bg-emerald-800 text-white') && str_contains($faq, '"block border-t'), 'آیتم اول: باز (آیکن چرخیده + بدنه block)');
check(substr_count($faq, ' class="hidden border-t') === 5 && substr_count($faq, 'bg-emerald-100 text-emerald-800') === 5, '۵ آیتم دیگر: بسته (hidden + آیکن زمردی روشن)');
check(substr_count($faq, 'data-faq-list="true"') === 1, 'کانتینر data-faq-list (اتصال JS میرور)');

// ═══ ۷) فرم استخدام recruitment (نمایشی تا ۸.۶) + تب‌ها/پنل‌ها/کارت‌ها ═══
$rec = $out['recruitment'];
check(!str_contains($rec, 'action="'), 'تا فاز ۸.۶ فرم استخدام action ندارد');
check(substr_count($rec, 'data-recruit-tab-btn="') === 4 && substr_count($rec, 'data-recruit-tab-panel="') === 4, '۴ دکمه تب + ۴ پنل (هوک‌های میرور)');
check(substr_count($rec, '<article class="card-light') === 7, '۷ کارت موقعیت شغلی (maskd-equal میرور)');
check(substr_count($rec, '<option value="') === 8, '۸ گزینه select: ۴ واحد سازمانی (از تب‌ها) + ۴ سابقه');
check(str_contains($rec, 'id="apply-form"') && str_contains($rec, 'href="#apply-form"'), 'لنگر فرم همکاری + دکمه‌های «ارسال رزومه»');
check(str_contains($rec, 'data-contact-success') && str_contains($rec, 'data-contact-reset'), 'جعبه موفقیت + بازنشانی (hookهای مشترک فرم)');
check(str_contains($rec, '<form data-contact-form="true" class="flex flex-col gap-4">'), 'فرم: دقیقاً کلاس میرور بدون attr اضافه');
$_rec_fx = static fn(string $extra, array $fields): string => ' data-es-recruit="ajax"';
add_filter('es_recruit_form_extra', $_rec_fx, 10, 2);
ob_start();
es_render_sections(9104, ['only' => 'recruit-jobs']);
$out_fx = (string) ob_get_clean();
remove_filter('es_recruit_form_extra', $_rec_fx);
check(str_contains($out_fx, 'gap-4" data-es-recruit="ajax">'), 'فیلتر es_recruit_form_extra → اتصال فاز ۸.۶');
ob_start();
es_render_sections(9104);
check(ob_get_clean() === $out['recruitment'], 'پس از حذف فیلتر، خروجی recruitment دوباره بایت‌دقیق');

// ═══ ۸) شماره‌های فارسی مراحل refund (es_fa_num از ایندکس) ═══
ob_start();
es_render_sections(9103, ['only' => 'refund-policy']);
$ref = (string) ob_get_clean();
check(str_contains($ref, '>۱<') && str_contains($ref, '>۲<') && str_contains($ref, '>۳<'), 'گام‌های فرآیند: ۱/۲/۳ فارسی از ایندکس (تکرار از es_fa_num)');

// ═══ ۹) ویرایش Repeater از ادمین اثر می‌کند (faq items + licenses certs) ═══
$GLOBALS['__wp_post_meta'][9102]['_es_sections'] = [
    ['id' => 'faq-hero', 'enabled' => 1, 'order' => 0, 'fields' => []],
    ['id' => 'faq-list', 'enabled' => 1, 'order' => 1, 'fields' => [
        'items' => [
            ['cat' => 'عمومی', 'question' => 'پرسش ویرایش‌شده ۱', 'answer' => 'پاسخ ۱', 'open' => 1],
            ['cat' => 'عمومی', 'question' => 'پرسش ویرایش‌شده ۲', 'answer' => 'پاسخ ۲', 'open' => 0],
        ],
    ]],
    ['id' => 'contact', 'enabled' => 1, 'order' => 2, 'fields' => []],
];
ob_start();
es_render_sections(9102, ['only' => 'faq-list']);
$faq_edit = (string) ob_get_clean();
check(str_contains($faq_edit, 'پرسش ویرایش‌شده ۱') && str_contains($faq_edit, 'پرسش ویرایش‌شده ۲'), 'ویرایش Repeater پرسش‌ها از ادمین اعمال شد');
check(!str_contains($faq_edit, 'چطور می‌توانم از عرفان صنعت فاکتور رسمی دریافت کنم؟'), 'مقدار پیش‌فرض جایگزین شد');
check(substr_count($faq_edit, 'data-faq-toggle="') === 2 && str_contains($faq_edit, '"block border-t'), 'دو آیتم جدید: open=1 اول → block');
$GLOBALS['__wp_post_meta'] = [];

$GLOBALS['__wp_post_meta'][9101]['_es_sections'] = [
    ['id' => 'licenses-hero', 'enabled' => 1, 'order' => 0, 'fields' => []],
    ['id' => 'licenses-certs', 'enabled' => 1, 'order' => 1, 'fields' => [
        'certs' => [
            ['cat' => 'گواهینامه', 'code' => 'ISIC-۳۱۰۰', 'title' => 'گواهی ویرایش‌شده', 'desc' => 'توضیح جدید', 'org' => 'مرجع جدید', 'period' => '۱۴۰۴'],
        ],
    ]],
    ['id' => 'contact', 'enabled' => 1, 'order' => 2, 'fields' => []],
];
ob_start();
es_render_sections(9101, ['only' => 'licenses-certs']);
$lic_edit = (string) ob_get_clean();
check(str_contains($lic_edit, 'گواهی ویرایش‌شده') && !str_contains($lic_edit, 'سیستم مدیریت کیفیت'), 'ویرایش Repeater گواهینامه‌ها (۶ ردیف → ۱ ردیف) اعمال شد');
$GLOBALS['__wp_post_meta'] = [];

// ═══ ۱۰) toggle: غیرفعال‌کردن سکشن میانی ═══
$GLOBALS['__wp_post_meta'][9101]['_es_sections'] = [
    ['id' => 'licenses-hero', 'enabled' => 1, 'order' => 0, 'fields' => []],
    ['id' => 'licenses-certs', 'enabled' => 0, 'order' => 1, 'fields' => []],
    ['id' => 'contact', 'enabled' => 1, 'order' => 2, 'fields' => []],
];
ob_start();
es_render_sections(9101);
$lic_off = (string) ob_get_clean();
check(substr_count($lic_off, '<section') === 2, 'toggle: سکشن certs غیرفعال → فقط ۲ سکشن رندر شد');
$GLOBALS['__wp_post_meta'] = [];

// ═══ ۱۱) reorder: refund-policy اول ═══
$GLOBALS['__wp_post_meta'][9103]['_es_sections'] = [
    ['id' => 'refund-hero', 'enabled' => 1, 'order' => 2, 'fields' => []],
    ['id' => 'refund-policy', 'enabled' => 1, 'order' => 0, 'fields' => []],
    ['id' => 'contact', 'enabled' => 1, 'order' => 1, 'fields' => []],
];
ob_start();
es_render_sections(9103);
$ref_re = (string) ob_get_clean();
check(strpos($ref_re, 'فرآیند اجرایی پشتیبانی فنی') < strpos($ref_re, 'href="https://test.example/"'), 'reorder ادمین: refund-policy قبل از hero');
$GLOBALS['__wp_post_meta'] = [];

// ═══ ۱۲) قالب‌های برگه: Template Name + جریان کامل ═══
$tn = ['licenses' => 'مجوزها و افتخارات', 'faq' => 'سوالات متداول', 'refund' => 'شرایط گارانتی و خدمات پس از فروش', 'recruitment' => 'فرصت‌های شغلی و استخدام'];
foreach ($P as $g => $pid) {
    $tp = (string) file_get_contents($root . '/page-templates/page-' . $g . '.php');
    check(str_contains($tp, 'Template Name: ' . $tn[$g]), "page-{$g}: Template Name در هدر قالب");
    check(str_contains($tp, 'es_render_sections((int) get_queried_object_id())'), "page-{$g}: رندر با id برگه کوئری");
}
foreach ($P as $g => $pid) {
    $GLOBALS['__wp_post_meta'] = [];
    $GLOBALS['__es_stub_queried_object_id'] = $pid;
    ob_start();
    include $root . '/page-templates/page-' . $g . '.php';
    $full = (string) ob_get_clean();
    $exp = (string) file_get_contents($root . '/temp/p78_expect_' . $g . '.html');
    check(str_contains($full, 'wp-head-marker') && str_contains($full, 'wp-footer-marker'), "page-{$g}: get_header/get_footer اجرا شد");
    check(str_contains($full, $exp), "page-{$g} کامل: خروجی سکشن‌ها = بایت‌دقیق میرور داخل شل قالب");
}
$GLOBALS['__es_stub_queried_object_id'] = 0;

// ═══ ۱۳) بازگردانی: رندرهای پیش‌فرض پس از همه تغییرات متا ═══
$GLOBALS['__wp_post_meta'] = [];
foreach ($P as $g => $pid) {
    ob_start();
    es_render_sections($pid);
    check(ob_get_clean() === $out[$g], "پایان تست: {$g} دوباره بایت‌دقیق (نشتی متا ندارد)");
}

// ═══ جمع‌بندی ═══
echo "\n== نتیجه: {$passes} PASS / " . count($failures) . " FAIL ==\n";
foreach ($failures as $f) {
    echo "  FAILED: $f\n";
}
exit(count($failures) === 0 ? 0 : 1);
