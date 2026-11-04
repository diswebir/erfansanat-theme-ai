<?php
/**
 * Test 4.4 — MegaMenu: بازسازی byte-exact فرگمنت‌ها + رندر داینامیک تاکسونومی/منو + حالت‌ها
 * اجرا: /home/user/tools/php -f temp/phase-04-header-footer-navigation/tests/test-task-04.php
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
echo "== Test 4.4: MegaMenuWalker/مگامنو ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Nav\MegaMenu;

$ref = (string) file_get_contents($root . '/temp/phase-04-header-footer-navigation/refs/header-ref.html');

// ═══ ۱) بازسازی byte-exact: فرگمنت + مقادیر میرور = بلوک مرجع ═══
$R = new ReflectionClass(MegaMenu::class);
frag_check($R, 'F_P_HEAD', ['{ES_TOTAL}' => '۱۵'], $ref);
frag_check($R, 'F_P_BOTTOM', ['{ES_TOTAL}' => '۱۵', '{ES_SHOP_Q}' => 'shop.html?purchasable=true', '{ES_SHOP}' => 'shop.html', '{ES_PRODUCTS_TOTAL}' => '۲۰'], $ref);

function frag_check(ReflectionClass $R, string $const, array $tokens, string $ref): void {
    global $failures, $passes;
    $frag = (string) $R->getConstant($const);
    $recon = strtr($frag, $tokens);
    if (str_contains($ref, $recon)) {
        $passes++;
        echo "  PASS  بازسازی byte-exact $const\n";
    } else {
        $failures[] = $const;
        echo "  FAIL  بازسازی byte-exact $const\n";
    }
}

// فرگمنت‌های اصلی داخل ref — ترتیب واقعی میرور: head, tab0(active), tab1..3(idle), mid, panel, bottom, tail
$tabs_active = (string) $R->getConstant('F_TAB_ACTIVE');
$tabs_idle   = (string) $R->getConstant('F_TAB_IDLE');
$panel       = (string) $R->getConstant('F_PANEL');
$cat         = (string) $R->getConstant('F_CAT');
$p_head      = (string) $R->getConstant('F_P_HEAD');
$p_mid       = (string) $R->getConstant('F_P_MID');
$p_bottom    = (string) $R->getConstant('F_P_BOTTOM');
$p_tail      = (string) $R->getConstant('F_P_TAIL');
$icons_c     = (array) $R->getConstant('ICONS');

$icons = ['cpu', 'sparkles', 'trees', 'waves'];
$titles = ['پوینت لایت و تجهیزات پیکسلی', 'ریسه‌ها و تزئینات نوری شهری', 'درخت نوری و المان‌های حجمی', 'والواشر، لینیرلایت و لامپ صنعتی'];
$recon = str_replace('{ES_TOTAL}', '۱۵', $p_head);
foreach ($titles as $i => $title) {
    $tpl = $i === 0 ? $tabs_active : $tabs_idle;
    $recon .= strtr($tpl, ['{ES_I}' => (string) $i, '{ES_TITLE}' => $title, '{ES_ICON}' => $icons_c[$icons[$i]] ?? '']);
}
$recon .= $p_mid;
$cat0 = strtr($cat, ['{ES_HREF}' => 'product_cat/point-light.html', '{ES_LABEL}' => 'پوینت لایت پیکسل LED (۴ و ۶ سانتی)']);
$panel0_full = strtr($panel, ['{ES_I}' => '0', '{ES_PANEL_CLASS}' => 'block', '{ES_TITLE}' => $titles[0], '{ES_VIEW_ALL}' => 'product_cat/point-light.html']);
$panel_prefix = substr($panel0_full, 0, (int) strpos($panel0_full, '{ES_CATS}'));
// بازسازی هر ۴ پنل — گرید هر پنل از مرجع + view_all از هدر پنل مرجع
for ($pi = 0; $pi < 4; $pi++) {
    $panel_pos = (int) strpos($ref, 'data-mega-prod-panel="' . $pi . '"');
    $grid_i = (int) strpos($ref, '<div class="grid grid-cols-2 gap-1.5">', $panel_pos);
    $grid_c = (int) strpos($ref, '</div>', $grid_i);
    $head_seg = substr($ref, $panel_pos, $grid_i - $panel_pos);
    preg_match('/href="([^"]+)"/', $head_seg, $hm);
    $panel_i = strtr($panel, [
        '{ES_I}' => (string) $pi,
        '{ES_PANEL_CLASS}' => $pi === 0 ? 'block' : 'hidden',
        '{ES_TITLE}' => $titles[$pi],
        '{ES_VIEW_ALL}' => $hm[1] ?? ('product_cat/x.html'),
    ]);
    $pfx = substr($panel_i, 0, (int) strpos($panel_i, '{ES_CATS}'));
    $sfx = substr($panel_i, (int) strpos($panel_i, '{ES_CATS}') + 9);
    $cats_i = substr($ref, $grid_i + 38, $grid_c - $grid_i - 38);
    $recon .= $pfx . $cats_i . $sfx;
}
$recon .= strtr($p_bottom, ['{ES_SHOP_Q}' => 'shop.html?purchasable=true', '{ES_SHOP}' => 'shop.html', '{ES_PRODUCTS_TOTAL}' => '۲۰']);
$recon .= $p_tail;
check(str_contains($ref, $recon), 'بازسازی byte-exact بلوک کامل محصولات (فرگمنت+مقادیر میرور=مرجع)');
check(str_contains($ref, strtr($tabs_idle, ['{ES_I}' => '1', '{ES_TITLE}' => $titles[1], '{ES_ICON}' => $icons_c['sparkles']])), 'بازسازی byte-exact تب idle');
// پنل پروژه‌ها (type) — پیشوند/پسوند حول {ES_LINKS}
$pt_tpl = (string) $R->getConstant('F_PT');
$pt0 = strtr($pt_tpl, ['{ES_HREF}' => 'project_type/square-lighting.html', '{ES_LABEL}' => 'نورپردازی میدان']);
check(str_contains($ref, $pt0), 'بازسازی byte-exact لینک project_type');
$pc_tpl = (string) $R->getConstant('F_PC');
$pc0 = strtr($pc_tpl, ['{ES_HREF}' => 'city/esfahan.html', '{ES_LABEL}' => 'اصفهان']);
check(str_contains($ref, $pc0), 'بازسازی byte-exact لینک city');
$sv_head = str_replace('{ES_ALL_URL}', 'services.html', (string) $R->getConstant('F_SV_HEAD'));
check(str_contains($ref, $sv_head), 'بازسازی byte-exact هدر خدمات');
$corp = strtr((string) $R->getConstant('F_CORP'), [
    '{ES_URL_ABOUT}' => 'about.html', '{ES_URL_LICENSES}' => 'licenses-and-honors.html', '{ES_URL_REFUND}' => 'refund_returns.html',
    '{ES_URL_FAQ}' => 'faq.html', '{ES_URL_RECRUIT}' => 'recruitment.html', '{ES_URL_CONTACT}' => 'contact.html',
]);
check(str_contains($ref, $corp), 'بازسازی byte-exact بلوک corporate');
$home_tpl = strtr((string) $R->getConstant('F_HOME'), ['{ES_HOME_URL}' => 'index.html', '{ES_HOME_CLASS}' => 'bg-emerald-50 text-emerald-800']);
check(str_contains($ref, $home_tpl), 'بازسازی byte-exact لینک خانه');
$blog_tpl = strtr((string) $R->getConstant('F_BLOG'), ['{ES_BLOG_URL}' => 'blog.html', '{ES_CONTACT_URL}' => 'contact.html']);
check(str_contains($ref, $blog_tpl), 'بازسازی byte-exact blog/contact');


// ═══ ۲) رندر داینامیک fallback تاکسونومی ═══
$GLOBALS['__wp_options'] = [];
es_option_reset_cache();
$GLOBALS['__es_stub_terms'] = [
    'product_cat'  => [
        (object) ['term_id' => 1, 'name' => 'پوینت لایت و تجهیزات پیکسلی', 'slug' => 'point-light', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 2, 'name' => 'ریسه‌های LED', 'slug' => 'strings', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 3, 'name' => 'درخت نوری', 'slug' => 'light-tree', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 4, 'name' => 'والواشر', 'slug' => 'wall-washer', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 5, 'name' => 'فرزند پوینت', 'slug' => 'point-child', 'taxonomy' => 'product_cat', 'parent' => 1],
    ],
    'project_type' => [
        (object) ['term_id' => 10, 'name' => 'نورپردازی میدان', 'slug' => 'square-lighting', 'taxonomy' => 'project_type', 'parent' => 0],
        (object) ['term_id' => 11, 'name' => 'تونل نوری', 'slug' => 'light-tunnel', 'taxonomy' => 'project_type', 'parent' => 0],
    ],
    'city'         => [
        (object) ['term_id' => 20, 'name' => 'اصفهان', 'slug' => 'esfahan', 'taxonomy' => 'city', 'parent' => 0],
        (object) ['term_id' => 21, 'name' => 'تهران', 'slug' => 'tehran', 'taxonomy' => 'city', 'parent' => 0],
    ],
];
$GLOBALS['__es_stub_post_counts'] = ['product' => 20];
$GLOBALS['__es_stub_posts'] = [
    (object) ['ID' => 90, 'post_type' => 'services', 'post_title' => 'نورپردازی میدان'],
    (object) ['ID' => 91, 'post_type' => 'services', 'post_title' => 'نورپردازی پل'],
];
$home = home_url('/');

ob_start();
MegaMenu::output();
$html = (string) ob_get_clean();

check(str_starts_with($html, '<a href="' . $home . '"'), 'شروع با لینک خانه');
check(str_ends_with(trim($html), '</a>'), 'پایان با blog/contact');
check(!str_contains($html, '{ES_'), 'بدون placeholder جامانده');
check(substr_count($html, 'data-desktop-mega-trigger=') === 4, '۴ مگا trigger (products/projects/services/corporate)');
check(substr_count($html, 'data-desktop-mega-panel=') === 4, '۴ مگا panel');
check(substr_count($html, 'data-mega-prod-tab=') === 4, '۴ تب محصولات');
check(substr_count($html, 'data-mega-prod-panel=') === 4, '۴ پنل محصولات');
check(substr_count($html, 'data-mega-proj-tab=') === 2, '۲ تب پروژه (type/city)');
check(substr_count($html, 'data-mega-menu-corporate="true"') === 1, 'بلوک corporate');
check(str_contains($html, 'دسته‌بندی تولیدات (۴ گروه)'), 'تعداد گروه با ارقام فارسی (۴ تاپ‌لول استاب)');
check(str_contains($html, 'کاتالوگ کامل (۲۰ محصول)'), 'شمارنده محصولات');
check(str_contains($html, 'data-mega-prod-tab="0"') && str_contains($html, 'bg-emerald-700 text-white'), 'تب اول فعال (کلاس میرور)');
check(str_contains($html, '<div data-mega-prod-panel="0" class="block">'), 'پنل اول block');
check(str_contains($html, '<div data-mega-prod-panel="1" class="hidden">'), 'پنل‌های بعدی hidden');
check(str_contains($html, 'lucide lucide-cpu'), 'آیکون cpu تب اول');
check(str_contains($html, 'lucide lucide-trees'), 'آیکون trees تب سوم');
check(str_contains($html, 'href="' . $home . 'product_cat/point-light/"'), 'لینک دسته از get_term_link');
check(str_contains($html, 'فرزند پوینت'), 'فرزندان دسته در پنل');
check(str_contains($html, 'href="' . $home . 'project_type/square-lighting/"'), 'لینک project_type');
check(str_contains($html, 'href="' . $home . 'city/esfahan/"'), 'لینک city');
check(str_contains($html, 'همه پروژه‌ها'), 'CTA همه پروژه‌ها');
check(str_contains($html, 'معرفی کامل کارخانه'), 'corporate — متن ثابت میرور');
check(str_contains($html, 'href="' . es_page_url('faq') . '"'), 'corporate — لینک faq داینامیک');
check(!str_contains($html, '.html"'), 'بدون لینک .html در خروجی داینامیک');
check(str_contains($html, 'data-search-item') === false, 'مودال جستجو خارج nav (تست 4.3)');

// خدمات: با CPT services (بدون منو)
check(MegaMenu::service_items() === [
    ['label' => 'نورپردازی میدان', 'href' => home_url('/?p=90')],
    ['label' => 'نورپردازی پل', 'href' => home_url('/?p=91')],
], 'services fallback از CPT');
check(str_contains($html, 'همه خدمات'), 'هدر پنل خدمات');

// ═══ ۳) حالت منوی وردپرس (کنوانسیون کلاس‌ها) ═══
$GLOBALS['__es_stub_nav_items'] = [
    (object) ['db_id' => 1, 'menu_item_parent' => 0, 'title' => 'محصولات', 'url' => 'http://example.org/shop/', 'classes' => ['es-mega-products']],
    (object) ['db_id' => 2, 'menu_item_parent' => 1, 'title' => 'پیکسل و برد', 'url' => 'http://example.org/product_cat/pixel/', 'classes' => ['es-mega-tab', 'es-icon-sparkles']],
    (object) ['db_id' => 3, 'menu_item_parent' => 2, 'title' => 'ریسه سوزنی', 'url' => 'http://example.org/product_cat/needle/', 'classes' => []],
    (object) ['db_id' => 4, 'menu_item_parent' => 0, 'title' => 'خدمات', 'url' => 'http://example.org/services/', 'classes' => ['es-mega-services']],
    (object) ['db_id' => 5, 'menu_item_parent' => 4, 'title' => 'خدمت سفارشی', 'url' => 'http://example.org/services/custom/', 'classes' => []],
];
$tabs_menu = MegaMenu::product_tabs();
check(count($tabs_menu) === 1 && $tabs_menu[0]['title'] === 'پیکسل و برد', 'تب از منو (es-mega-tab)');
check($tabs_menu[0]['icon'] === 'sparkles', 'آیکون از es-icon-*');
check($tabs_menu[0]['cats'] === [['label' => 'ریسه سوزنی', 'href' => 'http://example.org/product_cat/needle/']], 'فرزندان تب از منو');
check(MegaMenu::service_items() === [['label' => 'خدمت سفارشی', 'href' => 'http://example.org/services/custom/']], 'خدمات از منو (اولویت بر CPT)');

// فیلتر توسعه
$GLOBALS['__es_stub_nav_items'] = null;
$GLOBALS['__wp_hooks']['filters']['es_mega_products_tabs'] = [[
    'cb' => static fn(): array => [['title' => 'تب سفارشی', 'icon' => 'cpu', 'view_all' => '/x/', 'cats' => []]],
    'priority' => 10,
]];
$tabs_f = MegaMenu::product_tabs();
check($tabs_f[0]['title'] === 'تب سفارشی', 'فیلتر es_mega_products_tabs');

// ═══ ۴) حالت بدون داده → رندر امن ═══
$GLOBALS['__wp_hooks']['filters']['es_mega_products_tabs'] = [];
$GLOBALS['__es_stub_terms'] = [];
$GLOBALS['__es_stub_posts'] = [];
ob_start();
MegaMenu::output();
$empty = (string) ob_get_clean();
check(str_contains($empty, 'همه خدمات نورپردازی'), 'بدون خدمات → fallback آرشیو خدمات');
check(str_contains($empty, 'data-mega-menu-products="true"'), 'بدون دسته → بلوک با fallback آرشیو (هدر هرگز خالی نمی‌ماند)');
check(str_contains($empty, 'فروشگاه محصولات') && str_contains($empty, 'کاتالوگ کامل محصولات'), 'fallback تب آرشیو اصلی');
check(str_contains($empty, 'data-desktop-mega-trigger="projects"'), 'بلوک پروژه‌ها بدون ترم هم رندر می‌شود (پنل‌های خالی)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
