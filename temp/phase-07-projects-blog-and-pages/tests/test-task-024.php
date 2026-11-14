<?php
/**
 * P07-T024 — warranty page: editable terms/icons/cards, contact links, DOM parity.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-024.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

es_task( 'P07-T024', 'برگه شرایط گارانتی' );
es_check( 'target: page-templates/warranty.php', es_file( 'page-templates/warranty.php' ) );
es_check( 'AC-03: DOM مرجع (کارت‌ها/لیست‌ها/مراحل)', es_contains( 'page-templates/warranty.php', 'md:grid-cols-3' ) && es_contains( 'page-templates/warranty.php', 'موارد تحت پوشش' ) && es_contains( 'page-templates/warranty.php', 'موارد خارج از شمول' ) && es_contains( 'page-templates/warranty.php', 'مراحل ثبت درخواست' ) );
es_check( 'AC-02: لینک تماس/استعلام در قالب', es_contains( 'page-templates/warranty.php', "tel:" ) && es_contains( 'page-templates/warranty.php', '/contact/' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$fields = Schema::fields();
	es_check( 'AC-01: کارت‌ها Repeater با آیکون/عنوان/متن', isset( $fields['warranty_cards'] ) && 'repeater' === $fields['warranty_cards']['type'] && isset( $fields['warranty_cards']['subfields']['icon'], $fields['warranty_cards']['subfields']['title'], $fields['warranty_cards']['subfields']['description'] ) && 'icon' === $fields['warranty_cards']['subfields']['icon']['type'] );
	es_check( 'AC-01: لیست‌های پوشش/استثنا و مراحل Repeater', isset( $fields['warranty_covered'], $fields['warranty_excluded'], $fields['warranty_steps'] ) && 'repeater' === $fields['warranty_covered']['type'] && 'repeater' === $fields['warranty_steps']['type'] );

	$page_id = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'شرایط گارانتی', 'post_name' => 'warranty-024-' . uniqid() ) );
	update_post_meta( $page_id, '_wp_page_template', 'page-templates/warranty.php' );
	$render = static function ( $post_id ) {
		$q = new WP_Query( array( 'page_id' => $post_id, 'post_type' => 'page' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		include ES_THEME . '/page-templates/warranty.php';
		return ob_get_clean();
	};

	foreach ( array( 'warranty_cards', 'warranty_covered', 'warranty_excluded', 'warranty_steps' ) as $k ) { Store::delete( $k ); }
	$html = $render( $page_id );
	$contact = FooterTab::contact();
	es_check( 'مرجع: کارت‌ها/لیست‌های پیش‌فرض رندر می‌شوند', false !== strpos( $html, '۲۴ ماه گارانتی تعویض بی‌قید و شرط' ) && 3 === substr_count( $html, 'text-[17px] font-black' ) && false !== strpos( $html, 'موارد تحت پوشش گارانتی تعویض ۲۴ ماهه' ) );
	es_check( 'AC-02: تلفن از تنظیمات تماس می‌آید', false !== strpos( $html, 'tel:' . preg_replace( '/\s+/', '', $contact['phone'] ) ) );
	es_check( 'AC-02: لینک ثبت درخواست به برگه تماس', false !== strpos( $html, 'href="' . esc_url( home_url( '/contact/' ) ) . '"' ) );
	es_check( 'AC-03: آیکون کارت رندر می‌شود', false !== strpos( $html, 'lucide-shield-check' ) );

	// SC-01: edit the warranty duration in the first card title.
	Store::set( 'warranty_cards', array(
		array( 'icon' => 'star', 'title' => '۳۶ ماه گارانتی ویژه آزمایشی', 'description' => 'توضیح کارت آزمایشی گارانتی.' ),
	) );
	$edited = $render( $page_id );
	// The hero intro also mentions "۲۴ ماه…", so assert the edited card title
	// renders and the card grid now holds exactly the one saved card.
	es_check( 'SC-01: ویرایش مدت گارانتی در خروجی دیده می‌شود', false !== strpos( $edited, '۳۶ ماه گارانتی ویژه آزمایشی' ) && 1 === substr_count( $edited, 'text-[17px] font-black' ) );
	es_check( 'AC-01: آیکون کارت ویرایش‌پذیر است', false !== strpos( $edited, 'lucide-star' ) );

	// Editable covered/excluded lists + steps.
	Store::set( 'warranty_covered', array( array( 'text' => 'مورد پوشش آزمایشی یک' ) ) );
	Store::set( 'warranty_excluded', array( array( 'text' => 'مورد استثنای آزمایشی یک' ) ) );
	Store::set( 'warranty_steps', array( array( 'title' => 'مرحله آزمایشی', 'description' => 'توضیح مرحله آزمایشی.' ) ) );
	$lists = $render( $page_id );
	es_check( 'AC-01: لیست پوشش/استثنای سفارشی رندر می‌شود', false !== strpos( $lists, 'مورد پوشش آزمایشی یک' ) && false !== strpos( $lists, 'مورد استثنای آزمایشی یک' ) );
	es_check( 'AC-01: مراحل سفارشی رندر می‌شود', false !== strpos( $lists, 'مرحله آزمایشی' ) && false !== strpos( $lists, 'توضیح مرحله آزمایشی.' ) );

	foreach ( array( 'warranty_cards', 'warranty_covered', 'warranty_excluded', 'warranty_steps' ) as $k ) { Store::delete( $k ); }
	wp_reset_postdata();
	wp_delete_post( $page_id, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
