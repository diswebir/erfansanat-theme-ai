<?php
/**
 * Test 7.7 — قالب برگه‌های about و contact با SectionManager
 * اجرا: /home/user/tools/php -f temp/phase-07-projects-blog-and-pages/tests/test-task-07.php
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
echo "== Test 7.7: page-about / page-contact + AboutSections / ContactSections ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Sections\AboutSections;
use ErfanSanat\Theme\Sections\ContactSections;
use ErfanSanat\Theme\Sections\HomeSections;
use ErfanSanat\Theme\Admin\SectionManager;

do_action('init');

$GLOBALS['__es_stub_is_front_page'] = false;
$GLOBALS['__wp_post_meta'] = [];

// ═══ ۱) رجیستری: گروه about و contact با ترتیب میرور ═══
$registry = apply_filters('es_sections_registry', []);
$about_ids = array_map(static fn($i): string => (string) ($i['id'] ?? ''), (array) ($registry['about'] ?? []));
$contact_ids = array_map(static fn($i): string => (string) ($i['id'] ?? ''), (array) ($registry['contact'] ?? []));
check($about_ids === AboutSections::ORDER, 'گروه about: ۵ سکشن به ترتیب میرور');
check($about_ids === ['page-about-hero', 'page-about-intro', 'page-about-iso', 'page-about-values', 'contact'], 'شناسه‌های گروه about (بدون تداخل با سکشن‌های صفحه اصلی)');
check($contact_ids === ContactSections::ORDER, 'گروه contact: ۲ سکشن به ترتیب میرور');
check($contact_ids === ['contact-hero', 'contact-form'], 'شناسه‌های گروه contact');
foreach (AboutSections::ORDER as $id) {
    $schema = SectionManager::schema_of($id);
    check($schema !== [], "اسکیم {$id} پر است");
}
check(SectionManager::schema_of('contact-hero') !== [] && SectionManager::schema_of('contact-form') !== [], 'اسکیم contact-hero/contact-form ثبت شده');

// ═══ ۲) عدم تداخل اسکیم: page-about-intro ≠ about-intro (صفحه اصلی) ═══
$keys_new = array_column(SectionManager::schema_of('page-about-intro'), 'key');
$keys_home = array_column(SectionManager::schema_of('about-intro'), 'key');
check(in_array('fcard_v', $keys_new, true) && in_array('h2_hl', $keys_new, true), 'اسکیم page-about-intro سکشن برگه about است');
check(in_array('bg_image', $keys_home, true), 'اسکیم about-intro (اینکس) دست‌نخورده');
check(SectionManager::schema_of('page-about-intro') !== SectionManager::schema_of('about-intro'), 'schema_of دو گروه را قاطی نمی‌کند');

// ═══ ۳) اسکیم مشترک contact بین home و about ═══
$about_contact = SectionManager::sections_of('about')['contact']['schema'] ?? null;
check(is_array($about_contact) && serialize($about_contact) === serialize(HomeSections::contact_schema()), 'سکشن contact گروه about = HomeSections::contact_schema()');
check(serialize(SectionManager::schema_of('contact')) === serialize(HomeSections::contact_schema()), 'schema_of(contact) → اسکیم مشترک (گروه page در اولویت)');

// ═══ ۴) نگاشت قالب برگه → گروه ═══
$GLOBALS['__es_stub_template_slug'][9001] = 'page-templates/page-about.php';
$GLOBALS['__es_stub_template_slug'][9002] = 'page-templates/page-contact.php';
$GLOBALS['__es_stub_template_slug'][9003] = '';
check(SectionManager::group_for_post(9001) === 'about', 'group_for_post: قالب about → گروه about');
check(SectionManager::group_for_post(9002) === 'contact', 'group_for_post: قالب contact → گروه contact');
check(SectionManager::group_for_post(9003) === 'page', 'group_for_post: بدون قالب → گروه page');
$_map_filter = static fn(array $map): array => $map + ['page-templates/page-x.php' => 'landing'];
add_filter('es_sections_template_groups', $_map_filter);
$GLOBALS['__es_stub_template_slug'][9004] = 'page-templates/page-x.php';
check(SectionManager::group_for_post(9004) === 'landing', 'فیلتر es_sections_template_groups توسعه‌پذیر است');
remove_filter('es_sections_template_groups', $_map_filter);
unset($GLOBALS['__es_stub_template_slug'][9004]);

// ═══ ۵) get_sections پیش‌فرض: فقط سکشن‌های گروه قالب ═══
$rows_about = SectionManager::get_sections(9001);
$rows_contact = SectionManager::get_sections(9002);
$rows_page = SectionManager::get_sections(9003);
check(array_column($rows_about, 'id') === AboutSections::ORDER, 'برگه about: پیش‌فرض ۵ سکشن گروه خودش');
check(array_column($rows_contact, 'id') === ContactSections::ORDER, 'برگه contact: پیش‌فرض ۲ سکشن گروه خودش');
check(count($rows_page) === 15 && $rows_page[0]['id'] === 'hero-home1', 'برگه معمولی: پیش‌فرض ۱۵ سکشن صفحه اصلی (رفتار ۵.۱ حفظ شده)');
check(count(SectionManager::sections_of('page')) === 15, 'sections_of(page) = ۱۵ (سازگاری ۵.۱)');

// ═══ ۶) متاباکس: فقط سکشن‌های گروه قالب نمایش داده می‌شوند ═══
ob_start();
SectionManager::render_metabox((object) ['ID' => 9001, 'post_type' => 'page']);
$mb_about = (string) ob_get_clean();
ob_start();
SectionManager::render_metabox((object) ['ID' => 9003, 'post_type' => 'page']);
$mb_page = (string) ob_get_clean();
check(str_contains($mb_about, 'data-section-id="page-about-hero"') && str_contains($mb_about, 'data-section-id="contact"'), 'متاباکس برگه about: سکشن‌های گروه about');
check(!str_contains($mb_about, 'data-section-id="hero-home1"'), 'متاباکس برگه about: بدون سکشن‌های صفحه اصلی');
check(str_contains($mb_page, 'data-section-id="hero-home1"') && !str_contains($mb_page, 'data-section-id="page-about-hero"'), 'متاباکس برگه معمولی: بدون سکشن‌های about');
check(str_contains($mb_about, 'name="opt_sections[0][fields][crumb]"'), 'فیلدهای اسکیم در متاباکس رندر می‌شوند');

// ═══ ۷) رندر بایت‌دقیق about (حالت بدون متا) ═══
ob_start();
es_render_sections(9001);
$out_about = (string) ob_get_clean();
$exp_about = (string) file_get_contents($root . '/temp/p77_expect_about.html');
check($out_about === $exp_about, 'رندر پیش‌فرض about = بایت‌دقیق میرور (' . strlen($exp_about) . ' کاراکتر)');

// ═══ ۸) رندر بایت‌دقیق contact (حالت بدون متا) ═══
ob_start();
es_render_sections(9002);
$out_contact = (string) ob_get_clean();
$exp_contact = (string) file_get_contents($root . '/temp/p77_expect_contact.html');
check($out_contact === $exp_contact, 'رندر پیش‌فرض contact = بایت‌دقیق میرور (' . strlen($exp_contact) . ' کاراکتر)');

// ═══ ۹) hookهای فرم تماس (contact-form/reset/success) ═══
check(str_contains($out_contact, '<form data-contact-form="true" class="flex mt-8 flex-col gap-5">'), 'فرم: data-contact-form + کلاس میرور (بدون attr اضافه)');
check(str_contains($out_contact, 'data-contact-success="true"') && str_contains($out_contact, 'data-contact-reset="true"'), 'فرم: جعبه موفقیت + دکمه بازنشانی (hookهای میرور)');
check(str_contains($out_contact, 'type="tel" dir="rtl"') && str_contains($out_contact, '<textarea required="" rows="4"'), 'فیلدهای فرم: tel rtl + textarea');
check(!str_contains($out_contact, 'action="'), 'تا فاز ۸.۵ فرم action ندارد (فقط نمایشی)');
$_form_extra = static fn(string $extra, array $fields): string => ' data-es-contact="ajax"';
add_filter('es_contact_form_extra', $_form_extra, 10, 2);
ob_start();
es_render_sections(9002, ['only' => 'contact-form']);
$out_fx = (string) ob_get_clean();
remove_filter('es_contact_form_extra', $_form_extra);
check(str_contains($out_fx, 'gap-5" data-es-contact="ajax">'), 'فیلتر es_contact_form_extra → اتصال فاز ۸.۵ (action/nonce)');
ob_start();
es_render_sections(9002);
check(ob_get_clean() === $exp_contact, 'پس از حذف فیلتر، خروجی دوباره بایت‌دقیق');

// ═══ ۱۰) ویرایش فیلد از ادمین اثر می‌کند ═══
$GLOBALS['__wp_post_meta'][9001]['_es_sections'] = [
    ['id' => 'page-about-hero', 'enabled' => 1, 'order' => 0, 'fields' => []],
    ['id' => 'page-about-intro', 'enabled' => 1, 'order' => 1, 'fields' => ['h2_hl' => 'نورپردازی ایران']],
    ['id' => 'page-about-iso', 'enabled' => 1, 'order' => 2, 'fields' => []],
    ['id' => 'page-about-values', 'enabled' => 1, 'order' => 3, 'fields' => []],
    ['id' => 'contact', 'enabled' => 1, 'order' => 4, 'fields' => []],
];
ob_start();
es_render_sections(9001, ['only' => 'page-about-intro']);
$out_edit = (string) ob_get_clean();
check(str_contains($out_edit, '>نورپردازی ایران</span>'), 'ویرایش فیلد h2_hl از ادمین اعمال شد');
check(!str_contains($out_edit, '>صنعت روشنایی ایران</span>'), 'مقدار پیش‌فرض جایگزین شد');

// ═══ ۱۱) toggle: سکشن غیرفعال حذف ═══
$GLOBALS['__wp_post_meta'][9001]['_es_sections'] = [
    ['id' => 'page-about-hero', 'enabled' => 1, 'order' => 0, 'fields' => []],
    ['id' => 'page-about-intro', 'enabled' => 1, 'order' => 1, 'fields' => []],
    ['id' => 'page-about-iso', 'enabled' => 0, 'order' => 2, 'fields' => []],
    ['id' => 'page-about-values', 'enabled' => 1, 'order' => 3, 'fields' => []],
    ['id' => 'contact', 'enabled' => 1, 'order' => 4, 'fields' => []],
];
ob_start();
es_render_sections(9001);
$out_toggle = (string) ob_get_clean();
check(!str_contains($out_toggle, '>گواهینامه بین‌المللی<'), 'toggle: سکشن ISO غیرفعال رندر نمی‌شود');
check(str_contains($out_toggle, 'درخشان‌تر از همیشه') && str_contains($out_toggle, 'اصول بنیادین') && str_contains($out_toggle, 'بیش از دو دهه پیشگامی'), 'بقیه سکشن‌ها باقی هستند');

// ═══ ۱۲) reorder: values اول ═══
$GLOBALS['__wp_post_meta'][9001]['_es_sections'] = [
    ['id' => 'page-about-hero', 'enabled' => 1, 'order' => 3, 'fields' => []],
    ['id' => 'page-about-intro', 'enabled' => 1, 'order' => 4, 'fields' => []],
    ['id' => 'page-about-iso', 'enabled' => 1, 'order' => 2, 'fields' => []],
    ['id' => 'page-about-values', 'enabled' => 1, 'order' => 0, 'fields' => []],
    ['id' => 'contact', 'enabled' => 1, 'order' => 1, 'fields' => []],
];
ob_start();
es_render_sections(9001);
$out_reorder = (string) ob_get_clean();
check(strpos($out_reorder, 'اصول بنیادین') !== false && strpos($out_reorder, 'اصول بنیادین') < strpos($out_reorder, 'درخشان‌تر از همیشه'), 'reorder ادمین: values قبل از hero');
check(strpos($out_reorder, '>گواهینامه بین‌المللی<') < strpos($out_reorder, 'درخشان‌تر از همیشه'), 'ترتیب ISO قبل از hero');

// ═══ ۱۳) قالب‌های برگه: Template Name + جریان کامل ═══
$tp_about = (string) file_get_contents($root . '/page-templates/page-about.php');
$tp_contact = (string) file_get_contents($root . '/page-templates/page-contact.php');
check(str_contains($tp_about, 'Template Name: درباره ما'), 'page-about: Template Name در هدر قالب');
check(str_contains($tp_contact, 'Template Name: تماس با ما'), 'page-contact: Template Name در هدر قالب');
check(str_contains($tp_about, 'es_render_sections((int) get_queried_object_id())'), 'page-about: رندر با id برگه کوئری');

$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_queried_object_id'] = 9001;
$GLOBALS['__es_stub_template_slug'][9001] = 'page-templates/page-about.php';
ob_start();
include $root . '/page-templates/page-about.php';
$full_about = (string) ob_get_clean();
check(str_contains($full_about, 'wp-head-marker') && str_contains($full_about, 'wp-footer-marker'), 'page-about: get_header/get_footer اجرا شد');
check(str_contains($full_about, $exp_about), 'page-about کامل: خروجی سکشن‌ها = بایت‌دقیق میرور داخل شل قالب');

$GLOBALS['__es_stub_queried_object_id'] = 9002;
$GLOBALS['__es_stub_template_slug'][9002] = 'page-templates/page-contact.php';
ob_start();
include $root . '/page-templates/page-contact.php';
$full_contact = (string) ob_get_clean();
check(str_contains($full_contact, $exp_contact), 'page-contact کامل: خروجی سکشن‌ها = بایت‌دقیق میرور داخل شل قالب');

// ═══ ۱۴) MobileWalker: نشتی PHP رفع شده (باگ ۵.x) ═══
$mw = \ErfanSanat\Theme\Nav\MobileWalker::render();
check(!str_contains($mw, '<?php'), 'سایدبار موبایل: بدون نشتی کد PHP خام');
check(str_contains($mw, '<div class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 bg-emerald-50 text-xs font-black text-emerald-800">ESE</div>'), 'سایدبار موبایل: باکس ESE پیش‌فرض (عین میرور)');
$GLOBALS['__wp_theme_mods']['custom_logo'] = 55;
$GLOBALS['__es_stub_attachment_urls'][55] = 'https://test.example/wp-content/uploads/logo.png';
$mw2 = \ErfanSanat\Theme\Nav\MobileWalker::render();
unset($GLOBALS['__wp_theme_mods']['custom_logo'], $GLOBALS['__es_stub_attachment_urls'][55]);
check(str_contains($mw2, '<img src="https://test.example/wp-content/uploads/logo.png"') && str_contains($mw2, 'class="h-9 w-9 rounded-xl object-contain">'), 'سایدبار موبایل: لوگوی سفارشی → img (شاخه شرطی PHP قبلی)');
check(!str_contains($mw2, 'ESE</div>'), 'با لوگوی سفارشی، باکس ESE حذف می‌شود');

// ═══ ۱۵) فایل‌های تولیدی سکشن‌ها روی مسیرهای درست ═══
foreach (['page-about-hero', 'page-about-intro', 'page-about-iso', 'page-about-values', 'contact-hero', 'contact-form'] as $sid) {
    check(is_file($root . '/template-parts/sections/' . $sid . '.php'), "رپر sections/{$sid}.php وجود دارد");
}
check(is_file($root . '/template-parts/about/section-hero.php') && is_file($root . '/template-parts/contact/section-form.php'), 'مارک‌آپ‌های about/ در و contact/ در جای خودند');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
