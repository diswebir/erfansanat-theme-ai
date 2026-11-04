<?php
/** WordPress integration test for Task 8.2: secure public forms and private inbox. */
$root = dirname(__DIR__, 3);
$checks = [
    'inc/Forms/Submission.php' => ['wp_verify_nonce', 'wp_mail', 'honeypot', 'wp_insert_post', 'wp_send_json_error'],
    'inc/Admin/Forms.php' => ['manage_options', 'wp_nonce_field', 'ese_update_form_status', 'ese_form_status'],
    'inc/Theme.php' => ['Forms\\Submission::register', 'Admin\\Forms::register'],
    'assets/js/main.js' => ['data-ese-submission-form', 'ese_submit_form', 'data-ese-form-success'],
    'template-parts/pages/section-page.php' => ['template-parts/forms/contact', 'template-parts/forms/recruitment'],
    'single-product.php' => ['template-parts/forms/quote', 'template-parts/forms/client-feedback'],
];
$failed = 0;
foreach ($checks as $relative => $needles) {
    $source = is_file($root . '/' . $relative) ? file_get_contents($root . '/' . $relative) : false;
    if (false === $source) {
        fwrite(STDERR, "FAIL missing form integration file {$relative}\n");
        $failed++;
        continue;
    }
    foreach ($needles as $needle) {
        if (false === strpos($source, $needle)) {
            fwrite(STDERR, "FAIL form contract `{$needle}` missing in {$relative}\n");
            $failed++;
        }
    }
}
foreach (['contact', 'quote', 'recruitment', 'client-feedback'] as $formPart) {
    $relative = 'template-parts/forms/' . $formPart . '.php';
    if (!is_file($root . '/' . $relative)) {
        fwrite(STDERR, "FAIL missing form partial {$relative}\n");
        $failed++;
    }
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
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '192.0.2.81';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$submissionClass = 'ErfanSanat\\Theme\\Forms\\Submission';
$adminClass = 'ErfanSanat\\Theme\\Admin\\Forms';
if (!class_exists($submissionClass) || !class_exists($adminClass)) {
    fwrite(STDERR, "FAIL form handler/admin classes did not autoload\n");
    exit(1);
}
$submissionClass::register_post_type();
$submissionClass::register();
$adminClass::register();
foreach (['wp_ajax_ese_submit_form', 'wp_ajax_nopriv_ese_submit_form'] as $hook) {
    if (false === has_action($hook, [$submissionClass, 'dispatch'])) {
        fwrite(STDERR, "FAIL form action is not registered: {$hook}\n");
        $failed++;
    }
}
if (false === has_action('admin_menu', [$adminClass, 'register_menu']) || !post_type_exists($submissionClass::POST_TYPE)) {
    fwrite(STDERR, "FAIL private submissions inbox was not registered\n");
    $failed++;
}

$settingsBefore = get_option('ese_form_settings', null);
update_option('ese_form_settings', ['notifications_enabled' => false, 'notification_email' => ''], false);
$validNonce = wp_create_nonce($submissionClass::NONCE_ACTION);
$unique = strtolower(wp_generate_password(8, false, false));
$_SERVER['REMOTE_ADDR'] = '198.51.100.' . random_int(1, 254);
$createdIds = [];
$initialSubmissionIds = get_posts(['post_type' => $submissionClass::POST_TYPE, 'post_status' => 'private', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC']);
if (!is_array($initialSubmissionIds)) {
    $initialSubmissionIds = [];
}

if (!post_type_exists('product')) {
    register_post_type('product', ['public' => true, 'publicly_queryable' => true, 'supports' => ['title']]);
}
$productId = wp_insert_post(['post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'Task 8.2 Product ' . $unique]);
if (!$productId || is_wp_error($productId)) {
    fwrite(STDERR, "FAIL could not create product fixture\n");
    exit(1);
}
$createdIds[] = (int) $productId;

if (!function_exists('ese_task82_capture_wp_die')) {
    function ese_task82_capture_wp_die($message = '', $title = '', $args = [])
    {
        throw new RuntimeException('ese-task82-json-captured');
    }
}
add_filter('wp_die_ajax_handler', 'ese_task82_capture_wp_die');
if (!defined('DOING_AJAX')) {
    define('DOING_AJAX', true);
}
$capture = static function (array $post, string $method = 'POST') use ($submissionClass): array {
    $_SERVER['REQUEST_METHOD'] = $method;
    $_POST = $post;
    ob_start();
    try {
        $submissionClass::dispatch();
    } catch (RuntimeException $exception) {
        if ('ese-task82-json-captured' !== $exception->getMessage()) {
            throw $exception;
        }
    }
    $json = (string) ob_get_clean();
    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : [];
};
$base = ['form_nonce' => $validNonce, 'name' => '<script>alert(1)</script> QA Applicant ' . $unique, 'phone' => '۰۹۱۳۷۹۷۶۹۱۵', 'email' => 'qa-' . $unique . '@example.test'];

$contact = $capture($base + ['action' => 'ese_submit_form', 'form_type' => 'contact', 'message' => '<img src=x onerror=alert(1)>Please contact me about a project.']);
if (empty($contact['success']) || empty($contact['data']['message'])) {
    fwrite(STDERR, "FAIL valid contact form was not accepted\n");
    $failed++;
}
$submissions = get_posts(['post_type' => $submissionClass::POST_TYPE, 'post_status' => 'private', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC']);
$latestId = $submissions ? (int) $submissions[0] : 0;
if (!$latestId) {
    fwrite(STDERR, "FAIL valid contact request was not stored in the private inbox\n");
    $failed++;
} else {
    $createdIds[] = $latestId;
    $payload = get_post_meta($latestId, '_ese_form_payload', true);
    if (!is_array($payload) || false !== strpos((string) ($payload['name'] ?? ''), '<script') || false !== strpos((string) ($payload['message'] ?? ''), '<img')) {
        fwrite(STDERR, "FAIL submitted markup was not sanitized before private storage\n");
        $failed++;
    }
    if ('new' !== get_post_meta($latestId, '_ese_form_status', true) || 'disabled' !== get_post_meta($latestId, '_ese_notification_status', true)) {
        fwrite(STDERR, "FAIL inbox default status or notification opt-out was not recorded\n");
        $failed++;
    }
}

$beforeSpam = count($submissions);
$honeypot = $capture($base + ['action' => 'ese_submit_form', 'form_type' => 'contact', 'message' => 'A sufficiently long contact message.', 'website' => 'filled-by-bot']);
$afterSpam = get_posts(['post_type' => $submissionClass::POST_TYPE, 'post_status' => 'private', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC']);
if (empty($honeypot['success']) || count($afterSpam) !== $beforeSpam) {
    fwrite(STDERR, "FAIL honeypot submissions were not silently discarded\n");
    $failed++;
}
$badNonce = $capture(['action' => 'ese_submit_form', 'form_type' => 'contact', 'form_nonce' => 'bad', 'name' => 'Bot', 'phone' => '09123456789', 'message' => 'Invalid nonce should fail.']);
if (!empty($badNonce['success']) || false === strpos((string) ($badNonce['data']['message'] ?? ''), 'منقضی')) {
    fwrite(STDERR, "FAIL invalid form nonce was not rejected with a generic response\n");
    $failed++;
}
$badMethod = $capture(['action' => 'ese_submit_form', 'form_type' => 'contact', 'form_nonce' => $validNonce], 'GET');
if (!empty($badMethod['success'])) {
    fwrite(STDERR, "FAIL non-POST submission was accepted\n");
    $failed++;
}
$invalidPhone = $capture(['action' => 'ese_submit_form', 'form_type' => 'contact', 'form_nonce' => $validNonce, 'name' => 'Invalid Phone', 'phone' => '555', 'message' => 'A valid length message.']);
$invalidEmail = $capture(['action' => 'ese_submit_form', 'form_type' => 'contact', 'form_nonce' => $validNonce, 'name' => 'Invalid Email', 'phone' => '09123456789', 'email' => 'not-an-email', 'message' => 'A valid length message.']);
if (!empty($invalidPhone['success']) || !empty($invalidEmail['success'])) {
    fwrite(STDERR, "FAIL malformed contact details bypassed server validation\n");
    $failed++;
}

$quote = $capture($base + ['action' => 'ese_submit_form', 'form_type' => 'quote', 'product_id' => (string) $productId, 'city' => 'تهران', 'message' => 'Please send a price quotation for this product.']);
$recruitment = $capture($base + ['action' => 'ese_submit_form', 'form_type' => 'recruitment', 'position' => 'مهندس پروژه', 'department' => 'project', 'experience' => '1-3', 'resume_url' => 'https://example.test/cv', 'message' => 'I have relevant project engineering experience and technical skills.']);
$feedback = $capture($base + ['action' => 'ese_submit_form', 'form_type' => 'client_feedback', 'product_id' => (string) $productId, 'rating' => '5', 'organization' => 'QA Municipality', 'message' => 'The product quality and lighting performance are excellent.']);
foreach (['quote' => $quote, 'recruitment' => $recruitment, 'client feedback' => $feedback] as $label => $response) {
    if (empty($response['success'])) {
        fwrite(STDERR, "FAIL {$label} submission was not accepted\n");
        $failed++;
    }
}
$maliciousResume = $capture($base + ['action' => 'ese_submit_form', 'form_type' => 'recruitment', 'position' => 'Engineer', 'resume_url' => 'javascript:alert(1)', 'message' => 'I have enough experience to complete this application.']);
$invalidFeedback = $capture($base + ['action' => 'ese_submit_form', 'form_type' => 'client_feedback', 'product_id' => (string) $productId, 'rating' => '9', 'message' => 'This rating is outside the supported range.']);
if (!empty($maliciousResume['success']) || !empty($invalidFeedback['success'])) {
    fwrite(STDERR, "FAIL invalid resume scheme or feedback rating bypassed validation\n");
    $failed++;
}

$rateKey = 'ese_form_rate_' . substr(hash_hmac('sha256', '198.51.100.82|test-rate', wp_salt('auth')), 0, 32);
delete_transient($rateKey);
$rateResults = [];
for ($i = 0; $i < 6; $i++) {
    $rateResults[] = $submissionClass::allow_request('198.51.100.82', 'test-rate');
}
delete_transient($rateKey);
if (count(array_filter($rateResults)) !== 5 || true !== $rateResults[4] || true === $rateResults[5]) {
    fwrite(STDERR, "FAIL public form rate limiting did not enforce five requests per window\n");
    $failed++;
}

$mailArgs = null;
add_filter('pre_wp_mail', static function ($shortCircuit, $args) use (&$mailArgs) {
    $mailArgs = $args;
    return true;
}, 10, 2);
update_option('ese_form_settings', ['notifications_enabled' => true, 'notification_email' => 'forms@example.test'], false);
$mailRequest = $capture($base + ['action' => 'ese_submit_form', 'form_type' => 'contact', 'message' => 'Please call me about the city lighting project.']);
$mailPosts = get_posts(['post_type' => $submissionClass::POST_TYPE, 'post_status' => 'private', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC']);
$mailPostId = $mailPosts ? (int) $mailPosts[0] : 0;
if (empty($mailRequest['success']) || !is_array($mailArgs) || ($mailArgs['to'] ?? '') !== 'forms@example.test' || !$mailPostId || 'sent' !== get_post_meta($mailPostId, '_ese_notification_status', true)) {
    fwrite(STDERR, "FAIL configurable email notification was not sent through wp_mail\n");
    $failed++;
} elseif (!in_array($mailPostId, $createdIds, true)) {
    $createdIds[] = $mailPostId;
}

if (count($createdIds) > 0) {
    $firstStoredId = $createdIds[1] ?? $createdIds[0];
    if (!$submissionClass::set_status($firstStoredId, 'in_progress') || 'in_progress' !== get_post_meta($firstStoredId, '_ese_form_status', true) || $submissionClass::set_status($firstStoredId, 'administrator')) {
        fwrite(STDERR, "FAIL inbox status update did not enforce its allowlist\n");
        $failed++;
    }
}
require_once $wpRoot . '/wp-admin/includes/template.php';
$adminHtml = '';
ob_start();
$adminClass::render_inbox_page();
$adminHtml = (string) ob_get_clean();
if (false === strpos($adminHtml, 'درخواست‌های سایت') || false !== strpos($adminHtml, '<script>alert(1)</script>')) {
    fwrite(STDERR, "FAIL capability-bound inbox did not render safely\n");
    $failed++;
}

$partials = [
    'contact' => ['name="name"', 'name="phone"', 'form_nonce'],
    'quote' => ['name="product_id"', 'name="message"', 'form_nonce'],
    'recruitment' => ['name="resume_url"', 'name="position"', 'form_nonce'],
    'client-feedback' => ['name="rating"', 'name="product_id"', 'form_nonce'],
];
foreach ($partials as $part => $needles) {
    ob_start();
    get_template_part('template-parts/forms/' . $part, null, ['product_id' => (int) $productId]);
    $html = (string) ob_get_clean();
    foreach ($needles as $needle) {
        if (false === strpos($html, $needle)) {
            fwrite(STDERR, "FAIL rendered {$part} form missing `{$needle}`\n");
            $failed++;
        }
    }
}
$previousQueriedId = isset($GLOBALS['wp_query']->queried_object_id) ? (int) $GLOBALS['wp_query']->queried_object_id : 0;
foreach (['contact' => 'contact', 'recruitment' => 'recruitment'] as $slug => $expectedType) {
    $pageId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => 'Task 8.2 ' . $slug]);
    if (!$pageId || is_wp_error($pageId)) {
        fwrite(STDERR, "FAIL could not create {$slug} page fixture\\n");
        $failed++;
        continue;
    }
    $createdIds[] = (int) $pageId;
    $GLOBALS['wp_query']->queried_object_id = (int) $pageId;
    $GLOBALS['post'] = get_post((int) $pageId);
    setup_postdata($GLOBALS['post']);
    ob_start();
    include $root . '/template-parts/pages/section-page.php';
    $pageHtml = (string) ob_get_clean();
    if (false === strpos($pageHtml, 'data-form-type="' . $expectedType . '"')) {
        fwrite(STDERR, "FAIL {$slug} page did not render its secure form\\n");
        $failed++;
    }
}
wp_reset_postdata();
$GLOBALS['wp_query']->queried_object_id = $previousQueriedId;

$finalSubmissionIds = get_posts(['post_type' => $submissionClass::POST_TYPE, 'post_status' => 'private', 'numberposts' => -1, 'fields' => 'ids']);
foreach (array_unique(array_merge($createdIds, array_diff((array) $finalSubmissionIds, $initialSubmissionIds))) as $postId) {
    wp_delete_post((int) $postId, true);
}
if (is_null($settingsBefore)) {
    delete_option('ese_form_settings');
} else {
    update_option('ese_form_settings', $settingsBefore, false);
}
$_POST = [];
$wpTestDir = getenv('WP_TEST_DIR') ?: $root . '/node_modules/.cache/ese-wp-test';
if (is_file($wpTestDir . '/wp-debug.log') && filesize($wpTestDir . '/wp-debug.log') > 0) {
    fwrite(STDERR, "FAIL form integration recorded a WP_DEBUG warning\n");
    $failed++;
}
if ($failed) {
    exit(1);
}
fwrite(STDOUT, "PASS contact, quote, recruitment, and client-feedback validation, honeypot, nonce, rate limit, private inbox, status control, email notification, and escaping\n");
