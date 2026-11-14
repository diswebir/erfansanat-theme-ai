<?php
/**
 * About hero (P07-T020). DOM parity with static-mirror/about.html section 1.
 * Heading/highlight/badge/image/stats are editable section fields.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'hero' );

$es_heading   = (string) ( $es_fields['heading'] ?? 'درباره شرکت' );
$es_highlight = (string) ( $es_fields['highlight'] ?? 'عرفان صنعت اصفهان' );
$es_badge     = (string) ( $es_fields['badge'] ?? 'شرکت دانش‌بنیان — تأسیس ۱۳۷۸' );
$es_content   = (string) ( $es_fields['content'] ?? '' );
if ( '' === trim( wp_strip_all_tags( $es_content ) ) ) {
	$es_content = '<p>«درخشان‌تر از همیشه، آینده‌ای روشن را برای ایران می‌سازیم» — پیشگام طراحی، تولید و اجرای پروژه‌های نورپردازی شهری و صنعتی در ایران</p>';
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
	array( 'number' => '۹۰+', 'label' => 'محصول متنوع' ),
);
?>
<section class="relative bg-[#f6f8f7] pt-28 lg:pt-36" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="hero">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
		<div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-50"></div>
		<div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/50 blur-[110px]"></div>
		<div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/70 blur-[110px]"></div>
		<div class="absolute left-[8%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/30 to-transparent"></div>
		<div class="absolute right-[12%] top-0 h-full w-px bg-gradient-to-b from-transparent via-teal-300/25 to-transparent"></div>
		<span class="absolute -bottom-10 left-0 select-none whitespace-nowrap text-[10rem] font-black leading-none text-emerald-950/[0.03] lg:text-[14rem]"><?php esc_html_e( 'عرفان صنعت', 'erfansanat' ); ?></span>
	</div>
	<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
		<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a>
			<span class="flex min-w-0 items-center gap-1.5"><svg aria-hidden="true" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php esc_html_e( 'درباره ما', 'erfansanat' ); ?></span></span>
		</nav>
		<div class="grid items-center gap-12 pb-16 pt-10 lg:grid-cols-[1.02fr_0.98fr] lg:gap-14 lg:pb-20 lg:pt-14">
			<div>
				<div class="mb-6 inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-700 text-white shadow-[0_0_20px_rgba(0,125,45,0.35)]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-4 w-4" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg></span><span class="text-[13px] font-extrabold text-emerald-800"><?php echo esc_html( $es_badge ); ?></span></div>
				<h1 class="text-[2.5rem] font-black leading-[1.25] tracking-[-0.015em] text-slate-950 sm:text-[3.3rem] lg:text-[4rem] lg:leading-[1.18]"><?php echo esc_html( $es_heading ); ?><br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent"><?php echo esc_html( $es_highlight ); ?></span></h1>
				<div class="mt-6 max-w-[36rem] border-r-2 border-emerald-500/60 pr-5 text-[15.5px] font-medium leading-9 text-slate-600 lg:text-[16.5px] lg:leading-10"><?php echo wp_kses_post( $es_content ); ?></div>
			</div>
			<div class="relative mx-auto w-full max-w-[32rem]">
				<div aria-hidden="true" class="absolute -inset-0 translate-x-4 translate-y-4 rounded-[2.25rem] border border-emerald-200/80 bg-emerald-100/35"></div>
				<div aria-hidden="true" class="absolute left-1/2 top-1/2 h-[70%] w-[70%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-200/50 blur-[80px]"></div>
				<div class="relative overflow-hidden rounded-[2.25rem] border border-emerald-100 bg-white p-2 shadow-[0_36px_80px_-32px_rgba(0,80,40,0.28)]">
					<div class="relative overflow-hidden rounded-[1.85rem]">
						<img src="<?php echo esc_url( $es_image ); ?>" alt="<?php echo esc_attr( $es_highlight ); ?>" class="h-[21rem] w-full object-cover sm:h-[24rem] lg:h-[27rem]"/>
						<div class="absolute inset-0 bg-gradient-to-t from-slate-950/55 via-transparent to-transparent"></div>
						<div class="absolute inset-x-4 bottom-4 flex items-center justify-between rounded-2xl border border-white/80 bg-white/95 px-4 py-3 shadow-lg backdrop-blur-md"><span class="text-[12.5px] font-bold text-slate-900"><?php echo esc_html( $es_highlight ); ?></span><span class="flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-extrabold text-emerald-800 border border-emerald-200"><span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-600"></span></span><?php echo esc_html( $es_badge ); ?></span></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ( $es_stats ) : ?>
		<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
			<div class="grid translate-y-1/2 grid-cols-2 gap-y-2 overflow-hidden rounded-3xl border border-emerald-200/80 bg-white/95 shadow-[0_28px_60px_-28px_rgba(0,80,40,0.22)] backdrop-blur-xl sm:grid-cols-4">
				<?php foreach ( $es_stats as $es_i => $es_stat ) : ?>
					<div class="flex flex-col items-center gap-1.5 px-4 py-6 text-center <?php echo $es_i > 0 ? 'sm:border-r sm:border-emerald-100' : ''; ?>"><span class="bg-gradient-to-b from-emerald-600 to-emerald-800 bg-clip-text text-[2rem] font-black leading-none text-transparent lg:text-[2.3rem]"><?php echo esc_html( (string) ( $es_stat['number'] ?? '' ) ); ?></span><span class="text-[12.5px] font-bold text-slate-600"><?php echo esc_html( (string) ( $es_stat['label'] ?? '' ) ); ?></span></div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
	<div aria-hidden="true" class="h-16 bg-[#f8faf9] sm:h-[4.5rem]"></div>
</section>
