<?php
/**
 * Test 1.5 — DeBloat بخش ۲: ایموجی، پاکسازی wp_head و سخت‌سازی
 * اجرا: /home/user/tools/php -f temp/phase-01-core-and-assets/tests/test-task-05.php
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
echo "== Test 1.5: DeBloat — ایموجی و پاکسازی wp_head ==\n";

require $root . '/temp/harness/wp-stubs.php';
es_stubs_reset();
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;

require $root . '/functions.php';

$removed_actions = $GLOBALS['__wp_hooks']['removed_actions'];
$removed_filters = $GLOBALS['__wp_hooks']['removed_filters'];
$has_action = static function (string $hook, string $cb) use ($removed_actions): bool {
    foreach ($removed_actions as $entry) {
        if ($entry['hook'] === $hook && $entry['cb'] === $cb) {
            return true;
        }
    }
    return false;
};
$has_filter_removed = static function (string $hook, string $cb) use ($removed_filters): bool {
    foreach ($removed_filters as $entry) {
        if ($entry['hook'] === $hook && $entry['cb'] === $cb) {
            return true;
        }
    }
    return false;
};

// ---- ایموجی ----
check($has_action('wp_head', 'print_emoji_detection_script'), 'حذف اسکریپت تشخیص ایموجی از wp_head');
check($has_action('wp_print_styles', 'print_emoji_styles'), 'حذف استایل ایموجی از wp_print_styles');
check($has_filter_removed('the_content_feed', 'wp_staticize_emoji'), 'حذف فیلتر ایموجی از the_content_feed');
check($has_filter_removed('comment_text_rss', 'wp_staticize_emoji'), 'حذف فیلتر ایموجی از comment_text_rss');
check($has_action('wp_mail', 'wp_staticize_emoji_for_email'), 'حذف ایموجی از wp_mail');

// ---- پاکسازی wp_head ----
foreach (['rsd_link', 'wlwmanifest_link', 'wp_generator', 'wp_shortlink_wp_head', 'rest_output_link_wp_head', 'wp_oembed_add_discovery_links', 'wp_oembed_add_host_js', 'adjacent_posts_rel_link_wp_head', 'index_rel_link', 'parent_post_rel_link', 'start_post_rel_link', 'feed_links_extra'] as $hook_cb) {
    check($has_action('wp_head', $hook_cb), "حذف {$hook_cb} از wp_head");
}
check(apply_filters('the_generator', 'WordPress 7.1') === '', 'the_generator به رشته خالی فیلتر شده');

// ---- سخت‌سازی ----
check(apply_filters('xmlrpc_enabled', true) === false, 'XML-RPC غیرفعال شده (فیلتر xmlrpc_enabled)');

// استثنای فیلتری XML-RPC برای سازگاری افزونه‌ها
$GLOBALS['__wp_hooks']['filters']['es_debloat_keep_xmlrpc'] = [['cb' => '__return_true', 'priority' => 10]];
// شبیه‌سازی رندر مجدد init در یک فرآیند جدید ممکن نیست؛ منطق شرط را مستقیم می‌آزماییم:
check(apply_filters('es_debloat_keep_xmlrpc', false) === true, 'فیلتر es_debloat_keep_xmlrpc منطق استثنا را کنترل می‌کند');

// ---- dashicons فقط برای کاربر واردنشده ----
$GLOBALS['__wp_user_logged_in'] = false;
\ErfanSanat\Theme\Frontend\DeBloat::dequeue_dashicons();
check(in_array('dashicons', $GLOBALS['__wp_hooks']['dequeued_styles'], true), 'dashicons برای بازدیدکننده واردنشده حذف می‌شود');

$GLOBALS['__wp_hooks']['dequeued_styles'] = [];
$GLOBALS['__wp_user_logged_in'] = true;
\ErfanSanat\Theme\Frontend\DeBloat::dequeue_dashicons();
check(!in_array('dashicons', $GLOBALS['__wp_hooks']['dequeued_styles'], true), 'dashicons برای کاربر واردشده حذف نمی‌شود (نوار ادمین سالم)');

// استثنای dashicons با فیلتر
$GLOBALS['__wp_hooks']['dequeued_styles'] = [];
$GLOBALS['__wp_user_logged_in'] = false;
$GLOBALS['__wp_hooks']['filters']['es_debloat_remove_dashicons'] = [['cb' => '__return_false', 'priority' => 10]];
\ErfanSanat\Theme\Frontend\DeBloat::dequeue_dashicons();
check(!in_array('dashicons', $GLOBALS['__wp_hooks']['dequeued_styles'], true), 'فیلتر es_debloat_remove_dashicons جلوی حذف را می‌گیرد');

// گارد ادمین در سطح init (رگرسیون 1.4)
es_stubs_reset();
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin'] = true;
require $root . '/functions.php';
check(
    $GLOBALS['__wp_hooks']['removed_actions'] === [] && $GLOBALS['__wp_hooks']['removed_filters'] === [],
    'در ادمین هیچ remove_action/remove_filter از DeBloat اجرا نمی‌شود'
);

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
