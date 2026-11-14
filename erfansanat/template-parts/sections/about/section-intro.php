<?php
/**
 * About company intro (P07-T020). DOM parity with about.html section 2.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'intro' );

$es_heading   = (string) ( $es_fields['heading'] ?? 'بیش از دو دهه پیشگامی در صنعت روشنایی ایران' );
$es_establish = (string) ( $es_fields['establish'] ?? 'تأسیس ۱۳۷۸' );
$es_content   = (string) ( $es_fields['content'] ?? '' );
if ( '' === trim( wp_strip_all_tags( $es_content ) ) ) {
	$es_content = '<p><strong class="text-slate-900">شرکت دانش‌بنیان عرفان صنعت اصفهان</strong> با بیش از دو دهه سابقه فعالیت، یکی از شرکت‌های پیشرو در حوزه طراحی، تولید و اجرای پروژه‌های نورپردازی شهری و المان‌های نوری در کشور به‌شمار می‌رود. ما با تکیه بر دانش فنی نیروهای متخصص، بهره‌گیری از فناوری‌های نوین و استفاده از متریال باکیفیت، در مسیر ارتقاء کیفیت بصری فضاهای شهری و ایجاد محیط‌هایی جذاب، ایمن و ماندگار گام برداشته‌ایم.</p><p>حوزه فعالیت عرفان صنعت شامل طراحی و ساخت المان‌های نوری تزئینی، اجرای پروژه‌های نورپردازی محوطه‌ای، پارکی، خیابانی و میدانی، تولید تجهیزات تعاملی نوری و ارائه راهکارهای خلاقانه برای زیباسازی محیط‌های عمومی است.</p>';
}
$es_image_id = (int) ( $es_fields['image'] ?? 0 );
$es_image    = $es_image_id > 0 ? (string) wp_get_attachment_image_url( $es_image_id, 'large' ) : '';
if ( ! $es_image ) {
	$es_image = ES_ASSETS_URI . 'images/factory.jpg';
}
$es_stats = ! empty( $es_fields['stats'] ) && is_array( $es_fields['stats'] ) ? $es_fields['stats'] : array(
	array( 'number' => '۲۵+', 'label' => 'سال تجربه درخشان' ),
	array( 'number' => '۸۵۰+', 'label' => 'پروژه تکمیل‌شده' ),
	array( 'number' => '۲۸۹+', 'label' => 'مشتری فعال' ),
	array( 'number' => '۱۴۰+', 'label' => 'نیروی متخصص' ),
);
?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-20" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="intro">
	<div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
		<div>
			<div class="relative">
				<div class="relative overflow-hidden rounded-[1.75rem] border border-emerald-100 shadow-xl"><img src="<?php echo esc_url( $es_image ); ?>" alt="<?php echo esc_attr( $es_heading ); ?>" class="h-80 w-full object-cover lg:h-[26rem]"/></div>
				<div class="absolute -bottom-6 right-6 flex items-center gap-3 rounded-2xl border border-emerald-100 bg-white px-5 py-4 shadow-lg"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-factory h-8 w-8 text-emerald-600" aria-hidden="true"><path d="M12 16h.01"></path><path d="M16 16h.01"></path><path d="M3 19a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.5a.5.5 0 0 0-.769-.422l-4.462 2.844A.5.5 0 0 1 15 10.5v-2a.5.5 0 0 0-.769-.422L9.77 10.922A.5.5 0 0 1 9 10.5V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"></path><path d="M8 16h.01"></path></svg><span class="flex flex-col leading-tight"><span class="text-[14px] font-extrabold text-slate-900"><?php echo esc_html( $es_establish ); ?></span><span class="text-[11.5px] text-slate-500"><?php esc_html_e( 'اولین تولیدکننده ریسه و تزئینات شهری در ایران', 'erfansanat' ); ?></span></span></div>
			</div>
		</div>
		<div class="flex flex-col items-start gap-6">
			<div><span class="inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[13px] font-bold text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg><?php esc_html_e( 'شرکت دانش‌بنیان', 'erfansanat' ); ?></span></div>
			<div><h2 class="text-2xl font-extrabold leading-[1.4] text-slate-900 lg:text-3xl"><?php echo esc_html( $es_heading ); ?></h2></div>
			<div class="space-y-4 text-[15px] leading-9 text-slate-600 lg:leading-10"><?php echo wp_kses_post( $es_content ); ?></div>
		</div>
	</div>
	<?php if ( $es_stats ) : ?>
		<div class="mt-20 grid grid-cols-2 gap-5 lg:grid-cols-4">
			<?php foreach ( $es_stats as $es_stat ) : ?>
				<div><div class="card-light flex flex-col items-center gap-2 rounded-3xl p-7 text-center"><span class="text-4xl font-black text-emerald-700"><?php echo esc_html( (string) ( $es_stat['number'] ?? '' ) ); ?></span><span class="text-[13.5px] font-semibold text-slate-600"><?php echo esc_html( (string) ( $es_stat['label'] ?? '' ) ); ?></span></div></div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
