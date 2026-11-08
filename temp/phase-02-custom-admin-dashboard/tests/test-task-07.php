<?php
/**
 * Test 2.7 — SectionManager: ثبت سکشن، متاباکس، ذخیره امن (nonce/capability/sفیدلیست) و ترتیب درگ
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-task-07.php
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
echo "== Test 2.7: SectionManager — هسته ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\SectionManager;

// بوت رجیستری واقعی (HomeSections فاز ۵ — ۱۵ سکشن صفحه اصلی)
do_action('init');

// ثبت سه سکشن نمونه (به ترتیب)
SectionManager::register_section('home', 'hero', 'هیرو', [['key' => 'title', 'type' => 'text']]);
SectionManager::register_section('home', 'categories', 'دسته‌بندی‌ها', []);
SectionManager::register_section('home', 'contact', 'تماس', []);

check(count(SectionManager::registered()) === 8, 'هشت گروه ثبت شد (page/about/contact فاز ۷.۷ + licenses/faq/refund/recruitment فاز ۷.۸ + home نمونه)');
check(count(SectionManager::sections_of('home')) === 3, 'سه سکشن در گروه home');
check(count(SectionManager::sections_of('page')) === 15, '۱۵ سکشن واقعی در گروه page (HomeSections)');

// متاباکس روی page ثبت شده؟ (اکشن add_meta_boxes را fire می‌کنیم)
do_action('add_meta_boxes');
$metaboxes = $GLOBALS['__wp_hooks']['meta_boxes'] ?? [];
check(isset($metaboxes['es-sections']) && ($metaboxes['es-sections']['screen'] ?? '') === 'page', 'متاباکس es-sections روی page ثبت شد');
check(($metaboxes['es-sections']['priority'] ?? '') === 'high', 'اولویت متاباکس high');

// حالت بدون متا: همه ثبت‌شده‌ها فعال به ترتیب ثبت
$GLOBALS['__wp_post_meta'][100][SectionManager::META_KEY] = null;
$sections = SectionManager::get_sections(100);
check(count($sections) === 15, 'حالت متای خالی → ۱۵ سکشن گروه page (نگاشت قالب→گروه، فاز ۷.۷)');
$es_ids = array_column($sections, 'id');
check(array_slice($es_ids, 0, 15) === \ErfanSanat\Theme\Sections\HomeSections::ORDER, 'ترتیب پیش‌فرض = ترتیب ثبت (واقعی‌ها اول)');
check(min(array_column($sections, 'enabled')) === 1, 'همه پیش‌فرض فعال');
check(array_column($sections, 'order') === range(0, 14), 'order پیوسته ۰..۱۴');

// نگاشت قالب برگه → گروه نمونه (فاز ۷.۷): برگه با قالب تستی → فقط ۳ سکشن گروه home
$_map27 = static fn(array $map): array => $map + ['template-home.php' => 'home'];
add_filter('es_sections_template_groups', $_map27);
$GLOBALS['__es_stub_template_slug'][101] = 'template-home.php';
$GLOBALS['__wp_post_meta'][101][SectionManager::META_KEY] = null;
$sections_home = SectionManager::get_sections(101);
check(array_column($sections_home, 'id') === ['hero', 'categories', 'contact'], 'برگه با قالب نگاشت‌شده → فقط سکشن‌های گروه home');
check(array_column($sections_home, 'order') === range(0, 2), 'order گروه home پیوسته ۰..۲');
remove_filter('es_sections_template_groups', $_map27);
unset($GLOBALS['__es_stub_template_slug'][101]);

// رندر UI متاباکس
$GLOBALS['__es_stub_post_types'] = [100 => 'page'];
$post = new \stdClass();
$post->ID = 100;
$post->post_type = 'page';
ob_start();
SectionManager::render_metabox($post);
$ui = (string) ob_get_clean();
check(str_contains($ui, 'name="es_sections_nonce"'), 'نانس داخل متاباکس');
check(str_contains($ui, 'data-es-section-row'), 'ردیف سکشن رندر شد');
check(substr_count($ui, 'data-es-section-move') === 15, '۱۵ دستگیره درگ (گروه page)');
check(str_contains($ui, 'name="opt_sections[0][id]" value="hero-home1"'), 'hidden id ردیف اول = hero-home1');
check(str_contains($ui, 'name="opt_sections[0][enabled]" value="0"') && str_contains($ui, 'name="opt_sections[0][enabled]" value="1"'), 'سوییچ enabled با hidden صفر');

// ذخیره: بدون نانس → تغییری نمی‌کند
$_POST = ['opt_sections' => [['id' => 'contact', 'enabled' => '1']]];
SectionManager::save(100, $post);
check(!isset($GLOBALS['__wp_post_meta'][100][SectionManager::META_KEY]), 'بدون نانس، متا ذخیره نمی‌شود');

// ذخیره: نانس درست، بدون کاپابیلیتی → رد
$GLOBALS['__wp_current_user_can'] = false;
$_POST = ['es_sections_nonce' => 'test-nonce-es_sections', 'opt_sections' => [['id' => 'contact', 'enabled' => '1']]];
SectionManager::save(100, $post);
check(!isset($GLOBALS['__wp_post_meta'][100][SectionManager::META_KEY]), 'بدون کاپابیلیتی رد می‌شود');
$GLOBALS['__wp_current_user_can'] = true;

// ذخیره موفق: سفیدلیست ID + ترتیب سابمیت + enabled
$_POST = [
    'es_sections_nonce' => 'test-nonce-es_sections',
    'opt_sections' => [
        ['id' => 'contact',     'enabled' => '1'], // درگ: تماس به اول
        ['id' => 'hero',        'enabled' => '0'],
        ['id' => 'categories',  'enabled' => '1'],
        ['id' => 'hacked',      'enabled' => '1'], // خارج سفیدلیست
    ],
];
SectionManager::save(100, $post);
$stored = $GLOBALS['__wp_post_meta'][100][SectionManager::META_KEY] ?? [];
check(count($stored) === 3, 'سکشن خارج سفیدلیست ذخیره نشد (۳ ردیف)');
check(array_column($stored, 'id') === ['contact', 'hero', 'categories'], 'ترتیب ذخیره = ترتیب درگ سابمیت');
check($stored[1]['enabled'] === 0, 'سکشن hero غیرفعال ذخیره شد');
check(array_column($stored, 'order') === [0, 1, 2], 'order بازنویسی پیوسته');

// خواندن مجدد با ترتیب ذخیره‌شده
$sections = SectionManager::get_sections(100);
check(array_column($sections, 'id') === ['contact', 'hero', 'categories'], 'get_sections با ترتیب ذخیره‌شده');

// گارد autosave
$GLOBALS['__es_stub_autosave'] = 100;
$_POST = ['es_sections_nonce' => 'test-nonce-es_sections', 'opt_sections' => [['id' => 'hero', 'enabled' => '1']]];
SectionManager::save(100, $post);
$still = $GLOBALS['__wp_post_meta'][100][SectionManager::META_KEY];
check(array_column($still, 'id') === ['contact', 'hero', 'categories'], 'در autosave ذخیره نمی‌شود');
$GLOBALS['__es_stub_autosave'] = 0;

// گارد نوع پست ناخواسته
$other = new \stdClass();
$other->ID = 200;
$other->post_type = 'product';
$GLOBALS['__es_stub_post_types'][200] = 'product';
$_POST = ['es_sections_nonce' => 'test-nonce-es_sections', 'opt_sections' => [['id' => 'hero', 'enabled' => '1']]];
SectionManager::save(200, $other);
check(!isset($GLOBALS['__wp_post_meta'][200][SectionManager::META_KEY]), 'روی post type خارج لیست ذخیره نمی‌شود');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
