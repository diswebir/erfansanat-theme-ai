<?php
/** Task 2.1: Admin menu + dashboard shell */
declare(strict_types=1);
$themeRoot = dirname(__DIR__, 3); $wpRoot = '/opt/wp-testbed';
echo "=== [TEST] Task 2.1: Admin Menu & Dashboard Shell ===\n";
$failures=[]; $assert=static function(bool $c,string $l) use (&$failures):void{ echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l; };
$f=$themeRoot.'/inc/Admin/ThemeOptions.php'; $assert(is_file($f),'File exists inc/Admin/ThemeOptions.php');
if(is_file($f)){ $o=[];$c=0; exec('php -l '.escapeshellarg($f).' 2>&1',$o,$c); $assert($c===0,'php -l ThemeOptions'); }
$errs=[]; set_error_handler(static function($no,$str,$file,$line) use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file:$line"; return false; });
require_once $wpRoot.'/wp-load.php';
$assert(class_exists(\ErfanSanat\Theme\Admin\ThemeOptions::class),'ThemeOptions autoloaded');
$assert(\ErfanSanat\Theme\Core\Plugin::instance()->getService(\ErfanSanat\Theme\Admin\ThemeOptions::class)!==null,'ThemeOptions registered in Plugin');
wp_set_current_user(1);
do_action('admin_menu');
global $menu; $found=false; foreach((array)$menu as $m){ if(($m[2]??'')===\ErfanSanat\Theme\Admin\ThemeOptions::PAGE_SLUG) $found=true; }
$assert($found,'Top-level admin menu registered with slug '. \ErfanSanat\Theme\Admin\ThemeOptions::PAGE_SLUG);
$svc=\ErfanSanat\Theme\Core\Plugin::instance()->getService(\ErfanSanat\Theme\Admin\ThemeOptions::class);
ob_start(); $svc->renderPage(); $html=(string)ob_get_clean();
$assert(str_contains($html,'es-admin-wrap'),'Dashboard shell wrapper rendered');
$assert(str_contains($html,'es-admin-header'),'Professional dashboard header rendered');
$assert(str_contains($html,'aria-current="page"'),'Active dashboard link exposes current-page state');
$assert(str_contains($html,'data-es-tabs'),'Tab navigation present');
$assert(str_contains($html,'داشبورد عرفان صنعت'),'Persian dashboard title present');
$assert(is_array($svc::tabs()) && count($svc::tabs())>=5,'At least 5 tabs defined');
$hadTab = array_key_exists('tab', $_GET); $oldTab = $_GET['tab'] ?? null; $_GET['tab'] = ['unexpected-array'];
ob_start(); $svc->renderPage(); $invalidTabHtml=(string)ob_get_clean();
$assert(str_contains($invalidTabHtml,'data-es-panel="header"'),'Invalid array-shaped tab query safely falls back to the default tab');
if ($hadTab) { $_GET['tab'] = $oldTab; } else { unset($_GET['tab']); }
wp_set_current_user(0);
add_filter('wp_die_handler', static function(){ return static function($m){ throw new RuntimeException(is_string($m)?$m:'die'); }; });
$denied=false; try { $svc->renderPage(); } catch (RuntimeException $e) { $denied=true; }
remove_all_filters('wp_die_handler');
$assert($denied,'Non-admin user is blocked (wp_die) from dashboard shell');
$assert(empty($errs),'Zero theme warnings/notices ('.implode(';',$errs).')');
restore_error_handler();
if($failures){fwrite(STDERR,"\n[FAILED] Task 2.1 ".count($failures)."\n"); exit(1);} echo "\n[PASSED] Task 2.1\n";
