<?php
/**
 * Test Task 6.2 — archive-product + taxonomy templates with cards + pagination.
 * Run: php-wasm-cli temp/phase-06-woocommerce-and-products/tests/test-task-6-2.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c ? "[PASS] " : "[FAIL] ") . $l . "\n"; if (!$c) $fail++; }

foreach (['archive-product.php', 'taxonomy-product_cat.php', 'taxonomy-product_tag.php'] as $f) {
    check("$f exists", is_file("$THEME/$f"));
}
$a = file_get_contents("$THEME/archive-product.php");
check('archive loops products with card-product', strpos($a, "card', 'product") !== false && strpos($a, 'have_posts') !== false);
check('archive has pagination', strpos($a, 'woocommerce_pagination') !== false || strpos($a, 'the_posts_pagination') !== false);

$tc = file_get_contents("$THEME/taxonomy-product_cat.php");
check('product_cat shows thumbnail term meta', strpos($tc, "'thumbnail'") !== false && strpos($tc, 'get_term_meta') !== false);
check('product_cat shows faq term meta', strpos($tc, "'faq'") !== false);
check('product_cat renders grid + pagination', strpos($tc, "card', 'product") !== false && strpos($tc, 'the_posts_pagination') !== false);

$tt = file_get_contents("$THEME/taxonomy-product_tag.php");
check('product_tag archive renders grid', strpos($tt, "card', 'product") !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
