<?php
/** Task 5.16 — contact section follows canonical address and stays self-hosted. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_p5_contact_options'] = array();
function es_option( $key, $default = null ) { return $GLOBALS['es_p5_contact_options'][ $key ] ?? $default; }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'map-contact', 'template-parts/home/section-map-contact.php', 14, 'موقعیت و اطلاعات تماس' );
$GLOBALS['es_p5_contact_options']['footer.address'] = 'نشانی آزمایشی، تهران';
$args = array( 'section' => 'map-contact', 'data' => array( 'content_json' => (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/map-contact.json' ) ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-map-contact.php'; $output = (string) ob_get_clean();
check( 'نشانی از footer options جایگزین می‌شود و نبود نقشه third-party سکشن تماس را حفظ می‌کند', fn() => str_contains( $output, 'نشانی آزمایشی، تهران' ) && str_contains( $output, 'مراجعه حضوری' ) && ! preg_match( '/<iframe\\b|<script\\b/i', $output ) );
summary();
