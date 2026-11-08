<?php
/**
 * Test 7.5 — single.php + TOC — سینگل مقاله بایت‌دقیق مقابل میرور blog/*.html
 * اجرا: /home/user/tools/php -f temp/phase-07-projects-blog-and-pages/tests/test-task-05.php
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
echo "== Test 7.5: single.php (مقاله + TOC + مرتبط) ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';
$U = 'https://test.example/wp-content/uploads/';
$HOME = 'https://test.example/';
$BLOG_URL = $HOME . 'blog/';

/* ---------- فیکسچر ۷ مقاله (ترتیب نزولی تاریخ = قاعده مرتبط میرور) ---------- */
$fa2en = fn (string $v): string => strtr($v, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']);

function s75_inner(string $html): string {
    $m0 = strpos($html, '<main class="min-h-screen bg-[#f8faf9]">') + strlen('<main class="min-h-screen bg-[#f8faf9]">');
    return substr($html, $m0, (int) strpos($html, '</main>', $m0) - $m0);
}

$MIR = [
    'types-of-light-tunnel' => (string) file_get_contents($root . '/uploads/static-mirror/blog/types-of-light-tunnel.html'),
    'led-wall-washer-guide' => (string) file_get_contents($root . '/uploads/static-mirror/blog/led-wall-washer-guide.html'),
    'point-light-pixel-led' => (string) file_get_contents($root . '/uploads/static-mirror/blog/point-light-pixel-led.html'),
];

/** داده‌های هر مقاله از میرور خودش */
function s75_hero_data(string $html): array {
    $m0 = strpos($html, '<main class="min-h-screen bg-[#f8faf9]">') + strlen('<main class="min-h-screen bg-[#f8faf9]">');
    $seg = substr($html, $m0, (int) strpos($html, '</main>', $m0) - $m0);
    $hero = substr($seg, 0, (int) strpos($seg, '</section>') + 10);
    preg_match('/<h1[^>]*>([^<]+)<\/h1>/', $hero, $title);
    preg_match('/<p class="mx-auto mt-5 max-w-2xl text-center text-\[15px\] font-medium leading-9 text-slate-600">([^<]+)<\/p>/', $hero, $desc);
    preg_match('/lucide-clock3 lucide-clock-3 h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M12 6v6h4"><\/path><circle cx="12" cy="12" r="10"><\/circle><\/svg>([۰-۹]+) دقیقه مطالعه<\/span>/', $hero, $min);
    preg_match('/<img src="(\.\.\/images\/[^"]+)" alt="[^"]*" class="h-64 w-full rounded-\[1\.5rem\]/', $hero, $img);
    preg_match_all('/shadow-2xs">([^<]+)<\/span>/', $hero, $tags);
    $cat = 'مجله علمی';
    if (str_contains($hero, '>اخبار و رویداد ها</span>')) {
        $cat = 'اخبار و رویداد ها';
    }
    return ['title' => $title[1], 'desc' => $desc[1], 'min' => $min[1], 'img' => $img[1], 'tags' => $tags[1], 'cat' => $cat];
}

$DATA = [
    'types-of-light-tunnel'    => ['id' => 701, 'date' => '۱۲ مرداد ۱۴۰۴', 'excerpt' => 'تونل نوری یکی از جذاب‌ترین سازه‌های نورپردازی شهری است. در این راهنما انواع تونل نوری شامل کمانی، نیم‌آرک، ۹۰ درجه، مربعی و قلبی را با کاربردها و مزایای هر کدام بررسی می‌کنیم.', 'img' => 'images/light-tunnel.jpg'],
    'urban-lighting-elements'  => ['id' => 702, 'date' => '۵ مرداد ۱۴۰۴', 'excerpt' => 'المان‌های نورپردازی شهری بخشی از هویت بصری شهر هستند. در این مقاله ویژگی‌های کلیدی المان‌های نوری، استانداردهای IP67/IP68 و انواع محصولات درخت نوری، لوستر، خورشیدنما و ریسه را بررسی می‌کنیم.', 'img' => 'images/light-element.jpg'],
    'baloti-seven-color-string'=> ['id' => 703, 'date' => '۲۸ تیر ۱۴۰۴', 'excerpt' => 'ریسه بلوطی هفت رنگ با مصرف تنها ۱ وات در ساعت برای هر شعله، طول عمر ۵۰ هزار ساعت و مقاومت IP67؛ از پرطرفدارترین محصولات نورپردازی شهری. مشخصات کامل و کاربردها را بخوانید.', 'img' => 'images/string-lights.jpg'],
    'sun-display-32-vs-48'     => ['id' => 704, 'date' => '۲۱ تیر ۱۴۰۴', 'excerpt' => 'خورشیدنما یکی از ماندگارترین المان‌های نورپردازی شهری است. در این مقاله مدل‌های ۳۲ و ۴۸ پره را از نظر قطر، تراکم پیکسلی، کنترل و کاربرد با هم مقایسه می‌کنیم.', 'img' => 'images/sun-light.jpg'],
    'led-wall-washer-guide'    => ['id' => 705, 'date' => '۱۴ تیر ۱۴۰۴', 'excerpt' => 'والواشر RGB با تولید بیش از ۱۶ میلیون رنگ و مصرف تنها ۳۶ وات، گزینه‌ای ایده‌آل برای نورپردازی نمای ساختمان‌های مدرن است. استانداردها و کاربردها را بخوانید.', 'img' => 'images/light-element.jpg'],
    'point-light-pixel-led'    => ['id' => 706, 'date' => '۷ تیر ۱۴۰۴', 'excerpt' => 'پوینت لایت پیکسل با گواهی IP68 و قابلیت اجرای افکت‌های تعاملی، قلب تپنده تونل‌های نوری و تابلوهای پیکسلی شهری است. مزایا و کاربردها را بررسی می‌کنیم.', 'img' => 'images/light-tunnel.jpg'],
    'frame-holder-plt'         => ['id' => 707, 'date' => '۲ خرداد ۱۴۰۴', 'excerpt' => 'قاب نگهدارنده PLT با آلومینیوم صنعتی و فولاد گالوانیزه، نصب نظم‌دهنده پیکسل‌ها را ممکن می‌سازد؛ از محافظت کابل‌ها تا دسترسی آسان برای تعمیرات.', 'img' => 'images/pexels/pexels-35318900.jpg'],
];

/** محتوای رندرشده از میرور (ناحیه بین باز و بستهٔ custom) + raw (بدون wrapper) */
function s75_content(string $html): array {
    $m0 = strpos($html, '<main class="min-h-screen bg-[#f8faf9]">') + strlen('<main class="min-h-screen bg-[#f8faf9]">');
    $seg = substr($html, $m0, (int) strpos($html, '</main>', $m0) - $m0);
    $sp = [];
    $off = 0;
    while (($p = strpos($seg, '<section', $off)) !== false) {
        $sp[] = $p;
        $off = $p + 8;
    }
    $s1 = substr($seg, $sp[1], $sp[2] - $sp[1]);
    $i0 = strpos($s1, '<div custom="0"><div>') + strlen('<div custom="0"><div>');
    $i1 = strpos($s1, '</div></div><div custom="0.1">');
    $rendered = substr($s1, $i0, $i1 - $i0);
    $raw = preg_replace('/<div id="sec-\d+" class="scroll-mt-24">(<h2.*?<\/h2>)<\/div>/s', '$1', $rendered);
    // تصاویر درون‌محتوایی در وردپرس مطلق‌اند (آپلودها)
    $raw = str_replace('src="../images/', 'src="' . $GLOBALS['U'], $raw);
    return [$rendered, (string) $raw];
}

[$REN_L, $RAW_L] = s75_content($MIR['led-wall-washer-guide']);
[$REN_P, $RAW_P] = s75_content($MIR['point-light-pixel-led']);
check(strlen($REN_L) > 4000 && strlen($REN_P) > 3000, 'برش محتوای دو میرور');

$HD_L = s75_hero_data($MIR['led-wall-washer-guide']);
$HD_P = s75_hero_data($MIR['point-light-pixel-led']);
check($HD_L['cat'] === 'مجله علمی' && $HD_P['cat'] === 'اخبار و رویداد ها', 'دسته دو مقاله از میرور');

/* ساخت پست‌ها + متا + ترم‌ها */
$posts = [];
$permalinks = [];
$meta = [];
$tmap = [];
$img_ids = [];
$next_aid = 90;
foreach ($DATA as $slug => $d) {
    $pid = $d['id'];
    $hd = isset($MIR[$slug]) ? s75_hero_data($MIR[$slug]) : null;
    $title = $hd['title'] ?? $d['excerpt'];
    if ($hd !== null) {
        $title = $hd['title'];
    }
    // عنوان برای مقاله‌های بدون میرور لودشده: از فهرست کارت‌ها
    $titles_fix = [
        'types-of-light-tunnel' => 'آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری',
        'urban-lighting-elements' => 'المان‌های نورپردازی شهری | راهنمای جامع خرید و استانداردهای فنی',
        'baloti-seven-color-string' => 'ریسه بلوطی هفت رنگ | مشخصات فنی، قیمت و کاربردها در نورپردازی شهری',
        'sun-display-32-vs-48' => 'بررسی و مقایسه خورشیدنما ۳۲ و ۴۸ پره | کدام گزینه برای پروژه شما مناسب‌تر است؟',
        'frame-holder-plt' => 'حضور قاب نگهدارنده PLT عرفان صنعت در پروژه‌های نورپردازی شهری',
    ];
    if ($hd !== null) {
        $title = $hd['title'];
    } else {
        $title = $titles_fix[$slug];
    }
    $cat = $hd['cat'] ?? (in_array($slug, ['point-light-pixel-led', 'frame-holder-plt'], true) ? 'اخبار و رویداد ها' : 'مجله علمی');
    $ipath = $hd['img'] ?? ('../' . $d['img']);
    $ipath = preg_replace('~^\.\./~', '', (string) $ipath);
    if (!isset($img_ids[$ipath])) {
        $img_ids[$ipath] = (string) $next_aid++;
    }
    $posts[$pid] = (object) ['ID' => $pid, 'post_type' => 'post', 'post_title' => $title, 'post_excerpt' => $d['excerpt'], 'post_content' => ''];
    $titles_fix2[$pid] = $title;
    $permalinks[$pid] = $HOME . 'blog/' . $slug . '/';
    $meta[$pid] = [
        '_thumbnail_id' => $img_ids[$ipath],
        'es_read_time'  => $hd ? (string) $fa2en($hd['min']) : '5',
        'es_tags'       => $hd ? implode(',', array_map(static fn ($t) => ltrim($t, '#'), $hd['tags'])) : '',
    ];
    $tmap[$pid] = ['category' => [(object) ['term_id' => 0, 'taxonomy' => 'category', 'slug' => $cat === 'مجله علمی' ? 'magazine' : 'news-and-events', 'name' => $cat, 'count' => 0]]];
}

$GLOBALS['__es_stub_posts'] = array_values($posts); // ترتیب = نزولی تاریخ (قاعده مرتبط)
$GLOBALS['__es_stub_permalinks'] = $permalinks;
$GLOBALS['__es_stub_attachment_urls'] = [];
foreach ($img_ids as $path => $aid) {
    $GLOBALS['__es_stub_attachment_urls'][(int) $aid] = $U . substr((string) $path, strlen('images/'));
}
$GLOBALS['__wp_post_meta'] = $meta;
$GLOBALS['__es_stub_post_dates'] = [];
foreach ($DATA as $slug => $d) {
    $GLOBALS['__es_stub_post_dates'][$d['id']] = $d['date'];
}
$GLOBALS['__es_stub_post_term_map'] = $tmap;
$GLOBALS['__wp_options']['page_for_posts'] = 7426;
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['archives' => ['posts_per_page' => '4']];
es_option_reset_cache();

/** لینک‌سازی محیطی مرجع سینگل (نسبی میرور → مطلق تست) */
$env_single = static function (string $ref) use ($permalinks, $img_ids, $U, $HOME, $BLOG_URL): string {
    $ref = str_replace('href="../index.html"', 'href="' . $HOME . '"', $ref);
    $ref = str_replace('href="../blog.html"', 'href="' . $BLOG_URL . '"', $ref);
    foreach ($permalinks as $pid => $pl) {
        $slug = str_replace(['https://test.example/blog/', '/'], '', $pl);
        $ref = str_replace('href="../blog/' . $slug . '.html"', 'href="' . $pl . '"', $ref);
    }
    foreach ($img_ids as $path => $aid) {
        $ref = str_replace('src="../' . $path . '"', 'src="' . $U . substr((string) $path, strlen('images/')) . '"', $ref);
    }
    return $ref;
};

/** رندر سینگل یک مقاله و برگرداندن inner main */
$render_single = static function (object $post) use ($root): string {
    ob_start();
    es_get_template('template-parts/single/article-shell.php', ['post' => $post]);
    $out = (string) ob_get_clean();
    $m0 = strpos($out, '<main class="min-h-screen bg-[#f8faf9]">');
    if ($m0 === false) {
        return $out; // شل بدون main — برای assert رندر مستقیم
    }
    $m0 += strlen('<main class="min-h-screen bg-[#f8faf9]">');
    return substr($out, $m0, (int) strpos($out, '</main>', $m0) - $m0);
};

/* ---------- ۱) رندر LED — بایت‌دقیق ---------- */
$es_post_led = $posts[705];
$es_post_led->post_content = $RAW_L;
$inner_l = $render_single($es_post_led);
$ref_l = $env_single(s75_inner($MIR['led-wall-washer-guide']));
check($inner_l === $ref_l, 'بازسازی بایت‌دقیق سینگل LED (' . strlen($inner_l) . ' کاراکتر)');

/* ---------- ۲) رندر POINT — بایت‌دقیق ---------- */
$es_post_point = $posts[706];
$es_post_point->post_content = $RAW_P;
$inner_p = $render_single($es_post_point);
$ref_p = $env_single(s75_inner($MIR['point-light-pixel-led']));
check($inner_p === $ref_p, 'بازسازی بایت‌دقیق سینگل POINT (' . strlen($inner_p) . ' کاراکتر)');

/* ---------- ۳) TOC — واحد ---------- */
\ErfanSanat\Theme\Frontend\TOC::reset();
$raw = '<p>مقدمه</p><h2 class="x">سرفصل اول</h2><p>متن</p><h3 class="keep">زیرمجموعه</h3><h2>سرفصل دوم</h2>';
$proc = \ErfanSanat\Theme\Frontend\TOC::process($raw);
check(str_contains($proc, '<div id="sec-1" class="scroll-mt-24"><h2 class="x">سرفصل اول</h2></div>'), 'TOC: پوشش h2 اول با sec-1');
check(str_contains($proc, '<div id="sec-2" class="scroll-mt-24"><h2>سرفصل دوم</h2></div>'), 'TOC: پوشش h2 دوم با sec-2');
check(str_contains($proc, '<h3 class="keep">زیرمجموعه</h3>') && !str_contains($proc, '<h3 class="keep">زیرمجموعه</h3></div>'), 'TOC: h3 دست‌نخورده');
$items = \ErfanSanat\Theme\Frontend\TOC::items();
check(count($items) === 2 && $items[0]['id'] === 'sec-1' && $items[0]['title'] === 'سرفصل اول' && $items[1]['title'] === 'سرفصل دوم', 'TOC: آیتم‌ها با عنوان خالص');
check(\ErfanSanat\Theme\Frontend\TOC::process('بدون سرفصل') === 'بدون سرفصل' && \ErfanSanat\Theme\Frontend\TOC::items() === [], 'TOC: محتوای بدون h2 → خروجی و آیتم خالی');

/* ---------- ۴) هوک the_content ثبت شده (فاز ۸) ---------- */
$hooks = $GLOBALS['__wp_hooks']['filters']['the_content'] ?? [];
check($hooks !== [], 'TOC::init فیلتر the_content را ثبت می‌کند');

/* ---------- ۵) مرتبط = ۳ جدید به‌جز جاری ---------- */
$rel = es_related_posts(705, 3);
$rel_ids = array_map(static fn ($p) => (int) $p->ID, $rel);
check($rel_ids === [701, 702, 703], 'مرتبط LED = [تونل، المان، ریسه] (مطابق میرور)');
$rel2 = es_related_posts(701, 3);
$rel_ids2 = array_map(static fn ($p) => (int) $p->ID, $rel2);
check($rel_ids2 === [702, 703, 704], 'مرتبط تونل = [المان، ریسه، خورشیدنما] (مطابق میرور types)');
$rel3 = es_related_posts(707, 3);
check(array_map(static fn ($p) => (int) $p->ID, $rel3) === [701, 702, 703], 'مرتبط قاب PLT = سه جدید (خودش حذف)');

/* ---------- ۶) بازدید ---------- */
$before = \ErfanSanat\Theme\Frontend\ViewCounter::get_views(705);
$cnt = \ErfanSanat\Theme\Frontend\ViewCounter::increment(705);
check($cnt === $before + 1 && \ErfanSanat\Theme\Frontend\ViewCounter::get_views(705) === $cnt, 'بازدید با increment افزایش می‌یابد');

$GLOBALS['__es_stub_queried_object'] = $posts[706];
$GLOBALS['__es_stub_queried_object_id'] = 706;
$GLOBALS['__es_stub_current_post_type'] = 'post';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (test)';
unset($_COOKIE['es_vc_706']);
$_GET = [];
\ErfanSanat\Theme\Frontend\ViewCounter::maybe_track();
check(\ErfanSanat\Theme\Frontend\ViewCounter::get_views(706) === 1, 'maybe_track روی سینگل شمارش را بالا می‌برد');

/* بات شمرده نشود */
$_SERVER['HTTP_USER_AGENT'] = 'curl/8.0';
\ErfanSanat\Theme\Frontend\ViewCounter::maybe_track();
check(\ErfanSanat\Theme\Frontend\ViewCounter::get_views(706) === 1, 'UA بات شمرده نمی‌شود');

/* ---------- ۷) سازگاری کارت مرتبط مستقل ---------- */
$GLOBALS['__es_stub_post_dates'][701] = '۱۲ مرداد ۱۴۰۴';
ob_start();
es_get_template('template-parts/cards/card-related.php', ['post' => $posts[701], 'custom' => '0.05']);
$crd = (string) ob_get_clean();
check(str_contains($crd, '<div custom="0.05"><a href="' . $permalinks[701] . '" class="card-light') && str_contains($crd, 'h-44 overflow-hidden') && str_contains($crd, '>۱۲ مرداد ۱۴۰۴</span>'), 'کارت مرتبط مستقل: ساختار فشرده');

/* ---------- ۸) share و دکمه‌ها در رندر LED ---------- */
check(str_contains($inner_l, 'data-share-btn="true"') && str_contains($inner_l, 'data-share-label="true"') && str_contains($inner_l, '>اشتراک‌گذاری مقاله</span>'), 'دکمه اشتراک‌گذاری میرورگونه');
check(str_contains($inner_l, '<ul class="space-y-2.5 border-r border-emerald-100 pr-4">'), 'ناو TOC سایدبار');
check(substr_count($inner_l, 'class="scroll-mt-24"') === 4, 'چهار سرفصل LED با id تزریقی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
