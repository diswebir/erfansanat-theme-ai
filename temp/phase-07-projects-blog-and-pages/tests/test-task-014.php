<?php
/**
 * P07-T014 — single-post sharing: encoded network links + copy-link fallback.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-014.php
 * DOM: node temp/phase-07-projects-blog-and-pages/tests/test-task-014-dom.mjs
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T014', 'اشتراک‌گذاری تک‌نوشته' );
es_check( 'target: template-parts/post/share.php', es_file( 'template-parts/post/share.php' ) );
es_check( 'AC-03: قرارداد initShareButtons مرجع', es_contains( 'template-parts/post/share.php', 'data-share-btn="true"' ) && es_contains( 'template-parts/post/share.php', 'data-share-label="true"' ) );
es_check( 'AC-02: فیلد fallback کپی لینک', es_contains( 'template-parts/post/share.php', 'data-share-url' ) );
es_check( 'وابستگی T012: ردیف برچسب/اشتراک به part سپرده شد', es_contains( 'single.php', "get_template_part( 'template-parts/post/share'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$pid = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'راهنمای تونل نوری & نورپردازی', 'post_name' => 'share-014-' . uniqid(), 'post_content' => 'متن' ) );
	wp_set_post_tags( $pid, array( 'تونل نوری' ) );
	$q = new WP_Query( array( 'p' => $pid, 'post_type' => 'post' ) );
	$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
	ob_start();
	get_template_part( 'template-parts/post/share', null, array( 'post_id' => $pid ) );
	$html = ob_get_clean();
	$permalink = get_permalink( $pid );
	$encoded   = rawurlencode( $permalink );
	$title_enc = rawurlencode( get_the_title( $pid ) );

	es_check( 'SC-02: لینک تلگرام با URL encode کامل', false !== strpos( $html, 'https://t.me/share/url?url=' . $encoded ) );
	es_check( 'SC-02: لینک واتس‌اپ encode شده', false !== strpos( $html, 'https://wa.me/?text=' ) && false !== strpos( $html, $encoded ) );
	es_check( 'SC-02: لینک X/Twitter encode شده', false !== strpos( $html, 'https://twitter.com/intent/tweet?url=' . $encoded . '&#038;text=' . $title_enc ) );
	es_check( 'SC-02: لینک LinkedIn encode شده', false !== strpos( $html, 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded ) );
	es_check( 'AC-01: عنوان دارای & در لینک خام نشت نمی‌کند', false === strpos( $html, 'text=راهنمای تونل نوری &' ) );
	es_check( 'AC-02: مقدار fallback خود permalink است', false !== strpos( $html, 'value="' . esc_url( $permalink ) . '"' ) && false !== strpos( $html, 'readonly' ) );
	es_check( 'لینک‌های شبکه noopener/target امن', 4 === substr_count( $html, 'rel="noopener noreferrer"' ) && 4 === substr_count( $html, 'target="_blank"' ) );
	es_check( 'برچسب‌ها در همان ردیف مرجع حفظ شدند', false !== strpos( $html, 'برچسب‌ها:' ) && false !== strpos( $html, 'تونل نوری' ) && false !== strpos( $html, 'mt-12 flex flex-col gap-5 rounded-3xl border border-emerald-100 bg-white p-6 shadow-xs' ) );

	ob_start();
	include ES_THEME . '/single.php';
	$single = ob_get_clean();
	es_check( 'تک‌نوشته کامل دکمه اشتراک دارد', 1 === substr_count( $single, 'data-share-btn="true"' ) && false !== strpos( $single, 'اشتراک‌گذاری مقاله' ) );

	wp_update_post( array( 'ID' => $pid, 'post_password' => 'x' ) );
	$q = new WP_Query( array( 'p' => $pid, 'post_type' => 'post' ) );
	$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
	ob_start();
	get_template_part( 'template-parts/post/share', null, array( 'post_id' => $pid ) );
	$protected = ob_get_clean();
	es_check( 'نوشته رمزدار لینک اشتراک عمومی نمی‌دهد', '' === trim( $protected ) );

	wp_reset_postdata();
	wp_delete_post( $pid, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
