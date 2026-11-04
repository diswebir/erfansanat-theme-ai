<?php
/** Task 5.14 — four recent posts, Jalali-aware display dates, and empty-state omission. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'blog-preview', 'template-parts/home/section-blog-preview.php', 12, 'ویترین مقالات' );

$GLOBALS['es_p5_blog_posts'] = array();
$GLOBALS['es_p5_blog_transients'] = array();
function get_transient( $key ) { return $GLOBALS['es_p5_blog_transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl ) { $GLOBALS['es_p5_blog_transients'][ $key ] = $value; return true; }
function delete_transient( $key ) { unset( $GLOBALS['es_p5_blog_transients'][ $key ] ); return true; }
class WP_Query {
	public array $posts = array();
	public function __construct( array $args ) { $this->posts = array_slice( $GLOBALS['es_p5_blog_posts'], 0, (int) $args['posts_per_page'] ); }
}
function get_the_title( $post ) { return $post->post_title ?? ''; }
function get_permalink( $post ) { return $post->permalink ?? ''; }
function get_the_post_thumbnail_url( $post_id, $size ) { foreach ( $GLOBALS['es_p5_blog_posts'] as $post ) { if ( (int) $post->ID === (int) $post_id ) { return $post->thumbnail ?? ''; } } return ''; }
function get_the_category( $post_id ) { return array( (object) array( 'name' => 'مقاله تازه' ) ); }
function get_the_date( $format, $post ) { return $post->jalali_date ?? ''; }
function get_the_excerpt( $post ) { return $post->excerpt ?? ''; }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Query/HomeQueries.php';

$posts = array();
for ( $i = 1; $i <= 4; ++$i ) {
	$posts[] = (object) array( 'ID' => $i, 'post_title' => 'Dynamic article ' . $i, 'permalink' => 'https://example.test/article-' . $i, 'thumbnail' => 'https://example.test/article-' . $i . '.jpg', 'jalali_date' => '۲۱ مرداد ۱۴۰۵', 'excerpt' => 'Dynamic summary for article ' . $i . ' that is intentionally long enough to replace the saved reference excerpt in the card.' );
}
$GLOBALS['es_p5_blog_posts'] = $posts;
$args = array( 'section' => 'blog-preview', 'data' => array( 'content_json' => (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/blog-preview.json' ) ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-blog-preview.php'; $dynamic = (string) ob_get_clean();
check( '۴ مقاله تازه با permalink، تصویر شاخص، عنوان، excerpt، دسته و تاریخ شمسی جایگزین می‌شوند', fn() => str_contains( $dynamic, 'Dynamic article 1' ) && str_contains( $dynamic, 'https://example.test/article-1' ) && str_contains( $dynamic, 'https://example.test/article-1.jpg' ) && str_contains( $dynamic, 'Dynamic summary for article 1' ) && str_contains( $dynamic, 'مقاله تازه' ) && str_contains( $dynamic, '۲۱ مرداد ۱۴۰۵' ) && str_contains( $dynamic, 'Dynamic article 4' ) );
$GLOBALS['es_p5_blog_posts'] = array();
\ErfanSanat\Theme\Query\HomeQueries::invalidate();
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-blog-preview.php'; $empty = (string) ob_get_clean();
check( 'در نبود مقاله کل سکشن حذف می‌شود، بدون Warning', fn() => '' === trim( $empty ) );
summary();
