<?php
/**
 * Test 2.8 — SectionManager: رندر فیلدهای داینامیک هر سکشن + merge سازگاری رو به جلو
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-task-08.php
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
echo "== Test 2.8: SectionManager — فیلدهای سکشن و merge ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\SectionManager;

SectionManager::register_section('home', 'hero', 'هیرو', [
    ['key' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => 'عنوان میرور'],
    ['key' => 'image', 'type' => 'media', 'label' => 'تصویر'],
    [
        'key' => 'scenes', 'type' => 'repeater', 'label' => 'صحنه‌ها',
        'fields' => [
            ['key' => 'title', 'type' => 'text'],
            ['key' => 'link', 'type' => 'url'],
        ],
    ],
]);

$GLOBALS['__es_stub_post_types'][300] = 'page';
$GLOBALS['__wp_post_meta'][300][SectionManager::META_KEY] = [
    ['id' => 'hero', 'enabled' => 1, 'order' => 0, 'fields' => [
        'title' => 'عنوان ذخیره‌شده',
        'image' => 77,
        'scenes' => [
            ['title' => 'صحنه یک', 'link' => '/p/'],
        ],
        'legacy_key_from_future' => 'حفظ شود',
    ]],
];

$post = new \stdClass();
$post->ID = 300;
$post->post_type = 'page';

// رندر UI: مقادیر ذخیره‌شده داخل فیلدها + نام nesting
ob_start();
SectionManager::render_metabox($post);
$ui = (string) ob_get_clean();

check(str_contains($ui, 'name="opt_sections[0][fields][title]"'), 'نام فیلد nesting رندر شد');
check(str_contains($ui, 'value="عنوان ذخیره‌شده"'), 'مقدار ذخیره‌شده داخل اینپوت');
check(str_contains($ui, 'name="opt_sections[0][fields][image]" value="77"'), 'فیلد media با آیدی ذخیره‌شده');
check(str_contains($ui, 'name="opt_sections[0][fields][scenes][0][title]"'), 'repeater سکشن با نام nesting');
check(str_contains($ui, 'name="opt_sections[0][fields][scenes][0][link]"'), 'زیرفیلد url repeater');
check(!str_contains($ui, 'legacy_key_from_future'), 'کلید legacy در UI رندر نمی‌شود (خارج اسکیم)');

// ذخیره مجدد: کلید legacy حفظ شود (merge رو به جلو) + مقادیر sanitize
$_POST = [
    'es_sections_nonce' => 'test-nonce-es_sections',
    'opt_sections' => [[
        'id' => 'hero',
        'enabled' => '1',
        'fields' => [
            'title' => 'عنوان <b>جدید</b>',
            'image' => '88',
            'scenes' => [
                ['title' => 'صحنه جدید', 'link' => 'javascript:evil()'],
            ],
        ],
    ]],
];
SectionManager::save(300, $post);
$stored = $GLOBALS['__wp_post_meta'][300][SectionManager::META_KEY] ?? [];
check(isset($stored[0]['fields']['title']) && $stored[0]['fields']['title'] === 'عنوان جدید', 'ذخیره sanitize شده (تگ‌ها از متن حذف — رفتار core)');
check(($stored[0]['fields']['image'] ?? 0) === 88, 'media آیدی عددی ذخیره شد');
check(($stored[0]['fields']['scenes'][0]['link'] ?? 'x') === '', 'لینک مخرب repeater → خالی');
check(($stored[0]['fields']['legacy_key_from_future'] ?? 'LOST') === 'حفظ شود', 'کلید legacy پس از ذخیره حفظ شد (merge)');

// schema جدید بدون مقدار → پیش‌فرض اسکیم در UI
$GLOBALS['__wp_post_meta'][300][SectionManager::META_KEY] = [
    ['id' => 'hero', 'enabled' => 1, 'order' => 0, 'fields' => []],
];
ob_start();
SectionManager::render_metabox($post);
$ui2 = (string) ob_get_clean();
check(str_contains($ui2, 'value="عنوان میرور"'), 'فیلد بدون مقدار ذخیره → پیش‌فرض اسکیم');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
