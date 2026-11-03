<?php
/**
 * P02-T012 — تب فوتر/تماس/شبکه‌های اجتماعی
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Tabs\FooterTab;
use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Options\Sanitizer;

es_task( 'P02-T012', 'تب فوتر/تماس/شبکه‌های اجتماعی' );

es_check( 'target: inc/Options/Tabs/FooterTab.php', es_file( 'inc/Options/Tabs/FooterTab.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	Store::reset();

	$mirror  = (string) file_get_contents( ES_MIRROR . '/index.html' );
	$contact = FooterTab::contact();

	// AC-03: static defaults preserved (present in the mirror).
	es_check( 'AC-03: تلفن پیش‌فرض از mirror', false !== strpos( $mirror, $contact['phone'] ) );
	es_check( 'AC-03: ایمیل پیش‌فرض از mirror', false !== strpos( $mirror, $contact['email'] ) );
	es_check( 'AC-03: نشانی پیش‌فرض از mirror', false !== strpos( $mirror, $contact['address'] ) );
	es_check( 'AC-03: کپی‌رایت پیش‌فرض از mirror', false !== strpos( $mirror, FooterTab::copyright() ) );

	// SC-01: changing the phone is reflected in the footer/CTA data source.
	Store::set( 'contact_phone', '031-12345678' );
	es_check( 'SC-01: تغییر تلفن در فوتر/CTA دیده می‌شود', '031-12345678' === FooterTab::contact()['phone'] );
	Store::reset();

	// AC-01: contact fields sanitize through the Sanitizer.
	$clean = Sanitizer::sanitize_values(
		array(
			'contact_phone' => ' 031-91091011 ',
			'contact_email' => 'info@erfansanat.com',
		)
	);
	es_check( 'AC-01: تلفن sanitize می‌شود', '031-91091011' === $clean['values']['contact_phone'] );
	es_check( 'AC-01: ایمیل نامعتبر رد می‌شود', isset( Sanitizer::sanitize_values( array( 'contact_email' => 'bad' ) )['errors']['contact_email'] ) );

	// AC-02: social links carry a safe rel/target.
	$social = FooterTab::social_links();
	$safe   = ! empty( $social );
	foreach ( $social as $link ) {
		$safe = $safe && 'noopener noreferrer' === $link['rel'] && '_blank' === $link['target'];
	}
	es_check( 'AC-02: لینک شبکه اجتماعی با rel امن', $safe );

	// SC-02: an empty social URL is not rendered.
	Store::set(
		'social_links',
		array(
			array( 'network' => 'instagram', 'url' => 'https://instagram.com/erfan_sanat_esfahan' ),
			array( 'network' => 'telegram', 'url' => '' ),
		)
	);
	$filtered = FooterTab::social_links();
	es_check( 'SC-02: لینک خالی رندر نمی‌شود', 1 === count( $filtered ) && 'instagram' === $filtered[0]['network'] );

	Store::reset();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
