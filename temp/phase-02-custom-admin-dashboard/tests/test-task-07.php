<?php
/** Task 2.7 — editable header/footer settings backed by static-site defaults. */
declare(strict_types=1);

$bootstrap_candidates = array( __DIR__ . '/../../../../temp/tests/bootstrap.php', __DIR__ . '/../../tests/bootstrap.php' );
foreach ( $bootstrap_candidates as $bootstrap ) { if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; } }
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $s, $domain = null ) { return esc_html( $s ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_textarea' ) ) { function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $s ) { return preg_replace('/[^a-z0-9_\\-]/', '', strtolower((string)$s)); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return is_scalar( $v ) ? trim(strip_tags((string)$v)) : ''; } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $v ) { return strip_tags((string)$v, '<p><a><strong><em><br>'); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $v ) { return preg_match('~^https?://~i', (string)$v) ? filter_var($v, FILTER_SANITIZE_URL) : ''; } }
if ( ! function_exists( 'sanitize_email' ) ) { function sanitize_email( $v ) { return filter_var((string)$v, FILTER_SANITIZE_EMAIL) ?: ''; } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs((int)$v); } }
if ( ! function_exists( 'sanitize_hex_color' ) ) { function sanitize_hex_color( $v ) { return preg_match('/^#[a-f0-9]{3}(?:[a-f0-9]{3})?$/i', (string)$v) ? $v : null; } }
if ( ! function_exists( 'sanitize_html_class' ) ) { function sanitize_html_class( $v ) { return preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$v); } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $key, $default = false ) { return $GLOBALS['es_test_options'][$key] ?? $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $key, $value, $autoload = null ) { $GLOBALS['es_test_options'][$key] = $value; return true; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability ) { return 'manage_options' === $capability; } }
if ( ! function_exists( 'is_rtl' ) ) { function is_rtl() { return true; } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $action, $name, $referer = true, $display = true ) { return '<input type="hidden" name="' . esc_attr( $name ) . '" value="nonce">'; } }

$admin_dir = ES_REPO_ROOT . '/theme/inc/Admin/';
$available = is_file( $admin_dir . 'Tabs/HeaderTab.php' ) && is_file( $admin_dir . 'Tabs/FooterTab.php' ) && is_file( $admin_dir . 'AdminMenu.php' );
if ( $available ) {
	require_once ES_REPO_ROOT . '/theme/inc/Helpers/Defaults.php';
	require_once ES_REPO_ROOT . '/theme/inc/Helpers/Options.php';
	require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
	require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/Schema.php';
	require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/Sanitizer.php';
	require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/FieldRenderer.php';
	require_once $admin_dir . 'Tabs/HeaderTab.php';
	require_once $admin_dir . 'Tabs/FooterTab.php';
	require_once $admin_dir . 'Tabs/NavTab.php';
	require_once $admin_dir . 'AjaxSave.php';
	require_once $admin_dir . 'AdminMenu.php';
}

echo "== Task 2.7: header/footer settings ==\n";
check( 'header/footer tabs and settings renderer exist', fn() => $available && method_exists( '\ErfanSanat\Theme\Admin\AdminMenu', 'render_settings' ) );
if ( $available ) {
	\ErfanSanat\Theme\Admin\Fields\Schema::reset();
	( new \ErfanSanat\Theme\Admin\Tabs\HeaderTab() )->register();
	( new \ErfanSanat\Theme\Admin\Tabs\FooterTab() )->register();
	( new \ErfanSanat\Theme\Admin\Tabs\NavTab() )->register();
	$registered = \ErfanSanat\Theme\Admin\Fields\Schema::all();
	check( 'schema includes announcement, contact, logo, social and footer fields', fn() => isset( $registered['header.announcement.enabled'], $registered['header.phone'], $registered['header.sales_phone'], $registered['header.email'], $registered['header.logo_id'], $registered['social.links'], $registered['footer.address'], $registered['footer.work_hours'], $registered['footer.copyright'] ) );
	$defaults = \ErfanSanat\Theme\Helpers\Defaults::all();
	check( 'fallbacks match the static mirror contact details and social URLs', fn() => $defaults['header']['phone'] === '031-91091011' && $defaults['header']['sales_phone'] === '09137976915' && $defaults['header']['email'] === 'info@erfansanat.com' && $defaults['footer']['address'] === 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹' && $defaults['social']['instagram'] === 'https://instagram.com/erfan_sanat_esfahan' );
	$header_footer = (string) file_get_contents( ES_REPO_ROOT . '/theme/header.php' ) . (string) file_get_contents( ES_REPO_ROOT . '/theme/footer.php' );
	check( 'header/footer template shells contain no hardcoded contact or copyright copy', fn() => ! str_contains( $header_footer, '031-91091011' ) && ! str_contains( $header_footer, 'info@erfansanat.com' ) && ! str_contains( $header_footer, 'فرعی دوم، پلاک ۱۱۹' ) && ! str_contains( $header_footer, 'تمامی حقوق این وب‌سایت' ) );
	check( 'disabled announcement returns no markup at all', function () { \ErfanSanat\Theme\Helpers\Options::reset(); $enabled = \ErfanSanat\Theme\Admin\Tabs\HeaderTab::announcement_enabled(); es_update_option( 'header.announcement.enabled', false ); $markup = \ErfanSanat\Theme\Admin\Tabs\HeaderTab::render_announcement( static function () { return '<div class="announcement">text</div>'; } ); return $enabled === true && \ErfanSanat\Theme\Admin\Tabs\HeaderTab::announcement_enabled() === false && $markup === ''; } );
	$field = $registered['social.links'];
	check( 'social repeater restricts icons and rejects invalid social URL values before save', function () use ( $field ) {
		$schema_ok = $field['type'] === 'repeater' && $field['fields'][1]['type'] === 'select' && array_keys( $field['fields'][1]['options'] ) === array( 'instagram', 'linkedin', 'telegram', 'aparat', 'youtube' );
		$clean     = \ErfanSanat\Theme\Admin\AjaxSave::whitelisted_values( array( 'social' => array( 'links' => array( 'item-0' => array( 'name' => 'Test', 'icon' => 'instagram', 'url' => 'javascript:alert(1)' ) ) ) ) );
		return $schema_ok && isset( $clean['social.links']['item-0'] ) && $clean['social.links']['item-0']['url'] === '';
	} );
	check( 'footer menu items remain in Appearance Menus; settings only control display', fn() => ! isset( $registered['footer.quick_links'] ) && ! isset( $registered['footer.menu_items'] ) && isset( $registered['footer.show_quick_links'] ) );
	$GLOBALS['es_mock_options']['erfansanat_theme_options'] = array();
	\ErfanSanat\Theme\Helpers\Options::reset();
	$GLOBALS['es_mock_user_can'] = true;
	$html = \ErfanSanat\Theme\Admin\AdminMenu::render_settings();
	check( 'settings screen exposes accessible Header/Footer tabs and options form', fn() => str_contains( $html, 'data-es-settings-tab="header"' ) && str_contains( $html, 'data-es-settings-tab="footer"' ) && str_contains( $html, 'data-es-options-form' ) );
	check( 'settings form includes an AJAX nonce field', fn() => str_contains( $html, 'name="nonce"' ) );
	check( 'header and footer controls render the static fallback values', fn() => str_contains( $html, 'value="031-91091011"' ) && str_contains( $html, 'شنبه تا پنجشنبه' ) );
	$js = (string) file_get_contents( ES_REPO_ROOT . '/theme/assets/admin/admin.js' );
	check( 'admin JS supports tabs and posts saves to the authenticated AJAX endpoint', fn() => str_contains( $js, 'data-es-settings-tab' ) && str_contains( $js, 'es_save_options' ) && str_contains( $js, 'fetch(' ) );
} else {
	for ( $i = 1; $i <= 7; $i++ ) { check( 'header/footer setting condition ' . $i, fn() => false ); }
}
summary();
