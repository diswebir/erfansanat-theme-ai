<?php
/** Task 3.18 — Phase 3 integration and metadata compatibility gate. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
require_once ES_REPO_ROOT . '/theme/inc/Meta/RepeaterCodec.php';
$fixtures = require ES_REPO_ROOT . '/tools/harness/seed-fixtures.php';
$prior = $GLOBALS['es_phase_prior_result'] ?? array();
$phase_ok = 17 === (int) ( $prior['files'] ?? 0 ) && 0 === (int) ( $prior['failed'] ?? -1 ) && 0 === (int) ( $prior['pending'] ?? -1 ) && (int) ( $prior['passed'] ?? 0 ) >= 63;
$json_original = $fixtures['products'][9301]['meta']['product-feature'];
$serialized_original = $fixtures['products'][9302]['meta']['faq'];
$json_decoded = \ErfanSanat\Theme\Meta\RepeaterCodec::decode( $json_original );
$serialized_decoded = \ErfanSanat\Theme\Meta\RepeaterCodec::decode( $serialized_original );
$json_round_trip = \ErfanSanat\Theme\Meta\RepeaterCodec::preserve_format( $json_original, $json_decoded );
$serialized_round_trip = \ErfanSanat\Theme\Meta\RepeaterCodec::preserve_format( $serialized_original, $serialized_decoded );
$post_ids = array_merge( array_keys( $fixtures['projects'] ), array_keys( $fixtures['products'] ) );
$term_ids = array_keys( $fixtures['terms'] );
$postmeta_rows = array(); foreach ( ( $fixtures['projects'] + $fixtures['products'] ) as $parent_id => $record ) { foreach ( $record['meta'] as $key => $value ) { $postmeta_rows[] = array( 'post_id' => (int) $parent_id, 'meta_key' => $key, 'meta_value' => $value ); } }
$termmeta_rows = array(); foreach ( $fixtures['terms'] as $parent_id => $record ) { foreach ( $record['meta'] as $key => $value ) { $termmeta_rows[] = array( 'term_id' => (int) $parent_id, 'meta_key' => $key, 'meta_value' => $value ); } }
$orphan_postmeta = array_filter( $postmeta_rows, fn( $row ) => ! in_array( $row['post_id'], $post_ids, true ) );
$orphan_termmeta = array_filter( $termmeta_rows, fn( $row ) => ! in_array( $row['term_id'], $term_ids, true ) );
check( 'اجرای کل فاز: ۱۷ تسک قبلی سبزند و هیچ تستی fail/pending نیست', fn() => $phase_ok );
check( 'داده‌های JSON و PHP-serialized پس از round-trip معناداراً برابرند', fn() => $json_original === $json_round_trip && $serialized_original === $serialized_round_trip && isset( $json_decoded['item-0']['add-new-feature'], $serialized_decoded['item-0']['answer_question'] ) && 2 === count( $fixtures['projects'] ) && 2 === count( $fixtures['products'] ) && 2 === count( $fixtures['terms'] ) );
check( 'هیچ متای seed بدون parent پروژه/محصول/ترم وجود ندارد', fn() => ! $orphan_postmeta && ! $orphan_termmeta && 4 === count( $post_ids ) && 2 === count( $term_ids ) && count( $postmeta_rows ) > 0 && count( $termmeta_rows ) > 0 );
summary();
