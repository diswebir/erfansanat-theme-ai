<?php
/** Task 5.15 — contact CTA reads canonical phone/email options. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_p5_contact_options'] = array();
function es_option( $key, $default = null ) { return $GLOBALS['es_p5_contact_options'][ $key ] ?? $default; }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'contact-cta', 'template-parts/home/section-contact-cta.php', 13, 'CTA تماس' );
$GLOBALS['es_p5_contact_options'] = array( 'header.phone' => '021-555-0101', 'header.email' => 'sales@example.test' );
$args = array( 'section' => 'contact-cta', 'data' => array( 'content_json' => (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/contact-cta.json' ) ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-contact-cta.php'; $output = (string) ob_get_clean();
check( 'تلفن و mailto در متن و لینک از header options خوانده می‌شوند', fn() => str_contains( $output, '021-555-0101' ) && str_contains( $output, 'tel:021-555-0101' ) && str_contains( $output, 'sales@example.test' ) && str_contains( $output, 'mailto:sales@example.test' ) );
summary();
