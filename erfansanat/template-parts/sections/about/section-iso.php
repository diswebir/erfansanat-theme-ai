<?php
/**
 * About ISO certificate (P07-T020). DOM parity with about.html section 3.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'iso' );

$es_heading = (string) ( $es_fields['heading'] ?? 'گواهینامه ISO 9001:2015؛ نشانه تعهد ما به کیفیت' );
$es_badge   = (string) ( $es_fields['badge'] ?? 'ISO 9001:2015' );
$es_content = (string) ( $es_fields['content'] ?? '' );
if ( '' === trim( wp_strip_all_tags( $es_content ) ) ) {
	$es_content = '<p>شرکت ما با افتخار موفق به اخذ گواهینامه ISO 9001:2015 شده است. این گواهینامه بین‌المللی، مهر تأییدی است بر سیستم مدیریت کیفیت پیاده‌سازی‌شده در تمامی فرآیندهای سازمان ما؛ از طراحی و تولید تا ارائه خدمات پس از فروش، با رویکردی مشتری‌مدار و تمرکز بر بهبود مستمر.</p>';
}
$es_items = ! empty( $es_fields['items'] ) && is_array( $es_fields['items'] ) ? $es_fields['items'] : array(
	array( 'text' => 'سیستم مدیریت کیفیت در تمامی فرآیندها' ),
	array( 'text' => 'رویکرد مشتری‌مدار و بهبود مستمر' ),
	array( 'text' => 'انطباق با الزامات و استانداردهای جهانی' ),
);
?>
<section class="border-y border-emerald-100 bg-white py-16 lg:py-20" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="iso">
	<div class="mx-auto grid max-w-7xl items-center gap-12 px-5 lg:grid-cols-2 lg:px-8">
		<div class="flex flex-col items-start gap-6">
			<div><span class="inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[13px] font-bold text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-4 w-4" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg><?php esc_html_e( 'گواهینامه بین‌المللی', 'erfansanat' ); ?></span></div>
			<div><h2 class="text-2xl font-extrabold leading-[1.4] text-slate-900 lg:text-3xl"><?php echo esc_html( $es_heading ); ?></h2></div>
			<div class="text-[15px] leading-9 text-slate-600"><?php echo wp_kses_post( $es_content ); ?></div>
			<?php if ( $es_items ) : ?>
				<ul class="flex flex-col gap-3.5">
					<?php foreach ( $es_items as $es_item ) : ?>
						<li class="flex items-center gap-3 text-[14.5px] font-semibold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg><?php echo esc_html( (string) ( $es_item['text'] ?? '' ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<div>
			<div class="relative mx-auto max-w-md">
				<div class="absolute -inset-4 rounded-[2rem] bg-emerald-100/60 blur-2xl"></div>
				<div class="card-light relative flex flex-col items-center gap-5 rounded-[2rem] p-10 text-center">
					<span class="grid h-24 w-24 place-items-center rounded-full border-4 border-emerald-200 bg-emerald-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-12 w-12 text-emerald-700" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg></span>
					<span class="text-2xl font-black text-slate-900"><?php echo esc_html( $es_badge ); ?></span>
					<span class="text-[13px] font-semibold text-slate-500"><?php esc_html_e( 'سیستم مدیریت کیفیت بین‌المللی', 'erfansanat' ); ?></span>
				</div>
			</div>
		</div>
	</div>
</section>
