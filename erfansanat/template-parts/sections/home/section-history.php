<?php
/**
 * History timeline section (P05-T012). DOM parity with the reference
 * interactive timeline. Items come from the section timeline repeater
 * (AC-02); the section carries data-history-scroll-track / data-history-total
 * (AC-01) and every data-home2-history-* hook the immutable main.js needs for
 * the wheel/click scroll behaviour (AC-03). The total counter equals the item
 * count (SC-02).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'history' );
$es_type = (string) ( $es_section['type'] ?? 'history' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'تاریخچه و دستاوردها' );
$es_subheading = (string) apply_filters( 'es_history_subheading', 'بیش از دو دهه مهندسی و بومی‌سازی؛ از تاسیس کارخانه در سال ۱۳۸۰ تا عبور از ۸۵۰ پروژه شهری و ورود به صنعت نیروگاه‌های خورشیدی با برند «سانیرما» در سال ۱۴۰۵' );
$es_eyebrow    = (string) apply_filters( 'es_history_eyebrow', 'مسیر رشد و تکامل صنعتی' );

$es_default_timeline = array(
	array( 'year' => '۱۳۸۰', 'title' => 'فاز تاسیس صنعتی', 'description' => 'تاسیس مجموعه و آغاز تولید صنعتی المان‌های نوری شهری با تمرکز بر کیفیت عایق‌بندی و طول عمر بالا.' ),
	array( 'year' => '۱۳۹۲', 'title' => 'استاندارد IP68', 'description' => 'دستیابی به استاندارد حفاظتی واقعی IP68 با فناوری تزریق مستقیم رزین در خطوط تولید.' ),
	array( 'year' => '۱۳۹۷', 'title' => 'هوشمندسازی NRF', 'description' => 'تولید درختان نوری هوشمند با فناوری NRF و گسترش سبد محصولات روشنایی شهری.' ),
	array( 'year' => '۱۴۰۱', 'title' => 'گواهینامه‌های رسمی', 'description' => 'اخذ گواهینامه‌های بین‌المللی مدیریت کیفیت و ثبت رسمی در سامانه ستاد و ایران‌کد.' ),
	array( 'year' => '۱۴۰۴', 'title' => '+۸۵۰ پروژه شهری', 'description' => 'عبور از ۸۵۰ پروژه شهری اجراشده در کلان‌شهرهای ایران و عراق.' ),
	array( 'year' => '۱۴۰۵', 'title' => 'برند سانیرما (Sunirma)', 'description' => 'ورود به صنعت نیروگاه‌های خورشیدی با برند تخصصی سانیرما.' ),
);
$es_timeline = ! empty( $es_fields['timeline'] ) && is_array( $es_fields['timeline'] )
	? array_values( $es_fields['timeline'] )
	: (array) apply_filters( 'es_history_default_timeline', $es_default_timeline );
$es_total    = count( $es_timeline );

$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_fa = static function ( $n ) use ( $es_fa_digits ) {
	return strtr( (string) $n, $es_fa_digits );
};

$es_panel_images = (array) apply_filters(
	'es_history_panel_images',
	array(
		ES_ASSETS_URI . 'images/hero.jpg',
		ES_ASSETS_URI . 'images/light-tunnel.jpg',
		ES_ASSETS_URI . 'images/light-tree.jpg',
	)
);
$es_first_year = $es_total > 0 ? (string) ( $es_timeline[0]['year'] ?? '' ) : '';
$es_progress   = $es_total > 0 ? (int) round( ( 1 / $es_total ) * 100 ) : 0;
?>
<section data-history-scroll-track="true" data-history-total="<?php echo (int) $es_total; ?>" aria-label="تاریخچه شرکت دانش‌بنیان عرفان صنعت اصفهان" class="es-section es-section--history relative mx-auto max-w-[1300px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto">
		<div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]">
			<span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
			<span><?php echo esc_html( $es_eyebrow ); ?></span>
		</div>
		<h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]"><?php echo esc_html( $es_heading ); ?></h2>
		<?php if ( '' !== trim( $es_subheading ) ) : ?>
			<p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo esc_html( $es_subheading ); ?></p>
		<?php endif; ?>
	</div>
	<?php if ( $es_total > 0 ) : ?>
	<div data-history-stage="true" class="mt-8">
		<div class="rounded-3xl border border-slate-200/85 bg-white p-3.5 shadow-2xs sm:p-5">
			<div class="mb-3.5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
				<div class="flex items-center gap-2 text-xs font-extrabold text-slate-700">
					<span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
					<span>مسیر تکامل صنعتی شرکت</span>
					<span class="text-slate-300">·</span>
					<span data-history-step-label="true" class="font-black text-emerald-700 tabular-nums">گام <?php echo esc_html( $es_fa( 1 ) ); ?> از <?php echo esc_html( $es_fa( $es_total ) ); ?> (سال <?php echo esc_html( $es_first_year ); ?>)</span>
				</div>
				<div class="flex items-center gap-3">
					<span class="hidden text-[11px] font-bold text-slate-400 sm:inline">با اسکرول یا کلیک بین سال‌ها حرکت کنید</span>
					<div class="h-2 w-32 overflow-hidden rounded-full bg-slate-100 sm:w-44">
						<div data-history-progress-bar="true" style="width:<?php echo (int) $es_progress; ?>%" class="h-full rounded-full bg-gradient-to-l from-emerald-500 to-emerald-700 transition-all duration-300"></div>
					</div>
				</div>
			</div>
			<div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6" role="tablist" aria-label="سال‌های شاخص تاریخچه شرکت">
				<?php foreach ( $es_timeline as $es_i => $es_item ) : ?>
					<?php
					$es_active = ( 0 === $es_i );
					$es_year   = (string) ( $es_item['year'] ?? '' );
					$es_title  = (string) ( $es_item['title'] ?? '' );
					?>
					<button type="button" role="tab" aria-selected="<?php echo $es_active ? 'true' : 'false'; ?>" data-home2-history-btn="<?php echo (int) $es_i; ?>" class="group flex flex-col items-start justify-between rounded-2xl border px-3.5 py-2.5 text-right transition-all cursor-pointer <?php echo $es_active ? 'border-emerald-700 bg-emerald-700 text-white shadow-sm' : 'border-slate-200/75 bg-[#f6f8f7] text-slate-700 hover:border-emerald-200 hover:bg-white'; ?>">
						<div class="flex w-full items-center justify-between gap-1.5">
							<span class="text-sm sm:text-base font-black tabular-nums"><?php echo esc_html( $es_year ); ?></span>
							<span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-black tabular-nums <?php echo $es_active ? 'bg-white/20 text-white' : 'bg-slate-200/80 text-slate-600'; ?>"><?php echo esc_html( $es_fa( $es_i + 1 ) ); ?></span>
						</div>
						<span class="mt-1 line-clamp-1 text-[10.5px] font-bold <?php echo $es_active ? 'text-emerald-100' : 'text-slate-500'; ?>"><?php echo esc_html( $es_title ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
		<?php foreach ( $es_timeline as $es_i => $es_item ) : ?>
			<?php
			$es_active = ( 0 === $es_i );
			$es_year   = (string) ( $es_item['year'] ?? '' );
			$es_title  = (string) ( $es_item['title'] ?? '' );
			$es_desc   = (string) ( $es_item['description'] ?? '' );
			$es_img0   = (string) ( $es_panel_images[0] ?? ES_ASSETS_URI . 'images/hero.jpg' );
			$es_img1   = (string) ( $es_panel_images[1] ?? $es_img0 );
			$es_img2   = (string) ( $es_panel_images[2] ?? $es_img0 );
			?>
			<div data-home2-history-panel="<?php echo (int) $es_i; ?>" class="<?php echo $es_active ? 'grid' : 'hidden'; ?> card-light mt-5 items-center gap-6 rounded-3xl p-6 sm:p-8 lg:grid-cols-12 lg:gap-10">
				<div class="text-right lg:col-span-6">
					<div class="flex flex-wrap items-center gap-2">
						<span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-800 tabular-nums"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-4 w-4 text-emerald-600" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg><span>سال <?php echo esc_html( $es_year ); ?> خورشیدی</span></span>
						<span class="rounded-xl border border-emerald-200/80 bg-white px-3 py-1 text-[11px] font-extrabold text-emerald-700"><?php echo esc_html( $es_title ); ?></span>
					</div>
					<h3 class="mt-3.5 text-xl font-black text-slate-900 sm:text-2xl"><?php echo esc_html( $es_title ); ?></h3>
					<?php if ( '' !== trim( $es_desc ) ) : ?>
						<p class="mt-3.5 text-right text-xs leading-7 font-medium text-slate-600 sm:text-[13.5px] sm:leading-7"><?php echo esc_html( $es_desc ); ?></p>
					<?php endif; ?>
					<div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
						<span class="text-xs font-extrabold text-slate-500 tabular-nums">مرحله <?php echo esc_html( $es_fa( $es_i + 1 ) ); ?> از <?php echo esc_html( $es_fa( $es_total ) ); ?> · <span class="text-emerald-700">با اسکرول به پایین سال بعد را ببینید</span></span>
						<div class="flex items-center gap-2">
							<button type="button" data-home2-history-prev="true" aria-label="سال قبل" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-extrabold text-slate-700 transition-colors hover:border-emerald-400 hover:text-emerald-700 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right h-3.5 w-3.5" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg><span>قبلی</span></button>
							<button type="button" data-home2-history-next="true" aria-label="سال بعد" class="inline-flex items-center gap-1 rounded-xl bg-emerald-700 px-3 py-1.5 text-xs font-extrabold text-white transition-colors hover:bg-emerald-800 cursor-pointer"><span>بعدی</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></button>
						</div>
					</div>
				</div>
				<div class="lg:col-span-6">
					<div class="grid grid-cols-12 gap-3.5">
						<div class="relative col-span-7 h-60 overflow-hidden rounded-2xl bg-slate-900 sm:h-72">
							<img src="<?php echo esc_url( $es_img0 ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" class="h-full w-full object-cover"/>
							<div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
							<div class="absolute right-3.5 bottom-3 text-[11px] font-bold text-white">دستاوردهای اجرایی سال <?php echo esc_html( $es_year ); ?></div>
						</div>
						<div class="col-span-5 flex flex-col gap-3.5">
							<div class="h-[113px] overflow-hidden rounded-2xl bg-slate-900 sm:h-[137px]"><img src="<?php echo esc_url( $es_img1 ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" class="h-full w-full object-cover"/></div>
							<div class="h-[113px] overflow-hidden rounded-2xl bg-slate-900 sm:h-[137px]"><img src="<?php echo esc_url( $es_img2 ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" class="h-full w-full object-cover"/></div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</section>
