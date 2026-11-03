<?php
/**
 * P03-T011 — ستون‌ها و فیلترهای ادمین پروژه
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProjectColumns;

es_task( 'P03-T011', 'ستون‌ها و فیلترهای ادمین پروژه' );

es_check( 'target: inc/Admin/ProjectColumns.php', es_file( 'inc/Admin/ProjectColumns.php' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';

	// Columns added.
	$cols = apply_filters( 'manage_projects_posts_columns', array( 'title' => 'عنوان' ) );
	es_check( 'ستون city اضافه شد', isset( $cols['city'] ) );
	es_check( 'ستون project_type اضافه شد', isset( $cols['project_type'] ) );
	es_check( 'ستون same_key_hrefl اضافه شد', isset( $cols['same_key_hrefl'] ) );

	// AC-02: same_key_hrefl is sortable.
	$sortable = apply_filters( 'manage_edit-projects_sortable_columns', array() );
	es_check( 'AC-02: same_key_hrefl sortable است', isset( $sortable['same_key_hrefl'] ) );

	// AC-01: city + project_type filter dropdowns render on the projects screen.
	$city = wp_insert_term( 'اصفهان', 'city', array( 'slug' => 'esfahan-t011' ) );
	$type = wp_insert_term( 'تونل نوری', 'project_type', array( 'slug' => 'tunnel-t011' ) );
	ob_start();
	ProjectColumns::filter_dropdowns( 'projects' );
	$dropdowns = ob_get_clean();
	es_check( 'AC-01: dropdown شهر رندر می‌شود', 1 === preg_match( '/name=[\'"]city[\'"]/', $dropdowns ) );
	es_check( 'AC-01: dropdown نوع پروژه رندر می‌شود', 1 === preg_match( '/name=[\'"]project_type[\'"]/', $dropdowns ) );
	// Non-projects screen renders nothing.
	ob_start();
	ProjectColumns::filter_dropdowns( 'post' );
	es_check( 'AC-01: روی صفحه غیرپروژه چیزی رندر نمی‌شود', '' === ob_get_clean() );

	// SC-01: filtering by city returns only that city's projects.
	$p1 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه اصفهان' ) );
	$p2 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه قم' ) );
	wp_set_object_terms( $p1, array( (int) $city['term_id'] ), 'city' );
	$q = new WP_Query( array( 'post_type' => 'projects', 'city' => 'esfahan-t011' ) );
	$ids = wp_list_pluck( $q->posts, 'ID' );
	es_check( 'SC-01: فیلتر شهر فقط پروژه همان شهر', in_array( $p1, $ids, true ) && ! in_array( $p2, $ids, true ) );

	// SC-02: sorting by same_key_hrefl orders by the meta value.
	update_post_meta( $p1, 'same_key_hrefl', 'b-key' );
	update_post_meta( $p2, 'same_key_hrefl', 'a-key' );
	$query = new WP_Query();
	$query->set( 'post_type', 'projects' );
	$query->set( 'orderby', 'same_key_hrefl' );
	$query->set( 'order', 'ASC' );
	$GLOBALS['wp_the_query'] = $query; // make it the main query.
	ProjectColumns::apply_sort( $query );
	es_check( 'SC-02: meta_key تنظیم شد', 'same_key_hrefl' === $query->get( 'meta_key' ) );
	$query->get_posts();
	$sorted = wp_list_pluck( $query->posts, 'ID' );
	es_check( 'SC-02: ترتیب بر اساس کلید hreflang', array( $p2, $p1 ) === array_slice( $sorted, 0, 2 ) );

	// AC-03: column output is escaped.
	update_post_meta( $p1, 'same_key_hrefl', '<b>x</b>' );
	ob_start();
	ProjectColumns::render_column( 'same_key_hrefl', $p1 );
	$out = ob_get_clean();
	es_check( 'AC-03: خروجی ستون escape می‌شود', false === strpos( $out, '<b>x</b>' ) && false !== strpos( $out, '&lt;b&gt;' ) );

	wp_delete_post( $p1, true );
	wp_delete_post( $p2, true );
	wp_delete_term( $city['term_id'], 'city' );
	wp_delete_term( $type['term_id'], 'project_type' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
