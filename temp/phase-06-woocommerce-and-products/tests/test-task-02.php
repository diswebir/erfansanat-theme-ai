<?php
/**
 * Contract-test scaffold for task 6.2: آرشیو فروشگاه و دسته محصول.
 * Run from repository root with: php temp/phase-06-woocommerce-and-products/tests/test-task-02.php
 * This planning-stage test is expected to fail until its task is implemented.
 */
$root = dirname(__DIR__, 3);
$checks = [
    ['archive-product.php', 'woocommerce'],
    ['taxonomy-product_cat.php', 'faq'],
    ['taxonomy-product_tag.php', 'product_tag']
];
$failed = 0;
foreach ($checks as [$relative, $needle]) {
    $path = $root . DIRECTORY_SEPARATOR . $relative;
    if (!is_file($path)) {
        fwrite(STDERR, "FAIL missing file: {$relative}\n");
        $failed++;
        continue;
    }
    $source = file_get_contents($path);
    if ($source === false || strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL missing contract `{$needle}` in {$relative}\n");
        $failed++;
    } else {
        fwrite(STDOUT, "PASS {$relative} contains `{$needle}`\n");
    }
}
exit($failed === 0 ? 0 : 1);
