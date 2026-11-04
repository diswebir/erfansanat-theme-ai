<?php
/**
 * Integration contract for task 2.3: secure native section builder.
 * Run through PHP WASM or native PHP after installing the disposable WordPress test site.
 */
$root = dirname(__DIR__, 3);
$source = $root . '/inc/Admin/SectionManager.php';
$checks = [
    [$source, 'register_post_meta'],
    [$source, 'wp_nonce_field'],
    [$source, 'save_post_page'],
    [$source, 'section'],
    [$source, 'repeater'],
    [$source, 'media'],
    [$source, 'wp_verify_nonce'],
    [$source, 'get_renderable_sections'],
];
$failed = 0;
foreach ($checks as [$path, $needle]) {
    $contents = is_file($path) ? file_get_contents($path) : false;
    if ($contents === false || strpos($contents, $needle) === false) {
        fwrite(STDERR, "FAIL Section Builder contract missing: {$needle} in {$path}\n");
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
$_SERVER['REQUEST_URI'] = '/wp-admin/post.php?post=1&action=edit';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/wp-admin/post.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('WP_ADMIN', true);
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$class = 'ErfanSanat\Theme\Admin\SectionManager';
if (!class_exists($class)) {
    fwrite(STDERR, "FAIL SectionManager class was not loaded\n");
    exit(1);
}
$metaKey = $class::META_KEY;
$registered = get_registered_meta_keys('post', 'page');
if (!isset($registered[$metaKey])) {
    fwrite(STDERR, "FAIL section metadata was not registered for pages\n");
    $failed++;
}
if (!has_action('save_post_page', [$class, 'save_post'])) {
    fwrite(STDERR, "FAIL secure page metadata save handler was not registered\n");
    $failed++;
}

$clean = $class::sanitize_sections(
    [
        ['id' => 'hero', 'enabled' => '1', 'order' => 2, 'fields' => [
            'title' => '<b>عنوان اصلی</b>',
            'background_image' => 'javascript:alert(1)',
            'background_video' => 'https://cdn.example.test/intro.mp4',
            'primary_link' => ['label' => '<script>کلیک</script>', 'url' => 'https://example.test/products/'],
            'icon' => 'not-in-list',
            'highlights' => [
                ['title' => '<i>ویژگی</i>', 'description' => '<script>alert(1)</script>'],
                ['title' => 'دوم', 'description' => 'متن دوم'],
            ],
        ]],
        ['id' => 'about', 'enabled' => '0', 'order' => 0, 'fields' => ['title' => 'درباره ما']],
        ['id' => 'gallery', 'enabled' => '1', 'order' => 1, 'fields' => ['title' => 'گالری']],
        ['id' => 'unknown-section', 'enabled' => 1, 'order' => 1, 'fields' => ['title' => 'حذف شود']],
    ]
);
$hero = null;
foreach ($clean as $section) {
    if (($section['id'] ?? '') === 'hero') {
        $hero = $section;
        break;
    }
}
if (!$hero || ($hero['fields']['title'] ?? '') !== 'عنوان اصلی') {
    fwrite(STDERR, "FAIL section text was not sanitized or recognized\n");
    $failed++;
}
if (!$hero || ($hero['fields']['background_image'] ?? null) !== '') {
    fwrite(STDERR, "FAIL unsafe section media URL was accepted\n");
    $failed++;
}
if (!$hero || ($hero['fields']['primary_link']['url'] ?? '') !== 'https://example.test/products/') {
    fwrite(STDERR, "FAIL structured link field was not preserved/sanitized\n");
    $failed++;
}
if (!$hero || !isset($hero['fields']['highlights'][1])) {
    fwrite(STDERR, "FAIL repeater rows were not retained\n");
    $failed++;
}
foreach ($clean as $section) {
    if (($section['id'] ?? '') === 'unknown-section') {
        fwrite(STDERR, "FAIL unknown section was not discarded\n");
        $failed++;
    }
}

$pageId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Builder QA', 'post_name' => 'home']);
if (is_wp_error($pageId) || !$pageId) {
    fwrite(STDERR, "FAIL could not create Section Builder QA page\n");
    exit(1);
}
update_post_meta($pageId, $metaKey, $clean);
$fallbacks = [
    'hero' => ['html' => '<p>static hero fallback</p>'],
    'about' => ['html' => '<p>static about fallback</p>'],
    'gallery' => ['html' => '<p>static gallery fallback</p>'],
];
$renderable = $class::get_renderable_sections($pageId, $fallbacks);
if (count($renderable) !== 2 || ($renderable[0]['id'] ?? '') !== 'gallery' || ($renderable[1]['id'] ?? '') !== 'hero') {
    fwrite(STDERR, "FAIL visible sections did not respect configured visibility/order\n");
    $failed++;
}
ob_start();
$class::render_sections($pageId, $fallbacks);
$html = ob_get_clean();
if (
    strpos($html, 'static gallery fallback') === false
    || strpos($html, 'static hero fallback') === false
    || strpos($html, 'static about fallback') !== false
    || strpos($html, 'static gallery fallback') > strpos($html, 'static hero fallback')
) {
    fwrite(STDERR, "FAIL static section fallback/visibility/order rendering is incorrect\n");
    $failed++;
}
$post = get_post($pageId);
ob_start();
$class::render_meta_box($post);
$editor = ob_get_clean();
foreach (['es_page_sections_nonce', 'data-section-card', 'data-repeater-template', 'data-media-type'] as $needle) {
    if (strpos($editor, $needle) === false) {
        fwrite(STDERR, "FAIL section editor is missing {$needle}\n");
        $failed++;
    }
}
$_POST['es_page_sections_nonce'] = wp_create_nonce('es_save_page_sections');
$_POST['es_page_sections'] = [
    ['id' => 'hero', 'enabled' => '1', 'order' => 0, 'fields' => ['title' => 'Saved through verified nonce']],
];
$class::save_post($pageId, $post, true);
$saved = get_post_meta($pageId, $metaKey, true);
if (($saved[0]['fields']['title'] ?? '') !== 'Saved through verified nonce') {
    fwrite(STDERR, "FAIL authorized nonce-protected save did not update page metadata\n");
    $failed++;
}
$_POST['es_page_sections_nonce'] = 'invalid-nonce';
$_POST['es_page_sections'] = [
    ['id' => 'hero', 'enabled' => '1', 'order' => 0, 'fields' => ['title' => 'Forged value']],
];
$class::save_post($pageId, $post, true);
$saved = get_post_meta($pageId, $metaKey, true);
if (($saved[0]['fields']['title'] ?? '') !== 'Saved through verified nonce') {
    fwrite(STDERR, "FAIL invalid nonce changed protected page metadata\n");
    $failed++;
}
unset($_POST['es_page_sections_nonce'], $_POST['es_page_sections']);
wp_delete_post($pageId, true);

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS section schema, secure meta registration, sanitization, ordering, visibility, repeaters, and fallback rendering\n");
