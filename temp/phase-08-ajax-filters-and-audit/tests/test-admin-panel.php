<?php
/** ADMIN PANEL FUNCTIONAL TEST — real WP in admin context:
 *  ThemeOptions save/persist, SectionManager metabox save, Migrator run. */
$WP = '/home/user/wp-test';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
define('WP_ADMIN', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
require_once ABSPATH.'wp-admin/includes/post.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
add_filter('locale', fn() => 'fa_IR');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('after_setup_theme');
do_action('init');
wp_set_current_user(1);

use ErfanSanat\Theme\Admin\ThemeOptions;
use ErfanSanat\Theme\Admin\SectionManager;
use ErfanSanat\Theme\Migrator;

// ---- ThemeOptions: defaults + save + persist ----
$defaults = ThemeOptions::defaults();
check('ThemeOptions defaults non-empty', is_array($defaults) && count($defaults) > 3);

$_POST = [
    'erfansanat_options_submit' => '1',
    'erfansanat_nonce' => wp_create_nonce(ThemeOptions::NONCE),
    'footer' => ['phone' => '031-91091011', 'email' => 'info@erfansanat.com', 'address' => 'اصفهان'],
];
// save() checks nonce field name — inspect: uses check_admin_referer? call persist path directly
$_REQUEST = $_POST;
ThemeOptions::save();
$stored = get_option('erfansanat_theme_options', []);
check('ThemeOptions persisted phone', ($stored['footer']['phone'] ?? '') === '031-91091011');
check('ThemeOptions persisted email', ($stored['footer']['email'] ?? '') === 'info@erfansanat.com');
$_POST = [];

// ---- SectionManager: metabox save on a page ----
$page = wp_insert_post(['post_type' => 'page', 'post_title' => 'خانه', 'post_status' => 'publish']);
$sections = SectionManager::sections_for(get_post($page));
check('SectionManager registry has 15 sections', count($sections) >= 15);
$_POST = [
    SectionManager::NONCE => wp_create_nonce('erfansanat_sections_save'),
    'erfansanat_sections' => [
        'section-01' => ['enabled' => '1', 'order' => '1'],
        'section-02' => ['enabled' => '0', 'order' => '2'],
    ],
];
$_REQUEST = $_POST;
SectionManager::save($page, get_post($page));
$saved = get_post_meta($page, SectionManager::META, true);
check('sections saved to meta', is_array($saved) && isset($saved['section-01']));
check('section-01 enabled', ($saved['section-01']['enabled'] ?? false) == true);
check('section-02 disabled', ($saved['section-02']['enabled'] ?? true) == false);
$_POST = [];

// nonce rejection
$_POST = [SectionManager::NONCE => 'bad', 'erfansanat_sections' => ['x' => ['enabled' => 1]]];
$_REQUEST = $_POST;
SectionManager::save($page, get_post($page));
$saved2 = get_post_meta($page, SectionManager::META, true);
check('bad nonce rejected (meta unchanged)', $saved2 == $saved);
$_POST = [];

// ---- Migrator in admin context (previously fatal) ----
$el = json_encode([['elType' => 'section', 'id' => 's1', 'elements' => []]]);
$p2 = wp_insert_post(['post_type' => 'page', 'post_title' => 'mig', 'post_status' => 'publish']);
update_post_meta($p2, '_elementor_data', $el);
$log = Migrator::import_all();
check('migrator runs in admin context', is_array($log) && ($log['sections'] ?? 0) >= 1);
check('elementor meta converted', is_array(get_post_meta($p2, SectionManager::META, true)));

// ---- admin menus registered ----
do_action('admin_menu');
global $menu, $submenu;
$titles = array_column($menu ?? [], 2);
check('admin menu has theme options page', count(array_filter($menu ?? [], fn($m) => strpos((string)($m[2] ?? ''), 'erfansanat') !== false)) >= 1);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
