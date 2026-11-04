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
$GLOBALS['es_card_loop_posts'] = array(
	(object) array( 'ID' => 701, 'post_type' => 'product', 'post_title' => 'Lamp product' ),
	(object) array( 'ID' => 702, 'post_type' => 'projects', 'post_title' => 'Urban project' ),
	(object) array( 'ID' => 703, 'post_type' => 'post', 'post_title' => 'Company news' ),
);
$GLOBALS['es_card_loop_index'] = 0; $GLOBALS['es_card_current_post'] = null;
if ( ! function_exists( 'get_header' ) ) { function get_header() {} }
if ( ! function_exists( 'get_footer' ) ) { function get_footer() {} }
if ( ! function_exists( 'have_posts' ) ) { function have_posts() { return $GLOBALS['es_card_loop_index'] < count( $GLOBALS['es_card_loop_posts'] ); } }
if ( ! function_exists( 'the_post' ) ) { function the_post() { $GLOBALS['es_card_current_post'] = $GLOBALS['es_card_loop_posts'][ $GLOBALS['es_card_loop_index']++ ] ?? null; } }
if ( ! function_exists( 'get_post' ) ) { function get_post( $id = null ) { return $GLOBALS['es_card_current_post']; } }
if ( ! function_exists( 'get_the_ID' ) ) { function get_the_ID() { return (int) ( $GLOBALS['es_card_current_post']->ID ?? 0 ); } }
if ( ! function_exists( 'get_post_type' ) ) { function get_post_type( $id = null ) { return (string) ( $GLOBALS['es_card_current_post']->post_type ?? '' ); } }
if ( ! function_exists( 'get_the_title' ) ) { function get_the_title( $id = null ) { return (string) ( $GLOBALS['es_card_current_post']->post_title ?? '' ); } }
if ( ! function_exists( 'get_permalink' ) ) { function get_permalink( $id = null ) { return 'https://example.test/post/' . (int) ( $id ?? get_the_ID() ) . '/'; } }
if ( ! function_exists( 'get_the_post_thumbnail_url' ) ) { function get_the_post_thumbnail_url( $id = null, $size = 'large' ) { return 'https://example.test/image/' . (int) ( $id ?? get_the_ID() ) . '.jpg'; } }
if ( ! function_exists( 'get_the_excerpt' ) ) { function get_the_excerpt( $id = null ) { return 'Fixture excerpt'; } }
if ( ! function_exists( 'get_the_date' ) ) { function get_the_date( $format = '', $id = null ) { return '۱۴۰۵/۰۸/۰۴'; } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key, $single = false ) { return 'finish-pro-time' === $key ? '۱۴۰۴/۰۱/۰۱' : ''; } }
if ( ! function_exists( 'get_the_terms' ) ) { function get_the_terms( $id, $taxonomy ) { if ( 'city' === $taxonomy ) { return array( (object) array( 'name' => 'تهران' ) ); } if ( 'project_type' === $taxonomy ) { return array( (object) array( 'name' => 'نورپردازی شهری' ) ); } if ( 'product_cat' === $taxonomy ) { return array( (object) array( 'name' => 'روشنایی', 'slug' => 'lighting' ) ); } return array(); } }
if ( ! function_exists( 'get_the_category' ) ) { function get_the_category( $id = null ) { return array( (object) array( 'name' => 'خبرها' ) ); } }
if ( ! function_exists( 'the_posts_pagination' ) ) { function the_posts_pagination() {} }
if ( ! function_exists( 'esc_html_e' ) ) { function esc_html_e( $text, $domain = null ) { echo esc_html( $text ); } }
if ( ! function_exists( 'get_template_part' ) ) { function get_template_part( $slug, $name = null, $args = array() ) { $path = ES_REPO_ROOT . '/theme/' . $slug . '.php'; if ( is_file( $path ) ) { $args = is_array( $args ) ? $args : array(); include $path; } } }
if ( ! class_exists( 'EsCardMockProduct' ) ) { class EsCardMockProduct { public function get_id() { return 704; } public function is_visible() { return true; } public function get_name() { return 'Woo loop lamp'; } public function get_sku() { return 'SKU-704'; } public function get_price() { return '850'; } public function get_stock_status() { return 'instock'; } } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'wc_product_class' ) ) { function wc_product_class( $class = '', $product = null ) { echo 'class="product ' . htmlspecialchars( (string) $class, ENT_QUOTES, 'UTF-8' ) . '"'; } }
if ( ! function_exists( 'wc_get_product' ) ) { function wc_get_product( $id ) { return new EsCardMockProduct(); } }
ob_start(); include ES_REPO_ROOT . '/theme/index.php'; $archive_cards = (string) ob_get_clean();
check( 'index archive/search loop dispatches product, project and post results through shared card partials', fn() => str_contains( $archive_cards, 'data-product-item="true"' ) && str_contains( $archive_cards, 'data-product-category="lighting"' ) && str_contains( $archive_cards, 'تهران' ) && str_contains( $archive_cards, 'Urban project' ) && str_contains( $archive_cards, 'Company news' ) && str_contains( $archive_cards, 'grid-cols-1' ) );
$product = new EsCardMockProduct();
ob_start(); include ES_REPO_ROOT . '/theme/woocommerce/content-product.php'; $woo_loop_card = (string) ob_get_clean();
check( 'WooCommerce product archive and related loops use the shared product card and preserve filter data', fn() => str_contains( $woo_loop_card, '<li class="product' ) && str_contains( $woo_loop_card, 'Woo loop lamp' ) && str_contains( $woo_loop_card, 'href="https://example.test/post/704/"' ) && str_contains( $woo_loop_card, 'data-product-item="true"' ) && str_contains( $woo_loop_card, 'data-product-id="704"' ) && str_contains( $woo_loop_card, 'data-product-category="lighting"' ) && str_contains( $woo_loop_card, 'data-product-price="850"' ) && str_contains( $woo_loop_card, 'data-product-stock="instock"' ) );
summary();
