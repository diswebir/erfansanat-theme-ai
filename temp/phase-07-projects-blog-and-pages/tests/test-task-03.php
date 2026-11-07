<?php
/** WordPress integration contract for task 7.3: editable special pages and landing pages. */
$root = dirname(__DIR__, 3);
$pageTemplates = [
    'about' => 'about.php',
    'contact' => 'contact.php',
    'faq' => 'faq.php',
    'licenses-and-honors' => 'licenses-and-honors.php',
    'recruitment' => 'recruitment.php',
    'refund_returns' => 'refund_returns.php',
    'arya' => 'arya.php',
    'smartelement' => 'smartelement.php',
];
$failed = 0;
foreach ($pageTemplates as $slug => $template) {
    $path = $root . '/page-templates/' . $template;
    $source = is_file($path) ? file_get_contents($path) : false;
    if ($source === false || strpos($source, 'Template Name:') === false || strpos($source, 'section-page') === false) {
        fwrite(STDERR, "FAIL special page template contract missing: {$slug}\n");
        $failed++;
    }
}
foreach ([
    ['page.php', 'page-templates/about.php'],
    ['page.php', 'page-templates/contact.php'],
    ['page.php', 'page-templates/arya.php'],
    ['inc/Admin/SectionManager.php', "'arya' => ["],
    ['inc/Admin/SectionManager.php', "'smartelement' => ["],
    ['template-parts/pages/section-page.php', 'SectionManager::render_sections'],
    ['template-parts/sections/hero.php', 'background_image'],
    ['template-parts/sections/faq.php', '<details'],
    ['template-parts/sections/cards-grid.php', 'data-editable-section'],
] as [$relative, $needle]) {
    $source = is_file($root . '/' . $relative) ? file_get_contents($root . '/' . $relative) : false;
    if ($source === false || strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL editable page contract missing `{$needle}` in {$relative}\n");
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
$_SERVER['REQUEST_URI'] = '/about/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$sectionManager = 'ErfanSanat\\Theme\\Admin\\SectionManager';
if (!class_exists($sectionManager)) {
    fwrite(STDERR, "FAIL SectionManager was not loaded\n");
    exit(1);
}
if (!shortcode_exists('task73_page_content')) {
    add_shortcode('task73_page_content', static function (): string { return '<form class="native-page-shortcode">native form hook</form>'; });
}

$pageFixtures = [
    'about' => [
        'title' => 'QA About Page', 'template' => 'about.php', 'sections' => [
            ['id' => 'hero', 'enabled' => true, 'order' => 0, 'fields' => ['title' => 'Editable About Hero', 'eyebrow' => 'Company', 'description' => 'About description']],
            ['id' => 'company', 'enabled' => true, 'order' => 1, 'fields' => ['title' => 'Editable Company Section', 'content' => '<p>Company section copy.</p>']],
        ], 'needles' => ['Editable About Hero', 'Editable Company Section', 'Company section copy.'],
    ],
    'contact' => [
        'title' => 'QA Contact Page', 'template' => 'contact.php', 'sections' => [
            ['id' => 'hero', 'enabled' => true, 'order' => 0, 'fields' => ['title' => 'Editable Contact Hero']],
            ['id' => 'contact-info', 'enabled' => true, 'order' => 1, 'fields' => ['title' => 'Editable Contact Data', 'phone' => '031-1234567', 'email' => 'qa@example.test', 'address' => 'Test address']],
        ], 'needles' => ['Editable Contact Hero', 'Editable Contact Data', '031-1234567', 'qa@example.test', 'native form hook'],
    ],
    'faq' => [
        'title' => 'QA FAQ Page', 'template' => 'faq.php', 'sections' => [
            ['id' => 'faq', 'enabled' => true, 'order' => 0, 'fields' => ['title' => 'Editable FAQ', 'items' => [['question' => 'QA question?', 'answer' => 'QA answer.']]]],
        ], 'needles' => ['Editable FAQ', 'QA question?', 'QA answer.'],
    ],
    'licenses-and-honors' => [
        'title' => 'QA Licenses Page', 'template' => 'licenses-and-honors.php', 'sections' => [
            ['id' => 'licenses', 'enabled' => true, 'order' => 0, 'fields' => ['title' => 'Editable Licenses', 'items' => [['title' => 'QA Certification', 'description' => 'Certificate description']]]],
        ], 'needles' => ['Editable Licenses', 'QA Certification', 'Certificate description'],
    ],
    'recruitment' => [
        'title' => 'QA Recruitment Page', 'template' => 'recruitment.php', 'sections' => [
            ['id' => 'jobs', 'enabled' => true, 'order' => 0, 'fields' => ['title' => 'Editable Jobs', 'items' => [['title' => 'QA Engineer Role', 'category' => 'Engineering', 'description' => 'Role description']]]],
        ], 'needles' => ['Editable Jobs', 'QA Engineer Role', 'Role description', 'data-recruit-tabs="true"', 'data-recruit-tab-btn="department-1"', 'data-recruit-panel="department-1"'],
    ],
    'refund_returns' => [
        'title' => 'QA Warranty Page', 'template' => 'refund_returns.php', 'sections' => [
            ['id' => 'policy', 'enabled' => true, 'order' => 0, 'fields' => ['title' => 'Editable Policy', 'content' => '<p>Warranty policy copy.</p>']],
            ['id' => 'faq', 'enabled' => true, 'order' => 1, 'fields' => ['title' => 'Warranty FAQ', 'items' => [['question' => 'Warranty question?', 'answer' => 'Warranty answer.']]]],
        ], 'needles' => ['Editable Policy', 'Warranty policy copy.', 'Warranty FAQ', 'Warranty question?'],
    ],
    'arya' => [
        'title' => 'QA Arya Landing', 'template' => 'arya.php', 'sections' => [
            ['id' => 'hero', 'enabled' => true, 'order' => 0, 'fields' => ['title' => 'Editable Arya Hero']],
            ['id' => 'features', 'enabled' => true, 'order' => 1, 'fields' => ['title' => 'Arya Features', 'items' => [['title' => 'Smart feature', 'description' => 'Feature details']]]],
            ['id' => 'specs', 'enabled' => true, 'order' => 2, 'fields' => ['title' => 'Arya Specifications', 'items' => [['title' => 'Connection', 'description' => 'Wi-Fi']]]],
        ], 'needles' => ['Editable Arya Hero', 'Arya Features', 'Smart feature', 'Arya Specifications', 'Wi-Fi'],
    ],
    'smartelement' => [
        'title' => 'QA Smart Element Landing', 'template' => 'smartelement.php', 'sections' => [
            ['id' => 'hero', 'enabled' => true, 'order' => 0, 'fields' => ['title' => 'Editable Smart Element Hero']],
            ['id' => 'services-list', 'enabled' => true, 'order' => 1, 'fields' => ['title' => 'Smart Services', 'items' => [['title' => 'Smart Service Item', 'description' => 'Service details']]]],
        ], 'needles' => ['Editable Smart Element Hero', 'Smart Services', 'Smart Service Item'],
    ],
];
$createdIds = [];
$renderPage = static function (int $pageId, string $template) {
    global $wp_query, $wp_the_query, $post;
    $wp_query = new WP_Query(['page_id' => $pageId, 'post_type' => 'page', 'post_status' => 'publish']);
    $wp_query->is_page = true;
    $wp_query->is_singular = true;
    $wp_query->is_home = false;
    $wp_query->queried_object = get_post($pageId);
    $wp_query->queried_object_id = $pageId;
    $wp_the_query = $wp_query;
    $post = null;
    ob_start();
    include dirname(__DIR__, 3) . '/page-templates/' . $template;
    $html = (string) ob_get_clean();
    wp_reset_postdata();
    return $html;
};

foreach ($pageFixtures as $slug => $fixture) {
    $pageId = wp_insert_post([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $fixture['title'],
        'post_name' => $slug,
        'post_content' => '[task73_page_content]',
    ]);
    if (is_wp_error($pageId) || !$pageId) {
        fwrite(STDERR, "FAIL could not create {$slug} page fixture\n");
        exit(1);
    }
    $pageId = (int) $pageId;
    $createdIds[] = $pageId;
    $cleanSections = $sectionManager::sanitize_sections($fixture['sections'], $pageId);
    update_post_meta($pageId, $sectionManager::META_KEY, $cleanSections);
    $schema = $sectionManager::section_schema($pageId);
    foreach (array_column($fixture['sections'], 'id') as $sectionId) {
        if (!isset($schema[$sectionId])) {
            fwrite(STDERR, "FAIL SectionManager schema missing {$sectionId} on {$slug}\n");
            $failed++;
        }
    }
    $html = $renderPage($pageId, $fixture['template']);
    foreach ($fixture['needles'] as $needle) {
        if (strpos($html, $needle) === false) {
            fwrite(STDERR, "FAIL {$slug} page lost editable section/native content: {$needle}\n");
            $failed++;
        }
    }
    if (strpos($html, 'javascript:') !== false) {
        fwrite(STDERR, "FAIL {$slug} page rendered an unsafe URL\n");
        $failed++;
    }
}

$legacyPageAliases = [
    'درباره-ما' => ['company', 'about'],
    'سوالات-متداول' => ['faq', 'faq'],
    'پرسش-های-متداول' => ['faq', 'faq'],
    'مجوزها-و-افتخارات' => ['licenses', 'licenses-and-honors'],
    'فرصت-های-شغلی' => ['jobs', 'recruitment'],
    'شرایط-گارانتی' => ['policy', 'refund_returns'],
    'بازگشت-کالا' => ['policy', 'refund_returns'],
];
foreach ($legacyPageAliases as $legacySlug => [$expectedSection, $expectedRoute]) {
    $legacyPageId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'QA ' . $legacySlug, 'post_name' => $legacySlug, 'post_content' => '']);
    if (is_wp_error($legacyPageId) || !$legacyPageId) {
        fwrite(STDERR, "FAIL could not create Persian legacy page fixture: {$legacySlug}\\n");
        $failed++;
        continue;
    }
    $createdIds[] = (int) $legacyPageId;
    if (!isset($sectionManager::section_schema((int) $legacyPageId)[$expectedSection])) {
        fwrite(STDERR, "FAIL Persian legacy page slug did not resolve {$expectedSection}: {$legacySlug}\\n");
        $failed++;
    }
    if (strpos(file_get_contents($root . '/page.php'), "'{$legacySlug}' => '{$expectedRoute}'") === false) {
        fwrite(STDERR, "FAIL page.php is missing Persian legacy route: {$legacySlug}\\n");
        $failed++;
    }
}

$persianContact = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'تماس با ما', 'post_name' => 'تماس-با-ما', 'post_content' => '[task73_page_content]']);
if (is_wp_error($persianContact) || !$persianContact || !isset($sectionManager::section_schema((int) $persianContact)['contact-info'])) {
    fwrite(STDERR, "FAIL Persian legacy contact slug did not resolve the contact SectionManager schema\n");
    $failed++;
} elseif (is_numeric($persianContact)) {
    $createdIds[] = (int) $persianContact;
    global $wp_query, $wp_the_query, $post;
    $wp_query = new WP_Query(['page_id' => (int) $persianContact, 'post_type' => 'page', 'post_status' => 'publish']);
    $wp_query->is_page = true;
    $wp_query->is_singular = true;
    $wp_query->is_home = false;
    $wp_query->queried_object = get_post((int) $persianContact);
    $wp_query->queried_object_id = (int) $persianContact;
    $wp_the_query = $wp_query;
    $post = null;
    ob_start();
    include $root . '/page.php';
    $routedHtml = (string) ob_get_clean();
    wp_reset_postdata();
    if (strpos($routedHtml, 'data-special-page=') === false || strpos($routedHtml, 'تماس با ما') === false || strpos($routedHtml, 'native form hook') === false) {
        fwrite(STDERR, "FAIL page.php did not route the Persian contact slug to its editable page template\n");
        $failed++;
    }
}

foreach (array_reverse($createdIds) as $pageId) {
    wp_delete_post($pageId, true);
}

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS SectionManager schemas/rendering for all special pages and landing slugs, safe editable repeaters, and native shortcode content\n");
