<?php
/** Task 3.12 — legacy-compatible product repeaters. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_postmeta'] = array( 812 => array(
	'product-feature' => '{"item-0":{"add-new-feature":"Durable"}}',
	'product-feature2_copy' => serialize( array( 'item-0' => array( 'add-new-feature' => 'Power', 'vaule_of_variable' => '240V' ) ) ),
	'faq' => serialize( array( 'item-0' => array( 'name_question' => 'Can it dim?', 'answer_question' => 'Yes' ) ) ),
) );
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key = '', $single = false ) { return '' === $key ? ( $GLOBALS['es_phase3_postmeta'][$id] ?? array() ) : ( $GLOBALS['es_phase3_postmeta'][$id][$key] ?? '' ); } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['es_phase3_postmeta'][$id][$key] = $value; return true; } }
if ( ! function_exists( 'delete_post_meta' ) ) { function delete_post_meta( $id, $key ) { unset( $GLOBALS['es_phase3_postmeta'][$id][$key] ); return true; } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return trim(strip_tags((string)$v)); } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $v ) { return strip_tags((string)$v, '<a><strong><em><p><br>'); } }
if ( ! function_exists( 'wp_strip_all_tags' ) ) { function wp_strip_all_tags( $v ) { return strip_tags((string)$v); } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $v, $d = null ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_textarea' ) ) { function esc_textarea( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/RepeaterCodec.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/MetaRepository.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/functions.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/Tabs/ProductRepeatersTab.php';
$meta = '\\ErfanSanat\\Theme\\Meta\\MetaRepository'; $tab = '\\ErfanSanat\\Theme\\Admin\\Metaboxes\\Tabs\\ProductRepeatersTab'; $meta::clear_cache();
$tab::save( 812, array(
	'product-feature' => array( 'item-0' => array( 'add-new-feature' => 'Updated <script>x</script>' ) ),
	'product-feature2_copy' => array( 'item-0' => array( 'add-new-feature' => 'Voltage', 'vaule_of_variable' => '300V' ) ),
	'usage_product' => array( 'item-0' => array( 'add-new-usage' => 'Industrial' ) ),
	'faq' => array( 'item-0' => array( 'name_question' => 'Safe?', 'answer_question' => '<p>Yes <script>bad()</script><strong>indeed</strong></p>' ) ),
) );
$feature = json_decode( $GLOBALS['es_phase3_postmeta'][812]['product-feature'], true );
$spec = unserialize( $GLOBALS['es_phase3_postmeta'][812]['product-feature2_copy'], array( 'allowed_classes' => false ) );
$faq = unserialize( $GLOBALS['es_phase3_postmeta'][812]['faq'], array( 'allowed_classes' => false ) );
$markup = $tab::render( 812 );
$crud_saved = isset( $GLOBALS['es_phase3_postmeta'][812]['product-feature'], $GLOBALS['es_phase3_postmeta'][812]['product-feature2_copy'], $GLOBALS['es_phase3_postmeta'][812]['usage_product'], $GLOBALS['es_phase3_postmeta'][812]['faq'] );
$formats_kept = str_starts_with( $GLOBALS['es_phase3_postmeta'][812]['product-feature'], '{' ) && str_starts_with( $GLOBALS['es_phase3_postmeta'][812]['faq'], 'a:' );
$tab::save( 812, array( 'product-feature' => array( 'item-0' => array( 'add-new-feature' => '' ) ), 'product-feature2_copy' => array( 'item-0' => array( 'add-new-feature' => '', 'vaule_of_variable' => '' ) ), 'usage_product' => array( 'item-0' => array( 'add-new-usage' => '' ) ), 'faq' => array( 'item-0' => array( 'name_question' => '', 'answer_question' => '' ) ) ) );
check( 'CRUD چهارگانه در exact legacy keys', fn() => $crud_saved && isset( $feature['item-0']['add-new-feature'] ) && isset( $spec['item-0']['vaule_of_variable'] ) && isset( $faq['item-0']['answer_question'] ) );
check( 'املای vaule_of_variable عیناً نگه داشته می‌شود', fn() => '300V' === $spec['item-0']['vaule_of_variable'] && ! isset( $spec['item-0']['value_of_variable'] ) && str_contains( $markup, 'vaule_of_variable' ) );
check( 'JSON/serialized JetEngine item-0 شکل خود را حفظ می‌کند', fn() => array_keys( $feature ) === array( 'item-0' ) && array_keys( $faq ) === array( 'item-0' ) && $formats_kept );
check( 'ردیف‌های کاملاً خالی ذخیره نمی‌شوند و متای تکرارکننده حذف می‌شود', fn() => ! isset( $GLOBALS['es_phase3_postmeta'][812]['product-feature'] ) && ! isset( $GLOBALS['es_phase3_postmeta'][812]['product-feature2_copy'] ) && ! isset( $GLOBALS['es_phase3_postmeta'][812]['usage_product'] ) && ! isset( $GLOBALS['es_phase3_postmeta'][812]['faq'] ) );
summary();
