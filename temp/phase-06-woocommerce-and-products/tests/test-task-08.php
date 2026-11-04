<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.8 introduction and applications tab ===\n";
$id = $makeProduct();
$usageTerm = get_term_by('name', 'فضای سبز QA', 'project_type');
if (!$usageTerm instanceof WP_Term) { $created = wp_insert_term('فضای سبز QA', 'project_type'); if (!is_wp_error($created)) $usageTerm = get_term((int) $created['term_id'], 'project_type'); }
if ($usageTerm instanceof WP_Term) update_post_meta($id, 'usage_countr', [(int) $usageTerm->term_id]);
update_post_meta($id, 'product-feature', ['item-0' => ['add-new-feature' => 'بدنه مقاوم']]);
update_post_meta($id, 'usage_product', ['item-0' => ['add-new-usage' => 'نورپردازی میدان']]);
$html = $renderPart('tab-intro');
foreach (['شرح کامل محصول', 'بدنه مقاوم', 'نورپردازی میدان', 'فضای سبز QA'] as $needle) $assert(str_contains($html, $needle), 'intro contract ' . $needle);
wp_delete_post($id, true); $finishTest();
