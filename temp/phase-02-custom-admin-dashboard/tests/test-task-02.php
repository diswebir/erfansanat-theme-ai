<?php
/**
 * Test 2.2 — FieldRenderer (۹ نوع) + Sanitizer (پاکسازی هر نوع + repeater بازگشتی + کلید ناشناخته)
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-task-02.php
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
echo "== Test 2.2: FieldRenderer و Sanitizer ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\FieldRenderer;
use ErfanSanat\Theme\Admin\Sanitizer;

// ---- رندر هر ۹ نوع ----
$text = FieldRenderer::render(['key' => 'phone', 'type' => 'text', 'label' => 'تلفن', 'help' => 'راهنما', 'placeholder' => '۰۳۱-…', 'value' => '031-91091011']);
check(str_contains($text, 'name="opt[phone]"') && str_contains($text, 'value="031-91091011"'), 'text: نام/مقدار صحیح');
check(str_contains($text, 'placeholder="۰۳۱-…"') && str_contains($text, 'راهنما'), 'text: placeholder و help');

$url = FieldRenderer::render(['key' => 'map', 'type' => 'url', 'value' => 'https://neshan.org/maps/x']);
check(str_contains($url, 'type="url"') && str_contains($url, 'https://neshan.org/maps/x'), 'url: رندر شد');

$num = FieldRenderer::render(['key' => 'per_page', 'type' => 'number', 'value' => '20', 'min' => 1, 'max' => 99]);
check(str_contains($num, 'type="number"') && str_contains($num, 'min="1"') && str_contains($num, 'max="99"'), 'number: min/max');

$textarea = FieldRenderer::render(['key' => 'about', 'type' => 'textarea', 'value' => "خط اول\nخط دوم"]);
check(str_contains($textarea, '<textarea') && str_contains($textarea, 'خط دوم</textarea>'), 'textarea: رندر شد');

$color = FieldRenderer::render(['key' => 'brand_primary', 'type' => 'color', 'value' => '#059669']);
check(str_contains($color, 'type="color"') && str_contains($color, 'value="#059669"'), 'color: مقدار هگز');

$switch_on  = FieldRenderer::render(['key' => 'sticky', 'type' => 'switch', 'value' => '1']);
$switch_off = FieldRenderer::render(['key' => 'sticky', 'type' => 'switch', 'value' => '0']);
check(str_contains($switch_on, 'checked') && str_contains($switch_on, 'value="0"'), 'switch روشن: checked + hidden صفر');
check(!str_contains($switch_off, ' checked') && str_contains($switch_off, 'value="0"'), 'switch خاموش: بدون checked');

$select = FieldRenderer::render(['key' => 'cols', 'type' => 'select', 'options' => ['3' => 'سه', '4' => 'چهار'], 'value' => '4']);
check(str_contains($select, '<option value="3"') && str_contains($select, 'value="4" selected'), 'select: گزینه‌ها + selected');

$GLOBALS['__es_stub_attachment_urls'][123] = 'https://test.example/wp-content/uploads/logo.png';
$media = FieldRenderer::render(['key' => 'logo', 'type' => 'media', 'value' => '123']);
check(str_contains($media, 'data-es-media-value') && str_contains($media, 'value="123"'), 'media: مقدار آیدی');
check(str_contains($media, 'src="https://test.example/wp-content/uploads/logo.png"'), 'media: پیش‌نمایش از wp_get_attachment_url');

$repeater = FieldRenderer::render([
    'key' => 'faq', 'type' => 'repeater', 'add_label' => 'افزودن سوال',
    'fields' => [
        ['key' => 'name_question', 'type' => 'text', 'label' => 'سوال'],
        ['key' => 'answer_question', 'type' => 'textarea', 'label' => 'پاسخ'],
    ],
    'value' => [
        ['name_question' => 'گارانتی دارید؟', 'answer_question' => '<b>۲۴ ماهه</b><script>bad()</script>'],
        ['name_question' => 'ارسال دارید؟', 'answer_question' => 'بله'],
    ],
]);
check(str_contains($repeater, 'name="opt[faq][0][name_question]"'), 'repeater: نام nesting ردیف ۰');
check(str_contains($repeater, 'name="opt[faq][1][answer_question]"'), 'repeater: نام nesting ردیف ۱');
check(str_contains($repeater, 'data-es-repeater-add'), 'repeater: دکمه افزودن');
check(str_contains($repeater, 'opt[faq][1][answer_question]'), 'repeater: نام ردیف ۱');

// escaping خروجی
$xss = FieldRenderer::render(['key' => 't', 'type' => 'text', 'value' => '"><script>alert(1)</script>']);
check(!str_contains($xss, '<script>') && str_contains($xss, '&quot;&gt;'), 'escaping: ورودی خطرناک escape شد');

// ---- Sanitizer ----
check(Sanitizer::field(['type' => 'text'], '031<script>x</script>910') === '031x910', 'sanitize text: تگ حذف شد (رفتار strip_tags وردپرس — محتوا می‌ماند)');
check(Sanitizer::field(['type' => 'textarea'], '<b>سالم</b><script>bad()</script>') === '<b>سالم</b>bad()', 'sanitize textarea: تگ script حذف، تگ مجاز b ماند (رفتار kses)');
check(Sanitizer::field(['type' => 'url'], 'https://ok.example/a?b=1') === 'https://ok.example/a?b=1', 'sanitize url: آدرس سالم');
check(Sanitizer::field(['type' => 'url'], 'javascript:alert(1)') === '', 'sanitize url: javascript: خنثی');
check(Sanitizer::field(['type' => 'number', 'min' => 1, 'max' => 99], '250') === 99, 'sanitize number: clamp به max');
check(Sanitizer::field(['type' => 'number', 'min' => 1], '-5') === 1, 'sanitize number: clamp به min');
check(Sanitizer::field(['type' => 'color'], '#059669') === '#059669', 'sanitize color: هگز معتبر');
check(Sanitizer::field(['type' => 'color'], 'red') === '', 'sanitize color: نام رنگ نامعتبر → خالی');
check(Sanitizer::field(['type' => 'switch'], '1') === 1 && Sanitizer::field(['type' => 'switch'], '0') === 0, 'sanitize switch: ۱/۰ عددی');
check(Sanitizer::field(['type' => 'select', 'options' => ['a' => 'A', 'b' => 'B'], 'default' => 'a'], 'c') === 'a', 'sanitize select: خارج whitelist → پیش‌فرض');
check(Sanitizer::field(['type' => 'select', 'options' => ['a' => 'A']], 'a') === 'a', 'sanitize select: مقدار مجاز');
check(Sanitizer::field(['type' => 'media'], '123') === 123, 'sanitize media: عدد → int آیدی');
check(Sanitizer::field(['type' => 'media'], 'javascript:evil()') === '', 'sanitize media: URL مخرب → خالی');
check(Sanitizer::field(['type' => 'media'], 'https://test.example/cat.pdf') === 'https://test.example/cat.pdf', 'sanitize media: URL فایل مجاز');

// repeater بازگشتی + حذف ردیف خالی + زیرکلید ناشناخته
$faq_field = [
    'type' => 'repeater',
    'fields' => [
        ['key' => 'name_question', 'type' => 'text'],
        ['key' => 'answer_question', 'type' => 'textarea'],
    ],
];
$clean = Sanitizer::field($faq_field, [
    'item-0' => ['name_question' => 'س۱', 'answer_question' => 'ج۱', 'evil' => 'x'],
    'item-1' => ['name_question' => '', 'answer_question' => ''],
    'item-2' => ['name_question' => 'س۲', 'answer_question' => 'ج۲'],
]);
check(count($clean) === 2, 'sanitize repeater: ردیف خالی حذف شد (۲ ردیف باقی)');
check($clean[0]['name_question'] === 'س۱' && !isset($clean[0]['evil']), 'sanitize repeater: زیرکلید ناشناخته حذف، داده سالم');
check(array_keys($clean) === [0, 1], 'sanitize repeater: reindex عددی');

// sanitize_fields — فقط کلیدهای اسکیم
$fields = [
    ['key' => 'phone', 'type' => 'text'],
    ['key' => 'logo', 'type' => 'media'],
];
$out = Sanitizer::sanitize_fields($fields, ['phone' => 'ok', 'hacked' => 'x']);
check(isset($out['phone']) && !isset($out['hacked']), 'sanitize_fields: کلید خارج اسکیم دور ریخته شد');

// اسکریپت سفارشی — بدون مجوز حفظ مقدار قبلی
$GLOBALS['__wp_current_user_can'] = false;
$custom = Sanitizer::sanitize_custom_scripts(['head_js' => '<script>new</script>'], ['head_js' => '<script>old</script>']);
check($custom['head_js'] === '<script>old</script>', 'sanitize_custom_scripts: بدون unfiltered_html → مقدار قبلی حفظ شد');
$GLOBALS['__wp_current_user_can'] = true;
$custom2 = Sanitizer::sanitize_custom_scripts(['head_js' => "console.log('x');"], []);
check($custom2['head_js'] === "console.log('x');", 'sanitize_custom_scripts: با مجوز → کد جدید ذخیره شد');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
