<?php
/**
 * Harness setup: activate the theme, create the required pages, set
 * front page / blog page, and print a short report.
 */
declare(strict_types=1);

require_once '/home/user/.cache/harness/wpn/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/theme.php';

$theme = getenv( 'ES_THEME_SLUG' ) ?: 'erfansanat';

if ( ! wp_get_theme( $theme )->exists() ) {
	echo "THEME MISSING: $theme\n";
	exit( 1 );
}

switch_theme( $theme );
update_option( 'stylesheet', $theme );
update_option( 'template', $theme );

echo "active=", get_stylesheet(), " template=", get_template(), "\n";

// turn off the default "Hello world" noise for page rendering
$blogname = 'عرفان صنعت اصفهان';
update_option( 'blogname', $blogname );
update_option( 'blogdescription', 'نورپردازی شهری و المان‌های نوری' );

// pretty permalinks
global $wp_rewrite;
update_option( 'permalink_structure', '/%postname%/' );
$wp_rewrite->set_permalink_structure( '/%postname%/' );
flush_rewrite_rules( false );

// required static pages (slug => title) for later phases
$pages = array(
	'home'                => 'صفحه اصلی',
	'about'               => 'درباره ما',
	'contact'             => 'تماس با ما',
	'faq'                 => 'سوالات متداول',
	'licenses-and-honors' => 'مجوزها و افتخارات',
	'refund_returns'      => 'شرایط گارانتی',
	'recruitment'         => 'فرصت‌های شغلی',
	'shop'                => 'فروشگاه',
	'blog'                => 'وبلاگ',
	'projects'            => 'پروژه‌ها',
	'services'            => 'خدمات',
);
$ids = array();
foreach ( $pages as $slug => $title ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) { $ids[ $slug ] = $existing->ID; continue; }
	$ids[ $slug ] = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_name'   => $slug,
		'post_title'  => $title,
		'post_content'=> '',
	) );
}
wp_cache_flush();
echo "pages=", implode( ',', array_keys( $ids ) ), "\n";
