<?php
/** Task 5.8 — cached product-category cards with static image fallback. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_p5_terms'] = array(); $GLOBALS['es_p5_term_calls'] = 0; $GLOBALS['es_p5_term_cache'] = array();
function get_terms( $args ) { ++$GLOBALS['es_p5_term_calls']; return $GLOBALS['es_p5_terms']; }
function get_term_link( $term ) { return 'https://example.test/product-category/' . $term->slug; }
function get_term_meta( $term_id, $key, $single = false ) { return 1 === (int) $term_id ? 501 : 0; }
function wp_get_attachment_image_url( $image_id, $size ) { return 501 === (int) $image_id ? 'https://example.test/category-image.jpg' : false; }
function get_transient( $key ) { return $GLOBALS['es_p5_term_cache'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl ) { $GLOBALS['es_p5_term_cache'][ $key ] = $value; return true; }
function delete_transient( $key ) { unset( $GLOBALS['es_p5_term_cache'][ $key ] ); return true; }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'products', 'template-parts/home/section-products.php', 6, 'دسته‌بندی‌های محصول' );
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Query/HomeQueries.php';
for ( $i = 1; $i <= 4; ++$i ) { $GLOBALS['es_p5_terms'][] = (object) array( 'term_id' => $i, 'name' => 'Category ' . $i, 'slug' => 'category-' . $i, 'count' => 4 ); }
$args = array( 'section' => 'products', 'data' => array( 'content_json' => (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/products.json' ) ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-products.php'; $output = (string) ob_get_clean();
check( '۴ دستهٔ زنده لینک/عنوان دارند و تصویر شاخص موجود جایگزین می‌شود', fn() => str_contains( $output, 'Category 1' ) && str_contains( $output, 'Category 4' ) && str_contains( $output, 'https://example.test/product-category/category-1' ) && str_contains( $output, 'https://example.test/category-image.jpg' ) && substr_count( $output, '<img ' ) === 4 );
check( 'در نبود تصویر term، placeholder استاتیک حفظ می‌شود', fn() => str_contains( $output, 'theme/assets/img/sun-light.jpg' ) || str_contains( $output, 'sun-light.jpg' ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-products.php'; ob_end_clean();
check( 'بار دوم رندر، product_cat از transient cache می‌آید', fn() => 1 === $GLOBALS['es_p5_term_calls'] );
summary();
