<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.4 online purchase panel ===\n";
$id = $makeProduct(['true_sale' => 'true']);
$html = $renderPart('purchase-panel');
foreach (['form', 'name="add-to-cart"', 'quantity', 'single_add_to_cart_button', '125,000', 'data-product-min=', 'data-product-step='] as $needle) $assert(str_contains($html, $needle), 'online purchase contract ' . $needle);
$assert(str_contains($html, 'موجود'), 'stock status shown');
update_post_meta($id, 'true_sale', 'false');
$html2 = $renderPart('purchase-panel');
$assert(trim($html2) === '', 'purchase panel omitted for inquiry product');
wp_delete_post($id, true);
$finishTest();
