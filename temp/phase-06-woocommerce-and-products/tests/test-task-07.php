<?php
/**
 * Test 6.7 — آرشیو فروشگاه: فیلترها (args)، صفحه‌بندی میرور، حالت خالی، per-page
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-07.php
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
echo "== Test 6.7: shop archive ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

$mirror = (string) file_get_contents($root . '/uploads/static-mirror/shop.html');
$aside0 = strpos($mirror, '<aside', 139000);
$aside1 = strpos($mirror, '</aside>', $aside0) + 8;
$ASIDE_REF = substr($mirror, $aside0, $aside1 - $aside0);
$ct0 = strpos($ASIDE_REF, '<div class="space-y-1"><a href="product_cat/point-light.html"');
$ct1 = strpos($ASIDE_REF, '</nav>');
$CAT_TREE_REF = substr($ASIDE_REF, $ct0, $ct1 - $ct0);
$pagi0 = strpos($mirror, '<nav aria-label="صفحه‌بندی محصولات"');
$pagi1 = strpos($mirror, '</nav>', $pagi0) + 6;
$PAGI_REF = substr($mirror, $pagi0, $pagi1 - $pagi0);

/* ---------- ۱) aside فیلترها بایت‌دقیق (حالت پیش‌فرض) ---------- */
$GLOBALS['__wp_post_meta'] = [];
ob_start();
es_get_template('template-parts/archive/product-filters.php', [
    'total' => 20, 'search' => '', 'mode' => 'all',
    'price_label' => 'همه قیمت‌ها', 'price_value' => '15000000',
    'ip_options' => '<option value="IP68">IP68 (ضد غوطه‌وری)</option><option value="IP67">IP67 (ضدآب کامل)</option><option value="IP65">IP65 (فضای باز)</option>',
    'volt_options' => '<option value="24V">۲۴ ولت DC (ایمن)</option><option value="220V">۲۲۰ ولت شهری</option>',
    'cat_tree' => $CAT_TREE_REF,
]);
$aside = ob_get_clean();
check($aside === $ASIDE_REF, 'بایت‌دقیق: سایدبار فیلترها در حالت پیش‌فرض = میرور');

/* حالت online فعال + جستجو + قیمت */
ob_start();
es_get_template('template-parts/archive/product-filters.php', [
    'total' => 7, 'search' => 'ریسه', 'mode' => 'online',
    'price_label' => 'سقف: ۵ میلیون تومان', 'price_value' => '5000000',
    'ip_options' => '<option value="IP68" selected="">IP68 (ضد غوطه‌وری)</option>',
    'volt_options' => '', 'cat_tree' => '',
]);
$aside2 = ob_get_clean();
check(str_contains($aside2, 'data-product-purchase-mode="online" class="rounded-lg py-2 text-[11.5px] font-extrabold transition-all cursor-pointer bg-emerald-700 text-white shadow-2xs"'), 'فیلتر: حالت online فعال (کلاس میرور)');
check(str_contains($aside2, 'value="ریسه"'), 'فیلتر: مقدار جستجو حفظ شد');
check(str_contains($aside2, '>سقف: ۵ میلیون تومان</span>') && str_contains($aside2, 'value="5000000"'), 'فیلتر: برچسب/مقدار قیمت');
check(str_contains($aside2, '<option value="IP68" selected="">'), 'فیلتر: option انتخابی IP');

/* ---------- ۲) pagination بایت‌دقیق (صفحه ۱ از ۳، ۲۰ محصول) ---------- */
ob_start();
es_get_template('template-parts/archive/pagination.php', [
    'current' => 1, 'total_pages' => 3, 'total_products' => 20,
    'base_url' => 'https://test.example/shop/',
]);
$pagi = ob_get_clean();
$ref_p = str_replace('disabled:opacity-40 cursor-pointer"><span>بعدی</span>',
    'disabled:opacity-40 cursor-pointer" data-href="https://test.example/shop/?paged=2"><span>بعدی</span>', $PAGI_REF);
$ref_p = str_replace([
    '<button type="button" data-page-num="2" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer border border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-800">۲</button>',
    '<button type="button" data-page-num="3" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer border border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-800">۳</button>',
], [
    '<a href="https://test.example/shop/?paged=2" data-page-num="2" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer border border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-800">۲</a>',
    '<a href="https://test.example/shop/?paged=3" data-page-num="3" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer border border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-800">۳</a>',
], $ref_p);
check($pagi === $ref_p, 'بایت‌دقیق: نوار صفحه‌بندی (صفحه ۱ فعال، ۲/۳ لینک میرور)');

ob_start();
es_get_template('template-parts/archive/pagination.php', [
    'current' => 2, 'total_pages' => 3, 'total_products' => 20,
    'base_url' => 'https://test.example/shop/',
]);
$pagi2 = ob_get_clean();
check(str_contains($pagi2, 'data-href="https://test.example/shop/"') && !str_contains($pagi2, 'data-page-prev="true" disabled=""'), 'صفحه‌بندی: صفحه ۲ → قبلی فعال');
check(str_contains($pagi2, 'data-pagination-current="true" class="font-black text-emerald-800">۲</span>'), 'صفحه‌بندی: شمارنده صفحه فعلی ۲');

ob_start();
es_get_template('template-parts/archive/pagination.php', ['current' => 1, 'total_pages' => 1, 'total_products' => 3, 'base_url' => 'x']);
$pagi_one = ob_get_clean();
check($pagi_one === '', 'صفحه‌بندی: تک‌صفحه‌ای → بدون نوار');

/* ---------- ۳) args کوئری ShopFilters ---------- */
$SF = \ErfanSanat\Theme\Woo\ShopFilters::class;
$_GET = ['purchase' => 'online', 'price' => '5000000', 'ip' => 'IP68', 'voltage' => '24V', 'sort' => 'popular', 's' => 'ریسه'];
$args = $SF::query_args();
check(($args['s'] ?? '') === 'ریسه', 'args: جستجو');
$mq = $args['meta_query'] ?? [];
check(in_array(['key' => 'true_sale', 'value' => 'false', 'compare' => '!='], $mq, true) !== false, 'args: purchase=online → true_sale != false');
check(in_array(['key' => '_price', 'value' => 5000000, 'type' => 'numeric', 'compare' => '<='], $mq, true) !== false, 'args: سقف قیمت عددی');
$tq = $args['tax_query'] ?? [];
check(in_array(['taxonomy' => 'pa_ip-rating', 'field' => 'slug', 'terms' => 'IP68'], $tq, true) !== false, 'args: فیلتر IP (pa_ip-rating)');
check(in_array(['taxonomy' => 'pa_input-voltage', 'field' => 'slug', 'terms' => '24V'], $tq, true) !== false, 'args: فیلتر ولتاژ (pa_input-voltage)');
check(($args['meta_key'] ?? '') === 'number2-viewc' && ($args['orderby'] ?? '') === 'meta_value_num' && ($args['order'] ?? '') === 'DESC', 'args: مرتب‌سازی پربازدید');

$_GET = ['sort' => 'price-asc'];
$args2 = $SF::query_args();
check(($args2['meta_key'] ?? '') === '_price' && ($args2['order'] ?? '') === 'ASC', 'args: مرتب‌سازی ارزان‌ترین');

$_GET = ['sort' => 'x"><script>', 'price' => 'abc', 'ip' => 'all'];
$args3 = $SF::query_args();
check(!isset($args3['meta_key']) && !isset($args3['tax_query']) && !isset($args3['meta_query']), 'args: ورودی نامعتبر امن رد شد (no tax/meta)');

/* ---------- ۴) اعمال pre_get_posts + per-page از آپشن ---------- */
class ES_FakeQuery {
    public array $vars = [];
    public function get(string $k): mixed { return $this->vars[$k] ?? null; }
    public function set(string $k, mixed $v): void { $this->vars[$k] = $v; }
    public function is_main_query(): bool { return true; }
}
$GLOBALS['__wp_is_admin'] = false;
$GLOBALS['__wp_is_post_type_archive'] = true;
$GLOBALS['__wp_post_type_archive'] = 'product';
es_option_reset_cache();
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['archives' => ['products_per_page' => '9']];
$q = new ES_FakeQuery();
$_GET = [];
$SF::apply($q);
check((int) ($q->vars['posts_per_page'] ?? 0) === 9, 'per-page: از آپشن archives.products_per_page=9');

es_option_reset_cache();
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['archives' => ['products_per_page' => '500']];
$q2 = new ES_FakeQuery();
$SF::apply($q2);
check((int) ($q2->vars['posts_per_page'] ?? 0) === 100, 'per-page: کلومپ به سقف ۱۰۰');

// فیلترهای GET روی کوئری واقعی
es_option_reset_cache();
$_GET = ['purchase' => 'inquiry', 'sort' => 'price-desc'];
$q3 = new ES_FakeQuery();
$SF::apply($q3);
$mq3 = (array) ($q3->vars['meta_query'] ?? []);
$has_inquiry = false;
foreach ($mq3 as $m) {
    if (($m['key'] ?? '') === 'true_sale' && ($m['value'] ?? '') === 'false') { $has_inquiry = true; }
}
check($has_inquiry, 'apply: purchase=inquiry → meta true_sale=false روی کوئری');
check(($q3->vars['meta_key'] ?? '') === '_price' && ($q3->vars['order'] ?? '') === 'DESC', 'apply: sort=price-desc روی کوئری');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
