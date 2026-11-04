<?php
/**
 * Test 4.8 — تست جامع DOM: رندر هدر/سایدبار/فوتر با داده fixture و مقایسه ساختاری با refهای میرور
 * (DOMDocument — شمارش data-hookها و کلاس‌های کلیدی؛ اختلاف مجاز فقط مقادیر داینامیک)
 * اجرا: /home/user/tools/php -f temp/phase-04-header-footer-navigation/tests/test-task-08.php
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
echo "== Test 4.8: مقایسه ساختاری DOM ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

/** شمارش data-* و کلاس‌های یک HTML با DOMDocument */
function dom_inventory(string $html): array {
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<!doctype html><html><body>' . $html . '</body></html>');
    libxml_clear_errors();
    $data = [];
    $classes = [];
    foreach (iterable($doc->getElementsByTagName('*')) as $el) {
        foreach ($el->attributes ?? [] as $attr) {
            $name = (string) $attr->name;
            if (str_starts_with($name, 'data-')) {
                $data[$name] = ($data[$name] ?? 0) + 1;
            }
            if ($name === 'class') {
                foreach (preg_split('/\s+/', (string) $attr->value) as $c) {
                    if ($c !== '') {
                        $classes[$c] = ($classes[$c] ?? 0) + 1;
                    }
                }
            }
        }
    }
    return ['data' => $data, 'classes' => $classes];
}

function iterable($node_list): \Generator {
    foreach ($node_list as $node) {
        yield $node;
    }
}

/** هوک‌های JS سراسری که باید در ref و خروجی یکی باشند */
function hook_keys(array $data): array {
    $out = [];
    foreach ($data as $k => $v) {
        if ($v > 0) {
            $out[$k] = $v;
        }
    }
    return $out;
}

// ═══ داده fixture (تاپ‌لول product_cat + type/city + CPT) ═══
$GLOBALS['__wp_options'] = [];
es_option_reset_cache();
$GLOBALS['__es_stub_terms'] = [
    'product_cat'  => [
        (object) ['term_id' => 1, 'name' => 'پوینت لایت', 'slug' => 'point-light', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 2, 'name' => 'ریسه LED', 'slug' => 'strings', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 3, 'name' => 'درخت نوری', 'slug' => 'light-tree', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 4, 'name' => 'والواشر', 'slug' => 'wall-washer', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 5, 'name' => 'باکس', 'slug' => 'control-box', 'taxonomy' => 'product_cat', 'parent' => 1],
    ],
    'project_type' => [
        (object) ['term_id' => 10, 'name' => 'نورپردازی میدان', 'slug' => 'square-lighting', 'taxonomy' => 'project_type', 'parent' => 0],
        (object) ['term_id' => 11, 'name' => 'تونل نوری', 'slug' => 'light-tunnel', 'taxonomy' => 'project_type', 'parent' => 0],
    ],
    'city'         => [
        (object) ['term_id' => 20, 'name' => 'اصفهان', 'slug' => 'esfahan', 'taxonomy' => 'city', 'parent' => 0],
    ],
];
$GLOBALS['__es_stub_posts'] = [(object) ['ID' => 90, 'post_type' => 'services', 'post_title' => 'نورپردازی پل']];
$GLOBALS['__es_stub_post_counts'] = ['product' => 20];

// ═══ رندر هر سه بلوک ═══
ob_start();
es_get_template('template-parts/header/header-top.php');
$header_html = (string) ob_get_clean();
ob_start();
es_get_template('template-parts/header/mobile-sidebar.php');
$sidebar_html = (string) ob_get_clean();
ob_start();
es_get_template('template-parts/footer/footer-main.php');
$footer_html = (string) ob_get_clean();

$rendered = dom_inventory($header_html . $sidebar_html . $footer_html);
$ref = dom_inventory(
    (string) file_get_contents($root . '/temp/phase-04-header-footer-navigation/refs/header-ref.html')
    . (string) file_get_contents($root . '/temp/phase-04-header-footer-navigation/refs/mobile-sidebar-ref.html')
    . (string) file_get_contents($root . '/temp/phase-04-header-footer-navigation/refs/footer-ref.html')
);

// ═══ ۱) همه data-hookهای ref در خروجی باشند (شمارش‌های ساختاری برابر) ═══
$structural = [
    'data-navbar-header', 'data-desktop-mega-trigger', 'data-desktop-mega-panel',
    'data-mega-prod-tab', 'data-mega-prod-panel', 'data-mega-proj-tab', 'data-mega-proj-panel',
    'data-search-modal-open', 'data-cart-modal-open', 'data-header-cart-badge', 'data-mobile-toggle',
    'data-search-modal', 'data-global-search-input', 'data-search-modal-close',
    'data-mobile-menu', 'data-mobile-backdrop', 'data-mobile-sidebar', 'data-mobile-close',
    'data-mobile-acc-btn', 'data-mobile-acc-icon', 'data-mobile-acc-panel',
    'data-mobile-prod-tab', 'data-mobile-prod-panel', 'data-mobile-proj-tab', 'data-mobile-proj-panel',
    'data-scroll-top',
];
foreach ($structural as $hook) {
    $rc = $ref['data'][$hook] ?? 0;
    $dc = $rendered['data'][$hook] ?? 0;
    check($rc > 0 && $rc === $dc, "{$hook}: ref={$rc} / rendered={$dc}");
}

// ═══ ۲) کلاس‌های کلیدی میرور (structural) در خروجی ═══
$key_classes = [
    'fixed', 'inset-x-0', 'top-0', 'z-50',                        // هدر sticky
    'group', 'static',                                            // مگا گروه‌ها
    'lg:col-span-4', 'lg:col-span-2', 'lg:col-span-3',           // ستون‌های فوتر
    'w-[88vw]', 'max-w-[380px]',                                  // سایدبار
    'grid-cols-12',                                               // گرید مگا
];
foreach ($key_classes as $class) {
    check(($ref['classes'][$class] ?? 0) > 0 && ($ref['classes'][$class] ?? 0) === ($rendered['classes'][$class] ?? 0), "کلاس {$class}");
}

// ═══ ۳) حالت بدون منو — ساختار ref حفظ شود ═══
$GLOBALS['__es_stub_terms'] = [];
$GLOBALS['__es_stub_posts'] = [];
$GLOBALS['__es_stub_post_counts'] = [];
ob_start();
es_get_template('template-parts/header/header-top.php');
$header_empty = (string) ob_get_clean();
ob_start();
es_get_template('template-parts/header/mobile-sidebar.php');
$sidebar_empty = (string) ob_get_clean();
ob_start();
es_get_template('template-parts/footer/footer-main.php');
$footer_empty = (string) ob_get_clean();
$empty_inv = dom_inventory($header_empty . $sidebar_empty . $footer_empty);
foreach (['data-navbar-header', 'data-desktop-mega-trigger', 'data-mobile-menu', 'data-scroll-top', 'data-search-modal'] as $hook) {
    check(($empty_inv['data'][$hook] ?? 0) === ($ref['data'][$hook] ?? 0), "بدون داده — {$hook} حفظ");
}
check(!str_contains($header_empty . $sidebar_empty . $footer_empty, '{ES_'), 'حالت بدون داده — بدون placeholder');

// ═══ ۴) حالت با آپشن سفارشی (CTA/اعلان/کپی‌رایت) — ساختار ثابت ═══
$GLOBALS['__wp_options']['erfansanat_theme_options'] = [
    'header' => ['cta_text' => 'تماس مستقیم', 'cta_url' => 'https://erfansanat.com/contact/', 'ann_enabled' => 1, 'ann_text' => 'x', 'ann_url' => '', 'ann_bg' => '#065f46'],
    'footer' => ['copyright' => 'حقوق سفارشی'],
];
es_option_reset_cache();
ob_start();
es_get_template('template-parts/header/header-top.php');
$header_opt = (string) ob_get_clean();
ob_start();
es_get_template('template-parts/footer/footer-main.php');
$footer_opt = (string) ob_get_clean();
$opt_inv = dom_inventory($header_opt . $footer_opt);
check(($opt_inv['data']['data-navbar-header'] ?? 0) === ($ref['data']['data-navbar-header'] ?? 0), 'با آپشن — ساختار هدر ثابت');
check(str_contains($footer_opt, 'حقوق سفارشی') && str_contains($header_opt, 'تماس مستقیم'), 'مقادیر آپشن اعمال');
check(str_contains($header_opt, 'data-announcement-bar'), 'نوار اعلان روشن (فقط lg:hidden نیست — بلوک مستقل)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
