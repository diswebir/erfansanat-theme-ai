<?php
/** Task 3.8 — legacy metadata repository and bidirectional JetEngine codec. */
declare(strict_types=1);

require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_postmeta'] = array();
$GLOBALS['es_phase3_termmeta'] = array();
$GLOBALS['es_phase3_meta_calls'] = 0;
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key = '', $single = false ) { $GLOBALS['es_phase3_meta_calls']++; if ( '' === $key ) { return $GLOBALS['es_phase3_postmeta'][ $id ] ?? array(); } return $GLOBALS['es_phase3_postmeta'][ $id ][ $key ] ?? ''; } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['es_phase3_postmeta'][ $id ][ $key ] = $value; return true; } }
if ( ! function_exists( 'get_term_meta' ) ) { function get_term_meta( $id, $key = '', $single = false ) { $GLOBALS['es_phase3_meta_calls']++; if ( '' === $key ) { return $GLOBALS['es_phase3_termmeta'][ $id ] ?? array(); } return $GLOBALS['es_phase3_termmeta'][ $id ][ $key ] ?? ''; } }
if ( ! function_exists( 'update_term_meta' ) ) { function update_term_meta( $id, $key, $value ) { $GLOBALS['es_phase3_termmeta'][ $id ][ $key ] = $value; return true; } }

$repo_file = ES_REPO_ROOT . '/theme/inc/Meta/MetaRepository.php';
$codec_file = ES_REPO_ROOT . '/theme/inc/Meta/RepeaterCodec.php';
$helpers_file = ES_REPO_ROOT . '/theme/inc/Meta/functions.php';
$available = is_file( $repo_file ) && is_file( $codec_file ) && is_file( $helpers_file );
if ( $available ) { require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php'; require_once $repo_file; require_once $codec_file; require_once $helpers_file; }

$original_json = '{"item-0":{"add-new-feature":"Lumen","vaule_of_variable":"240"}}';
$original_serialized = serialize( array( 'item-0' => array( 'name_question' => 'Q', 'answer_question' => 'A' ) ) );
$GLOBALS['es_phase3_postmeta'][ 31 ] = array( 'features_json' => $original_json, 'faq_serialized' => $original_serialized, 'galerry_produ' => '15,28,39', 'cached_value' => 'cached' );
$GLOBALS['es_phase3_termmeta'][ 7 ] = array( 'thumbnail' => '90' );
if ( $available ) {
	$decoded_json = \ErfanSanat\Theme\Meta\MetaRepository::get( 31, 'features_json' );
	$decoded_serialized = \ErfanSanat\Theme\Meta\MetaRepository::get( 31, 'faq_serialized' );
	\ErfanSanat\Theme\Meta\MetaRepository::set( 31, 'features_json', array( 'item-0' => array( 'add-new-feature' => 'Updated', 'vaule_of_variable' => '300' ) ) );
	\ErfanSanat\Theme\Meta\MetaRepository::set( 31, 'faq_serialized', array( 'item-0' => array( 'name_question' => 'Q2', 'answer_question' => 'A2' ) ) );
	$gallery = \ErfanSanat\Theme\Meta\MetaRepository::get( 31, 'galerry_produ' );
	$round_trip_json = json_decode( $GLOBALS['es_phase3_postmeta'][31]['features_json'], true );
	$round_trip_serialized = unserialize( $GLOBALS['es_phase3_postmeta'][31]['faq_serialized'], array( 'allowed_classes' => false ) );
	$GLOBALS['es_phase3_meta_calls'] = 0;
	\ErfanSanat\Theme\Meta\MetaRepository::clear_cache();
	for ( $i = 0; $i < 10; $i++ ) { \ErfanSanat\Theme\Meta\MetaRepository::get( 31, 'cached_value' ); }
	$cached_reads = $GLOBALS['es_phase3_meta_calls'];
	$term_value = es_term_meta( 7, 'thumbnail' );
	$post_helper = es_meta( 31, 'cached_value' );
	$encoded_gallery = \ErfanSanat\Theme\Meta\RepeaterCodec::encode_csv( $gallery );
} else { $decoded_json = $decoded_serialized = $gallery = $round_trip_json = $round_trip_serialized = $term_value = $post_helper = $encoded_gallery = null; $cached_reads = 99; }

check( 'JSON and PHP-serialized repeaters round-trip without changing their stored format', fn() => $available && is_array( $decoded_json ) && isset( $decoded_json['item-0'] ) && is_array( $decoded_serialized ) && isset( $decoded_serialized['item-0'] ) && 'Updated' === $round_trip_json['item-0']['add-new-feature'] && 'Q2' === $round_trip_serialized['item-0']['name_question'] && str_starts_with( $GLOBALS['es_phase3_postmeta'][31]['features_json'], '{' ) && str_starts_with( $GLOBALS['es_phase3_postmeta'][31]['faq_serialized'], 'a:' ) );
check( 'single JetEngine item-N JSON row keeps its exact key and shape', fn() => is_array( $decoded_json ) && array_keys( $decoded_json ) === array( 'item-0' ) && isset( $decoded_json['item-0']['vaule_of_variable'] ) );
check( 'CSV gallery IDs decode to a list and encode back as stable CSV', fn() => array( 15, 28, 39 ) === $gallery && '15,28,39' === $encoded_gallery );
check( 'post and term helper APIs work, cache hits once, and repository is wired into Theme', fn() => 1 === $cached_reads && 'cached' === $post_helper && '90' === $term_value && str_contains( (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Theme.php' ), 'new MetaRepository()' ) );
summary();
