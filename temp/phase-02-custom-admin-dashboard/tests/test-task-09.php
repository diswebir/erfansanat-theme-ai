<?php
/**
 * Test 2.9 — es_render_sections: رندر پویا (ترتیب/toggle) + Registry + فیلتر args
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-task-09.php
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
echo "== Test 2.9: رندر سکشن‌ها و Registry ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
// فیلتر Registry باید پیش از بوت (که Registry::init را اجرا می‌کند) ثبت شود
$GLOBALS['__wp_hooks']['filters']['es_sections_registry'] = [[
    'cb' => static fn(array $r): array => $r + [
        'about' => [
            ['id' => 'about-intro', 'label' => 'معرفی', 'schema' => []],
        ],
    ],
    'priority' => 10,
]];
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\SectionManager;
use ErfanSanat\Theme\Sections\Registry;

// ثبت سکشن‌های تست با template واقعی (override locate)
$fixture_dir = sys_get_temp_dir() . '/es-sec-fixture-' . getmypid();
@mkdir($fixture_dir . '/template-parts/sections', 0777, true);
file_put_contents($fixture_dir . '/template-parts/sections/demo-hero.php', "<?php echo 'HERO:' . esc_html(\$fields['title'] ?? '') . ';post=' . \$post_id;");
file_put_contents($fixture_dir . '/template-parts/sections/demo-gallery.php', "<?php echo 'GALLERY';");
file_put_contents($fixture_dir . '/template-parts/sections/demo-contact.php', "<?php echo 'CONTACT:' . (isset(\$extra) ? \$extra : '');");
$GLOBALS['__es_locate_override'] = [
    'template-parts/sections/demo-hero.php'    => $fixture_dir . '/template-parts/sections/demo-hero.php',
    'template-parts/sections/demo-gallery.php' => $fixture_dir . '/template-parts/sections/demo-gallery.php',
    'template-parts/sections/demo-contact.php' => $fixture_dir . '/template-parts/sections/demo-contact.php',
];

SectionManager::register_section('home', 'demo-hero', 'هیرو', [['key' => 'title', 'type' => 'text', 'default' => 'میرور']]);
SectionManager::register_section('home', 'demo-gallery', 'گالری', []);
SectionManager::register_section('home', 'demo-contact', 'تماس', []);

// نگاشت قالب برگه 500 → گروه home نمونه (فاز ۷.۷ — رندر پیش‌فرض سکشن‌های گروه قالب)
$_map29 = static fn(array $map): array => $map + ['template-demo.php' => 'home'];
add_filter('es_sections_template_groups', $_map29);
$GLOBALS['__es_stub_template_slug'][500] = 'template-demo.php';

// Registry در بوت با فیلترِ پیش‌ثبت‌شده اجرا شد
check(isset(SectionManager::registered()['about']['about-intro']), 'Registry سکشن about-intro را ثبت کرد');

// رندر پیش‌فرض (متای خالی): همه فعال به ترتیب + args پاس‌شده
$GLOBALS['__wp_post_meta'][500][SectionManager::META_KEY] = null;
ob_start();
es_render_sections(500, ['extra' => 'EX']);
$rendered = (string) ob_get_clean();
check(str_contains($rendered, 'HERO:میرور;post=500'), 'سکشن hero با fields و post_id رندر شد');
check(str_contains($rendered, 'GALLERY'), 'سکشن gallery رندر شد');
check(str_contains($rendered, 'CONTACT:EX'), 'سکشن contact با extra_args رندر شد');
check(strpos($rendered, 'HERO') < strpos($rendered, 'GALLERY') && strpos($rendered, 'GALLERY') < strpos($rendered, 'CONTACT'), 'ترتیب رندر = ترتیب ثبت');

// toggle: hero خاموش → فقط gallery/contact به ترتیب
$GLOBALS['__wp_post_meta'][500][SectionManager::META_KEY] = [
    ['id' => 'demo-hero', 'enabled' => 0, 'order' => 0, 'fields' => []],
    ['id' => 'demo-contact', 'enabled' => 1, 'order' => 1, 'fields' => []],
    ['id' => 'demo-gallery', 'enabled' => 1, 'order' => 2, 'fields' => []],
];
ob_start();
es_render_sections(500);
$rendered2 = (string) ob_get_clean();
check(!str_contains($rendered2, 'HERO:'), 'سکشن غیرفعال رندر نشد');
check(strpos($rendered2, 'CONTACT') < strpos($rendered2, 'GALLERY'), 'ترتیب ذخیره‌شده ادمین اعمال شد');

// فیلتر es_sections_render_args
$GLOBALS['__wp_hooks']['filters']['es_sections_render_args'] = [[
    'cb' => static fn(array $args, int $post_id): array => $args + ['extra' => 'FILTRED'],
    'priority' => 10,
]];
ob_start();
es_render_sections(500);
$rendered3 = (string) ob_get_clean();
check(str_contains($rendered3, 'CONTACT:FILTRED'), 'فیلتر es_sections_render_args اعمال شد');

// قالب ناموجود → بدون خطا
$GLOBALS['__wp_post_meta'][500][SectionManager::META_KEY] = [
    ['id' => 'missing-section', 'enabled' => 1, 'order' => 0, 'fields' => []],
];
ob_start();
es_render_sections(500);
check(trim((string) ob_get_clean()) === '', 'قالب ناموجود → خروجی خالی بدون خطا');

@unlink($fixture_dir . '/template-parts/sections/demo-hero.php');
@unlink($fixture_dir . '/template-parts/sections/demo-gallery.php');
@unlink($fixture_dir . '/template-parts/sections/demo-contact.php');
@rmdir($fixture_dir . '/template-parts/sections');
@rmdir($fixture_dir . '/template-parts');
@rmdir($fixture_dir);

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
