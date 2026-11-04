<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.3 product gallery and lightbox ===\n";
$id = $makeProduct();
$attachment = wp_insert_attachment(['post_title' => 'QA gallery image', 'post_mime_type' => 'image/jpeg', 'post_status' => 'inherit'], '', $id);
update_post_meta($id, 'galerry_produ', (string) $attachment);
$html = $renderPart('gallery');
foreach (['data-gallery-root="true"', 'data-gallery-main-img="true"', 'data-gallery-thumb', 'data-gallery-src=', 'data-gallery-counter="true"', 'data-gallery-prev="true"', 'data-gallery-next="true"', 'data-gallery-expand="true"', 'data-gallery-lightbox="true"', 'data-gallery-lightbox-dot'] as $needle) $assert(str_contains($html, $needle), 'gallery contract ' . $needle);
$assert(\ErfanSanat\Theme\Helpers\LegacyMeta::productGalleryIds($id) === [(int) $attachment], 'exact galerry_produ key is parsed by fallback helper');
$assert(substr_count($html, 'گارانتی') > 0 && str_contains($html, 'ارسال سراسری') && str_contains($html, 'فاکتور رسمی'), 'four trust badges are present');
wp_delete_post($attachment, true); wp_delete_post($id, true);
$finishTest();
