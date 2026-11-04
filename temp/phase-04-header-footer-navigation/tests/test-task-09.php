<?php
/** Task 4.9 — static-compatible search modal and bounded initial data. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_search_queries'] = array();
$GLOBALS['es_phase4_search_products'] = array();
$GLOBALS['es_phase4_search_posts'] = array();
class WP_Query {
	public array $posts = array();
	public function __construct( $args ) { $GLOBALS['es_phase4_search_queries'][] = $args; $this->posts = $GLOBALS['es_phase4_search_posts'][ $args['post_type'] ] ?? array(); }
}
class Phase4SearchProduct {
	public int $id; public function __construct( int $id ) { $this->id = $id; }
	public function get_id() { return $this->id; }
	public function get_name() { return 'محصول ' . $this->id; }
	public function get_permalink() { return 'https://example.test/product/' . $this->id . '/'; }
	public function get_short_description() { return '<b>توضیح محصول</b>'; }
}
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value, $protocols = null ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function absint( $value ) { return abs( (int) $value ); }
function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); }
function wc_get_products( $args ) { $GLOBALS['es_phase4_product_args'] = $args; return $GLOBALS['es_phase4_search_products']; }
function get_permalink( $id ) { return 'https://example.test/post/' . $id . '/'; }
function get_post_type_archive_link( $type ) { return false; }
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function get_template_part( $slug ) { require ES_REPO_ROOT . '/theme/' . $slug . '.php'; }
function has_nav_menu( $location ) { return false; }
for ( $i = 1; $i <= 10; ++$i ) { $GLOBALS['es_phase4_search_products'][] = new Phase4SearchProduct( $i ); }
foreach ( array( 'projects', 'post' ) as $type ) {
	for ( $i = 1; $i <= 10; ++$i ) { $GLOBALS['es_phase4_search_posts'][ $type ][] = (object) array( 'ID' => $i, 'post_title' => ucfirst( $type ) . ' ' . $i, 'post_excerpt' => 'خلاصه ' . $i ); }
}
require_once ES_REPO_ROOT . '/theme/inc/Search/SearchData.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/LegacyMarkup.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/HeaderMarkup.php';
$source = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/search-modal.php' );
ob_start(); require ES_REPO_ROOT . '/theme/template-parts/header/search-modal.php'; $modal = (string) ob_get_clean();
$hydrated = \ErfanSanat\Theme\Search\SearchData::hydrate( $modal );
$integrated = \ErfanSanat\Theme\Nav\HeaderMarkup::render( '<header><div data-search-modal="true">stale modal</div></header>' );
$product_count = substr_count( $hydrated, 'data-search-kind="product"' );
$project_count = substr_count( $hydrated, 'data-search-kind="project"' );
$article_count = substr_count( $hydrated, 'data-search-kind="article"' );
check( 'مودال و data hooks با کلاس‌های مرجع و JS موجود حفظ می‌شوند', fn() => str_contains( $source, 'data-search-modal="true"' ) && str_contains( $source, 'data-global-search-input="true"' ) && str_contains( $hydrated, 'md:grid-cols-3' ) && str_contains( $integrated, 'data-search-modal="true"' ) && str_contains( $integrated, 'data-search-modal-close="true"' ) );
check( 'هر گروه حداکثر هشت نتیجه و queryها سبک و محدودند', fn() => $product_count === 8 && $project_count === 8 && $article_count === 8 && ( $GLOBALS['es_phase4_product_args']['limit'] ?? 0 ) === 8 && count( $GLOBALS['es_phase4_search_queries'] ) === 4 && array_reduce( $GLOBALS['es_phase4_search_queries'], fn( $ok, $q ) => $ok && 8 === ( $q['posts_per_page'] ?? 0 ) && true === ( $q['no_found_rows'] ?? false ), true ) );
$js = (string) file_get_contents( ES_REPO_ROOT . '/theme/assets/js/main.js' );
check( 'دکمه‌های باز/بسته و فیلتر کلاینت به selectorهای main.js متصل‌اند', fn() => str_contains( $js, '[data-search-modal-open="true"]' ) && str_contains( $js, '[data-search-modal-close="true"]' ) && str_contains( $js, '[data-global-search-input="true"]' ) && str_contains( $integrated, 'data-search-kind="article"' ) );
summary();
