<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.5 inquiry panel ===\n";
$id = $makeProduct(['true_sale' => 'false', 'before-sale-c' => 'لطفاً پیش از سفارش تماس بگیرید']);
$html = $renderPart('inquiry-panel');
foreach (['استعلام قیمت', 'درخواست پیش‌فاکتور رسمی', 'tel:', '031-91091011'] as $needle) $assert(str_contains($html, $needle), 'inquiry contract ' . $needle);
$assert(!str_contains($html, 'add-to-cart'), 'no direct purchase button for inquiry products');
update_post_meta($id, 'true_sale', 'true');
$assert(trim($renderPart('inquiry-panel')) === '', 'inquiry panel omitted for online products');
wp_delete_post($id, true);
$finishTest();
