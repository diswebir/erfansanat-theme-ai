<?php
/**
 * Test 3.7 — موتور Repeater دو-مد: حفظ دقیق فرمت JSON/serial/array + ترتیب + ترم‌متا
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-07.php
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
echo "== Test 3.7: موتور Repeater سازگار JetEngine ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
require $root . '/functions.php';

use ErfanSanat\Theme\Data\RepeaterEngine;

// ---- fixture JSON (ساختار فعلی دیتابیس زنده) ----
$json_raw = '{"item-0":{"name_question":"گارانتی دارید؟","answer_question":"۲۴ ماهه"},"item-1":{"name_question":"ارسال دارید؟","answer_question":"بله"}}';
$parsed = RepeaterEngine::parse($json_raw);
check($parsed['format'] === RepeaterEngine::FORMAT_JSON, 'فرمت JSON تشخیص داده شد');
check(count($parsed['rows']) === 2, 'دو ردیف از JSON');
check($parsed['rows'][0]['name_question'] === 'گارانتی دارید؟', 'محتوای ردیف ۰');
check($parsed['rows'][1]['answer_question'] === 'بله', 'محتوای ردیف ۱');

// نوشتن مجدد با فرمت JSON → باید JSON بماند (نه serialize!)
RepeaterEngine::write(10, 'faq', $parsed['rows'], $parsed['format']);
$stored = $GLOBALS['__wp_post_meta'][10]['faq'];
check(is_string($stored), 'ذخیره JSON به‌صورت رشته');
$decoded = json_decode((string) $stored, true);
check(is_array($decoded) && isset($decoded['item-0']['name_question']), 'ساختار item-N در JSON حفظ شد');
check(str_starts_with((string) $stored, '{') && !str_contains((string) $stored, 's:3:"faq"'), 'بازنویسی با همان فرمت JSON (نه سریالایز)');

// ---- fixture سریالایز ----
$serial_raw = serialize(['item-0' => ['add-new-feature' => 'ولتاژ ۱۲ ولت'], 'item-1' => ['add-new-feature' => 'IP68']]);
$parsed_s = RepeaterEngine::parse($serial_raw);
check($parsed_s['format'] === RepeaterEngine::FORMAT_SERIAL, 'فرمت serial تشخیص داده شد');
check(count($parsed_s['rows']) === 2, 'دو ردیف از serial');

RepeaterEngine::write(11, 'product-feature', $parsed_s['rows'], $parsed_s['format']);
$stored_s = $GLOBALS['__wp_post_meta'][11]['product-feature'];
check(is_string($stored_s) && str_starts_with((string) $stored_s, 'a:2:{'), 'بازنویسی سریالایز بماند');
$un = unserialize((string) $stored_s, ['allowed_classes' => false]);
check(isset($un['item-1']['add-new-feature']), 'کلیدهای item-N در سریالایز');

// ---- fixture آرایه خام ----
$array_raw = ['item-0' => ['add-new-usage' => 'نورپردازی نما'], 'item-1' => ['add-new-usage' => 'تونل']];
$parsed_a = RepeaterEngine::parse($array_raw);
check($parsed_a['format'] === RepeaterEngine::FORMAT_ARRAY, 'فرمت آرایه تشخیص داده شد');
RepeaterEngine::write(12, 'usage_product', $parsed_a['rows'], $parsed_a['format']);
check(is_array($GLOBALS['__wp_post_meta'][12]['usage_product']), 'بازنویسی آرایه بماند');

// ---- مقادیر خالی/نامعتبر ----
check(RepeaterEngine::parse('')['format'] === RepeaterEngine::FORMAT_EMPTY, 'رشته خالی → empty');
check(RepeaterEngine::parse(null)['rows'] === [], 'null → بدون ردیف');
check(RepeaterEngine::parse('not json nor serial')['format'] === RepeaterEngine::FORMAT_EMPTY, 'رشته نامعتبر → empty');
check(RepeaterEngine::parse('a:1:{broken')['format'] === RepeaterEngine::FORMAT_EMPTY, 'سریالایز خراب → empty');

// ---- ترتیب ردیف‌ها (مهم: جابه‌جایی ترتیب در UI) ----
$reordered = RepeaterEngine::parse($json_raw)['rows'];
$reordered = array_reverse($reordered);
$assoc = RepeaterEngine::to_assoc($reordered);
check(array_keys($assoc) === ['item-0', 'item-1'], 'بازچینش item-0/item-1 پس از جابه‌جایی');
check($assoc['item-0']['name_question'] === 'ارسال دارید؟', 'ترتیب جدید حفظ شد');

// ---- اسکالر ردیف ----
$scalar_rows = RepeaterEngine::parse('{"item-0":"متن ساده"}');
check($scalar_rows['rows'][0]['value'] === 'متن ساده', 'ردیف اسکالر در کانتینر value');

// ---- ترم‌متا ----
$GLOBALS['__wp_term_meta'][5]['faq'] = $json_raw;
$parsed_t = RepeaterEngine::read_term(5, 'faq');
check($parsed_t['format'] === RepeaterEngine::FORMAT_JSON && count($parsed_t['rows']) === 2, 'read_term با fixture JSON');
RepeaterEngine::write_term(5, 'faq', [['name_question' => 'ق۱', 'answer_question' => 'ج۱']], RepeaterEngine::FORMAT_JSON);
$term_stored = $GLOBALS['__wp_term_meta'][5]['faq'];
check(is_string($term_stored) && str_contains((string) $term_stored, 'ق۱'), 'write_term با فرمت JSON');

// ---- پیش‌فرض write بدون فرمت → آرایه ----
RepeaterEngine::write(13, 'x', [['a' => 'b']]);
check(is_array($GLOBALS['__wp_post_meta'][13]['x']) && isset($GLOBALS['__wp_post_meta'][13]['x']['item-0']), 'پیش‌فرض write = آرایه با کلیدهای item-N');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
