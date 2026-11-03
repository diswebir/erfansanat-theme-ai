<?php
/**
 * Task 3.1 — ثبت CPTها/تکسونومی‌ها + Rewrite + ریدایرکت‌ها
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-01.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 3.1: ثبت CPTها/تکسونومی‌ها + Rewrite + ریدایرکت‌ها ==\n";

es_check( 'PostTypes module', (bool) (es_file('inc/Core/PostTypes.php')) );
es_check( 'Taxonomies module', (bool) (es_file('inc/Core/Taxonomies.php')) );
es_check( 'Rewrites module', (bool) (es_file('inc/Core/Rewrites.php')) );
es_check( 'LegacyRedirects module', (bool) (es_file('inc/Core/LegacyRedirects.php')) );
es_check( 'services CPT', (bool) (es_contains('inc/Core/PostTypes.php','services')) );
es_check( 'project redirect', (bool) (es_contains('inc/Core/LegacyRedirects.php','projects')) );
// WP-dependent checks run ONLY when our theme is active (avoids false
// positives from the default theme that ships with the QA install).
if ( es_activate_theme() ) {
  es_check( 'projects CPT registered', (bool) (post_type_exists('projects')) );
  es_check( 'services CPT registered', (bool) (post_type_exists('services')) );
  es_check( 'project_type taxonomy', (bool) (taxonomy_exists('project_type')) );
  es_check( 'city taxonomy', (bool) (taxonomy_exists('city')) );
  es_check( 'projects rewrite slug', (bool) ((($o=get_post_type_object('projects')) && $o->rewrite['slug']==='projects')) );
  es_check( 'city rewrite with_front false', (bool) ((($t=get_taxonomy('city')) && $t->rewrite['with_front']===false)) );
} else {
  es_skip( 'projects CPT registered', 'erfansanat theme not active yet' );
  es_skip( 'services CPT registered', 'erfansanat theme not active yet' );
  es_skip( 'project_type taxonomy', 'erfansanat theme not active yet' );
  es_skip( 'city taxonomy', 'erfansanat theme not active yet' );
  es_skip( 'projects rewrite slug', 'erfansanat theme not active yet' );
  es_skip( 'city rewrite with_front false', 'erfansanat theme not active yet' );
}
es_summary();
