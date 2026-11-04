<?php
/** Task 4.7 — navigation display options, safe defaults, and frontend behavior. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_options'] = array();
$GLOBALS['es_phase4_front_assets'] = array();
function get_option( $key, $default = false ) { return 'erfansanat_theme_options' === $key ? $GLOBALS['es_phase4_options'] : $default; }
function add_action( $hook, $callback, $priority = 10, $accepted = 1 ) {}
function add_filter( $hook, $callback, $priority = 10, $accepted = 1 ) {}
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_textarea( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $value, $domain = null ) { return esc_html( $value ); }
function selected( $a, $b, $echo = true ) { return (string) $a === (string) $b ? 'selected="selected"' : ''; }
function checked( $value, $current = true, $echo = true ) { return $value === $current ? 'checked="checked"' : ''; }
function wp_enqueue_script( $handle, $src, $deps = array(), $ver = false, $footer = false ) { $GLOBALS['es_phase4_front_assets'][] = array( 'script', $handle, $deps ); }
function wp_enqueue_style( $handle, $src, $deps = array(), $ver = false ) { $GLOBALS['es_phase4_front_assets'][] = array( 'style', $handle, $deps ); }
function is_admin() { return false; }
function get_template_directory_uri() { return 'https://example.test/theme'; }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Helpers/Defaults.php';
require_once ES_REPO_ROOT . '/theme/inc/Helpers/Options.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/Schema.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/FieldRenderer.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Tabs/NavTab.php';
require_once ES_REPO_ROOT . '/theme/inc/Frontend/NavConfig.php';
\ErfanSanat\Theme\Helpers\Options::reset();
$nav_tab = new \ErfanSanat\Theme\Admin\Tabs\NavTab();
$nav_tab->register();
$default_markup = '<header data-navbar-header="true" class="fixed inset-x-0"><div class="group" data-mega-menu-products="true"><div data-desktop-mega-panel="products">items</div></div><div class="group" data-mega-menu-projects="true"><div data-desktop-mega-panel="projects">items</div></div></header>';
$default_result = \ErfanSanat\Theme\Frontend\NavConfig::apply_markup( $default_markup );
$default_classes = \ErfanSanat\Theme\Frontend\NavConfig::body_classes( array() );
$GLOBALS['es_phase4_options'] = array( 'nav' => array( 'mega' => array( 'enabled' => false ), 'open_mode' => 'click', 'header_height' => 'compact', 'sticky' => false ) );
\ErfanSanat\Theme\Helpers\Options::reset();
$custom_result = \ErfanSanat\Theme\Frontend\NavConfig::apply_markup( $default_markup );
$custom_classes = \ErfanSanat\Theme\Frontend\NavConfig::body_classes( array() );
\ErfanSanat\Theme\Frontend\NavConfig::enqueue_assets();
$panel = \ErfanSanat\Theme\Admin\Tabs\NavTab::render();
check( 'پیش‌فرض‌ها بدون تغییر markup یا افزودن کلاس حفظ می‌شوند', fn() => $default_result === $default_markup && array() === $default_classes );
check( 'خاموش‌کردن مگامنو پنل‌ها را حذف و لینک‌های اصلی را نگه می‌دارد', fn() => ! str_contains( $custom_result, 'data-desktop-mega-panel' ) && ! str_contains( $custom_result, 'data-mega-menu-' ) && str_contains( $custom_result, 'href="/shop/"' ) && str_contains( $custom_result, 'href="/projects/"' ) );
check( 'فقط تنظیمات whitelisted کلاس و data attribute می‌سازند', fn() => str_contains( $custom_result, 'data-nav-open="click"' ) && str_contains( $custom_result, 'data-header-size="compact"' ) && ! str_contains( $custom_result, 'class="fixed inset-x-0"' ) && count( $custom_classes ) === 4 );
check( 'تنظیمات در پنل موجود ذخیره می‌شوند و asset adapter پس از main.js می‌آید', fn() => str_contains( $panel, 'name="es_options[header][announcement][enabled]"' ) && str_contains( $panel, 'name="es_options[nav][mega][enabled]"' ) && str_contains( $panel, 'name="es_options[nav][open_mode]"' ) && ! str_contains( $panel, 'menu-item-es-' ) && isset( $GLOBALS['es_phase4_front_assets'][0][2][0] ) && 'es-main' === $GLOBALS['es_phase4_front_assets'][0][2][0] );
summary();
