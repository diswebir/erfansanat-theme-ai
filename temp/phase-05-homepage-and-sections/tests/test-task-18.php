<?php
/** Task 5.18 — shared product, project, and post cards. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $value ) { $value = (string) $value; return preg_match( '/^(?:https?|mailto|tel):/i', $value ) ? htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ) : ''; } }
require_once __DIR__ . '/section-test-support.php';
require_once ES_REPO_ROOT . '/theme/inc/Sections/HomeCardMarkup.php';
$cases = array(
	'product' => array( 'template-parts/cards/card-product.php', array( 'title' => 'Product sample', 'url' => 'https://example.test/product/sample', 'image' => 'https://example.test/product.jpg', 'alt' => 'Product sample', 'category' => 'Outdoor', 'badge' => 'IP68', 'excerpt' => 'Product features', 'post_id' => 42, 'price' => '500', 'stock_status' => 'instock' ) ),
	'project' => array( 'template-parts/cards/card-project.php', array( 'title' => 'Project sample', 'url' => 'https://example.test/projects/sample', 'image' => 'https://example.test/project.jpg', 'alt' => 'Project sample', 'city' => 'Tehran', 'meta' => 'Lighting · 1405', 'cta' => 'View project' ) ),
	'post' => array( 'template-parts/cards/card-post.php', array( 'title' => 'Post sample', 'url' => 'https://example.test/blog/sample', 'image' => 'https://example.test/post.jpg', 'alt' => 'Post sample', 'category' => 'Insights', 'date' => '۱۴۰۵/۰۸/۱۳', 'reading_time' => '5 minutes', 'excerpt' => 'Useful article summary', 'cta' => 'Read article' ) ),
);
foreach ( $cases as $type => $case ) {
	[ $partial, $args ] = $case;
	$source = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/cards/static/card-' . $type . '.html' );
	ob_start(); $GLOBALS['args'] = $args; include ES_REPO_ROOT . '/theme/' . $partial; $rendered = (string) ob_get_clean();
	check( $type . ' card keeps the static reference tag/class signature', fn() => \ErfanSanat\Harness\DomDiff::similarity( $source, $rendered ) >= 99.0 );
	check( $type . ' card uses passed data and emits nonempty image/URL', fn() => str_contains( $rendered, $args['title'] ) && str_contains( $rendered, $args['url'] ) && str_contains( $rendered, $args['image'] ) );
}
$product = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/cards/static/card-product.html' );
ob_start(); $args = array( 'post_id' => 43, 'title' => 'Outdoor lamp', 'url' => 'https://example.test/lamp', 'image' => '', 'product_category' => 'lighting', 'price' => '1200', 'stock_status' => 'instock' ); include ES_REPO_ROOT . '/theme/template-parts/cards/card-product.php'; $product = (string) ob_get_clean();
check( 'کارت محصول همه data-product-* را پر می‌کند و تصویر خالی placeholder دارد', fn() => str_contains( $product, 'data-product-item="true"' ) && str_contains( $product, 'data-product-id="43"' ) && str_contains( $product, 'data-product-category="lighting"' ) && str_contains( $product, 'data-product-price="1200"' ) && str_contains( $product, 'data-product-stock="instock"' ) && str_contains( $product, '/assets/img/light-tunnel.jpg' ) );
ob_start(); $args = array( 'title' => '<script>alert(1)</script>', 'url' => 'javascript:alert(1)', 'image' => 'https://example.test/safe.jpg' ); include ES_REPO_ROOT . '/theme/template-parts/cards/card-post.php'; $safe = (string) ob_get_clean();
check( 'متن کارت escape و ورودی URL از پروتکل اجرایی رد می‌شود', fn() => ! str_contains( $safe, '<script>' ) && ! str_contains( $safe, 'href="javascript:' ) );
summary();
