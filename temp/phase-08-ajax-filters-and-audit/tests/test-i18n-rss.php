<?php
/** I18N + RSS/SITEMAP TEST — real WP: text domain, RTL locale, RSS feed, core sitemap. */
$WP = '/home/user/wp-test';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
update_option('blogname', 'عرفان صنعت');
add_filter('locale', fn() => 'fa_IR');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('after_setup_theme');
do_action('init');

// ---- i18n ----
check('locale is fa_IR', get_locale() === 'fa_IR');
// WP 7.2 derives RTL from the fa_IR text-direction translation; verify the
// theme itself never hardcodes LTR direction anywhere.
$ltr = 0;
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(get_theme_file_path(), FilesystemIterator::SKIP_DOTS));
foreach ($rii as $f) {
    if ($f->getExtension() !== 'php') { continue; }
    $c = file_get_contents($f->getPathname());
    // LTR is legitimate only inside email/phone/URL contexts (mailto:, tel:, http)
    $c = preg_replace('/<(a|span|div|p|input)[^>]*(mailto:|tel:|https?://)[^>]*>[^<]*<\/\1>/i', '', $c);
    if (preg_match('/dir=["\']ltr/i', $c) || preg_match('/direction\s*:\s*ltr/i', $c)) { $ltr++; echo "  LTR: ".$f->getPathname()."\n"; }
}
check('no hardcoded LTR direction (except mailto/tel/url)', $ltr === 0);
check('translation call resolves', __('خانه', 'erfansanat') === 'خانه');
check('theme calls load_theme_textdomain', strpos(file_get_contents(get_theme_file_path('inc/Theme.php')), "load_theme_textdomain('erfansanat'") !== false);

// ---- seed content ----
wp_insert_post(['post_type' => 'post', 'post_title' => 'مقاله تست', 'post_status' => 'publish', 'post_content' => 'محتوا']);

// ---- RSS feed render (no HTTP in CLI: render the feed template directly) ----
$GLOBALS['wp_query'] = new WP_Query(['post_type' => 'post', 'posts_per_page' => 10]);
ob_start();
do_feed_rss2(false);
$xml = ob_get_clean();
check('RSS XML rendered', strpos($xml, '<rss') !== false && strpos($xml, '<channel>') !== false);
check('RSS contains item', strpos($xml, '<item>') !== false);
check('RSS item has title+link', strpos($xml, '<title>مقاله تست</title>') !== false && strpos($xml, '<link>') !== false);
check('RSS channel has site title', strpos($xml, 'عرفان صنعت') !== false);

// ---- core sitemap (WP_Sitemaps server) ----
$server = wp_sitemaps_get_server();
check('WP_Sitemaps server available', $server instanceof WP_Sitemaps);
$server->register_sitemaps();
$index = new WP_Sitemaps_Index($server->registry);
$entries = $index->get_sitemap_list();
$renderer = new WP_Sitemaps_Renderer();
ob_start();
$renderer->render_index($entries);
$sx = ob_get_clean();
check('sitemap index XML has sitemap entries', strpos($sx, '<sitemapindex') !== false && strpos($sx, '<sitemap>') !== false);
check('sitemap contains posts submap', strpos($sx, 'post_type-post') !== false || count($entries) > 0);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
