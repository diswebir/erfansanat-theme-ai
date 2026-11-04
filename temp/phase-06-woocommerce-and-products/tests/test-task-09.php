<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.9 technical specs and identity codes ===\n";
$id = $makeProduct(['gs1_id' => 'GS1-123', 'national_code' => 'IR-456', 'hs_id' => 'HS-789', 'isic_id' => 'ISIC-10', 'cpc_id' => 'CPC-11']);
if (!taxonomy_exists('pa_color')) register_taxonomy('pa_color', 'product', ['public' => true, 'hierarchical' => false]);
$termObject = get_term_by('name', 'آفتابی', 'pa_color');
if (!$termObject instanceof WP_Term) { $term = wp_insert_term('آفتابی', 'pa_color'); if (!is_wp_error($term)) $termObject = get_term((int) $term['term_id'], 'pa_color'); }
if ($termObject instanceof WP_Term) wp_set_object_terms($id, [(int) $termObject->term_id], 'pa_color');
$html = $renderPart('tab-specs');
foreach (['۱۸ ویژگی فنی', 'رنگ نور', 'آفتابی', 'GS1-123', 'IR-456', 'HS-789', 'ISIC-10', 'CPC-11'] as $needle) $assert(str_contains($html, $needle), 'technical spec contract ' . $needle);
$assert(str_contains($html, 'data-product-specs'), 'specification component hook');
wp_delete_post($id, true); $finishTest();
