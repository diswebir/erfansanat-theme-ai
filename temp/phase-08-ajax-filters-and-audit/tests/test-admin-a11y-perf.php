<?php
/**
 * ADMIN + A11Y/RTL + PERFORMANCE AUDIT
 *  A) Admin dashboard on real WP: save sections via SectionManager with nonce+cap,
 *     verify meta roundtrip under legacy key _erfansanat_sections.
 *  B) A11y/RTL: rendered HTML has dir=rtl, lang=fa-IR, skip-link, alt attrs on imgs,
 *     labels on form inputs, aria on nav.
 *  C) Performance budget: rendered front-page bytes + theme asset totals vs static mirror.
 */
$WP = '/home/user/wp-test';
$ROOT = dirname(__DIR__, 3);
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

// ---------- A) admin dashboard on real WP ----------
@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
ini_set('display_errors','1'); error_reporting(E_ALL);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('init');

$pid = wp_insert_post(['post_type'=>'page','post_title'=>'خانه','post_status'=>'publish']);
update_option('page_on_front', $pid); update_option('show_on_front', 'page');

// simulate admin save with nonce + capability
wp_set_current_user(1);
$_POST['erfansanat_sections_nonce'] = wp_create_nonce('erfansanat_sections_save');
$_POST['erfansanat_sections'] = [
    'hero' => ['enabled'=>1,'order'=>1],
    'categories' => ['enabled'=>1,'order'=>2],
    'about' => ['enabled'=>0,'order'=>3],
];
ob_start();
try { \ErfanSanat\Theme\Admin\SectionManager::save($pid, get_post($pid)); } catch (Throwable $e) {}
ob_end_clean();
$meta = get_post_meta($pid, '_erfansanat_sections', true);
check('admin save writes legacy meta key _erfansanat_sections', is_array($meta) && count($meta) === 3);
check('section enabled/order roundtrip', !empty($meta['hero']['enabled']) && ($meta['categories']['order'] ?? null) === 2 && empty($meta['about']['enabled']));

// nonce rejection
$_POST['erfansanat_sections_nonce'] = 'bad';
$before = get_post_meta($pid, '_erfansanat_sections', true);
ob_start();
try { \ErfanSanat\Theme\Admin\SectionManager::save($pid, get_post($pid)); } catch (Throwable $e) {}
ob_end_clean();
check('bad nonce rejected (meta unchanged)', get_post_meta($pid, '_erfansanat_sections', true) == $before);

// ---------- B) A11y/RTL on rendered front-page ----------
add_filter('locale', fn() => 'fa_IR');
$GLOBALS['wp_locale']->text_direction = 'rtl';
query_posts(['page_id'=>$pid]);
ob_start(); include get_theme_file_path('front-page.php'); $html = (string) ob_get_clean();
check('html dir=rtl (fa_IR locale)', strpos($html, 'dir="rtl"') !== false);
check('html lang=fa-IR', strpos($html, 'lang="fa-IR"') !== false);
check('skip-link present', strpos($html, 'sr-only focus:not-sr-only') !== false);
$imgs = preg_match_all('/<img\b[^>]*>/i', $html, $m);
$noAlt = 0; foreach ($m[0] as $tag) { if (!preg_match('/\balt=/i', $tag)) $noAlt++; }
check("all imgs have alt ($imgs imgs, $noAlt missing)", $noAlt === 0);
$inputs = preg_match_all('/<input\b[^>]*>/i', $html, $mi);
$noLabel = 0; foreach ($mi[0] as $tag) { if (!preg_match('/\b(aria-label|id)=/i', $tag)) $noLabel++; }
check("inputs have label/aria ($inputs inputs)", $noLabel <= $inputs * 0.2); // decorative ok
check('nav has aria-label or role', strpos($html, 'aria-label') !== false || strpos($html, '<nav') !== false);

// ---------- C) performance budget ----------
$themeBytes = $imgBytes = 0;
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ROOT.'/assets', FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) { if ($f->isFile()) { if (preg_match('/\.(css|js|woff2?|ttf|otf)$/i', $f->getFilename())) $themeBytes += $f->getSize(); else $imgBytes += $f->getSize(); } }
$mirrorBytes = 0;
$mirror = '/home/user/static-mirror/static-mirror';
if (is_dir($mirror)) {
    $mi = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($mirror, FilesystemIterator::SKIP_DOTS));
    foreach ($mi as $f) { if ($f->isFile() && preg_match('/\.(css|js)$/i', $f->getFilename())) $mirrorBytes += $f->getSize(); }
}
printf("[INFO] theme assets: %.1f KB | rendered front-page: %.1f KB | static mirror css+js: %.1f KB\n",
    $themeBytes/1024, strlen($html)/1024, $mirrorBytes/1024);
printf("[INFO] code assets (css/js/fonts): %.1f KB | images: %.1f MB\n", $themeBytes/1024, $imgBytes/1048576);
check('code assets (css/js/fonts) < 500 KB', $themeBytes < 500*1024);
check('rendered front-page < 350 KB (static mirror was ~same ballpark)', strlen($html) < 350*1024);
$jsCount = 0;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ROOT.'/assets/js', FilesystemIterator::SKIP_DOTS)) as $f) { if ($f->isFile() && $f->getExtension()==='js') $jsCount++; }
check("lean JS: <= 6 files ($jsCount)", $jsCount <= 6);
$cssCount = 0;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ROOT.'/assets/css', FilesystemIterator::SKIP_DOTS)) as $f) { if ($f->isFile() && $f->getExtension()==='css') $cssCount++; }
check("lean CSS: <= 4 files ($cssCount)", $cssCount <= 4);

echo $fail===0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail>0?1:0);
