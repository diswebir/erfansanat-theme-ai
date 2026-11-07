<?php
/**
 * Arya landing purchase CTA (P07-T028). Editable heading/button; empty button
 * URL falls back to the contact page. Uses the shared gradient CTA card.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'cta' );

$es_heading = (string) ( $es_fields['heading'] ?? 'درخواست خرید آریا' );
$es_label   = (string) ( $es_fields['button_label'] ?? 'ثبت درخواست' );
$es_url     = (string) ( $es_fields['button_url'] ?? '' );
if ( '' === trim( $es_url ) ) {
	$es_url = Menus::page_url( 'contact' );
}
$es_contact = FooterTab::contact();
$es_phone   = (string) $es_contact['phone'];
$es_tel     = preg_replace( '/\s+/', '', $es_phone );
$es_fa      = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
?>
<section id="<?php echo esc_attr( $es_id ); ?>" class="mx-auto max-w-7xl scroll-mt-28 px-5 pb-16 lg:px-8 lg:pb-20" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="cta">
	<div class="relative overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-teal-50/70 p-8 text-center shadow-sm lg:p-12">
		<div aria-hidden="true" class="absolute -top-12 left-1/2 h-32 w-32 -translate-x-1/2 rounded-full bg-emerald-200/40 blur-[50px]"></div>
		<h2 class="relative text-2xl font-extrabold text-slate-900 lg:text-3xl"><?php echo esc_html( $es_heading ); ?></h2>
		<p class="relative mx-auto mt-3 max-w-2xl text-[14px] leading-8 text-slate-600"><?php esc_html_e( 'برای دریافت مشاوره رایگان، استعلام قیمت و هماهنگی نصب آریا همین حالا با کارشناسان کارخانه تماس بگیرید یا درخواست خود را ثبت کنید.', 'erfansanat' ); ?></p>
		<div class="relative mt-7 flex flex-wrap items-center justify-center gap-3">
			<a href="<?php echo esc_url( $es_url ); ?>" class="group inline-flex items-center gap-2.5 rounded-full bg-emerald-700 px-8 py-4 text-[14.5px] font-extrabold text-white shadow-lg shadow-emerald-700/25 transition-all hover:bg-emerald-800"><?php echo esc_html( $es_label ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a>
			<?php if ( '' !== $es_phone ) : ?><a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-7 py-3.5 text-[14px] font-bold text-emerald-800 shadow-sm transition-colors hover:border-emerald-400 hover:bg-emerald-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><?php echo esc_html( strtr( str_replace( '-', ' - ', $es_phone ), $es_fa ) ); ?></a><?php endif; ?>
		</div>
	</div>
</section>
