<?php
/**
 * P02-T002 — Helper es_option و کش
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Helpers;
use ErfanSanat\Theme\Options\Store;

es_task( 'P02-T002', 'Helper es_option و کش' );

es_check( 'target: inc/Helpers.php', es_file( 'inc/Helpers.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'AC-01: تابع es_option تعریف شده', function_exists( 'es_option' ) );

	Store::reset();
	Helpers::flush();

	// AC-01 / SC-02: exact fallback + unknown key default.
	es_check( 'AC-01: پیش‌فرض دقیق brand_name', 'عرفان صنعت اصفهان' === es_option( 'brand_name' ) );
	es_check( 'SC-02: کلید ناموجود default می‌دهد', 'پیش‌فرض من' === es_option( 'no_such_key_xyz', 'پیش‌فرض من' ) );
	es_check( 'AC-01: کلید شناخته‌شده با default سفارشی، مقدار schema را می‌دهد', 'info@erfansanat.com' === es_option( 'contact_email', 'x@y.z' ) );

	// AC-03: repeated reads add no DB queries once the static cache is warm.
	$queries = 0;
	$counter = function ( $q ) use ( &$queries ) {
		$queries++;
		return $q;
	};
	add_filter( 'query', $counter );
	Helpers::flush();
	es_option( 'brand_name' ); // warm the cache (may run one query).
	$baseline = $queries;
	for ( $i = 0; $i < 50; $i++ ) {
		es_option( 'brand_name' );
		es_option( 'contact_email' );
		es_option( 'shop_posts_per_page' );
	}
	$extra = $queries - $baseline;
	remove_filter( 'query', $counter );
	es_check( 'AC-03: خواندن مکرر کوئری اضافه نمی‌دهد', 0 === $extra );
	es_check( 'AC-03: کش استاتیک گرم است', Helpers::is_cached() );

	// SC-01: a Store write is visible immediately in the same request.
	Store::set( 'brand_name', 'تغییر آنی' );
	es_check( 'SC-01: تغییر مقدار بلافاصله دیده می‌شود', 'تغییر آنی' === es_option( 'brand_name' ) );

	// AC-02: a direct update_option also invalidates the cache.
	$raw = Store::raw();
	$raw['brand_name'] = 'تغییر مستقیم';
	update_option( Store::OPTION, $raw );
	es_check( 'AC-02: پس از update_option کش پاک می‌شود', 'تغییر مستقیم' === es_option( 'brand_name' ) );

	Store::reset();
	es_check( 'پاک‌سازی: بازگشت به پیش‌فرض', 'عرفان صنعت اصفهان' === es_option( 'brand_name' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
