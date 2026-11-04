<?php
/**
 * Test 3.11 — گالری پروژه: کلیدهای تاریخی (foure-image!) + گالری مدرن + همگام‌سازی + خواندن ادغامی
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-11.php
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
echo "== Test 3.11: گالری پروژه ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\ProjectMetaboxes;
use ErfanSanat\Theme\Frontend\ProjectGallery;

// متاباکس ثبت شده
do_action('add_meta_boxes');
check(($GLOBALS['__wp_hooks']['meta_boxes']['es-project-info']['screen'] ?? '') === 'projects', 'متاباکس روی projects');

// رندر: ۵ کلید تاریخی با املای دقیق
$post = new \stdClass();
$post->ID = 120;
$post->post_type = 'projects';
$GLOBALS['__es_stub_post_types'][120] = 'projects';
ob_start();
ProjectMetaboxes::render($post);
$ui = (string) ob_get_clean();
foreach (['second-image', 'three-image', 'foure-image', 'five-image', 'six-image'] as $key) {
    check(str_contains($ui, 'name="opt_project[' . $key . ']"'), "اینپوت {$key}");
}
check(str_contains($ui, 'name="opt_project[_es_project_gallery]"'), 'گالری مدرن');

// ذخیره: بدون نانس → رد
$_POST = ['opt_project' => ['second-image' => '1']];
ProjectMetaboxes::save(120, $post);
check(!isset($GLOBALS['__wp_post_meta'][120]['second-image']), 'بدون نانس ذخیره نمی‌شود');

// ذخیره کلیدهای تاریخی + همگام‌سازی گالری مدرن
$_POST = [
    'es_project_meta_nonce' => 'test-nonce-es_project_meta',
    'opt_project' => [
        'second-image'        => '201',
        'three-image'         => '202',
        'foure-image'         => '203',
        'five-image'          => '204',
        'six-image'           => '205',
        '_es_project_gallery' => '201,202,203,204,205,206',
        'evil-key'            => 'x',
    ],
];
ProjectMetaboxes::save(120, $post);
check(($GLOBALS['__wp_post_meta'][120]['second-image'] ?? '') === '201', 'ذخیره second-image');
check(($GLOBALS['__wp_post_meta'][120]['foure-image'] ?? '') === '203', 'ذخیره foure-image (املای دقیق دیتابیس)');
check(!isset($GLOBALS['__wp_post_meta'][120]['evil-key']), 'کلید خارج لیست ذخیره نمی‌شود');
check(($GLOBALS['__wp_post_meta'][120]['_es_project_gallery'] ?? '') === '201,202,203,204,205,206', 'گالری مدرن ذخیره شد');

// ---- ذخیره گالری مدرن خالی → همگام‌سازی ۵ آیدی اول ----
$GLOBALS['__es_stub_post_types'][121] = 'projects';
$GLOBALS['__wp_post_meta'][121] = [];
$post121 = new \stdClass();
$post121->ID = 121;
$post121->post_type = 'projects';
$_POST = [
    'es_project_meta_nonce' => 'test-nonce-es_project_meta',
    'opt_project' => ['_es_project_gallery' => '301,302,303'],
];
ProjectMetaboxes::save(121, $post121);
check(($GLOBALS['__wp_post_meta'][121]['second-image'] ?? '') === '301', 'همگام‌سازی: آیدی ۱ → second-image');
check(($GLOBALS['__wp_post_meta'][121]['three-image'] ?? '') === '302', 'همگام‌سازی: آیدی ۲ → three-image');
check(($GLOBALS['__wp_post_meta'][121]['foure-image'] ?? '') === '303', 'همگام‌سازی: آیدی ۳ → foure-image');
check(($GLOBALS['__wp_post_meta'][121]['five-image'] ?? '') === '', 'بدون آیدی ۴ → five-image خالی (داده قدیمی پاک نمی‌شود مگر مقدار جدید)');

// ---- خواندن ادغامی ProjectGallery::get_gallery ----
$GLOBALS['__wp_post_meta'][122] = [
    'second-image'        => '401',
    'three-image'         => '402',
    'foure-image'         => '403',
    'five-image'          => '404',
    'six-image'           => '405',
    '_es_project_gallery' => '401,403,406',
];
check(ProjectGallery::get_gallery(122) === [401, 402, 403, 404, 405, 406], 'ادغام تاریخی+مدرن با dedup و ترتیب');

// مقدار URL قدیمی در کلید → تبدیل به آیدی (فیلتر توسعه‌پذیر کلاس)
$GLOBALS['__es_stub_url_to_id'] = ['/wp-content/uploads/old.jpg' => 409];
$GLOBALS['__wp_hooks']['filters']['es_attachment_id_from_url'] = [[
    'cb'       => static fn(int $id, string $url): int => $GLOBALS['__es_stub_url_to_id'][$url] ?? 0,
    'priority' => 10,
]];
$GLOBALS['__wp_post_meta'][123] = ['second-image' => '/wp-content/uploads/old.jpg', 'three-image' => '410'];
check(ProjectGallery::get_gallery(123) === [409, 410], 'مقدار URL قدیمی → تبدیل به آیدی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
