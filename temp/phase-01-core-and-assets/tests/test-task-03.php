<?php
/**
 * Task 1.3 — انتقال Assets + AssetManager (enqueue شرطی)
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-03.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 1.3: انتقال Assets + AssetManager (enqueue شرطی) ==\n";

es_check( 'styles.css copied', (bool) (es_file('assets/css/styles.css')) );
es_check( 'main.js copied', (bool) (es_file('assets/js/main.js')) );
es_check( 'fonts dir', (bool) (es_dir('assets/fonts')) );
es_check( 'AssetManager exists', (bool) (es_file('inc/Core/AssetManager.php')) );
es_check( 'filemtime versioning', (bool) (es_contains('inc/Core/AssetManager.php','filemtime')) );
es_check( 'conditional enqueue', (bool) (es_contains('inc/Core/AssetManager.php','is_')) );

es_summary();
