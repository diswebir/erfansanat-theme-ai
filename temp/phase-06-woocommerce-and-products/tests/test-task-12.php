<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.12 product FAQ and reviews ===\n";
$id = $makeProduct();
wp_update_post(['ID' => $id, 'comment_status' => 'open']);
update_post_meta($id, 'faq', ['item-0' => ['name_question' => 'پرسش QA چیست؟', 'answer_question' => 'پاسخ QA این است.']]);
$html = $renderPart('faq-reviews');
foreach (['data-faq-list="true"', 'data-faq-toggle="0"', 'data-faq-body="0"', 'پرسش QA چیست؟', 'پاسخ QA این است.', 'data-review-form="true"', 'data-review-star=', 'data-review-rating-input="true"', 'data-reviews-list="true"'] as $needle) $assert(str_contains($html, $needle), 'FAQ/review contract ' . $needle);
wp_delete_post($id, true); $finishTest();
