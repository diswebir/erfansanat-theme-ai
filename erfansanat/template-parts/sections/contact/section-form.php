<?php
/**
 * Contact form + info cards (P07-T021). DOM parity with contact.html
 * section 2. Info falls back to the shared contact options; each field can be
 * overridden per page. Map embed only accepts http(s) URLs. The form keeps
 * the reference data-contact-* DOM but has NO action/method and NO named
 * inputs, so nothing is submitted or stored before the Phase 8 handler; a
 * visible note explains the interim phone-first flow.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\FooterTab;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'form' );

$es_contact = FooterTab::contact();
$es_heading = (string) ( $es_fields['heading'] ?? 'فرم درخواست مشاوره رایگان' );
$es_desc    = (string) ( $es_fields['description'] ?? '' );
if ( '' === trim( $es_desc ) ) {
	$es_desc = 'اطلاعات پروژه خود را وارد کنید تا کارشناسان فنی و فروش ما در سریع‌ترین زمان با شما تماس بگیرند.';
}
$es_phone   = '' !== trim( (string) ( $es_fields['phone'] ?? '' ) ) ? (string) $es_fields['phone'] : (string) $es_contact['phone'];
$es_email   = '' !== trim( (string) ( $es_fields['email'] ?? '' ) ) ? (string) $es_fields['email'] : (string) $es_contact['email'];
$es_address = '' !== trim( (string) ( $es_fields['address'] ?? '' ) ) ? (string) $es_fields['address'] : (string) $es_contact['address'];
$es_note    = (string) ( $es_fields['form_note'] ?? 'ثبت آنلاین درخواست به‌زودی فعال می‌شود؛ تا آن زمان لطفاً تلفنی تماس بگیرید.' );
$es_map     = (string) ( $es_fields['map_embed'] ?? '' );
if ( '' !== $es_map && ! preg_match( '#^https?://#i', $es_map ) ) {
	$es_map = '';
}
$es_fa  = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_tel = preg_replace( '/\s+/', '', $es_phone );
$es_input = 'w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pr-11 pl-4 text-[14px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10';
?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-20" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="form">
	<div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:gap-12">
		<div>
			<div class="card-light rounded-[2rem] p-8">
				<h2 class="text-xl font-extrabold text-slate-900"><?php echo esc_html( $es_heading ); ?></h2>
				<p class="mt-2 text-[13.5px] leading-7 text-slate-500"><?php echo esc_html( $es_desc ); ?></p>
				<?php if ( '' !== trim( $es_note ) ) : ?>
					<p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[12.5px] font-bold leading-6 text-amber-800" data-contact-note="true"><?php echo esc_html( $es_note ); ?></p>
				<?php endif; ?>
				<div data-contact-success="true" class="hidden mt-8 flex-col items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-8 text-center"><span class="grid h-14 w-14 place-items-center rounded-full bg-emerald-600 text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span><h3 class="text-lg font-extrabold text-emerald-900"><?php esc_html_e( 'درخواست شما با موفقیت ثبت شد!', 'erfansanat' ); ?></h3><p class="text-[13.5px] leading-7 text-emerald-800"><?php esc_html_e( 'کارشناسان فروش عرفان صنعت به‌زودی با شما تماس خواهند گرفت.', 'erfansanat' ); ?></p><button type="button" data-contact-reset="true" class="mt-2 rounded-full border border-emerald-300 bg-white px-5 py-2 text-xs font-extrabold text-emerald-800 transition-colors hover:bg-emerald-100 cursor-pointer"><?php esc_html_e( 'ارسال پیام جدید', 'erfansanat' ); ?></button></div>
				<?php get_template_part( 'template-parts/forms/contact' ); // P08-T013: real handler (dependency recorded). ?>
			</div>
		</div>
		<div class="flex flex-col gap-6">
			<div class="card-light rounded-[2rem] p-8">
				<h3 class="text-lg font-extrabold text-slate-900"><?php esc_html_e( 'تماس مستقیم با کارشناسان فروش', 'erfansanat' ); ?></h3>
				<div class="mt-5 flex flex-col gap-4">
					<?php if ( '' !== $es_phone ) : ?>
						<div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg></span><div class="min-w-0 flex-1"><div class="text-[11.5px] font-semibold text-slate-500"><?php esc_html_e( 'تلفن کارخانه', 'erfansanat' ); ?></div><a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="mt-0.5 block truncate text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700"><?php echo esc_html( strtr( str_replace( '-', ' - ', $es_phone ), $es_fa ) ); ?></a><div class="mt-0.5 text-[11px] font-medium text-slate-500"><?php esc_html_e( '۱۰ خط ویژه', 'erfansanat' ); ?></div></div></div>
					<?php endif; ?>
					<?php if ( '' !== $es_email ) : ?>
						<div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg></span><div class="min-w-0 flex-1"><div class="text-[11.5px] font-semibold text-slate-500"><?php esc_html_e( 'ایمیل شرکت', 'erfansanat' ); ?></div><a href="<?php echo esc_url( 'mailto:' . $es_email ); ?>" dir="ltr" class="mt-0.5 block truncate text-base font-black text-slate-900 transition-colors hover:text-emerald-700"><?php echo esc_html( $es_email ); ?></a><div class="mt-0.5 text-[11px] font-medium text-slate-500"><?php esc_html_e( 'پاسخگویی در سریع‌ترین زمان', 'erfansanat' ); ?></div></div></div>
					<?php endif; ?>
					<?php if ( '' !== $es_address ) : ?>
						<div class="flex items-start gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg></span><div class="min-w-0 flex-1"><div class="text-[11.5px] font-semibold text-slate-500"><?php esc_html_e( 'آدرس کارخانه', 'erfansanat' ); ?></div><div class="mt-0.5 text-[13.5px] font-bold leading-7 text-slate-900"><?php echo esc_html( $es_address ); ?></div><?php if ( ! empty( $es_contact['postal_code'] ) ) : ?><div class="mt-0.5 text-[11px] font-medium text-slate-500"><?php echo esc_html( sprintf( __( 'کدپستی %s', 'erfansanat' ), strtr( (string) $es_contact['postal_code'], $es_fa ) ) ); ?></div><?php endif; ?></div></div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( '' !== $es_map ) : ?>
				<div class="card-light overflow-hidden rounded-[2rem]">
					<iframe src="<?php echo esc_url( $es_map ); ?>" title="<?php esc_attr_e( 'نقشه موقعیت کارخانه', 'erfansanat' ); ?>" class="h-72 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
