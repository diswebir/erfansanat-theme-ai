<?php
/**
 * Test 3.2 — فیلترهای ادمین پروژه‌ها: دراپ‌داون‌ها، اعمال tax_query، ستون‌ها و مرتب‌سازی hreflang
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-02.php
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
echo "== Test 3.2: فیلترهای ادمین لیست پروژه‌ها ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Data\AdminListFilters;

check(class_exists(AdminListFilters::class), 'کلاس AdminListFilters resolve شد');

// هوک‌های ادمین فقط در ادمین ثبت شده‌اند
$has_restrict = false;
foreach ($GLOBALS['__wp_hooks']['actions']['restrict_manage_posts'] ?? [] as $entry) {
    if (is_array($entry['cb']) && $entry['cb'] === [AdminListFilters::class, 'render_filters']) {
        $has_restrict = true;
    }
}
check($has_restrict, 'هوک restrict_manage_posts → render_filters');

// دراپ‌داون‌ها: برای projects رندر می‌شود، برای post نه
$GLOBALS['__es_stub_terms'] = [
    'city'         => [
        (object) ['term_id' => 1, 'name' => 'اصفهان', 'slug' => 'esfahan'],
        (object) ['term_id' => 2, 'name' => 'قم', 'slug' => 'qom'],
    ],
    'project_type' => [
        (object) ['term_id' => 10, 'name' => 'تونل نوری', 'slug' => 'light-tunnel'],
    ],
];
$_GET = [];
ob_start();
AdminListFilters::render_filters('projects');
$out = (string) ob_get_clean();
check(substr_count($out, '<select') === 2, 'دو دراپ‌داون (city + project_type)');
check(str_contains($out, 'name="city"') && str_contains($out, 'name="project_type"'), 'نام فیلترهای GET درست');
check(str_contains($out, '>اصفهان</option>') && str_contains($out, 'value="light-tunnel"'), 'ترم‌ها با نام/اسلاگ');

ob_start();
AdminListFilters::render_filters('post');
check(trim((string) ob_get_clean()) === '', 'برای post type دیگر رندر نمی‌شود');

// انتخاب فعلی
$_GET = ['city' => 'qom'];
ob_start();
AdminListFilters::render_filters('projects');
$out2 = (string) ob_get_clean();
check(str_contains($out2, 'value="qom" selected'), 'گزینه انتخاب‌شده فعلی selected');

// اعمال فیلتر روی کوئری
$make_query = static function (array $vars = []) {
    return new class ($vars) {
        public array $query_vars;
        public function __construct(array $vars) { $this->query_vars = $vars; }
        public function get(string $key): mixed { return $this->query_vars[$key] ?? ''; }
        public function set(string $key, mixed $val): void { $this->query_vars[$key] = $val; }
    };
};
$query = $make_query(['post_type' => 'projects']);
$_GET = ['city' => 'qom', 'project_type' => 'light-tunnel', 'evil' => 'x'];
AdminListFilters::apply_tax_filter($query);
$tq = $query->query_vars['tax_query'] ?? [];
check(count($tq) === 2, 'دو فیلتر تکسونومی به tax_query اعمال شد');
check($tq[0]['terms'] === 'qom' && $tq[1]['taxonomy'] === 'project_type', 'مقادیر فیلتر صحیح (sanitize شده)');

// بدون فیلتر → tax_query تنظیم نمی‌شود
$query2 = $make_query(['post_type' => 'projects']);
$_GET = [];
AdminListFilters::apply_tax_filter($query2);
check(!isset($query2->query_vars['tax_query']), 'بدون GET، tax_query دست‌نخورده');

// ستون‌ها
$cols = AdminListFilters::columns(['cb' => '<input>', 'title' => 'عنوان', 'date' => 'تاریخ']);
check(isset($cols['es-city'], $cols['es-project_type'], $cols['es-hrefkey']), 'سه ستون جدید بعد از title');
$sortable = AdminListFilters::sortable_columns($cols);
check(($sortable['es-hrefkey'] ?? '') === 'same_key_hrefl', 'ستون hreflang قابل مرتب‌سازی (orderby=same_key_hrefl)');

// محتوای ستون‌ها
$GLOBALS['__es_stub_terms'] = [];
$GLOBALS['__es_stub_post_term_map'][9] = ['city' => [(object) ['name' => 'اصفهان']], 'project_type' => []];
$GLOBALS['__wp_post_meta'][9]['same_key_hrefl'] = 'abc-123';
ob_start();
AdminListFilters::column_content('es-city', 9);
$out3 = (string) ob_get_clean();
check(str_contains($out3, 'اصفهان'), 'ستون محل پروژه: نام ترم');
ob_start();
AdminListFilters::column_content('es-hrefkey', 9);
$out4 = (string) ob_get_clean();
check(str_contains($out4, '<code>abc-123</code>'), 'ستون hreflang: مقدار متا با code');
ob_start();
AdminListFilters::column_content('es-project_type', 9);
$out5 = (string) ob_get_clean();
check(trim($out5) === '—', 'ستون خالی: خط تیره');

// orderby در pre_get_posts
$query3 = $make_query(['orderby' => 'same_key_hrefl']);
AdminListFilters::orderby_meta($query3);
check(($query3->query_vars['meta_key'] ?? '') === 'same_key_hrefl' && ($query3->query_vars['orderby'] ?? '') === 'meta_value', 'orderby=meta_value با meta_key=same_key_hrefl');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
