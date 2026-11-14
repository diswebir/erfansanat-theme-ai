<?php
/**
 * P07-T015 — view counter hardening: bots, previews, admins, editors, dedupe.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\ViewCounter;

es_task( 'P07-T015', 'شمارنده بازدید نوشته/پروژه/محصول' );
es_check( 'target: inc/Core/ViewCounter.php', es_file( 'inc/Core/ViewCounter.php' ) );
es_check( 'AC-03: کلید اصلی number2-viewc', es_contains( 'inc/Core/ViewCounter.php', "'number2-viewc'" ) );
es_check( 'AC-02: تشخیص بات و پیش‌نمایش در سورس', es_contains( 'inc/Core/ViewCounter.php', 'is_bot' ) && es_contains( 'inc/Core/ViewCounter.php', 'is_preview' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// Pure bot detection.
	es_check( 'AC-02: Googlebot/bot/crawler/spider شناسایی می‌شوند', ViewCounter::is_bot( 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' ) && ViewCounter::is_bot( 'bingbot/2.0' ) && ViewCounter::is_bot( 'Some Crawler v1' ) && ViewCounter::is_bot( 'YandexSpider' ) );
	es_check( 'AC-02: UA خالی بات محسوب می‌شود', ViewCounter::is_bot( '' ) );
	es_check( 'AC-02: مرورگر واقعی بات نیست', ! ViewCounter::is_bot( 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36' ) );
	$ua_filter = static function ( $bots ) { $bots[] = 'customprobe'; return $bots; };
	add_filter( 'es_view_counter_bots', $ua_filter );
	es_check('فیلتر لیست بات کار می‌کند', ViewCounter::is_bot( 'CustomProbe/1.0' ) );
	remove_filter( 'es_view_counter_bots', $ua_filter );

	$make = static function ( $type = 'post' ) {
		return (int) wp_insert_post( array( 'post_type' => $type, 'post_status' => 'publish', 'post_title' => 'بازدید ' . uniqid(), 'post_name' => 'vc-015-' . uniqid() ) );
	};
	$set_single = static function ( $pid, $type = 'post' ) {
		$q = new WP_Query( array( 'p' => $pid, 'post_type' => $type ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
	};
	$_COOKIE = array();
	$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0) Chrome/126.0 Safari/537.36';

	// SC-01: normal singular view increments exactly once.
	$pid = $make();
	$set_single( $pid );
	ViewCounter::maybe_count();
	es_check( 'SC-01: بازدید عادی single دقیقاً +1', 1 === ViewCounter::get( $pid ) && '1' === (string) get_post_meta( $pid, 'number2-viewc', true ) );

	// Dedupe: same visitor (cookie) does not double count.
	$_COOKIE[ ViewCounter::COOKIE ] = wp_json_encode( array( $pid ) );
	ViewCounter::maybe_count();
	es_check( 'AC-01: بازدید تکراری همان بازدیدکننده شمارش نمی‌شود', 1 === ViewCounter::get( $pid ) );
	$_COOKIE = array();

	// SC-02: bot UA never increments.
	$bot_pid = $make();
	$set_single( $bot_pid );
	$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (compatible; Googlebot/2.1)';
	ViewCounter::maybe_count();
	es_check( 'SC-02: بات افزایش نمی‌دهد', 0 === ViewCounter::get( $bot_pid ) );
	$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0) Chrome/126.0 Safari/537.36';

	// SC-02: preview never increments.
	$prev_pid = $make();
	$prev_q = new WP_Query( array( 'p' => $prev_pid, 'post_type' => 'post', 'preview' => true ) );
	$GLOBALS['wp_query'] = $prev_q; $GLOBALS['wp_the_query'] = $prev_q; $prev_q->the_post();
	$prev_q->set( 'is_preview', true );
	ViewCounter::maybe_count();
	es_check( 'SC-02: پیش‌نمایش افزایش نمی‌دهد', 0 === ViewCounter::get( $prev_pid ) );

	// SC-02: logged-in editor of the post is not counted.
	$editor = (int) wp_insert_user( array( 'user_login' => 'vc015_' . uniqid(), 'user_pass' => wp_generate_password(), 'role' => 'editor' ) );
	$ed_pid = $make();
	$set_single( $ed_pid );
	wp_set_current_user( $editor );
	ViewCounter::maybe_count();
	es_check( 'SC-02: مدیر/ویرایشگر واردشده شمارش نمی‌شود', 0 === ViewCounter::get( $ed_pid ) );
	wp_set_current_user( 0 );

	// Archive/non-singular context never increments.
	$GLOBALS['wp_query'] = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 1 ) );
	$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
	$arch_pid = $make();
	ViewCounter::maybe_count();
	es_check( 'AC-01: زمینه غیر single شمارش نمی‌شود', 0 === ViewCounter::get( $arch_pid ) );

	// Unsupported post type is ignored even when singular.
	$page_id = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه بازدید' ) );
	$pq = new WP_Query( array( 'page_id' => $page_id ) );
	$GLOBALS['wp_query'] = $pq; $GLOBALS['wp_the_query'] = $pq; $pq->the_post();
	ViewCounter::maybe_count();
	es_check( 'AC-01: نوع پشتیبانی‌نشده (page) شمارش نمی‌شود', 0 === ViewCounter::get( $page_id ) );

	// Protected post is not counted before unlock.
	$prot = $make();
	wp_update_post( array( 'ID' => $prot, 'post_password' => 'x' ) );
	$set_single( $prot );
	ViewCounter::maybe_count();
	es_check( 'نوشته رمزدار قفل‌شده شمارش نمی‌شود', 0 === ViewCounter::get( $prot ) );

	// Projects singular still counts (shared counter contract).
	if ( post_type_exists( 'projects' ) ) {
		$proj = $make( 'projects' );
		$set_single( $proj, 'projects' );
		ViewCounter::maybe_count();
		es_check( 'single پروژه هم +1 می‌گیرد', 1 === ViewCounter::get( $proj ) );
		wp_delete_post( $proj, true );
	} else {
		es_check( 'single پروژه هم +1 می‌گیرد', false );
	}

	es_check( 'اتصال به اکشن wp حفظ شده', false !== has_action( 'wp', array( ViewCounter::class, 'maybe_count' ) ) );
	wp_reset_postdata();
	foreach ( array( $pid, $bot_pid, $prev_pid, $ed_pid, $arch_pid, $page_id, $prot ) as $id ) { wp_delete_post( $id, true ); }
	require_once ABSPATH . 'wp-admin/includes/user.php';
	wp_delete_user( $editor );
	$_COOKIE = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
