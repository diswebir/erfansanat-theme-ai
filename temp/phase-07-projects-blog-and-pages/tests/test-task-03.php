<?php
/**
 * Task 7.3 — قالب‌های برگه اختصاصی + لندینگ‌ها
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-03.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 7.3: قالب‌های برگه اختصاصی + لندینگ‌ها ==\n";

es_check( 'about template', (bool) (es_file('page-templates/about.php')) );
es_check( 'contact template', (bool) (es_file('page-templates/contact.php')) );
es_check( 'faq template', (bool) (es_file('page-templates/faq.php')) );
es_check( 'recruitment template', (bool) (es_file('page-templates/recruitment.php')) );
es_check( 'services template', (bool) (es_file('page-templates/services.php')) );
es_check( 'arya landing', (bool) (es_file('page-templates/landing-arya.php')) );
es_check( 'warranty template', (bool) (es_file('page-templates/warranty.php')) );

es_summary();
