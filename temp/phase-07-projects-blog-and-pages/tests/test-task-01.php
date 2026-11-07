<?php
/**
 * Test 7.1 — single-projects: سینگل کامل پروژه — برش بایت‌دقیق میرور esfahan-shohada-square
 * اجرا: /home/user/tools/php -f temp/phase-07-projects-blog-and-pages/tests/test-task-01.php
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
echo "== Test 7.1: single-projects (سینگل پروژه) ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

/* ---------- فیکسچر: پروژه میدان شهدای جهان اسلام اصفهان (عین میرور) ---------- */
$TITLE    = 'نورپردازی میدان شهدای جهان اسلام اصفهان';
$CLIENT   = 'شهرداری اصفهان';
$DURATION = '۱۰ روز (۲۰ تا ۳۰ دی‌ماه ۱۴۰۱)';
$COMPL    = '۳۰ دی ۱۴۰۱';
$STD      = 'عایق‌بندی استاندارد صنعتی IP67 / IP68 · ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش';
$EXCERPT  = 'شاهکاری از هنر و فناوری با گوی‌های نورانی و پیکسل‌های ۴ سانتی فول‌کالر';

$mirror = (string) file_get_contents($root . '/uploads/static-mirror/projects/esfahan-shohada-square.html');
$m0    = strpos($mirror, '<main class="bg-slate-50/50">') + strlen('<main class="bg-slate-50/50">');
$gal0  = strpos($mirror, '<section class="mx-auto max-w-6xl px-4 py-8', $m0);
$tabs0 = strpos($mirror, '<section id="project-details"', $m0);
$prod0 = strrpos(substr($mirror, $m0, (int) strpos($mirror, 'محصولات و تجهیزات استفاده', $m0) - $m0), '<section') + $m0;
$rel0  = strrpos(substr($mirror, $m0, (int) strpos($mirror, 'سایر پروژه‌های شاخص', $m0) - $m0), '<section') + $m0;
$m1    = strpos($mirror, '</main>', $m0);

// بدنه desc از میرور (۷۲۰ کاراکتر — از فایل استخراج می‌شود نه تایپ)
$tabs_raw = substr($mirror, $tabs0, $prod0 - $tabs0);
$h2m      = 'درباره ' . $TITLE . '</h2>';
$body_r   = (int) strpos($tabs_raw, $h2m) + strlen($h2m);
$c0r      = (int) strpos($tabs_raw, '<div class="lg:col-span-5">', $body_r);
$DESC     = substr($tabs_raw, $body_r, $c0r - $body_r);
$DESC     = substr($DESC, 0, -strlen('</div>')); // بستن کول گرید static می‌ماند

$U = 'https://test.example/wp-content/uploads/';
$GLOBALS['__es_stub_titles'] = [
    400 => $TITLE,
    401 => 'اجرای پروژه نیم هرم نوری اهواز',
    402 => 'تونل نوری نیم‌آرک استان البرز',
    403 => 'تونل نوری ۹۰ درجه استان البرز',
    601 => 'گوی نورانی هوشمند ۳۶۰ درجه',
    602 => 'پوینت لایت پیکسل ۴ سانتی فول‌کالر',
    603 => 'والواشر RGB و تک‌رنگ صنعتی (IP68)',
    604 => 'باکس کنترل هوشمند مرکزی نورپردازی (NRF / WiFi)',
];
$mkpost = static fn(int $id, string $type, string $exc): object => (object) [
    'ID' => $id, 'post_type' => $type, 'post_excerpt' => $exc, 'post_content' => '', 'post_title' => '',
];
$GLOBALS['__es_stub_posts'] = [
    400 => $mkpost(400, 'projects', $EXCERPT),
    401 => $mkpost(401, 'projects', 'طراحی و ساخت سازه حجمی نیم‌هرم نوری پیکسلی با مقاومت کامل در برابر اقلیم گرم و مرطوب اهواز'),
    402 => $mkpost(402, 'projects', 'تونل نیم‌آرک با پایه‌های قوسی و بخش فوقانی باز برای ترکیب نور طبیعی'),
    403 => $mkpost(403, 'projects', 'تونل زاویه‌دار با مسیر متفاوت و ریسه‌های فول‌کالر هوشمند'),
    601 => $mkpost(601, 'product', 'کره درخشان با پیکسل‌های فول‌کالر و باکس کنترل مرکزی حرفه‌ای'),
    602 => $mkpost(602, 'product', 'پیکسل فول‌کالر با گواهی IP68 برای تونل‌های نوری، گوی‌های نورانی و نمای رسانه‌ای ساختمان'),
    603 => $mkpost(603, 'product', 'بدنه آلومینیومی با IP68، تولید ۱۶ میلیون رنگ و مصرف تنها ۳۶ وات'),
    604 => $mkpost(604, 'product', 'مدیریت یکپارچه هزاران پیکسل، درخت نوری و خورشیدنما با اپلیکیشن موبایل و پروتکل بی‌سیم NRF'),
];
$GLOBALS['__es_stub_posts'][400]->post_content = $DESC;

$GLOBALS['__wp_post_meta'] = [
    400 => [
        'second-image' => '81', 'three-image' => '82', 'foure-image' => '83', 'five-image' => '84', 'six-image' => '85',
        'es_client' => $CLIENT, 'es_duration' => $DURATION, 'es_standard' => $STD,
        'finish-pro-time' => $COMPL, 'mortabet_mahsole_2' => '601,602,603,604',
    ],
    401 => ['_thumbnail_id' => '73'],
    402 => ['_thumbnail_id' => '72'],
    403 => ['_thumbnail_id' => '72'],
    601 => ['_thumbnail_id' => '71'],
    602 => ['_thumbnail_id' => '72'],
    603 => ['_thumbnail_id' => '73'],
    604 => ['_thumbnail_id' => '74'],
];
$term = static fn(int $tid, string $tax, string $slug, string $name): object => (object) [
    'term_id' => $tid, 'taxonomy' => $tax, 'slug' => $slug, 'name' => $name,
];
$t_sq = $term(21, 'project_type', 'square-lighting', 'نورپردازی میدان');
$t_ef = $term(22, 'city', 'esfahan', 'اصفهان');
$t_lm = $term(41, 'project_type', 'luminous-elements', 'المان‌های نورانی');
$t_ah = $term(31, 'city', 'ahvaz', 'اهواز');
$t_al = $term(32, 'city', 'alborz', 'استان البرز');
$t_tn = $term(42, 'project_type', 'tunnel-lighting', 'تونل نوری');
// کارت‌های مرتبط: ترم اول (نمایش chip) + ترم پروژه جاری (عبور از tax_query استاب — OR منطقی)
$GLOBALS['__es_stub_post_term_map'] = [
    400 => ['city' => [$t_ef], 'project_type' => [$t_sq]],
    401 => ['city' => [$t_ah, $t_ef], 'project_type' => [$t_lm, $t_sq]],
    402 => ['city' => [$t_al, $t_ef], 'project_type' => [$t_tn, $t_sq]],
    403 => ['city' => [$t_al, $t_ef], 'project_type' => [$t_tn, $t_sq]],
    601 => ['product_cat' => [$term(51, 'product_cat', 'urban-elements', 'المان های نورپردازی شهری')]],
    602 => ['product_cat' => [$term(52, 'product_cat', 'point-light-pixel', 'پوینت لایت پیکسل LED')]],
    603 => ['product_cat' => [$term(53, 'product_cat', 'wall-washer', 'لینیرلایت و والواشر')]],
    604 => ['product_cat' => [$term(54, 'product_cat', 'control-box', 'باکس کنترل هوشمند نورپردازی')]],
];
$GLOBALS['__es_stub_attachment_urls'] = [
    71 => $U . 'light-sphere.jpg',
    72 => $U . 'light-tunnel.jpg',
    73 => $U . 'light-element.jpg',
    74 => $U . 'light-element.jpg',
    81 => $U . 'light-sphere.jpg',
    82 => $U . 'pexels/pexels-14720175.jpg',
    83 => $U . 'pexels/pexels-17789372.jpg',
    84 => $U . 'pexels/pexels-35318900.jpg',
    85 => $U . 'pexels/pexels-29570545.jpg',
];
$GLOBALS['__es_stub_permalinks'] = [
    9   => 'https://test.example/shop/',
    401 => 'https://test.example/projects/ahvaz-semi-pyramid-light/',
    402 => 'https://test.example/projects/half-arch-tunnel-alborz/',
    403 => 'https://test.example/projects/tunnel-90-alborz/',
    601 => 'https://test.example/product/lighting-ball/',
    602 => 'https://test.example/product/point-light-pixel/',
    603 => 'https://test.example/product/wall-washer/',
    604 => 'https://test.example/product/smart-control-box/',
];
$GLOBALS['__es_stub_wc_page_ids'] = ['shop' => 9];
$GLOBALS['__es_stub_current_id']  = 400;

$TYPE_URL = 'https://test.example/project_type/square-lighting/';
$CITY_URL = 'https://test.example/city/esfahan/';
$common   = ['title' => $TITLE, 'type_url' => $TYPE_URL, 'type_name' => 'نورپردازی میدان', 'city_url' => $CITY_URL, 'city_name' => 'اصفهان'];

/* ---------- ۱) فرگمنت هیرو + بایت‌دقیق ---------- */
ob_start();
es_get_template('template-parts/single-project/hero.php', array_merge($common, ['badge' => 'نورپردازی میدان · اصفهان', 'desc' => $EXCERPT]));
$hero_html = ob_get_clean();
$hero_ref  = substr($mirror, $m0, $gal0 - $m0);
$hero_ref  = str_replace('href="../index.html"', 'href="https://test.example/"', $hero_ref);
$hero_ref  = str_replace('href="../projects.html"', 'href="https://test.example/projects/"', $hero_ref);
$hero_ref  = str_replace('href="../project_type/square-lighting.html"', 'href="' . $TYPE_URL . '"', $hero_ref);
check($hero_html === $hero_ref, 'بایت‌دقیق: هیرو = برش میرور (breadcrumb/بج/H1/خلاصه)');

/* ---------- ۲) فرگمنت گالری + سایدبار + بایت‌دقیق ---------- */
$imgs = [$U . 'light-sphere.jpg', $U . 'pexels/pexels-14720175.jpg', $U . 'pexels/pexels-17789372.jpg', $U . 'pexels/pexels-35318900.jpg', $U . 'pexels/pexels-29570545.jpg'];
ob_start();
es_get_template('template-parts/single-project/gallery.php', array_merge($common, ['imgs' => $imgs, 'chip' => 'نورپردازی میدان', 'client' => $CLIENT, 'duration' => $DURATION]));
$gal_html = ob_get_clean();
$gal_ref  = substr($mirror, $gal0, $tabs0 - $gal0);
foreach (['light-sphere.jpg', 'pexels/pexels-14720175.jpg', 'pexels/pexels-17789372.jpg', 'pexels/pexels-35318900.jpg', 'pexels/pexels-29570545.jpg'] as $p) {
    $gal_ref = str_replace('../images/' . $p, $U . $p, $gal_ref);
}
$gal_ref = str_replace('href="../project_type/square-lighting.html"', 'href="' . $TYPE_URL . '"', $gal_ref);
$gal_ref = str_replace('href="../city/esfahan.html"', 'href="' . $CITY_URL . '"', $gal_ref);
check($gal_html === $gal_ref, 'بایت‌دقیق: گالری + سایدبار = برش میرور (URLها جایگزین شد)');
check(str_contains($gal_html, 'data-gallery-root="true"') && str_contains($gal_html, 'data-gallery-main-img="true"'), 'گالری: هوک‌های root و main-img');
check(substr_count($gal_html, 'data-gallery-thumb=') === 5 && substr_count($gal_html, 'data-gallery-src=') === 5, 'گالری: پنج thumb با data-gallery-src');
check(str_contains($gal_html, 'data-gallery-expand="true"') && str_contains($gal_html, '>۱ / ۵</span>'), 'گالری: expand + شمارنده فارسی «۱ / ۵»');
check(str_contains($gal_html, $CLIENT) && str_contains($gal_html, $DURATION), 'متا: کارفرما و مدت اجرا از متا (سایدبار شناسنامه)');

/* ---------- ۳) فرگمنت تب‌ها + بایت‌دقیق ---------- */
ob_start();
es_get_template('template-parts/single-project/tabs.php', array_merge($common, [
    'content'   => $DESC,
    'client'    => $CLIENT, 'duration' => $DURATION, 'completed' => $COMPL, 'standard' => $STD,
]));
$tabs_html = ob_get_clean();
$tabs_ref  = substr($mirror, $tabs0, $prod0 - $tabs0);
$tabs_ref  = str_replace('href="../city/esfahan.html"', 'href="' . $CITY_URL . '"', $tabs_ref);
$tabs_ref  = str_replace('href="../project_type/square-lighting.html"', 'href="' . $TYPE_URL . '"', $tabs_ref);
check($tabs_html === $tabs_ref, 'بایت‌دقیق: تب‌ها (درباره/شناسنامه/فرآیند) = برش میرور');
check(str_contains($tabs_html, $STD) && str_contains($tabs_html, $COMPL), 'متا: استاندارد و تاریخ تکمیل از متا (جدول شناسنامه)');
check(str_contains($tabs_html, 'id="project-details"') && str_contains($tabs_html, 'data-prod-tab-panel="specs"'), 'تب‌ها: هوک‌های tab-panel حفظ شده');

/* ---------- ۴) محصولات از متا (mortabet_mahsole_2) + بایت‌دقیق ---------- */
$cards_html = '';
$prod_descs = [
    601 => 'کره درخشان با پیکسل‌های فول‌کالر و باکس کنترل مرکزی حرفه‌ای',
    602 => 'پیکسل فول‌کالر با گواهی IP68 برای تونل‌های نوری، گوی‌های نورانی و نمای رسانه‌ای ساختمان',
    603 => 'بدنه آلومینیومی با IP68، تولید ۱۶ میلیون رنگ و مصرف تنها ۳۶ وات',
    604 => 'مدیریت یکپارچه هزاران پیکسل، درخت نوری و خورشیدنما با اپلیکیشن موبایل و پروتکل بی‌سیم NRF',
];
$CUSTOMS = ['0', '0.06', '0.12', '0.18']; // اتریبیوت design میرور
foreach ([601, 602, 603, 604] as $i => $pid) {
    ob_start();
    es_get_template('template-parts/single-project/product-card.php', [
        'custom' => $CUSTOMS[$i],
        'url'    => (string) get_permalink($pid),
        'img'    => es_media_url((int) get_post_meta($pid, '_thumbnail_id', true)),
        'title'  => (string) get_the_title($pid),
        'chip'   => (string) get_the_terms($pid, 'product_cat')[0]->name,
        'desc'   => $prod_descs[$pid],
    ]);
    $cards_html .= (string) ob_get_clean();
}
ob_start();
es_get_template('template-parts/single-project/products.php', ['cards' => [601, 602, 603, 604], 'cards_html' => $cards_html]);
$prod_html = ob_get_clean();
$prod_ref  = substr($mirror, $prod0, $rel0 - $prod0);
$prod_ref  = str_replace('href="../shop.html"', 'href="https://test.example/shop/"', $prod_ref);
foreach (['lighting-ball' => 601, 'point-light-pixel' => 602, 'wall-washer' => 603, 'smart-control-box' => 604] as $slug => $pid) {
    $prod_ref = str_replace('href="../product/' . $slug . '.html"', 'href="https://test.example/product/' . $slug . '/"', $prod_ref);
}
foreach (['light-sphere.jpg', 'light-tunnel.jpg', 'light-element.jpg'] as $p) {
    $prod_ref = str_replace('../images/' . $p, $U . $p, $prod_ref);
}
check($prod_html === $prod_ref, 'بایت‌دقیق: سکشن محصولات = برش میرور (۴ کارت از mortabet_mahsole_2)');
foreach ($CUSTOMS as $ci) {
    check(substr_count($prod_html, 'custom="' . $ci . '"') === 1, 'محصولات: اتریبیوت custom="' . $ci . '" دقیقاً یک‌بار');
}

/* ---------- ۵) مرتبط‌ها (هم‌city یا هم‌type) + بایت‌دقیق ---------- */
$GLOBALS['__es_stub_get_posts_args'] = [];
$rel_descs = [
    401 => 'طراحی و ساخت سازه حجمی نیم‌هرم نوری پیکسلی با مقاومت کامل در برابر اقلیم گرم و مرطوب اهواز',
    402 => 'تونل نیم‌آرک با پایه‌های قوسی و بخش فوقانی باز برای ترکیب نور طبیعی',
    403 => 'تونل زاویه‌دار با مسیر متفاوت و ریسه‌های فول‌کالر هوشمند',
];
$rel_html = '';
foreach ([401, 402, 403] as $rid) {
    ob_start();
    es_get_template('template-parts/single-project/related-card.php', [
        'url'   => (string) get_permalink($rid),
        'img'   => es_media_url((int) get_post_meta($rid, '_thumbnail_id', true)),
        'title' => (string) get_the_title($rid),
        'chip'  => (string) get_the_terms($rid, 'project_type')[0]->name,
        'desc'  => $rel_descs[$rid],
        'city'  => (string) get_the_terms($rid, 'city')[0]->name,
    ]);
    $rel_html .= (string) ob_get_clean();
}
ob_start();
es_get_template('template-parts/single-project/related.php', ['cards_html' => $rel_html]);
$rel_sec_html = ob_get_clean();
$rel_ref      = substr($mirror, $rel0, $m1 - $rel0);
$rel_ref      = str_replace('href="../projects.html"', 'href="https://test.example/projects/"', $rel_ref);
foreach (['ahvaz-semi-pyramid-light' => 401, 'half-arch-tunnel-alborz' => 402, 'tunnel-90-alborz' => 403] as $slug => $rid) {
    $rel_ref = str_replace('href="../projects/' . $slug . '.html"', 'href="https://test.example/projects/' . $slug . '/"', $rel_ref);
}
foreach (['light-element.jpg', 'light-tunnel.jpg'] as $p) {
    $rel_ref = str_replace('../images/' . $p, $U . $p, $rel_ref);
}
check($rel_sec_html === $rel_ref, 'بایت‌دقیق: سکشن مرتبط‌ها = برش میرور (۳ کارت لایت)');
$qargs = end($GLOBALS['__es_stub_get_posts_args']);
$shell_query = null; // args واقعی شل در رندر بعدی ضبط می‌شود
check(str_contains($rel_sec_html, 'اهواز</span>') && str_contains($rel_sec_html, 'استان البرز</span>'), 'مرتبط: chip شهر هر کارت از ترم اول');

/* ---------- ۶) رندر کامل شل — ترکیب بایت‌دقیق main ---------- */
ob_start();
require $root . '/single-projects.php';
$shell_html = (string) ob_get_clean();
$main_expected = $hero_html . $gal_html . $tabs_html . $prod_html . $rel_sec_html;
$s0 = strpos($shell_html, '<main class="bg-slate-50/50">');
$s1 = strpos($shell_html, '</main>', $s0);
check($s0 !== false && $s1 !== false, 'شل: main باز و بسته شد');
check(substr($shell_html, $s0 + strlen('<main class="bg-slate-50/50">'), $s1 - $s0 - strlen('<main class="bg-slate-50/50">')) === $main_expected, 'بایت‌دقیق: بدنه main شل = مجموع پنج فرگمنت (هیرو+گالری+تب‌ها+محصولات+مرتبط)');
check(str_starts_with($shell_html, '<!doctype html>') && strlen($shell_html) > $s1 + 100, 'شل: header و footer رندر شدند');
check(str_contains($shell_html, 'dir="rtl"'), 'شل: خروجی RTL (header)');

// args کوئری مرتبط‌های شل
$last_args = [];
foreach ($GLOBALS['__es_stub_get_posts_args'] as $ga) {
    if ((string) ($ga['post_type'] ?? '') === 'projects') {
        $last_args = $ga;
    }
}
check(($last_args['posts_per_page'] ?? 0) === 3 && in_array(400, (array) ($last_args['post__not_in'] ?? []), true), 'کوئری مرتبط: posts_per_page=3 و not_in=[400]');
$tq = (array) ($last_args['tax_query'] ?? []);
check(($tq['relation'] ?? '') === 'OR' && count(array_filter($tq, 'is_array')) === 2, 'کوئری مرتبط: tax_query دو شاخه با relation=OR');

/* ---------- ۷) سکشن‌های مخفی وقتی داده خالی است ---------- */
$GLOBALS['__wp_post_meta'][400]['mortabet_mahsole_2'] = '';
ob_start();
require $root . '/single-projects.php';
$shell_noprod = (string) ob_get_clean();
check(!str_contains($shell_noprod, 'محصولات و تجهیزات استفاده') && !str_contains($shell_noprod, 'custom="0.18"'), 'محصولات خالی → سکشن مخفی');
$GLOBALS['__es_stub_post_term_map'][400] = [];
ob_start();
require $root . '/single-projects.php';
$shell_norel = (string) ob_get_clean();
check(!str_contains($shell_norel, 'سایر پروژه‌های شاخص') && !str_contains($shell_norel, 'tunnel-90-alborz'), 'بدون ترم شهر/نوع → سکشن مرتبط مخفی (بدون کوئری)');
$GLOBALS['__es_stub_post_term_map'][400] = ['city' => [$t_ef], 'project_type' => [$t_sq]];
$GLOBALS['__wp_post_meta'][400]['mortabet_mahsole_2'] = '601,602,603,604';

/* ---------- ۸) گالری: مقاومت وقتی فقط کلیدهای قدیمی partially ست‌اند ---------- */
$GLOBALS['__wp_post_meta'][400]['six-image'] = ''; // چهار تصویر → شمارنده ۱ / ۴ و اسلات پنجم placeholder
ob_start();
es_get_template('template-parts/single-project/gallery.php', array_merge($common, [
    'imgs'    => array_map('es_media_url', \ErfanSanat\Theme\Frontend\ProjectGallery::get_gallery(400)),
    'chip'    => 'نورپردازی میدان', 'client' => $CLIENT, 'duration' => $DURATION,
]));
$gal_partial = (string) ob_get_clean();
check(str_contains($gal_partial, '>۱ / ۴</span>') && substr_count($gal_partial, 'data-gallery-thumb=') === 5, 'گالری مقاوم: ۴ تصویر → شمارنده «۱ / ۴» و همچنان ۵ اسلات (placeholder میرور)');

echo "\nنتیجه: $passes پاس، " . count($failures) . " رد\n";
if ($failures !== []) {
    echo "FAILURES:\n";
    foreach ($failures as $f) {
        echo "  - $f\n";
    }
    exit(1);
}
exit(0);
