<?php
/** Task 5.4 — query-backed project gallery with the static reference as fallback. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'gallery', 'template-parts/home/section-gallery.php', 2, 'گالری پروژه‌ها' );
$GLOBALS['es_gallery_options'] = array();
function es_option( $path, $default = '' ) { return $GLOBALS['es_gallery_options'][ $path ] ?? $default; }

$GLOBALS['es_gallery_queries'] = array();
$GLOBALS['es_gallery_transients'] = array();
$GLOBALS['es_gallery_groups'] = array( 'all' => array(), 'tunnel' => array(), 'urban' => array() );
for ( $id = 1; $id <= 6; ++$id ) {
	$post = (object) array( 'ID' => $id, 'post_type' => 'projects', 'post_title' => 1 === $id ? 'پروژه <script>unsafe</script>' : 'Project ' . $id );
	$GLOBALS['es_gallery_groups']['all'][] = $post;
	if ( $id <= 3 ) { $GLOBALS['es_gallery_groups']['tunnel'][] = $post; }
	if ( 4 === $id || 5 === $id ) { $GLOBALS['es_gallery_groups']['urban'][] = $post; }
}
class WP_Query {
	public array $posts = array();
	public function __construct( array $args ) {
		$GLOBALS['es_gallery_queries'][] = $args;
		$slug = $args['tax_query'][0]['terms'] ?? 'all';
		$posts = $GLOBALS['es_gallery_groups'][ $slug ] ?? array();
		$this->posts = array_slice( $posts, 0, (int) $args['posts_per_page'] );
	}
}
function get_transient( $key ) { return $GLOBALS['es_gallery_transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl ) { $GLOBALS['es_gallery_transients'][ $key ] = $value; return true; }
function delete_transient( $key ) { unset( $GLOBALS['es_gallery_transients'][ $key ] ); return true; }
function get_the_title( $id ) { foreach ( $GLOBALS['es_gallery_groups']['all'] as $post ) { if ( (int) $post->ID === (int) $id ) { return $post->post_title; } } return ''; }
function get_permalink( $id ) { return 'https://example.test/projects/project-' . (int) $id . '/'; }
function get_the_post_thumbnail_url( $id, $size = 'large' ) { return 2 === (int) $id ? false : 'https://example.test/uploads/project-' . (int) $id . '.jpg'; }
function get_the_terms( $id, $taxonomy ) {
	if ( 'city' === $taxonomy ) { return array( (object) array( 'name' => 'شهر ' . (int) $id ) ); }
	if ( 'project_type' === $taxonomy ) { return array( (object) array( 'name' => ( (int) $id <= 3 ) ? 'تونل نوری' : 'پروژه شهری' ) ); }
	return array();
}
function get_post_meta( $id, $key, $single = false ) { return 'finish-pro-time' === $key ? '۱۴۰۴/۰۱/۰۱' : ''; }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Query/HomeQueries.php';

function es_gallery_panel_fragment( string $html, string $slug ): string {
	$needle = 'data-home2-port-panel="' . $slug . '"'; $position = strpos( $html, $needle );
	if ( false === $position ) { return ''; }
	$start = strrpos( substr( $html, 0, $position ), '<div' );
	if ( false === $start || ! preg_match_all( '/<\\/?div\\b[^>]*>/i', substr( $html, $start ), $tags, PREG_OFFSET_CAPTURE ) ) { return ''; }
	$depth = 0;
	foreach ( $tags[0] as $tag ) { $depth += str_starts_with( $tag[0], '</' ) ? -1 : 1; if ( 0 === $depth ) { return substr( $html, $start, $tag[1] + strlen( $tag[0] ) ); } }
	return '';
}
function es_gallery_card_count( string $html ): int { preg_match_all( '/<a\\b[^>]*class="card-light[^>]*>/i', $html, $matches ); return count( $matches[0] ); }

$json = (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/gallery.json' );
$args = array( 'section' => 'gallery', 'data' => array( 'content_json' => $json ) );
$render = static function () use ( &$args ): string { ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-gallery.php'; return (string) ob_get_clean(); };
$dynamic = $render();
$all_panel = es_gallery_panel_fragment( $dynamic, 'all' ); $tunnel_panel = es_gallery_panel_fragment( $dynamic, 'tunnel' ); $urban_panel = es_gallery_panel_fragment( $dynamic, 'urban' );
check( 'سه تب و پنل مرجع حفظ می‌شوند و تعداد کارت هر پنل با کوئری محدود پروژه هماهنگ است', fn() => substr_count( $dynamic, 'data-home2-port-tab=' ) === 3 && substr_count( $dynamic, 'data-home2-port-panel=' ) === 3 && 6 === es_gallery_card_count( $all_panel ) && 3 === es_gallery_card_count( $tunnel_panel ) && 2 === es_gallery_card_count( $urban_panel ) );
check( 'کارت‌های واقعی پروژه لینک single، عنوان/شهر و تصویر شاخص دارند و متن ورودی escape می‌شود', fn() => str_contains( $all_panel, 'href="https://example.test/projects/project-1/"' ) && str_contains( $all_panel, 'src="https://example.test/uploads/project-1.jpg"' ) && str_contains( $all_panel, 'alt="پروژه &lt;script&gt;unsafe&lt;/script&gt;"' ) && str_contains( $all_panel, 'شهر 1' ) && ! str_contains( $all_panel, '<script>unsafe</script>' ) );
check( 'کوئری‌ها تا شش پروژه را می‌گیرند و شمارنده تب‌ها با نتیجه واقعی یکی است', fn() => count( $GLOBALS['es_gallery_queries'] ) === 3 && array_reduce( $GLOBALS['es_gallery_queries'], static fn( $ok, $query ) => $ok && 6 === (int) ( $query['posts_per_page'] ?? 0 ) && true === ( $query['no_found_rows'] ?? false ), true ) && str_contains( $dynamic, '__ES_T004__' ) === false && str_contains( $dynamic, '>۶</span>' ) && str_contains( $dynamic, '>۳</span>' ) && str_contains( $dynamic, '>۲</span>' ) );
check( 'پروژه فاقد تصویر شاخص از تصویر fallback محلی کارت استفاده می‌کند', fn() => str_contains( $all_panel, '/assets/img/light-element.jpg' ) && false === strpos( $all_panel, 'src=""' ) );
$GLOBALS['es_gallery_options']['archive.home_projects_limit'] = '4'; \ErfanSanat\Theme\Query\HomeQueries::invalidate(); $GLOBALS['es_gallery_queries'] = array(); $limited = $render(); $limited_all = es_gallery_panel_fragment( $limited, 'all' );
check( 'تعداد کارت نمونه‌کار از ArchiveTab قابل تنظیم است و هرگز از شش بیشتر نمی‌شود', fn() => 4 === es_gallery_card_count( $limited_all ) && str_contains( $limited, '>۴</span>' ) && count( $GLOBALS['es_gallery_queries'] ) === 3 && array_reduce( $GLOBALS['es_gallery_queries'], static fn( $ok, $query ) => $ok && 4 === (int) ( $query['posts_per_page'] ?? 0 ), true ) );
\ErfanSanat\Theme\Query\HomeQueries::invalidate(); $GLOBALS['es_gallery_groups'] = array( 'all' => array(), 'tunnel' => array(), 'urban' => array() );
$warnings = array(); set_error_handler( static function ( $severity, $message ) use ( &$warnings ) { $warnings[] = $message; return true; } ); $empty = $render(); restore_error_handler();
check( 'کوئری خالی بدون warning از محتوای ایستای قابل ویرایش به‌عنوان fallback استفاده می‌کند', fn() => empty( $warnings ) && str_contains( $empty, 'اجرای پروژه نیم هرم نوری اهواز' ) && str_contains( $empty, 'data-home2-port-panel="all"' ) );
summary();
