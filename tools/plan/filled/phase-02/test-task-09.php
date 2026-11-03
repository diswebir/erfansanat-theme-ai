<?php
/** Task 2.9 — archive controls, main-query config and visible filters. */
declare(strict_types=1);

$bootstrap_candidates = array( __DIR__ . '/../../../../temp/tests/bootstrap.php', __DIR__ . '/../../tests/bootstrap.php' );
foreach ( $bootstrap_candidates as $bootstrap ) { if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; } }
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $s, $domain = null ) { return esc_html( $s ); } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $s ) { return preg_replace('/[^a-z0-9_\\-]/', '', strtolower((string)$s)); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return is_scalar( $v ) ? trim(strip_tags((string)$v)) : ''; } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $v ) { return strip_tags((string)$v, '<p><a><strong><em><br>'); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $v ) { return preg_match('~^https?://~i', (string)$v) ? filter_var($v, FILTER_SANITIZE_URL) : ''; } }
if ( ! function_exists( 'sanitize_email' ) ) { function sanitize_email( $v ) { return filter_var((string)$v, FILTER_SANITIZE_EMAIL) ?: ''; } }
if ( ! function_exists( 'sanitize_hex_color' ) ) { function sanitize_hex_color( $v ) { return preg_match('/^#[a-f0-9]{3}(?:[a-f0-9]{3})?$/i', (string)$v) ? (string)$v : null; } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs((int)$v); } }
if ( ! function_exists( 'sanitize_html_class' ) ) { function sanitize_html_class( $v ) { return preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$v); } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $key, $default = false ) { return $GLOBALS['es_mock_options'][$key] ?? $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $key, $value, $autoload = null ) { $GLOBALS['es_mock_options'][$key] = $value; return true; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability ) { return 'manage_options' === $capability; } }
if ( ! function_exists( 'is_rtl' ) ) { function is_rtl() { return true; } }
if ( ! function_exists( 'is_admin' ) ) { function is_admin() { return false; } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $action, $name, $referer = true, $display = true ) { return '<input type="hidden" name="' . esc_attr( $name ) . '" value="nonce">'; } }

class EsMockArchiveQuery {
	public bool $main = true;
	public bool $archive = true;
	public array $vars = array();
	public function is_main_query(): bool { return $this->main; }
	public function is_post_type_archive(): bool { return $this->archive; }
	public function is_tax(): bool { return false; }
	public function is_home(): bool { return false; }
	public function is_category(): bool { return false; }
	public function is_tag(): bool { return false; }
	public function set( string $key, $value ): void { $this->vars[ $key ] = $value; }
	public function get( string $key, $default = null ) { return $this->vars[ $key ] ?? $default; }
}

$root = ES_REPO_ROOT . '/theme/';
$admin = $root . 'inc/Admin/';
$available = is_file( $admin . 'Tabs/ArchiveTab.php' ) && is_file( $root . 'inc/Frontend/ArchiveConfig.php' );
if ( $available ) {
	require_once $root . 'inc/Helpers/Defaults.php';
	require_once $root . 'inc/Helpers/Options.php';
	require_once $root . 'inc/Contracts/ModuleInterface.php';
	require_once $admin . 'Fields/Schema.php';
	require_once $admin . 'Fields/Sanitizer.php';
	require_once $admin . 'Fields/FieldRenderer.php';
	require_once $admin . 'Tabs/ArchiveTab.php';
	require_once $admin . 'AjaxSave.php';
	require_once $root . 'inc/Frontend/ArchiveConfig.php';
}

echo "== Task 2.9: archive settings ==\n";
check( 'archive settings and query modules exist', fn() => $available );
if ( $available ) {
	\ErfanSanat\Theme\Admin\Fields\Schema::reset();
	( new \ErfanSanat\Theme\Admin\Tabs\ArchiveTab() )->register();
	$GLOBALS['es_mock_options']['erfansanat_theme_options'] = array();
	\ErfanSanat\Theme\Helpers\Options::reset();
	$query = new EsMockArchiveQuery();
	\ErfanSanat\Theme\Frontend\ArchiveConfig::configure_query( $query );
	check( 'main archive query receives validated posts-per-page and sort order', fn() => $query->get( 'posts_per_page' ) === 12 && $query->get( 'orderby' ) === 'date' && $query->get( 'order' ) === 'DESC' );
	$GLOBALS['es_mock_options']['erfansanat_theme_options']['archive']['posts_per_page'] = 24;
	$GLOBALS['es_mock_options']['erfansanat_theme_options']['archive']['order'] = 'title_asc';
	\ErfanSanat\Theme\Helpers\Options::reset();
	$query2 = new EsMockArchiveQuery();
	\ErfanSanat\Theme\Frontend\ArchiveConfig::configure_query( $query2 );
	check( 'valid archive options alter query values while invalid options fall back', function () use ( $query2 ) {
		$GLOBALS['es_mock_options']['erfansanat_theme_options']['archive']['posts_per_page'] = 99;
		$GLOBALS['es_mock_options']['erfansanat_theme_options']['archive']['order'] = 'malicious_order';
		\ErfanSanat\Theme\Helpers\Options::reset();
		$fallback = new EsMockArchiveQuery();
		\ErfanSanat\Theme\Frontend\ArchiveConfig::configure_query( $fallback );
		return $query2->get( 'posts_per_page' ) === 24 && $query2->get( 'orderby' ) === 'title' && $query2->get( 'order' ) === 'ASC' && $fallback->get( 'posts_per_page' ) === 12 && $fallback->get( 'orderby' ) === 'date';
	} );
	$GLOBALS['es_mock_options']['erfansanat_theme_options'] = array( 'archive' => array( 'filters' => array( 'price' => false, 'rating' => false ) ) );
	\ErfanSanat\Theme\Helpers\Options::reset();
	$filters = \ErfanSanat\Theme\Frontend\ArchiveConfig::visible_filters();
	check( 'disabled archive filters are omitted from the renderable DOM list', fn() => ! in_array( 'price', $filters, true ) && ! in_array( 'rating', $filters, true ) && in_array( 'category', $filters, true ) );
	$GLOBALS['es_mock_user_can'] = true;
	$settings = \ErfanSanat\Theme\Admin\Tabs\ArchiveTab::render_settings();
	check( 'archive admin settings include whitelisted controls and AJAX nonce', fn() => str_contains( $settings, 'name="nonce"' ) && str_contains( $settings, 'archive][columns' ) && str_contains( $settings, 'archive][posts_per_page' ) && str_contains( $settings, 'archive][filters][price' ) );
} else {
	for ( $i = 1; $i <= 5; $i++ ) { check( 'archive setting condition ' . $i, fn() => false ); }
}
summary();
