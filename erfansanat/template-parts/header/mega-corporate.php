<?php
/**
 * Corporate / about mega menu panel.
 *
 * Links resolve to real site pages via Menus::page_url (AC-02). The panel
 * title, text and image are editable from theme options (AC-03); an empty
 * image falls back to the stats block (SC-02). DOM mirrors the reference.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;

$es_corp_title = (string) es_option( 'mega_corporate_title' );
$es_corp_text  = (string) es_option( 'mega_corporate_text' );
$es_corp_img_id = (int) es_option( 'mega_corporate_image_id' );
$es_corp_img   = $es_corp_img_id > 0 ? (string) wp_get_attachment_image_url( $es_corp_img_id, 'medium' ) : '';

$es_icons = array(
		'award' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-4 w-4" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg>',
		'shield-check' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>',
		'circle-question-mark' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-question-mark h-4 w-4" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><path d="M12 17h.01"></path></svg>',
		'phone-call' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>',
		'file-check' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check2 lucide-file-check-2 h-4 w-4" aria-hidden="true"><path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="m3 15 2 2 4-4"></path></svg>',
		'building' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 h-4 w-4" aria-hidden="true"><path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-3a2 2 0 0 0-4 0v3"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path></svg>',
);

$es_corp_links = array(
		array( 'slug' => 'about', 'title' => 'درباره کارخانه عرفان صنعت', 'desc' => 'بیش از ۲۵ سال تجربه دانش‌بنیان در صنعت روشنایی', 'icon' => 'file-check' ),
		array( 'slug' => 'licenses-and-honors', 'title' => 'مجوزها و افتخارات رسمی', 'desc' => 'گواهینامه ISO 9001:2015، کد ستاد و GS1', 'icon' => 'award' ),
		array( 'slug' => 'refund_returns', 'title' => 'شرایط گارانتی ۲۴ ماهه', 'desc' => 'ضمانت تعویض کارخانه و ۵ سال خدمات پس از فروش', 'icon' => 'shield-check' ),
		array( 'slug' => 'faq', 'title' => 'سوالات متداول (FAQ)', 'desc' => 'پاسخ به پرسش‌های فنی، خرید آنلاین و نصب', 'icon' => 'circle-question-mark' ),
		array( 'slug' => 'recruitment', 'title' => 'فرصت‌های شغلی و استخدام', 'desc' => 'جذب نیروی متخصص در واحدهای فنی و فروش', 'icon' => 'building' ),
		array( 'slug' => 'contact', 'title' => 'تماس با کارخانه و دفتر مرکزی', 'desc' => 'مشاوره رایگان مهندسی و استعلام قیمت روز', 'icon' => 'phone-call' ),
);

$es_arrow_left = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>';
?>
<div data-desktop-mega-panel="corporate" class="absolute inset-x-4 top-full z-50 mx-auto max-w-[740px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
	<div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]">
		<div class="grid grid-cols-12">

			<div class="col-span-5 flex flex-col justify-between border-l border-slate-100 bg-gradient-to-b from-emerald-50/75 via-emerald-50/30 to-slate-50/60 p-5">
				<div>
					<div class="inline-flex items-center gap-2 rounded-xl border border-emerald-200/80 bg-white px-3 py-1 text-[10.5px] font-black text-emerald-800 shadow-2xs">
						<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
						<span>تاسیس ۱۳۸۰ · شرکت دانش‌بنیان</span>
					</div>
					<?php if ( '' !== $es_corp_img ) : ?>
						<img src="<?php echo esc_url( $es_corp_img ); ?>" alt="<?php echo esc_attr( $es_corp_title ); ?>" class="mt-3.5 w-full rounded-xl border border-emerald-100 object-cover">
					<?php endif; ?>
					<div class="mt-3.5 text-sm font-black text-slate-900"><?php echo esc_html( $es_corp_title ); ?></div>
					<p class="mt-1.5 text-[11.5px] leading-6 font-medium text-slate-600"><?php echo esc_html( $es_corp_text ); ?></p>
					<div class="mt-4 grid grid-cols-2 gap-2">
						<div class="rounded-xl border border-emerald-100 bg-white/90 px-2.5 py-2 text-center">
							<div class="text-xs font-black text-emerald-800 tabular-nums">+۸۵۰</div>
							<div class="text-[10px] font-bold text-slate-500">پروژه شهری</div>
						</div>
						<div class="rounded-xl border border-emerald-100 bg-white/90 px-2.5 py-2 text-center">
							<div class="text-xs font-black text-emerald-800 tabular-nums">۲۴ ماه</div>
							<div class="text-[10px] font-bold text-slate-500">گارانتی تعویض</div>
						</div>
					</div>
				</div>
				<div class="mt-4 border-t border-emerald-100/80 pt-3">
					<a href="<?php echo esc_url( Menus::page_url( 'about' ) ); ?>" class="inline-flex w-full items-center justify-between rounded-xl bg-emerald-700 px-3.5 py-2.5 text-xs font-extrabold text-white transition-colors hover:bg-emerald-800"><span>معرفی کامل کارخانه و خطوط تولید</span><?php echo $es_arrow_left; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
			</div>

			<div class="col-span-7 p-4">
				<div class="mb-2.5 flex items-center justify-between border-b border-slate-100 px-1 pb-2">
					<span class="text-[11px] font-black text-slate-400">بخش‌های سازمانی، مجوزها و پشتیبانی</span>
					<span class="font-mono text-[10px] font-bold text-emerald-700">ISO 9001:2015 · GS1</span>
				</div>
				<div class="grid grid-cols-2 gap-2">
					<?php foreach ( $es_corp_links as $es_link ) : ?>
						<a href="<?php echo esc_url( Menus::page_url( $es_link['slug'] ) ); ?>" class="group/corp flex items-start gap-2.5 rounded-2xl border p-2.5 transition-all border-slate-100 bg-slate-50/50 text-slate-800 hover:border-emerald-200 hover:bg-white hover:shadow-2xs">
							<span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 transition-colors group-hover/corp:bg-emerald-700 group-hover/corp:text-white"><?php echo $es_icons[ $es_link['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
							<div class="min-w-0 flex-1">
								<div class="truncate text-xs font-extrabold text-slate-900 group-hover/corp:text-emerald-800"><?php echo esc_html( $es_link['title'] ); ?></div>
								<div class="mt-0.5 line-clamp-1 text-[10px] font-medium text-slate-500"><?php echo esc_html( $es_link['desc'] ); ?></div>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

		</div>
	</div>
</div>
