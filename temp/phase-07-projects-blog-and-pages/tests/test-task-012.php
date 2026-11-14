<?php
/**
 * P07-T012 — single post shell: reference main/aside, full content, password form.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Tabs\FooterTab;

es_task( 'P07-T012', 'Shell تک‌نوشته' );
es_check( 'target: single.php', es_file( 'single.php' ) );
es_check( 'AC-01: ساختار main/aside مرجع', es_contains( 'single.php', 'min-h-screen bg-[#f8faf9]' ) && es_contains( 'single.php', 'lg:grid-cols-[minmax(0,1fr)_300px]' ) && es_contains( 'single.php', '<aside class="flex flex-col gap-6"' ) );
es_check( 'AC-02: محتوای کامل با the_content', es_contains( 'single.php', 'the_content()' ) );
es_check( 'AC-03: فرم رمز وردپرس', es_contains( 'single.php', 'post_password_required' ) && es_contains( 'single.php', 'get_the_password_form' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$cat = es_insert_term( 'مجله علمی', 'category', array( 'slug' => 'single-012-' . uniqid() ) )['term_id'];
	add_shortcode( 'es_t012_probe', static function () { return '<mark id="es-shortcode-proof">SHORTCODE_OK</mark>'; } );
	$content = "<!-- wp:paragraph --><p>پاراگراف بلوک واقعی</p><!-- /wp:paragraph -->\n[es_t012_probe]";
	$pid = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'راهنمای کامل تونل نوری', 'post_excerpt' => 'خلاصه اختصاصی مقاله', 'post_content' => $content, 'post_date' => '2025-08-03 10:00:00', 'post_name' => 'single-012-' . uniqid() ) );
	wp_set_object_terms( $pid, array( (int) $cat ), 'category' );
	wp_set_post_tags( $pid, array( 'تونل نوری', 'نورپردازی شهری' ) );
	$related_ids = array();
	for ( $i = 1; $i <= 2; $i++ ) {
		$rid = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله مرتبط ' . $i, 'post_name' => 'related-012-' . $i . '-' . uniqid() ) );
		wp_set_object_terms( $rid, array( (int) $cat ), 'category' );
		$related_ids[] = $rid;
	}
	$project_id = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه نباید مرتبط باشد', 'post_name' => 'project-012-' . uniqid() ) );

	$render = static function ( $post_id ) {
		$q = new WP_Query( array( 'p' => $post_id, 'post_type' => 'post' ) );
		$GLOBALS['wp_query'] = $q;
		$GLOBALS['wp_the_query'] = $q;
		$q->the_post();
		ob_start();
		include ES_THEME . '/single.php';
		$html = ob_get_clean();
		wp_reset_postdata();
		return $html;
	};
	$html = $render( $pid );
	es_check( 'SC-01: مقاله کامل با عنوان/خلاصه/تاریخ/برچسب', is_single() && false !== strpos( $html, 'راهنمای کامل تونل نوری' ) && false !== strpos( $html, 'خلاصه اختصاصی مقاله' ) && false !== strpos( $html, '۱۲ مرداد ۱۴۰۴' ) && false !== strpos( $html, '#تونل نوری' ) );
	es_check( 'AC-01: main/article/aside رندر شد', false !== strpos( $html, '<main class="min-h-screen bg-[#f8faf9]"' ) && false !== strpos( $html, '<article class="min-w-0"' ) && false !== strpos( $html, '<aside class="flex flex-col gap-6"' ) );
	es_check( 'AC-02: بلوک و شورت‌کد حذف نشدند', false !== strpos( $html, 'پاراگراف بلوک واقعی' ) && false !== strpos( $html, 'SHORTCODE_OK' ) );
	es_check( 'تصویر شاخص/جایگزین رندر شد', 1 === preg_match( '/<img[^>]+class="h-64 w-full rounded-\[1\.5rem\] object-cover sm:h-96"/', $html ) );
	es_check( 'بخش مقالات مرتبط رندر شد', false !== strpos( $html, 'مقالات مرتبط' ) );
	$rel_pos = strpos( $html, 'مقالات مرتبط' );
	$rel_html = false !== $rel_pos ? substr( $html, $rel_pos ) : '';
	es_check( 'مرتبط فقط مقاله است و پروژه/نوشته جاری ندارد', false !== strpos( $rel_html, 'مقاله مرتبط 1' ) && false !== strpos( $rel_html, 'مقاله مرتبط 2' ) && false === strpos( $rel_html, 'پروژه نباید مرتبط باشد' ) && false === strpos( $rel_html, 'href="' . esc_url( get_permalink( $pid ) ) . '"' ) );
	$contact = FooterTab::contact();
	es_check( 'سایدبار تماس واقعی از تنظیمات', false !== strpos( $html, 'مشاوره فنی رایگان پروژه' ) && false !== strpos( $html, 'tel:' . preg_replace( '/\s+/', '', (string) $contact['phone'] ) ) );

	wp_update_post( array( 'ID' => $pid, 'post_password' => 'secret-012' ) );
	$protected = $render( $pid );
	es_check( 'SC-02: نوشته محافظت‌شده فرم رمز درست می‌دهد', false !== strpos( $protected, 'post-password-form' ) && false !== strpos( $protected, 'name="post_password"' ) && false === strpos( $protected, 'SHORTCODE_OK' ) );

	es_check( 'سلسله‌مراتب قالب تک‌نوشته', 'single.php' === basename( locate_template( array( 'single-post.php', 'single.php' ) ) ) );
	$GLOBALS['post'] = null;
	$GLOBALS['wp_query'] = new WP_Query( array( 'p' => 999999999, 'post_type' => 'post' ) );
	ob_start();
	include ES_THEME . '/single.php';
	$missing = ob_get_clean();
	es_check( 'نوشته نامعتبر fatal نمی‌دهد', false !== strpos( $missing, 'مقاله مورد نظر یافت نشد.' ) );

	remove_shortcode( 'es_t012_probe' );
	foreach ( array_merge( array( $pid, $project_id ), $related_ids ) as $id ) { wp_delete_post( $id, true ); }
	wp_delete_term( $cat, 'category' );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
