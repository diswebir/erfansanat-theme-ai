<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.10 two-column key features ===\n";
$id = $makeProduct();
update_post_meta($id, 'product-feature2_copy', ['item-0' => ['add-new-feature' => 'گارانتی', 'vaule_of_variable' => '۲۴ ماه'], 'item-1' => ['add-new-feature' => 'توان', 'vaule_of_variable' => '۱۰۰ وات']]);
$html = $renderPart('key-features');
foreach (['گارانتی', '۲۴ ماه', 'توان', '۱۰۰ وات'] as $needle) $assert(str_contains($html, $needle), 'key feature ' . $needle);
$assert(str_contains($html, 'data-key-features'), 'key features section hook');
wp_delete_post($id, true); $finishTest();
