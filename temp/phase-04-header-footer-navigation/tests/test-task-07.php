<?php
/**
 * Test 4.7 — SimpleWalker (تولیدات شرکت) و LandingWalker (منوی اسکرول لندینگ)
 * اجرا: /home/user/tools/php -f temp/phase-04-header-footer-navigation/tests/test-task-07.php
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
echo "== Test 4.7: SimpleWalker / LandingWalker ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Nav\SimpleWalker;
use ErfanSanat\Theme\Nav\LandingWalker;

// ── SimpleWalker: رندر از آرایه ──
$ref_footer = (string) file_get_contents($root . '/temp/phase-04-header-footer-navigation/refs/footer-ref.html');
$links = [
    ['label' => 'درخت نوری', 'href' => home_url('/product_cat/light-tree/')],
    ['label' => 'ریسه LED', 'href' => home_url('/product_cat/strings/')],
];
$out = SimpleWalker::render($links);
check(str_starts_with($out, '<ul class="space-y-2.5 text-[13px] text-slate-600">'), 'wrap میرور فوتر');
check(substr_count($out, '<li>') === 2, '۲ li');
check(str_contains($out, 'class="transition-colors hover:text-emerald-700"'), 'کلاس a عین میرور');
foreach (explode('</li>', $out) as $li) {
    if (str_contains($li, 'href=')) {
        check(str_contains($ref_footer, 'class="transition-colors hover:text-emerald-700"'), 'کلاس آیتم در مرجع فوتر موجود');
        break;
    }
}
check(SimpleWalker::render([]) === '', 'بدون لینک → خروجی خالی');
check(SimpleWalker::render($links, false) === '<li><a href="' . home_url('/product_cat/light-tree/') . '" class="transition-colors hover:text-emerald-700">درخت نوری</a></li><li><a href="' . home_url('/product_cat/strings/') . '" class="transition-colors hover:text-emerald-700">ریسه LED</a></li>', 'حالت بدون wrap');

// ── SimpleWalker: منو → fallback ──
$GLOBALS['__es_stub_nav_items'] = [
    (object) ['db_id' => 1, 'menu_item_parent' => 0, 'title' => 'تولید منو', 'url' => home_url('/product_cat/menu-item/'), 'classes' => []],
    (object) ['db_id' => 2, 'menu_item_parent' => 1, 'title' => 'زیرمنو (رد)', 'url' => home_url('/x/'), 'classes' => []],
];
$out2 = SimpleWalker::render_menu('tolidat-sherkat', $links);
check(str_contains($out2, 'تولید منو') && !str_contains($out2, 'درخت نوری'), 'منو اولویت دارد + تک‌سطحی');
$GLOBALS['__es_stub_nav_items'] = null;
$out3 = SimpleWalker::render_menu('tolidat-sherkat', $links);
check(str_contains($out3, 'درخت نوری'), 'fallback وقتی منو نیست');

// ── LandingWalker: fallback ──
$GLOBALS['__es_stub_nav_items'] = null;
$fb = [
    ['label' => 'معرفی', 'slug' => 'intro'],
    ['label' => 'محصولات آریا', 'slug' => 'products'],
    ['label' => 'مشاوره', 'slug' => 'consult'],
];
$landing = LandingWalker::render($fb);
check(str_contains($landing, 'href="#es-sec-intro"'), 'انکر به سکشن');
check(substr_count($landing, 'data-landing-scroll=') === 3, '۳ هوک data-landing-scroll');
check(str_contains($landing, 'is-active text-emerald-700'), 'آیتم اول فعال');
check(str_starts_with($landing, '<nav aria-label="فهرست بخش‌ها"'), 'wrap nav');
check(LandingWalker::render([]) === '', 'بدون آیتم → خالی');
$bare = LandingWalker::render($fb, false);
check(str_starts_with($bare, '<a href="#es-sec-intro"') && !str_contains($bare, '<nav'), 'حالت بدون wrap');

// اسکیپ
$fb_x = [['label' => 'مت"ن"<script>', 'slug' => 'sec-1']];
$lx = LandingWalker::render($fb_x, false);
check(!str_contains($lx, '<script>') && str_contains($lx, 'data-landing-scroll="sec-1"'), 'اسکیپ لیبل + slug امن');

// ── LandingWalker: منوی landing با انکر ──
$GLOBALS['__es_stub_nav_items'] = [
    (object) ['db_id' => 1, 'menu_item_parent' => 0, 'title' => 'گالری', 'url' => home_url('/landing-arya/') . '#gallery', 'classes' => []],
    (object) ['db_id' => 2, 'menu_item_parent' => 0, 'title' => 'بدون انکر (رد)', 'url' => home_url('/other/'), 'classes' => []],
];
$resolved = LandingWalker::resolve($fb);
check(count($resolved) === 1 && $resolved[0]['slug'] === 'gallery' && $resolved[0]['label'] === 'گالری', 'resolve از منو (انکر → slug)');
$landing2 = LandingWalker::render($fb);
check(str_contains($landing2, 'data-landing-scroll="gallery"') && !str_contains($landing2, 'intro'), 'رندر از منو');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
