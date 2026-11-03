<?php
/**
 * Test 3.6 — تب عناوین و وضعیت فروش: zireonvan / english_titl / before-sale-c / true_sale + ستون وضعیت
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-06.php
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
echo "== Test 3.6: عناوین و وضعیت فروش ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;

// رندر تب titles
$post = new \stdClass();
$post->ID = 60;
$post->post_type = 'product';
ob_start();
ProductMetaboxes::render($post);
$ui = (string) ob_get_clean();
check(str_contains($ui, 'name="opt_meta[zireonvan]"'), 'فیلد zireonvan');
check(str_contains($ui, 'name="opt_meta[english_titl]"'), 'فیلد english_titl (املای دقیق بدون e)');
check(str_contains($ui, 'name="opt_meta[before-sale-c]"'), 'فیلد before-sale-c (خط‌تیره)');
check(str_contains($ui, 'name="opt_meta[true_sale]" value="true"') && str_contains($ui, 'name="opt_meta[true_sale]" value="false"'), 'رادیو true/false');

// پیش‌فرض true وقتی متا خالی
check(str_contains($ui, 'value="true" checked'), 'پیش‌فرض true_sale = true');

// ذخیره
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta' => [
        'zireonvan'     => 'نورپردازی هوشمند ضدآب',
        'english_titl'  => 'Smart Waterproof Lighting',
        'before-sale-c' => '<strong>قبل از خرید</strong> با کارشناس تماس بگیرید<script>bad()</script>',
        'true_sale'     => 'false',
    ],
];
ProductMetaboxes::save(60, $post);
check(($GLOBALS['__wp_post_meta'][60]['zireonvan'] ?? '') === 'نورپردازی هوشمند ضدآب', 'ذخیره zireonvan');
check(($GLOBALS['__wp_post_meta'][60]['english_titl'] ?? '') === 'Smart Waterproof Lighting', 'ذخیره english_titl با املای کلید صحیح');
check(($GLOBALS['__wp_post_meta'][60]['before-sale-c'] ?? '') === '<strong>قبل از خرید</strong> با کارشناس تماس بگیریدbad()', 'ذخیره before-sale-c با kses (تگ مجاز ماند)');
check(($GLOBALS['__wp_post_meta'][60]['true_sale'] ?? '') === 'false', 'ذخیره true_sale=false');

// مقدار نامعتبر true_sale → normalize به true
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta' => ['true_sale' => 'banana'],
];
ProductMetaboxes::save(60, $post);
check(($GLOBALS['__wp_post_meta'][60]['true_sale'] ?? '') === 'true', 'مقدار نامعتبر → true (قابل خرید پیش‌فرض)');

// ستون وضعیت دو حالت
ob_start();
ProductMetaboxes::column_content('es-sale-mode', 60);
check(str_contains((string) ob_get_clean(), 'آنلاین'), 'ستون: حالت آنلاین');
$GLOBALS['__wp_post_meta'][60]['true_sale'] = 'false';
ob_start();
ProductMetaboxes::column_content('es-sale-mode', 60);
check(str_contains((string) ob_get_clean(), 'استعلامی'), 'ستون: حالت استعلامی');

// هلپر purchasable (وضعیت را به true برمی‌گردانیم — override تست قبل)
$GLOBALS['__wp_post_meta'][60]['true_sale'] = 'true';
check(\ErfanSanat\Theme\Frontend\Purchasable::is_online(60) === true, 'es_product_is_purchasable: true_sale=true → true');
$GLOBALS['__wp_post_meta'][60]['true_sale'] = 'false';
check(\ErfanSanat\Theme\Frontend\Purchasable::is_online(60) === false, 'es_product_is_purchasable: false → استعلامی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
