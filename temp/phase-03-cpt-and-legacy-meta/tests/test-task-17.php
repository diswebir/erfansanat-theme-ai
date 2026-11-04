<?php
/** Task 3.17 — bot-aware session view count with legacy fallback. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_postmeta'] = array( 817 => array( 'number2-viewc' => '', 'number-viewc' => '9' ), 818 => array( 'number-viewc' => '12' ) ); $GLOBALS['es_phase3_posttypes'] = array( 817 => 'post', 818 => 'projects', 819 => 'product' );
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key = '', $single = false ) { return '' === $key ? ( $GLOBALS['es_phase3_postmeta'][$id] ?? array() ) : ( $GLOBALS['es_phase3_postmeta'][$id][$key] ?? '' ); } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['es_phase3_postmeta'][$id][$key] = $value; return true; } }
if ( ! function_exists( 'get_post_type' ) ) { function get_post_type( $id ) { return $GLOBALS['es_phase3_posttypes'][$id] ?? false; } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs((int)$v); } }
if ( ! function_exists( 'is_ssl' ) ) { function is_ssl() { return false; } }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/Views.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/functions.php';
$class = '\\ErfanSanat\\Theme\\Meta\\Views';
$first = $class::queue_view( 817 ); $second_same_request = $class::queue_view( 817 ); $class::flush_queue();
$legacy_read = $class::get_views( 818 ); $only_new_key = isset( $GLOBALS['es_phase3_postmeta'][817]['number2-viewc'] ) && 10 === (int) $GLOBALS['es_phase3_postmeta'][817]['number2-viewc'] && '9' === $GLOBALS['es_phase3_postmeta'][817]['number-viewc'];
$bot_checks = $class::is_bot( 'Googlebot/2.1' ) && $class::is_bot( 'curl/8.0' ) && ! $class::is_bot( 'Mozilla/5.0 (X11; Linux x86_64) Chrome/120.0' );
$_COOKIE['es_viewed_819'] = '1'; $cookie_blocks = ! $class::queue_view( 819 );
check( 'در یک نشست یک شناسه فقط یک بار در صف شمارش قرار می‌گیرد', fn() => $first && ! $second_same_request && $cookie_blocks );
check( 'ربات‌ها و ابزارهای خودکار قابل تشخیص‌اند', fn() => $bot_checks );
check( 'نوشتن فقط روی number2-viewc انجام می‌شود و در shutdown از کلید قبلی مقدار می‌گیرد', fn() => $only_new_key );
check( 'خواندن در نبود کلید جدید به number-viewc fallback می‌کند', fn() => 12 === $legacy_read && 10 === es_get_views( 817 ) );
summary();
