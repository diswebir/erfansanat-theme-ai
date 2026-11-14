<?php
/**
 * P05-T008 — سکشن انواع پروژه
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T008', 'سکشن انواع پروژه' );

es_check( 'target: بخش انواع پروژه ساخته شده', es_file( 'template-parts/sections/home/section-project-types.php' ) );
es_check( 'AC-02: از ترم‌های project_type استفاده می‌کند', es_contains( 'template-parts/sections/home/section-project-types.php', "'project_type'" ) );
es_check( 'SC-01: لینک از get_term_link می‌آید', es_contains( 'template-parts/sections/home/section-project-types.php', 'get_term_link' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}
	if ( ! taxonomy_exists( 'project_type' ) ) {
		register_taxonomy( 'project_type', 'projects', array( 'public' => true, 'hierarchical' => true ) );
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'project-types', 'type' => 'project-types', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// Fixtures: three Persian project types + projects (for real count/image).
	$t1 = wp_insert_term( 'تونل نوری شهری', 'project_type', array( 'slug' => 'light-tunnel', 'description' => 'مسیرهای عبوری با جلوه‌ای چشم‌نواز' ) );
	$t2 = wp_insert_term( 'گوی و المان حجمی نورانی', 'project_type', array( 'slug' => 'light-sphere', 'description' => 'کره‌ها و حجم‌های نوری درخشان' ) );
	$t3 = wp_insert_term( 'درخت و المان نوری هوشمند', 'project_type', array( 'slug' => 'light-tree', 'description' => 'المان‌های نوری منحصربه‌فرد' ) );

	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'type-img', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/tunnel.jpg' );
	$pr1 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه تونل ۱' ) );
	$pr2 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه تونل ۲' ) );
	$pr3 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه گوی' ) );
	set_post_thumbnail( $pr1, $att );
	wp_set_object_terms( $pr1, array( (int) $t1['term_id'] ), 'project_type' );
	wp_set_object_terms( $pr2, array( (int) $t1['term_id'] ), 'project_type' );
	wp_set_object_terms( $pr3, array( (int) $t2['term_id'] ), 'project_type' );

	$html = $render();

	// ── AC-01: id=projects preserved ──────────────────────────────────────.
	es_check( 'AC-01: id=projects حفظ شده', false !== strpos( $html, 'id="projects"' ) );
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="project-types"' ) );

	// ── AC-02: real terms / image / counter ───────────────────────────────.
	es_check( 'AC-02: نام ترم‌ها رندر می‌شود', false !== strpos( $html, 'تونل نوری شهری' ) && false !== strpos( $html, 'گوی و المان حجمی نورانی' ) && false !== strpos( $html, 'درخت و المان نوری هوشمند' ) );
	es_check( 'AC-02: تصویر واقعی از اولین پروژه نوع', false !== strpos( $html, 'tunnel.jpg' ) );
	es_check( 'AC-02: شمارنده واقعی پروژه‌ها (۲ پروژه)', false !== strpos( $html, '۲ پروژه اجراشده' ) );
	es_check( 'AC-02: نشانگر شماره کارت فارسی (۰۱/۰۲/۰۳)', false !== strpos( $html, '۰۱' ) && false !== strpos( $html, '۰۲' ) && false !== strpos( $html, '۰۳' ) );
	es_check( 'AC-02: توضیح ترم رندر می‌شود', false !== strpos( $html, 'مسیرهای عبوری با جلوه‌ای چشم‌نواز' ) );

	// ── SC-01 / SC-02: card link goes to the type archive (Persian-safe) ──.
	$link1 = get_term_link( (int) $t1['term_id'], 'project_type' );
	es_check( 'SC-01: کلیک کارت به آرشیو نوع می‌رود', false !== strpos( $html, 'href="' . esc_url( $link1 ) . '"' ) );
	es_check( 'SC-02: ترم فارسی درست لینک می‌شود', false !== strpos( $html, 'href="' . esc_url( get_term_link( (int) $t3['term_id'], 'project_type' ) ) . '"' ) );

	// ── AC-03: count from settings (per_page) ─────────────────────────────.
	$html2 = $render( array( 'per_page' => 2 ) );
	es_check( 'AC-03: per_page تعداد کارت‌ها را کنترل می‌کند', 2 === substr_count( $html2, 'card-glow group relative block' ) );
	$html3 = $render( array( 'per_page' => 1 ) );
	es_check( 'AC-03: per_page=1 فقط کارت بزرگ', 1 === substr_count( $html3, 'card-glow group relative block' ) && false !== strpos( $html3, 'lg:col-span-3' ) );

	// ── AC-03: order from settings (filter) ───────────────────────────────.
	add_filter( 'es_project_types_orderby', static function () { return 'name'; } );
	add_filter( 'es_project_types_order', static function () { return 'ASC'; } );
	$html4 = $render();
	// name ASC (Unicode): تونل (ت) < درخت (د) < گوی (گ). Count DESC had گوی before درخت.
	$pos_tree   = strpos( $html4, 'درخت و المان نوری هوشمند' );
	$pos_sphere = strpos( $html4, 'گوی و المان حجمی نورانی' );
	es_check( 'AC-03: ترتیب از تنظیمات (name ASC) اعمال می‌شود', false !== $pos_tree && false !== $pos_sphere && $pos_tree < $pos_sphere );
	remove_all_filters( 'es_project_types_orderby' );
	remove_all_filters( 'es_project_types_order' );

	// ── Heading escaped + empty state ─────────────────────────────────────.
	$html5 = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html5, '<b>bold</b>' ) && false !== strpos( $html5, '&lt;b&gt;' ) );

	// Cleanup.
	foreach ( array( $pr1, $pr2, $pr3, $att ) as $id ) { wp_delete_post( $id, true ); }
	foreach ( array( $t1, $t2, $t3 ) as $t ) { wp_delete_term( $t['term_id'], 'project_type' ); }
	$html6 = $render();
	es_check( 'حالت خالی fatal نمی‌دهد و id حفظ می‌شود', false !== strpos( $html6, 'id="projects"' ) && false === strpos( $html6, 'card-glow' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
