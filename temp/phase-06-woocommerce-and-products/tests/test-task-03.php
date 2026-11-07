<?php
/**
 * WordPress integration test for task 6.3: WooCommerce cart, checkout, and account pages.
 * Run through PHP WASM or native PHP with WP_TEST_ROOT set.
 */
$root = dirname(__DIR__, 3);
$contracts = [
    ['page.php', 'is_cart()'],
    ['page.php', 'is_checkout()'],
    ['page.php', 'is_account_page()'],
    ['page.php', 'the_content()'],
    ['page.php', 'woocommerce_before_main_content'],
    ['page.php', 'woocommerce_after_main_content'],
    ['page.php', 'data-woocommerce-page'],
    ['assets/css/styles.css', '.woocommerce-page-shell'],
    ['assets/css/styles.css', '.woocommerce-cart'],
    ['assets/css/styles.css', '.woocommerce-checkout'],
    ['assets/css/styles.css', '.woocommerce-account'],
    ['assets/css/styles.css', '.wc-block-cart'],
    ['assets/css/styles.css', '.wc-block-checkout'],
    ['assets/js/main.js', 'data-woocommerce-page="cart"'],
    ['assets/js/main.js', 'data-woocommerce-cart-url'],
    ['page.php', 'data-woocommerce-cart-url'],
];
$failed = 0;
foreach ($contracts as [$relative, $needle]) {
    $path = $root . '/' . $relative;
    $source = is_file($path) ? file_get_contents($path) : false;
    if ($source === false || strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL WooCommerce page contract missing `{$needle}` in {$relative}\n");
        $failed++;
    }
}
foreach (['woocommerce/cart/cart.php', 'woocommerce/checkout/form-checkout.php', 'woocommerce/myaccount/my-account.php'] as $override) {
    if (is_file($root . '/' . $override)) {
        fwrite(STDERR, "FAIL unsafe WooCommerce transaction template override found: {$override}\n");
        $failed++;
    }
}
$mainJs = is_file($root . '/assets/js/main.js') ? file_get_contents($root . '/assets/js/main.js') : '';
if (strpos($mainJs, 'form.checkout') !== false || strpos($mainJs, 'form.woocommerce-cart-form') !== false) {
    fwrite(STDERR, "FAIL theme JavaScript directly intercepts native WooCommerce transaction forms\n");
    $failed++;
}

$wpRoot = getenv('WP_TEST_ROOT');
if (!$wpRoot) {
    $wpTestDir = getenv('WP_TEST_DIR') ?: $root . '/node_modules/.cache/ese-wp-test';
    $wpRoot = rtrim($wpTestDir, DIRECTORY_SEPARATOR) . '/WordPress-master';
}
if (!is_file($wpRoot . '/wp-load.php')) {
    fwrite(STDERR, "FAIL WordPress test installation not found at {$wpRoot}\n");
    exit(1);
}
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['SERVER_NAME'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/cart/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$GLOBALS['es_task_63_context'] = '';
if (!function_exists('is_cart')) {
    function is_cart() { return 'cart' === $GLOBALS['es_task_63_context']; }
}
if (!function_exists('is_checkout')) {
    function is_checkout() { return 'checkout' === $GLOBALS['es_task_63_context']; }
}
if (!function_exists('is_account_page')) {
    function is_account_page() { return 'account' === $GLOBALS['es_task_63_context']; }
}
if (!shortcode_exists('woocommerce_cart')) {
    add_shortcode('woocommerce_cart', static function (): string { return '<form class="woocommerce-cart-form"><button type="submit">Native cart shortcode</button></form>'; });
}
if (!shortcode_exists('woocommerce_checkout')) {
    add_shortcode('woocommerce_checkout', static function (): string { return '<form class="checkout"><button type="submit">Native checkout shortcode</button></form>'; });
}
if (!shortcode_exists('woocommerce_my_account')) {
    add_shortcode('woocommerce_my_account', static function (): string { return '<div class="woocommerce-account"><form class="woocommerce-form-login">Native account shortcode</form></div>'; });
}

$fixtures = [
    'cart' => ['سبد خرید آزمایشی', 'task-63-cart', '[woocommerce_cart]'],
    'checkout' => ['پرداخت آزمایشی', 'task-63-checkout', '[woocommerce_checkout]'],
    'account' => ['حساب کاربری آزمایشی', 'task-63-account', '[woocommerce_my_account]'],
];
$pageIds = [];
foreach ($fixtures as $context => [$title, $slug, $content]) {
    $pageId = wp_insert_post([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => $content,
    ]);
    if (is_wp_error($pageId) || !$pageId) {
        fwrite(STDERR, "FAIL could not create the {$context} page fixture\n");
        exit(1);
    }
    $pageIds[$context] = (int) $pageId;
}

$renderPage = static function (string $context, int $pageId): string {
    global $wp_query, $wp_the_query, $post;
    $GLOBALS['es_task_63_context'] = $context;
    $wp_query = new WP_Query(['page_id' => $pageId, 'post_type' => 'page', 'post_status' => 'publish']);
    $wp_query->is_page = true;
    $wp_query->is_singular = true;
    $wp_query->is_home = false;
    $wp_query->queried_object = get_post($pageId);
    $wp_query->queried_object_id = $pageId;
    $wp_the_query = $wp_query;
    $post = null;
    ob_start();
    include dirname(__DIR__, 3) . '/page.php';
    $html = (string) ob_get_clean();
    wp_reset_postdata();
    return $html;
};

$expectedContent = [
    'cart' => ['data-woocommerce-page="cart"', 'data-woocommerce-cart-url=', 'Native cart shortcode', 'woocommerce-cart-form'],
    'checkout' => ['data-woocommerce-page="checkout"', 'data-woocommerce-cart-url=', 'Native checkout shortcode', 'class="checkout"'],
    'account' => ['data-woocommerce-page="account"', 'data-woocommerce-cart-url=', 'Native account shortcode', 'woocommerce-account'],
];
foreach ($pageIds as $context => $pageId) {
    $html = $renderPage($context, $pageId);
    foreach ($expectedContent[$context] as $needle) {
        if (strpos($html, $needle) === false) {
            fwrite(STDERR, "FAIL {$context} page did not preserve WordPress/WooCommerce content: {$needle}\n");
            $failed++;
        }
    }
    if (strpos($html, 'data-cart-page="true"') !== false || strpos($html, 'data-checkout-page="true"') !== false || strpos($html, 'data-checkout-form="true"') !== false) {
        fwrite(STDERR, "FAIL {$context} page was replaced with the static-mirror local-storage cart flow\n");
        $failed++;
    }
}

foreach ($pageIds as $pageId) {
    wp_delete_post($pageId, true);
}

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS native WooCommerce cart/checkout/account shortcode pages, RTL wrappers, payment-safe template behavior, and responsive styling contracts\n");
