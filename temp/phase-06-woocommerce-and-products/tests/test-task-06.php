<?php
/**
 * Test 6.6 — پروژه‌های مرتبط، محصولات مرتبط و دیدگاه‌ها
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-06.php
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
echo "== Test 6.6: related-projects + related-products + reviews ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

/* ---------- مرجع میرور ---------- */
$mirror = (string) file_get_contents($root . '/uploads/static-mirror/product/wall-washer.html');
$a0 = strpos($mirror, '<section class="mx-auto max-w-6xl px-5 pb-14 lg:px-8"><div class="mb-7 flex flex-wrap items-end justify-between gap-4"><div><div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700"><svg');
$a1 = strpos($mirror, '</section>', $a0);
$SEC_A = substr($mirror, $a0, $a1 - $a0);
$c0 = strpos($mirror, '<section class="border-t border-slate-200/80 bg-white py-14">');
$c1 = strpos($mirror, '</section>', $c0);
$SEC_C = substr($mirror, $c0, $c1 - $c0);

/* ---------- فیکسچر: محصول ۹۰۰ + سه پروژه + سه محصول ---------- */
$U = 'https://test.example/wp-content/uploads/';
$GLOBALS['__es_stub_titles'] = [
    900 => 'والواشر RGB و تک‌رنگ صنعتی (IP68)',
    501 => 'اجرای پروژه نیم هرم نوری اهواز',
    502 => 'نورپردازی میدان شهدای جهان اسلام اصفهان',
    503 => 'نورپردازی مالتی‌مدیا نمای مجتمع تجاری و اداری — تهران',
    601 => 'پوینت لایت پیکسل ۴ سانتی فول‌کالر',
    602 => 'پوینت لایت پیکسل ۶ سانتی پرقدرت',
    603 => 'ریسه سوزنی شهری ضد آب (IP68)',
];
$GLOBALS['__es_stub_posts'] = [
    (object) ['ID' => 900, 'post_name' => 'wall-washer', 'post_type' => 'product', 'post_content' => 'x',
        'post_excerpt' => 'والواشر صنعتی IP68'],
    (object) ['ID' => 501, 'post_name' => 'ahvaz-semi-pyramid-light', 'post_type' => 'projects',
        'post_excerpt' => 'طراحی و ساخت سازه حجمی نیم‌هرم نوری پیکسلی با مقاومت کامل در برابر اقلیم گرم و مرطوب اهواز'],
    (object) ['ID' => 502, 'post_name' => 'esfahan-shohada-square', 'post_type' => 'projects',
        'post_excerpt' => 'شاهکاری از هنر و فناوری با گوی‌های نورانی و پیکسل‌های ۴ سانتی فول‌کالر'],
    (object) ['ID' => 503, 'post_name' => 'tehran-commercial-facade-lighting', 'post_type' => 'projects',
        'post_excerpt' => 'ترکیب والواشرهای خطی RGB و لینیر لایت‌های IP68 روی نمای مدرن برج تجاری در تهران'],
    (object) ['ID' => 601, 'post_name' => 'point-light-pixel', 'post_type' => 'product',
        'post_excerpt' => 'پیکسل فول‌کالر با گواهی IP68 برای تونل‌های نوری، گوی‌های نورانی و نمای رسانه‌ای ساختمان'],
    (object) ['ID' => 602, 'post_name' => 'point-light-pixel-6cm', 'post_type' => 'product',
        'post_excerpt' => 'قطر ۶ سانتی‌متر با ۶ عدد LED SMD پرقدرت، دید عالی از فواصل دور و درجه حفاظت IP68'],
    (object) ['ID' => 603, 'post_name' => 'needle-string-lights', 'post_type' => 'product',
        'post_excerpt' => 'ریسه سوزنی شهری با گواهی IP68، مقاوم در برابر کشش و اشعه UV؛ ویژه آذین‌بندی درختان و سازه‌های نوری'],
];
$GLOBALS['__wp_post_meta'] = [
    900 => ['_thumbnail_id' => '71', 'true_sale' => 'true', '_price' => '2890000', 'project-ids' => '501,502,503'],
    501 => ['_thumbnail_id' => '81'],
    502 => ['_thumbnail_id' => '82'],
    503 => ['_thumbnail_id' => '83'],
    601 => ['_thumbnail_id' => '84'],
    602 => ['_thumbnail_id' => '85'],
    603 => ['_thumbnail_id' => '86'],
];
$GLOBALS['__es_stub_post_term_map'] = [
    900 => ['product_cat' => [(object) ['term_id' => 21, 'name' => 'لینیرلایت و والواشر', 'slug' => 'wall-washer', 'taxonomy' => 'product_cat']]],
    501 => ['project_type' => [(object) ['term_id' => 31, 'name' => 'المان‌های نورانی', 'slug' => 'light-elements', 'taxonomy' => 'project_type']],
        'city' => [(object) ['term_id' => 41, 'name' => 'اهواز', 'slug' => 'ahvaz', 'taxonomy' => 'city']]],
    502 => ['project_type' => [(object) ['term_id' => 33, 'name' => 'نورپردازی میدان', 'slug' => 'square-lighting', 'taxonomy' => 'project_type']],
        'city' => [(object) ['term_id' => 42, 'name' => 'اصفهان', 'slug' => 'esfahan', 'taxonomy' => 'city']]],
    503 => ['project_type' => [(object) ['term_id' => 34, 'name' => 'نورپردازی ساختمان', 'slug' => 'building-lighting', 'taxonomy' => 'project_type']],
        'city' => [(object) ['term_id' => 43, 'name' => 'تهران', 'slug' => 'tehran', 'taxonomy' => 'city']]],
    601 => ['product_cat' => [
        (object) ['term_id' => 21, 'name' => 'لینیرلایت و والواشر', 'slug' => 'wall-washer', 'taxonomy' => 'product_cat'],
        (object) ['term_id' => 55, 'name' => 'پوینت لایت پیکسل LED', 'slug' => 'point-light', 'taxonomy' => 'product_cat'],
    ]],
    602 => ['product_cat' => [
        (object) ['term_id' => 21, 'name' => 'لینیرلایت و والواشر', 'slug' => 'wall-washer', 'taxonomy' => 'product_cat'],
        (object) ['term_id' => 55, 'name' => 'پوینت لایت پیکسل LED', 'slug' => 'point-light', 'taxonomy' => 'product_cat'],
    ]],
    603 => ['product_cat' => [
        (object) ['term_id' => 21, 'name' => 'لینیرلایت و والواشر', 'slug' => 'wall-washer', 'taxonomy' => 'product_cat'],
        (object) ['term_id' => 56, 'name' => 'ریسه ال ای دی مدل سوزنی', 'slug' => 'needle-thread', 'taxonomy' => 'product_cat'],
    ]],
];
$GLOBALS['__es_stub_attachment_urls'] = [
    71 => $U . 'light-element.jpg',
    81 => $U . 'light-element.jpg',
    82 => $U . 'light-sphere.jpg',
    83 => $U . 'pexels/pexels-35318900.jpg',
    84 => $U . 'light-tunnel.jpg',
    85 => $U . 'light-sphere.jpg',
    86 => $U . 'string-lights.jpg',
];
$GLOBALS['__es_stub_permalinks'] = [
    501 => 'https://test.example/projects/ahvaz-semi-pyramid-light/',
    502 => 'https://test.example/projects/esfahan-shohada-square/',
    503 => 'https://test.example/projects/tehran-commercial-facade-lighting/',
    601 => 'https://test.example/product/point-light-pixel/',
    602 => 'https://test.example/product/point-light-pixel-6cm/',
    603 => 'https://test.example/product/needle-string-lights/',
];

/* ---------- ۱) SEC-A بایت‌دقیق: پروژه‌های مرتبط ---------- */
ob_start();
es_get_template('template-parts/single-product/related-projects.php', ['pid' => 900]);
$relp = ob_get_clean();
$ref_a = str_replace('href="../projects.html"', 'href="https://test.example/projects/"', $SEC_A);
foreach ([
    'ahvaz-semi-pyramid-light' => 'https://test.example/projects/ahvaz-semi-pyramid-light/',
    'esfahan-shohada-square' => 'https://test.example/projects/esfahan-shohada-square/',
    'tehran-commercial-facade-lighting' => 'https://test.example/projects/tehran-commercial-facade-lighting/',
] as $slug => $url) {
    $ref_a = str_replace('href="../projects/' . $slug . '.html"', 'href="' . $url . '"', $ref_a);
}
$ref_a = str_replace('src="../images/light-element.jpg"', 'src="' . $U . 'light-element.jpg"', $ref_a);
$ref_a = str_replace('src="../images/light-sphere.jpg"', 'src="' . $U . 'light-sphere.jpg"', $ref_a);
$ref_a = str_replace('src="../images/pexels/pexels-35318900.jpg"', 'src="' . $U . 'pexels/pexels-35318900.jpg"', $ref_a);
check($relp === $ref_a, 'بایت‌دقیق: سکشن پروژه‌های مرتبط = برش میرور (کارت‌ها/چیپ/شهر/custom)');

/* ---------- ۲) سکشن خالی مخفی ---------- */
$GLOBALS['__wp_post_meta'][905] = ['_thumbnail_id' => '71'];
$GLOBALS['__es_stub_titles'][905] = 'بی‌پروژه';
$GLOBALS['__es_stub_posts'][] = (object) ['ID' => 905, 'post_name' => 'no-project', 'post_type' => 'product', 'post_excerpt' => ''];
ob_start();
es_get_template('template-parts/single-product/related-projects.php', ['pid' => 905]);
$empty_a = ob_get_clean();
check($empty_a === '', 'پروژه‌ها: بدون متای project-ids → سکشن مخفی');

/* ---------- ۳) SEC-C بایت‌دقیق: محصولات مشابه ---------- */
ob_start();
es_get_template('template-parts/single-product/related-products.php', ['pid' => 900]);
$relpr = ob_get_clean();
$ref_c = str_replace('href="../shop.html"', 'href="https://test.example/shop/"', $SEC_C);
foreach ([
    'point-light-pixel' => 'https://test.example/product/point-light-pixel/',
    'point-light-pixel-6cm' => 'https://test.example/product/point-light-pixel-6cm/',
    'needle-string-lights' => 'https://test.example/product/needle-string-lights/',
] as $slug => $url) {
    $ref_c = str_replace('href="../product/' . $slug . '.html"', 'href="' . $url . '"', $ref_c);
}
$ref_c = str_replace('src="../images/light-tunnel.jpg"', 'src="' . $U . 'light-tunnel.jpg"', $ref_c);
$ref_c = str_replace('src="../images/light-sphere.jpg"', 'src="' . $U . 'light-sphere.jpg"', $ref_c);
$ref_c = str_replace('src="../images/string-lights.jpg"', 'src="' . $U . 'string-lights.jpg"', $ref_c);
check($relpr === $ref_c, 'بایت‌دقیق: سکشن محصولات مشابه = برش میرور (هم‌دسته، خود محصول حذف)');

/* ---------- ۴) SEC-C خالی: محصول بدون دسته ---------- */
$GLOBALS['__wp_post_meta'][906] = ['_thumbnail_id' => '71'];
$GLOBALS['__es_stub_titles'][906] = 'بی‌دسته';
$GLOBALS['__es_stub_posts'][] = (object) ['ID' => 906, 'post_name' => 'no-cat', 'post_type' => 'product', 'post_excerpt' => ''];
ob_start();
es_get_template('template-parts/single-product/related-products.php', ['pid' => 906]);
$empty_c = ob_get_clean();
check($empty_c === '', 'محصولات مشابه: بدون دسته → سکشن مخفی');

/* ---------- ۵) دیدگاه‌ها: سه دیدگاه استاب ---------- */
$GLOBALS['__es_stub_comments'] = [
    (object) ['comment_ID' => 31, 'comment_post_ID' => 900, 'comment_approved' => 'approve',
        'comment_author' => 'مهندس سعید رضایی‌پور',
        'comment_content' => 'کیفیت عایق‌بندی IP68 و شدت نوردهی این محصول در پروژه اجرایی ما بسیار عالی بود. بعد از بارندگی‌های اخیر بدون کوچک‌ترین نفوذ رطوبت یا افت نور کار می‌کند.'],
    (object) ['comment_ID' => 32, 'comment_post_ID' => 900, 'comment_approved' => 'approve',
        'comment_author' => 'مهندس محمدحسین کاظمی',
        'comment_content' => 'خرید مستقیم از کارخانه عرفان صنعت اصفهان همراه با پیش‌فاکتور رسمی سامانه ستاد و بسته‌بندی ایمن، سرعت تحویل پروژه ما را دو برابر کرد.'],
    (object) ['comment_ID' => 33, 'comment_post_ID' => 900, 'comment_approved' => 'approve',
        'comment_author' => 'مهندس علی‌اکبر شریفی',
        'comment_content' => 'کیفیت ساخت بدنه و کابل‌ها کاملاً صنعتی و بادوام است و پشتیبانی فنی کارخانه برای راه‌اندازی بسیار همراه و پاسخگو بودند.'],
];
$GLOBALS['__wp_comment_meta'] = [
    31 => ['rating' => 5, 'es_review_org' => 'واحد زیباسازی و تاسیسات شهری'],
    32 => ['rating' => 5, 'es_review_org' => 'پیمانکار پروژه‌های روشنایی معابر'],
    33 => ['rating' => 4, 'es_review_org' => 'کارشناس مسئول برق و نورپردازی'],
];
$GLOBALS['__es_stub_comment_dates'] = [31 => '۱۴۰۴/۰۱/۱۸', 32 => '۱۴۰۳/۱۲/۲۲', 33 => '۱۴۰۳/۱۱/۰۹'];
$GLOBALS['__wp_post_meta'][900]['_wc_average_rating'] = '4.95';
$GLOBALS['__es_stub_comment_counts'] = [900 => 3];

ob_start();
es_get_template('template-parts/single-product/reviews.php', ['pid' => 900]);
$rv = ob_get_clean();

/* بایت‌دقیق کارت اول */
$rv_list_at = strpos($mirror, 'data-reviews-list="true"');
$card_ref0 = strpos($mirror, '<div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-2xs">', $rv_list_at);
$card_ref1 = strpos($mirror, '<div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-2xs">', $card_ref0 + 10);
$CARD1_REF = substr($mirror, $card_ref0, $card_ref1 - $card_ref0);
check(str_contains($rv, $CARD1_REF), 'دیدگاه‌ها: کارت اول بایت‌دقیق میرور (نام/نقش/۵ستاره/تاریخ/متن)');
$card3_ref0 = $card_ref1;
$card3_end = strpos($mirror, '<div class="lg:col-span-5">', $card3_ref0);
$CARD3_SEG = substr($mirror, $card3_ref0, $card3_end - $card3_ref0);
$CARD3 = substr($CARD3_SEG, 0, strrpos($CARD3_SEG, '</div>') + 6);
$CARD3 = preg_replace('/<div class="rounded-2xl[^>]*>$/', '', $CARD3);
$CARD3 = substr($CARD3, 0, strrpos($CARD3, '</div>') + 6);
check(str_contains($rv, '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 text-slate-200" aria-hidden="true">'), 'دیدگاه‌ها: ستاره خالی با کلاس میرور (text-slate-200) برای امتیاز ۴');
check(str_contains($rv, 'aria-label="امتیاز ۴ از ۵"'), 'دیدگاه‌ها: aria-label امتیاز ۴ از ۵');
check(str_contains($rv, '>۳ دیدگاه ثبت‌شده</span>'), 'دیدگاه‌ها: بج تعداد فارسی');
check(str_contains($rv, 'rounded-2xl bg-emerald-700 text-lg font-black text-white tabular-nums">۴.۹۵</div>'), 'دیدگاه‌ها: جعبه میانگین ۴.۹۵');
check(str_contains($rv, 'میانگین امتیاز خریداران برای والواشر RGB و تک‌رنگ صنعتی (IP68)'), 'دیدگاه‌ها: متن میانگین با عنوان محصول');

/* فرم */
check(str_contains($rv, '<form method="post" action="https://test.example/wp-comments-post.php" data-review-form="true">'), 'فرم: متد/اکشن استاندارد wp-comments-post + data-review-form');
check(str_contains($rv, '<input type="hidden" name="comment_post_ID" value="900"/>'), 'فرم: hidden comment_post_ID');
check(str_contains($rv, 'name="rating" value="5"') && str_contains($rv, 'data-review-star="5"'), 'فرم: ورودی امتیاز و پنج دکمه ستاره');
check(str_contains($rv, 'data-review-name="true"') && str_contains($rv, 'data-review-org="true"') && str_contains($rv, 'data-review-comment="true"'), 'فرم: فیلدهای نام/سازمان/متن میرور');
check(str_contains($rv, 'data-review-success="true"'), 'فرم: جعبه موفقیت مخفی');

/* ---------- ۶) هوک‌های Woo\Reviews ---------- */
$_POST = ['rating' => '4', 'es_review_org' => 'شهرداری قم <script>x</script>'];
\ErfanSanat\Theme\Woo\Reviews::save_meta(77, 1, []);
$meta77 = $GLOBALS['__wp_comment_meta'][77] ?? [];
check(($meta77['rating'] ?? 0) === 4, 'هوک: rating عددی ذخیره شد');
check(($meta77['es_review_org'] ?? '') === 'شهرداری قم x', 'هوک: es_review_org sanitize شد (تگ حذف)');
$_POST = ['rating' => '9'];
\ErfanSanat\Theme\Woo\Reviews::save_meta(78, 1, []);
check(!isset($GLOBALS['__wp_comment_meta'][78]['rating']), 'هوک: امتیاز خارج از ۱..۵ ذخیره نشد');

$_POST = [];
$fallback = \ErfanSanat\Theme\Woo\Reviews::guest_email_fallback(['comment_author' => 'مهندس تست', 'comment_author_email' => '']);
check(str_starts_with((string) $fallback['comment_author_email'], 'no-reply@'), 'هوک: مهمان بدون ایمیل → placeholder no-reply@');
$fallback2 = \ErfanSanat\Theme\Woo\Reviews::guest_email_fallback(['comment_author' => 'a@b.ir', 'comment_author_email' => 'a@b.ir']);
check($fallback2['comment_author_email'] === 'a@b.ir', 'هوک: ایمیل موجود دست‌نخورده');

/* ---------- ۷) ادغام specs-tabs: فیلتر has_reviews → پنل reviews ---------- */
$GLOBALS['__es_stub_current_id'] = 900;
$GLOBALS['__wp_hooks']['filters']['es_product_specs_args'] = [[
    'cb' => static function (array $d): array {
        $d['has_reviews'] = true;
        return $d;
    },
    'priority' => 10,
]];
ob_start();
es_get_template('template-parts/single-product/specs-tabs.php', ['pid' => 900]);
$tabs = ob_get_clean();
check(str_contains($tabs, 'data-prod-tab-panel="reviews"'), 'ادغام: فیلتر has_reviews → پنل reviews در specs-tabs رندر شد');
check(str_contains($tabs, 'data-review-form="true"'), 'ادغام: فرم دیدگاه داخل پنل تب‌ها');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
