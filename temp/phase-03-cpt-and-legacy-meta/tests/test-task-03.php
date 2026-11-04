<?php
/**
 * Integration contract for task 3.3: project/term metadata, hreflang, and view counts.
 * Run through PHP WASM or native PHP after installing the disposable WordPress test site.
 */
$root = dirname(__DIR__, 3);
$projectSource = $root . '/inc/Admin/ProjectMetaboxes.php';
$termSource = $root . '/inc/Admin/TermMetaboxes.php';
$contentSource = $root . '/inc/Admin/ContentMeta.php';
$checks = [
    [$projectSource, 'foure-image'],
    [$projectSource, 'mortabet_mahsole_2'],
    [$projectSource, 'finish-pro-time'],
    [$termSource, 'hrefmeta_fa'],
    [$termSource, 'register_term_meta'],
    [$contentSource, 'number2-viewc'],
    [$contentSource, 'number-viewc'],
    [$contentSource, 'wp_verify_nonce'],
];
$failed = 0;
foreach ($checks as [$path, $needle]) {
    $contents = is_file($path) ? file_get_contents($path) : false;
    if ($contents === false || strpos($contents, $needle) === false) {
        fwrite(STDERR, "FAIL legacy metadata contract missing: {$needle} in {$path}\n");
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
$_SERVER['REQUEST_URI'] = '/wp-admin/post.php?action=edit';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/wp-admin/post.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('WP_ADMIN', true);
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$projectClass = 'ErfanSanat\Theme\Admin\ProjectMetaboxes';
$termClass = 'ErfanSanat\Theme\Admin\TermMetaboxes';
$contentClass = 'ErfanSanat\Theme\Admin\ContentMeta';
foreach ([$projectClass, $termClass, $contentClass] as $class) {
    if (!class_exists($class)) {
        fwrite(STDERR, "FAIL metadata class {$class} was not loaded\n");
        exit(1);
    }
}

$projectSchema = $projectClass::meta_schema();
foreach (['second-image', 'three-image', 'foure-image', 'five-image', 'six-image', 'finish-pro-time', 'mortabet_mahsole_2', '_es_project_gallery'] as $key) {
    if (!isset($projectSchema[$key])) {
        fwrite(STDERR, "FAIL project schema omitted {$key}\n");
        $failed++;
    }
}
$projectClean = $projectClass::sanitize_project_payload(
    [
        '_es_project_gallery' => ['21', '22', 'javascript:alert(1)'],
        'finish-pro-time' => '<b>1404/01/01</b>',
        'mortabet_mahsole_2' => ['4', '5', 'invalid'],
    ]
);
if (($projectClean['_es_project_gallery'] ?? []) !== [21, 22] || ($projectClean['finish-pro-time'] ?? '') !== '1404/01/01') {
    fwrite(STDERR, "FAIL project gallery/date values were not sanitized\n");
    $failed++;
}

$projectId = wp_insert_post(['post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'Project Metabox QA']);
if (is_wp_error($projectId) || !$projectId) {
    fwrite(STDERR, "FAIL could not create project metadata QA post\n");
    exit(1);
}
ob_start();
$projectClass::render_meta_box(get_post($projectId));
$projectEditor = ob_get_clean();
foreach (['es_project_meta_nonce', 'foure-image', 'finish-pro-time', 'mortabet_mahsole_2'] as $needle) {
    if (strpos($projectEditor, $needle) === false) {
        fwrite(STDERR, "FAIL project metabox is missing {$needle}\n");
        $failed++;
    }
}
$_POST['es_project_meta_nonce'] = wp_create_nonce('es_save_project_meta');
$_POST['es_project_meta'] = [
    '_es_project_gallery' => ['21', '22'],
    'finish-pro-time' => '1404/01/01',
    'mortabet_mahsole_2' => ['4', '5'],
];
$projectClass::save_post($projectId, get_post($projectId), true);
if ($projectClass::get_project_gallery_ids($projectId) !== [21, 22]) {
    fwrite(STDERR, "FAIL modern project gallery was not synced to the legacy image meta keys\n");
    $failed++;
}
if (get_post_meta($projectId, 'foure-image', true) !== 0 && get_post_meta($projectId, 'foure-image', true) !== '0') {
    fwrite(STDERR, "FAIL missing fourth gallery image was not safely cleared\n");
    $failed++;
}
if (($projectClass::decode_legacy_value(get_post_meta($projectId, 'mortabet_mahsole_2', true))[1] ?? 0) !== 5) {
    fwrite(STDERR, "FAIL related product IDs were not saved in native meta\n");
    $failed++;
}
$projectBefore = get_post_meta($projectId, 'finish-pro-time', true);
$_POST['es_project_meta_nonce'] = 'invalid-nonce';
$_POST['es_project_meta'] = ['finish-pro-time' => 'forged'];
$projectClass::save_post($projectId, get_post($projectId), true);
if (get_post_meta($projectId, 'finish-pro-time', true) !== $projectBefore) {
    fwrite(STDERR, "FAIL invalid project nonce changed protected metadata\n");
    $failed++;
}
unset($_POST['es_project_meta_nonce'], $_POST['es_project_meta']);

$contentSchema = $contentClass::meta_schema();
foreach (['hrefmeta_en', 'hrefmeta_ar', 'hrefmeta_fa', 'same_key_hrefl', 'number2-viewc'] as $key) {
    if (!isset($contentSchema[$key])) {
        fwrite(STDERR, "FAIL shared content metadata omitted {$key}\n");
        $failed++;
    }
}
$postId = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'View Counter QA']);
update_post_meta($postId, 'number-viewc', '37');
if ($contentClass::get_view_count($postId) !== 37) {
    fwrite(STDERR, "FAIL number-viewc fallback was not read\n");
    $failed++;
}
update_post_meta($postId, 'number2-viewc', '45');
if ($contentClass::get_view_count($postId) !== 45) {
    fwrite(STDERR, "FAIL number2-viewc did not override the legacy fallback\n");
    $failed++;
}
ob_start();
$contentClass::render_meta_box(get_post($postId));
$contentEditor = ob_get_clean();
foreach (['es_content_meta_nonce', 'hrefmeta_en', 'same_key_hrefl', 'number2-viewc'] as $needle) {
    if (strpos($contentEditor, $needle) === false) {
        fwrite(STDERR, "FAIL shared content editor is missing {$needle}\n");
        $failed++;
    }
}
$_POST['es_content_meta_nonce'] = wp_create_nonce('es_save_content_meta');
$_POST['es_content_meta'] = [
    'hrefmeta_en' => 'javascript:alert(1)',
    'hrefmeta_fa' => 'https://example.test/fa/',
    'same_key_hrefl' => '<b>shared-key</b>',
    'number2-viewc' => '53',
];
$contentClass::save_post($postId, get_post($postId), true);
if (get_post_meta($postId, 'hrefmeta_en', true) !== '' || get_post_meta($postId, 'same_key_hrefl', true) !== 'shared-key' || $contentClass::get_view_count($postId) !== 53) {
    fwrite(STDERR, "FAIL nonce-protected shared content metadata save/sanitization failed\n");
    $failed++;
}
$hreflangBefore = get_post_meta($postId, 'hrefmeta_fa', true);
$_POST['es_content_meta_nonce'] = 'invalid-nonce';
$_POST['es_content_meta'] = ['hrefmeta_fa' => 'forged'];
$contentClass::save_post($postId, get_post($postId), true);
if (get_post_meta($postId, 'hrefmeta_fa', true) !== $hreflangBefore) {
    fwrite(STDERR, "FAIL invalid shared content nonce changed protected metadata\n");
    $failed++;
}
unset($_POST['es_content_meta_nonce'], $_POST['es_content_meta']);

if (!post_type_exists('product')) {
    register_post_type('product', ['public' => true, 'show_ui' => true, 'supports' => ['title']]);
}
if (!taxonomy_exists('product_cat')) {
    register_taxonomy('product_cat', ['product'], ['public' => true, 'hierarchical' => true, 'show_ui' => true, 'show_in_rest' => true]);
}
$termSchema = $termClass::meta_schema('product_cat');
foreach (['thumbnail', 'faq', 'hrefmeta_en', 'hrefmeta_ar', 'hrefmeta_fa', 'same_key_hrefl'] as $key) {
    if (!isset($termSchema[$key])) {
        fwrite(STDERR, "FAIL product_cat term schema omitted {$key}\n");
        $failed++;
    }
}
$termClean = $termClass::sanitize_term_payload(
    'product_cat',
    [
        'thumbnail' => '31',
        'faq' => ['item-0' => ['name_question' => '<b>سوال</b>', 'answer_question' => '<b>پاسخ</b>']],
        'hrefmeta_fa' => 'https://example.test/product_cat/test/',
        'same_key_hrefl' => '<script>cluster</script>qa-term',
    ]
);
if (($termClean['thumbnail'] ?? 0) !== 31 || ($termClean['faq']['item-0']['name_question'] ?? '') !== 'سوال') {
    fwrite(STDERR, "FAIL product category thumbnail/FAQ fields were not sanitized\n");
    $failed++;
}
$termId = wp_insert_term('QA Category', 'product_cat');
if (is_wp_error($termId)) {
    fwrite(STDERR, "FAIL could not create product category term\n");
    exit(1);
}
$termId = (int) $termId['term_id'];
$term = get_term($termId, 'product_cat');
ob_start();
$termClass::render_edit_fields($term);
$termEditor = ob_get_clean();
foreach (['es_term_meta_nonce', 'thumbnail', 'hrefmeta_fa', 'name_question'] as $needle) {
    if (strpos($termEditor, $needle) === false) {
        fwrite(STDERR, "FAIL term editor is missing {$needle}\n");
        $failed++;
    }
}
$_POST['es_term_meta_nonce'] = wp_create_nonce('es_save_term_meta_product_cat');
$_POST['es_term_meta'] = [
    'thumbnail' => '31',
    'faq' => ['item-0' => ['name_question' => 'سوال امن', 'answer_question' => 'پاسخ امن']],
    'hrefmeta_fa' => 'https://example.test/category/',
    'same_key_hrefl' => 'qa-term',
];
$termClass::save_term_meta($termId, 'product_cat');
if ((int) get_term_meta($termId, 'thumbnail', true) !== 31 || ($termClass::decode_legacy_value(get_term_meta($termId, 'faq', true))['item-0']['name_question'] ?? '') !== 'سوال امن') {
    fwrite(STDERR, "FAIL secure term meta save did not persist thumbnail/FAQ\n");
    $failed++;
}
$termBefore = get_term_meta($termId, 'same_key_hrefl', true);
$_POST['es_term_meta_nonce'] = 'invalid-nonce';
$_POST['es_term_meta'] = ['same_key_hrefl' => 'forged'];
$termClass::save_term_meta($termId, 'product_cat');
if (get_term_meta($termId, 'same_key_hrefl', true) !== $termBefore) {
    fwrite(STDERR, "FAIL invalid term nonce changed protected metadata\n");
    $failed++;
}
unset($_POST['es_term_meta_nonce'], $_POST['es_term_meta']);
wp_delete_term($termId, 'product_cat');
wp_delete_post($projectId, true);
wp_delete_post($postId, true);

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS project legacy fields/gallery, term FAQ/thumbnail, post/term hreflang, view fallback, sanitization, and nonce-protected saves\n");
