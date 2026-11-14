<?php
/**
 * P07-T016 — related posts by category/tag overlap, standard card, configurable.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T016', 'مقالات مرتبط' );
es_check( 'target: template-parts/post/related.php', es_file( 'template-parts/post/related.php' ) );
es_check( 'AC-02: تعداد فیلترپذیر es_related_posts_count', es_contains( 'template-parts/post/related.php', 'es_related_posts_count' ) );
es_check( 'کارت استاندارد با گونه فشرده مرجع', es_contains( 'template-parts/post/related.php', "get_template_part( 'template-parts/cards/card-post'" ) && es_contains( 'template-parts/cards/card-post.php', "'compact'" ) );
es_check( 'وابستگی T012: سکشن مرتبط به part سپرده شد', es_contains( 'single.php', "get_template_part( 'template-parts/post/related'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$cat_a = es_insert_term( 'دسته الف', 'category', array( 'slug' => 'rel-a-' . uniqid() ) )['term_id'];
	$cat_b = es_insert_term( 'دسته ب', 'category', array( 'slug' => 'rel-b-' . uniqid() ) )['term_id'];
	$tag   = wp_insert_term( 'برچسب مشترک', 'post_tag', array( 'slug' => 'rel-tag-' . uniqid() ) )['term_id'];

	$mk = static function ( $title, $cats, $tags = array(), $status = 'publish' ) {
		$id = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => $status, 'post_title' => $title, 'post_name' => sanitize_title( $title ) . '-' . uniqid(), 'post_content' => 'متن مقاله' ) );
		wp_set_object_terms( $id, array_map( 'intval', $cats ), 'category' );
		if ( $tags ) { wp_set_object_terms( $id, array_map( 'intval', $tags ), 'post_tag' ); }
		return $id;
	};
	$current = $mk( 'مقاله جاری', array( $cat_a ), array( $tag ) );
	$both    = $mk( 'مرتبط دسته و برچسب', array( $cat_a ), array( $tag ) );
	$catonly = $mk( 'مرتبط فقط دسته', array( $cat_a ) );
	$tagonly = $mk( 'مرتبط فقط برچسب', array( $cat_b ), array( $tag ) );
	$far     = $mk( 'مقاله دور', array( $cat_b ) );
	$draft   = $mk( 'پیش‌نویس پنهان', array( $cat_a ), array( $tag ), 'draft' );

	$render = static function ( $pid, $args = array() ) {
		$q = new WP_Query( array( 'p' => $pid, 'post_type' => 'post' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		get_template_part( 'template-parts/post/related', null, array_merge( array( 'post_id' => $pid ), $args ) );
		return ob_get_clean();
	};
	$html = $render( $current );
	$pos_both = strpos( $html, 'مرتبط دسته و برچسب' );
	$pos_cat  = strpos( $html, 'مرتبط فقط دسته' );
	$pos_tag  = strpos( $html, 'مرتبط فقط برچسب' );
	es_check( 'SC-01: سه مرتبط رندر می‌شود', 3 === substr_count( $html, 'card-light group flex h-full' ) && false !== $pos_both && false !== $pos_cat && false !== $pos_tag );
	es_check( 'AC-01: نوشته جاری حذف شده', false === strpos( $html, 'href="' . esc_url( get_permalink( $current ) ) . '"' ) );
	es_check( 'هم‌پوشانی دسته+برچسب جلوتر است', $pos_both < $pos_cat && $pos_both < $pos_tag );
	es_check( 'پیش‌نویس هرگز نمایش داده نمی‌شود', false === strpos( $html, 'پیش‌نویس پنهان' ) );
	es_check( 'کارت فشرده مرجع (h-44/ادامه مطلب)', false !== strpos( $html, 'relative h-44 overflow-hidden' ) && false !== strpos( $html, 'ادامه مطلب' ) );

	$two = $render( $current, array( 'count' => 2 ) );
	es_check( 'AC-02: args count تعداد را کنترل می‌کند', 2 === substr_count( $two, 'card-light group flex h-full' ) );
	$count_filter = static function () { return 1; };
	add_filter( 'es_related_posts_count', $count_filter );
	$one = $render( $current );
	remove_filter( 'es_related_posts_count', $count_filter );
	es_check( 'AC-02: فیلتر تعداد کار می‌کند', 1 === substr_count( $one, 'card-light group flex h-full' ) );

	// Empty: no other posts at all → no section.
	$lonely_cat = es_insert_term( 'دسته تنها', 'category', array( 'slug' => 'rel-lonely-' . uniqid() ) )['term_id'];
	$lonely = $mk( 'مقاله تنها', array( $lonely_cat ) );
	$hidden_ids = array( $both, $catonly, $tagonly, $far, $draft );
	$exclude = static function ( $args ) use ( $hidden_ids ) { $args['post__not_in'] = array_merge( (array) ( $args['post__not_in'] ?? array() ), $GLOBALS['es_rel_hidden'] ); return $args; };
	$GLOBALS['es_rel_hidden'] = $hidden_ids;
	add_filter( 'es_related_posts_query_args', $exclude );
	$empty = $render( $lonely );
	remove_filter( 'es_related_posts_query_args', $exclude );
	es_check( 'AC-03: بدون مرتبط هیچ سکشنی ساخته نمی‌شود', '' === trim( $empty ) );

	// Full single still shows the related heading via the part.
	$q = new WP_Query( array( 'p' => $current, 'post_type' => 'post' ) );
	$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
	ob_start(); include ES_THEME . '/single.php'; $single = ob_get_clean();
	es_check( 'تک‌نوشته کامل بخش مرتبط دارد', false !== strpos( $single, 'مقالات مرتبط' ) && false !== strpos( $single, 'مرتبط دسته و برچسب' ) );

	wp_reset_postdata();
	foreach ( array( $current, $both, $catonly, $tagonly, $far, $draft, $lonely ) as $id ) { wp_delete_post( $id, true ); }
	foreach ( array( $cat_a, $cat_b, $lonely_cat ) as $tid ) { wp_delete_term( $tid, 'category' ); }
	wp_delete_term( $tag, 'post_tag' );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
