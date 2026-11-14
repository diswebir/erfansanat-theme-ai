<?php
/**
 * P07-T021 — contact page: editable info/map, safe form until Phase 8.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-021.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\SectionManager;
use ErfanSanat\Theme\Sections\Renderer;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

es_task( 'P07-T021', 'برگه تماس با ما' );
es_check( 'target: page-templates/contact.php', es_file( 'page-templates/contact.php' ) );
es_check( 'AC-03: DOM فرم مرجع', es_contains( 'template-parts/forms/contact.php', 'data-contact-form="true"' ) && es_contains( 'template-parts/sections/contact/section-form.php', 'data-contact-success="true"' ) ); // P08-T013: form extracted to template-parts/forms/contact.php (dependency recorded).
es_check( 'AC-02: فرم بدون action/method ذخیره‌کننده', ! es_contains( 'template-parts/sections/contact/section-form.php', 'admin-post.php' ) && ! es_contains( 'template-parts/sections/contact/section-form.php', 'method="post"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$page_id = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'تماس با ما', 'post_name' => 'contact-021-' . uniqid() ) );
	update_post_meta( $page_id, '_wp_page_template', 'page-templates/contact.php' );
	$render = static function ( $post_id ) {
		Renderer::invalidate( $post_id );
		$q = new WP_Query( array( 'page_id' => $post_id, 'post_type' => 'page' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		include ES_THEME . '/page-templates/contact.php';
		return ob_get_clean();
	};
	$html = $render( $page_id );
	$contact = FooterTab::contact();
	es_check( 'اطلاعات تماس از تنظیمات می‌آید', false !== strpos( $html, esc_html( $contact['phone'] ) ) || false !== strpos( $html, 'tel:' . preg_replace( '/\s+/', '', $contact['phone'] ) ) );
	es_check( 'ایمیل/نشانی تنظیمات رندر می‌شود', false !== strpos( $html, esc_html( $contact['email'] ) ) && false !== strpos( $html, esc_html( $contact['address'] ) ) );
	es_check( 'AC-02: پیام امن وضعیت فرم دیده می‌شود', false !== strpos( $html, 'به‌زودی فعال می‌شود' ) );
	es_check( 'SC-02: فرم واقعی امن شد (name + nonce + honeypot، بدون action خارجی)', es_contains( 'template-parts/forms/contact.php', 'name="es_cf_name"' ) && es_contains( 'template-parts/forms/contact.php', 'name="nonce"' ) && es_contains( 'template-parts/forms/contact.php', 'es_cf_hp' ) && false === strpos( (string) file_get_contents( ES_THEME . '/template-parts/forms/contact.php' ), 'action="http' ) ); // P08-T013: real handler replaced the pre-Phase-8 stub (dependency recorded).

	// SC-01: phone override in the section shows up.
	$cfg = SectionManager::get_sections( $page_id, 'contact' );
	foreach ( $cfg as &$sec ) {
		if ( 'form' === $sec['type'] ) {
			$sec['fields']['phone'] = '021-12345678';
			$sec['fields']['map_embed'] = 'https://www.openstreetmap.org/export/embed.html?bbox=51.3,32.6,51.5,32.8';
		}
	}
	unset( $sec );
	SectionManager::save_sections( $page_id, $cfg, 'contact' );
	$edited = $render( $page_id );
	es_check( 'SC-01: تغییر تلفن در صفحه دیده می‌شود', false !== strpos( $edited, 'tel:021-12345678' ) );
	es_check( 'AC-01: نقشه قابل ویرایش رندر می‌شود', false !== strpos( $edited, '<iframe' ) && false !== strpos( $edited, 'openstreetmap.org' ) );

	// Unsafe map schemes are rejected.
	foreach ( $cfg as &$sec ) { if ( 'form' === $sec['type'] ) { $sec['fields']['map_embed'] = 'javascript:alert(1)'; } }
	unset( $sec );
	SectionManager::save_sections( $page_id, $cfg, 'contact' );
	$unsafe = $render( $page_id );
	es_check( 'AC-01: طرح ناامن نقشه رد می‌شود', false === strpos( $unsafe, 'javascript:alert' ) );

	wp_reset_postdata();
	wp_delete_post( $page_id, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
