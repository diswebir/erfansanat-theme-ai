<?php
/**
 * P07-T018 — search results across post/product/projects, typed + paginated.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T018', 'صفحه جستجو' );
es_check( 'target: search.php', es_file( 'search.php' ) );
es_check( 'AC-01: کوئری publish-only و صفحه‌بندی', es_contains( 'search.php', "'post_status'" ) && es_contains( 'search.php', 'paginate_links' ) );
es_check( 'AC-02: نوع نتیجه قابل تشخیص', es_contains( 'search.php', 'data-result-type' ) );
es_check( 'AC-03: حالت خالی امن', es_contains( 'search.php', 'هیچ نتیجه‌ای' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php'; // registers the product CPT like production Woo.
	$term = 'والواشرنمایتست' . substr( uniqid(), -5 );
	$post = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله ' . $term, 'post_name' => 's018-post-' . uniqid(), 'post_content' => 'متن ' . $term ) );
	$proj = post_type_exists( 'projects' ) ? (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه ' . $term, 'post_name' => 's018-proj-' . uniqid() ) ) : 0;
	$prod = post_type_exists( 'product' ) ? (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول ' . $term, 'post_name' => 's018-prod-' . uniqid() ) ) : 0;
	$draft = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'draft', 'post_title' => 'پیش‌نویس ' . $term, 'post_name' => 's018-draft-' . uniqid() ) );
	$page = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه ' . $term, 'post_name' => 's018-page-' . uniqid() ) );

	$render = static function ( $s, $paged = 1 ) {
		$q = new WP_Query( array( 's' => $s, 'paged' => $paged ) );
		$q->is_search = true;
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q;
		ob_start();
		include ES_THEME . '/search.php';
		return ob_get_clean();
	};
	$html = $render( $term );
	es_check( 'SC-01: جستجوی فارسی عنوان مقاله را می‌یابد', false !== strpos( $html, 'مقاله ' . $term ) );
	es_check( 'AC-02: نوع مقاله برچسب دارد', false !== strpos( $html, 'data-result-type="post"' ) );
	if ( $proj ) { es_check( 'نتیجه پروژه هم می‌آید', false !== strpos( $html, 'پروژه ' . $term ) && false !== strpos( $html, 'data-result-type="projects"' ) ); }
	else { es_check( 'نتیجه پروژه هم می‌آید', false ); }
	if ( $prod ) { es_check( 'نتیجه محصول هم می‌آید', false !== strpos( $html, 'محصول ' . $term ) && false !== strpos( $html, 'data-result-type="product"' ) ); }
	else { es_check( 'نتیجه محصول هم می‌آید', false ); }
	es_check( 'AC-01: پیش‌نویس هرگز نمی‌آید', false === strpos( $html, 'پیش‌نویس ' . $term ) );
	es_check( 'فرم جستجو عبارت را escape نگه می‌دارد', false !== strpos( $html, 'name="s"' ) && false !== strpos( $html, esc_attr( $term ) ) );

	$empty = $render( 'عبارت‌کاملاً‌ناموجود-' . uniqid() );
	es_check( 'SC-02: عبارت ناموجود حالت خالی می‌دهد', false !== strpos( $empty, 'هیچ نتیجه‌ای' ) && false === strpos( $empty, 'data-result-type' ) );

	$blank = $render( '' );
	es_check( 'جستجوی خالی پیام دعوت می‌دهد نه نتایج تصادفی', false !== strpos( $blank, 'عبارت مورد نظر' ) && false === strpos( $blank, 'data-result-type' ) );

	// XSS: search term with markup is escaped in the heading/attribute.
	$xss = $render( '<script>alert(1)</script>' );
	es_check( 'AC-01: عبارت جستجو escape می‌شود', false === strpos( $xss, '<script>alert(1)</script>' ) );

	// Pagination: many results → paginate_links present.
	$many_term = 'پرتکرار' . substr( uniqid(), -5 );
	$many = array();
	for ( $i = 0; $i < 6; $i++ ) { $many[] = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => $many_term . ' ' . $i, 'post_name' => 's018-many-' . $i . '-' . uniqid() ) ); }
	add_filter( 'es_search_per_page', static function () { return 2; } );
	$paged_html = $render( $many_term );
	es_check( 'AC-01: صفحه‌بندی برای نتایج زیاد ساخته می‌شود', false !== strpos( $paged_html, 'page-numbers' ) );

	wp_reset_postdata();
	foreach ( array_merge( array( $post, $proj, $prod, $draft, $page ), $many ) as $id ) { if ( $id ) { wp_delete_post( $id, true ); } }
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
