<?php
/** SEO OUTPUT TEST — real WP render: title, description, OG, hreflang (incl.
 *  hrefmeta_* overrides), canonical, robots, JSON-LD. */
$WP = '/home/user/wp-test';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

function render_page(array $q, string $file): string { return ''; }
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$HERE = __DIR__;
function seo_render(string $key): string {
    global $PHP, $HERE;
    return (string) shell_exec(escapeshellarg($PHP).' '.escapeshellarg("$HERE/seo-render.php").' '.escapeshellarg($key).' 2>/dev/null');
}

// front-page SEO
$h = seo_render('home');
check('title tag present with site name', strpos($h, '<title>') !== false && strpos($h, 'عرفان') !== false);
check('meta robots index,follow', strpos($h, 'index, follow') !== false);
check('og:site_name present', strpos($h, 'og:site_name') !== false);
check('og:locale fa_IR', strpos($h, 'og:locale" content="fa_IR"') !== false);
check('hreflang fa link', strpos($h, 'hreflang="fa"') !== false);
check('hreflang en + ar + x-default', strpos($h, 'hreflang="en"') !== false && strpos($h, 'hreflang="ar"') !== false && strpos($h, 'hreflang="x-default"') !== false);
check('canonical or og:url', strpos($h, 'canonical') !== false || strpos($h, 'og:url') !== false);
check('viewport meta', strpos($h, 'name="viewport"') !== false);
check('charset utf-8', strpos($h, 'charset="UTF-8"') !== false || strpos($h, "charset='UTF-8'") !== false);

// single product SEO with hrefmeta overrides
$h2 = seo_render('product');
check('single: hrefmeta_en override used', strpos($h2, 'https://erfansanat.com/en/wall-washer-rgb/') !== false);
check('single: hrefmeta_ar override used', strpos($h2, 'https://erfansanat.com/ar/wall-washer-rgb/') !== false);
check('single: product title in <title>', strpos($h2, 'والاشر RGB') !== false);

// theme supports title-tag
check('title-tag rendered as <title> (WP 7.2 builtin)', strpos($h, '<title>') !== false);
check('theme supports post-thumbnails', current_theme_supports('post-thumbnails'));
check('theme supports html5', current_theme_supports('html5'));
check('theme supports woocommerce', current_theme_supports('woocommerce'));

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
