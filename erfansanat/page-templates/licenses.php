<?php
/**
 * Licenses & honors page (P07-T022).
 * Reference: static-mirror/licenses-and-honors.html.
 *
 * Certificates come from the editable media repeater (PagesTab::licenses());
 * order is preserved exactly. Each certificate may carry an image that opens
 * in an accessible lightbox (assets/js/certificates.js) with its alt text.
 * The global contact CTA is rendered by the footer — never duplicated here.
 *
 * Template Name: مجوزها و افتخارات
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\PagesTab;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

get_header();

$es_heading = (string) es_option( 'licenses_heading' );
if ( '' === trim( $es_heading ) ) {
	$es_heading = 'مجوزها و افتخارات';
}
$es_intro = (string) es_option( 'licenses_intro' );
$es_items = PagesTab::licenses();
$es_contact = FooterTab::contact();
$es_fa      = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_phone   = (string) $es_contact['phone'];
$es_tel     = preg_replace( '/\s+/', '', $es_phone );
$es_shop    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

// Resolve certificate images (full size for the lightbox, large for the card).
$es_has_images = false;
foreach ( $es_items as $es_i => $es_item ) {
	$es_img_id = (int) $es_item['image'];
	$es_items[ $es_i ]['image_url']  = $es_img_id > 0 ? (string) wp_get_attachment_image_url( $es_img_id, 'large' ) : '';
	$es_items[ $es_i ]['full_url']   = $es_img_id > 0 ? (string) wp_get_attachment_image_url( $es_img_id, 'full' ) : '';
	if ( '' !== $es_items[ $es_i ]['full_url'] ) {
		$es_has_images = true;
	}
	if ( '' === trim( $es_items[ $es_i ]['alt'] ) ) {
		$es_items[ $es_i ]['alt'] = $es_item['title'];
	}
}

$es_badges = array(
	array( 'شرکت دانش‌بنیان', 'مورد تایید معاونت علمی ریاست جمهوری' ),
	array( 'ISO 9001:2015', 'گواهینامه بین‌المللی مدیریت کیفیت تولید' ),
	array( 'تست آزمایشگاهی IP68', 'گواهی مقاومت کامل در برابر آب و گردوغبار' ),
	array( 'شناسه رسمی GS1 / ستاد', 'کدگذاری بین‌المللی GTIN و ایران‌کد' ),
);
?>
<main class="min-h-screen bg-[#f8faf9]">
	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div><div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div></div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php echo esc_html( $es_heading ); ?></span></span></nav>
			<div class="pt-6">
				<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs"><span class="h-2 w-2 rounded-full bg-emerald-600"></span><span><?php esc_html_e( 'گواهینامه‌ها، استانداردها و تاییدیه‌های رسمی', 'erfansanat' ); ?></span></div>
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $es_heading ); ?> <span class="text-emerald-700"><?php esc_html_e( 'شرکت دانش‌بنیان عرفان صنعت اصفهان', 'erfansanat' ); ?></span></h1>
				<?php if ( '' !== trim( $es_intro ) ) : ?><p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $es_intro ); ?></p><?php endif; ?>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20">
		<div class="mb-12 grid gap-4 rounded-[2rem] border border-emerald-100 bg-white p-6 shadow-[0_20px_50px_-28px_rgba(5,150,105,0.12)] sm:grid-cols-2 lg:grid-cols-4 lg:p-8">
			<?php foreach ( $es_badges as $es_badge ) : ?>
				<div class="flex items-start gap-3.5 rounded-2xl border border-slate-100 bg-slate-50/70 p-4"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-emerald-700 text-white shadow-xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-5 w-5" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg></span><div><div class="text-[14px] font-black text-slate-900"><?php echo esc_html( $es_badge[0] ); ?></div><p class="mt-1 text-[12px] leading-6 text-slate-600"><?php echo esc_html( $es_badge[1] ); ?></p></div></div>
			<?php endforeach; ?>
		</div>

		<?php if ( $es_items ) : ?>
			<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"<?php echo $es_has_images ? ' data-cert-gallery="true"' : ''; ?>>
				<?php foreach ( $es_items as $es_item ) : ?>
					<article class="card-light flex h-full flex-col justify-between rounded-[1.75rem] border border-slate-200/80 bg-white p-6 transition-all hover:border-emerald-300">
						<div>
							<?php if ( '' !== $es_item['image_url'] ) : ?>
								<button type="button" class="group mb-4 block w-full overflow-hidden rounded-2xl border border-slate-100 bg-slate-50" data-cert-open="true" data-cert-src="<?php echo esc_url( $es_item['full_url'] ); ?>" data-cert-alt="<?php echo esc_attr( $es_item['alt'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'بزرگ‌نمایی تصویر %s', 'erfansanat' ), $es_item['alt'] ) ); ?>">
									<img src="<?php echo esc_url( $es_item['image_url'] ); ?>" alt="<?php echo esc_attr( $es_item['alt'] ); ?>" class="h-48 w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" loading="lazy"/>
									<span class="flex items-center justify-center gap-1.5 bg-white/90 py-2 text-[11.5px] font-extrabold text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path><path d="M11 8v6"></path><path d="M8 11h6"></path></svg><?php esc_html_e( 'مشاهده تصویر گواهی', 'erfansanat' ); ?></span>
								</button>
							<?php endif; ?>
							<div class="mb-4 flex items-center justify-between gap-2">
								<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11.5px] font-extrabold text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg><?php echo esc_html( $es_item['category'] ); ?></span>
								<?php if ( '' !== $es_item['code'] ) : ?><span dir="ltr" class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-600"><?php echo esc_html( $es_item['code'] ); ?></span><?php endif; ?>
							</div>
							<h2 class="text-[16.5px] font-black leading-8 text-slate-900"><?php echo esc_html( $es_item['title'] ); ?></h2>
							<?php if ( '' !== $es_item['description'] ) : ?><p class="mt-3 text-[13.5px] leading-7 text-slate-600"><?php echo esc_html( $es_item['description'] ); ?></p><?php endif; ?>
						</div>
						<div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4 text-[12px] font-bold text-slate-500">
							<span class="flex items-center gap-1.5 text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check2 lucide-file-check-2 h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="m3 15 2 2 4-4"></path></svg><?php echo esc_html( $es_item['issuer'] ); ?></span>
							<?php if ( '' !== $es_item['year'] ) : ?><span class="text-emerald-700"><?php echo esc_html( $es_item['year'] ); ?></span><?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>

				<?php if ( $es_has_images ) : ?>
					<div data-cert-lightbox="true" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/85 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'نمایش بزرگ گواهی', 'erfansanat' ); ?>">
						<button type="button" data-cert-close="true" class="absolute left-5 top-5 grid h-11 w-11 place-items-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20" aria-label="<?php esc_attr_e( 'بستن', 'erfansanat' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button>
						<button type="button" data-cert-prev="true" class="absolute right-4 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20" aria-label="<?php esc_attr_e( 'گواهی قبلی', 'erfansanat' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg></button>
						<figure class="flex max-h-[90vh] max-w-4xl flex-col items-center gap-3">
							<img data-cert-img="true" src="" alt="" class="max-h-[78vh] w-auto rounded-2xl object-contain shadow-2xl"/>
							<figcaption data-cert-caption="true" class="text-center text-[13px] font-bold text-white/90"></figcaption>
							<span data-cert-counter="true" class="rounded-full bg-white/10 px-3 py-1 text-[12px] font-bold text-white/80"></span>
						</figure>
						<button type="button" data-cert-next="true" class="absolute left-4 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20" aria-label="<?php esc_attr_e( 'گواهی بعدی', 'erfansanat' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="mt-14">
			<div class="rounded-[2rem] border border-emerald-200/80 bg-gradient-to-br from-emerald-50/90 via-white to-emerald-50/40 p-7 sm:p-10">
				<div class="grid items-center gap-8 lg:grid-cols-12">
					<div class="space-y-4 lg:col-span-8">
						<div class="inline-flex items-center gap-2 rounded-xl bg-emerald-800 px-3.5 py-1.5 text-[12px] font-extrabold text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-4 w-4" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg><span><?php esc_html_e( 'ویژه شهرداری‌ها، دهیاری‌ها و دستگاه‌های اجرایی', 'erfansanat' ); ?></span></div>
						<h2 class="text-xl font-black text-slate-950 sm:text-2xl"><?php esc_html_e( 'آمادگی کامل جهت عقد قراردادهای رسمی و عرضه در سامانه تدارکات الکترونیکی دولت (ستاد)', 'erfansanat' ); ?></h2>
						<p class="text-[14.5px] leading-8 text-slate-600"><?php esc_html_e( 'تمامی محصولات تولیدی شرکت دانش‌بنیان عرفان صنعت اصفهان دارای کد ملی کالا، کد بین‌المللی GS1 (GTIN)، کد HS گمرکی و کد ISIC صنعتی بوده و مستندات آزمایشگاهی آن‌ها جهت پیوست در اسناد مناقصه و خریدهای دولتی آماده ارائه به کارفرمایان محترم است.', 'erfansanat' ); ?></p>
						<div class="grid gap-2.5 pt-2 sm:grid-cols-2">
							<?php
							$es_points = array(
								'ارائه پیش‌فاکتور رسمی ممهور در کمتر از ۲ ساعت کاری',
								'پیوست برگه‌های آنالیز فنی و گواهی‌های آزمایشگاهی IP67 / IP68',
								'امکان بازدید حضوری هیئت‌های کارشناسی از خطوط تولید کارخانه',
								'ضمانت‌نامه معتبر ۲۴ ماهه تعویض و ۵ سال خدمات پس از فروش',
							);
							foreach ( $es_points as $es_point ) :
								?>
								<div class="flex items-center gap-2 text-[13px] font-bold text-slate-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><span><?php echo esc_html( $es_point ); ?></span></div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="flex flex-col gap-3 rounded-2xl border border-emerald-200 bg-white p-6 shadow-sm lg:col-span-4">
						<div class="flex items-center gap-2.5 text-[14px] font-black text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-5 w-5 text-emerald-700" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg><span><?php esc_html_e( 'دریافت مستندات و کاتالوگ رسمی', 'erfansanat' ); ?></span></div>
						<p class="text-[12.5px] leading-6 text-slate-600"><?php esc_html_e( 'جهت دریافت فایل PDF گواهینامه‌ها و استعلام بهای محصولات با واحد فروش کارخانه تماس بگیرید:', 'erfansanat' ); ?></p>
						<?php if ( '' !== $es_phone ) : ?><a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="mt-2 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-800 px-5 py-3.5 text-[13.5px] font-black text-white transition-colors hover:bg-emerald-900 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span><?php echo esc_html( strtr( str_replace( '-', ' - ', $es_phone ), $es_fa ) ); ?></span></a><?php endif; ?>
						<a href="<?php echo esc_url( $es_shop ); ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[13px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-900 whitespace-nowrap"><?php esc_html_e( 'مشاهده محصولات دارای شناسه رسمی', 'erfansanat' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
