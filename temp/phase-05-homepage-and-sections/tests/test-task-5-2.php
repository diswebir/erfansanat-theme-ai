<?php
/**
 * Test Task 5.2 — dynamic sections wired to WP_Query + standard cards.
 * Run: php-wasm-cli temp/phase-05-homepage-and-sections/tests/test-task-5-2.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

// Query helpers
$q = "$THEME/inc/Queries.php";
check('inc/Queries.php exists', is_file($q));
$qt = is_file($q) ? file_get_contents($q) : '';
foreach (['featured_products', 'latest_projects', 'latest_posts', 'product_categories'] as $fn) {
    check("Queries::$fn defined", strpos($qt, "function $fn") !== false);
}
check('queries use WP_Query with no_found_rows', strpos($qt, 'WP_Query') !== false && strpos($qt, 'no_found_rows') !== false);
check('featured products query post_type product', strpos($qt, "'post_type'      => 'product'") !== false || strpos($qt, "'product'") !== false);
check('projects query post_type projects', strpos($qt, "'projects'") !== false);

// Standard cards
foreach (['card-product.php', 'card-project.php', 'card-post.php'] as $c) {
    check("cards/$c exists", is_file("$THEME/template-parts/cards/$c"));
}
$cp = file_get_contents("$THEME/template-parts/cards/card-product.php");
check('card-product uses permalink/title/thumbnail', strpos($cp, 'get_permalink') !== false && strpos($cp, 'get_the_title') !== false && strpos($cp, 'get_the_post_thumbnail_url') !== false);
check('card-product keeps card-glow classes', strpos($cp, 'card-glow') !== false);
check('card-product reads true_sale meta', strpos($cp, 'true_sale') !== false);

// Sections wired to loops
$s7 = file_get_contents("$THEME/template-parts/home/section-07-products.php");
check('products section loops featured_products + card-product', strpos($s7, 'featured_products') !== false && strpos($s7, "card', 'product") !== false);
check('products section has no hardcoded product links left', strpos($s7, 'href="product/') === false && strpos($s7, 'href="<?php') === false || strpos($s7, 'product/artam') === false);
$s2 = file_get_contents("$THEME/template-parts/home/section-02-projects.php");
check('projects section loops latest_projects + card-project', strpos($s2, 'latest_projects') !== false && strpos($s2, "card', 'project") !== false);
check('projects section no hardcoded project links', strpos($s2, 'projects/tehran') === false);
$s13 = file_get_contents("$THEME/template-parts/home/section-13-blog-preview.php");
check('blog section loops latest_posts + card-post', strpos($s13, 'latest_posts') !== false && strpos($s13, "card', 'post") !== false);

// Theme wires Queries (autoloaded via namespace — check Theme references it)
check('front-page still renders all parts', strpos(file_get_contents("$THEME/front-page.php"), 'get_template_part') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
