<?php
/**
 * Project details: the three-tab panel above the related strips.
 *
 * The tabs, the panels and every `data-prod-tab-*` hook are the ones the
 * transferred `main.js` binds, so the behaviour is the reference's without a
 * second script. The content is the project's own description plus the
 * contract facts the metabox stores; nothing here is a sample value.
 *
 * @package ErfanSanat
 *
 * @var \WP_Post                                          $erfansanat_project Project post.
 * @var array{year: string, month: string, day: string, month_name: string, display: string} $erfansanat_date Completion date.
 * @var array<int, string>                                $erfansanat_cities  City names.
 * @var array<int, string>                                $erfansanat_types   Type names.
 * @var array<string, string>                             $erfansanat_facts   Label => value pairs.
 * @var array<int, string>                                $erfansanat_highlights Bullet points.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_project ) || ! $erfansanat_project instanceof \WP_Post ) {
	return;
}

$erfansanat_id         = (int) $erfansanat_project->ID;
$erfansanat_title      = (string) get_the_title( $erfansanat_id );
$erfansanat_date       = isset( $erfansanat_date ) && is_array( $erfansanat_date ) ? $erfansanat_date : array();
$erfansanat_cities     = isset( $erfansanat_cities ) && is_array( $erfansanat_cities ) ? $erfansanat_cities : array();
$erfansanat_types      = isset( $erfansanat_types ) && is_array( $erfansanat_types ) ? $erfansanat_types : array();
$erfansanat_facts      = isset( $erfansanat_facts ) && is_array( $erfansanat_facts ) ? $erfansanat_facts : array();
$erfansanat_highlights = isset( $erfansanat_highlights ) && is_array( $erfansanat_highlights ) ? $erfansanat_highlights : array();

// The description is the editor's own content, filtered as WordPress does.
$erfansanat_body = apply_filters( 'the_content', (string) $erfansanat_project->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core filter.
$erfansanat_body = trim( (string) $erfansanat_body );

// Facts that belong to the spec sheet rather than the summary card.
$erfansanat_specs = array();
if ( '' !== (string) ( $erfansanat_date['display'] ?? '' ) ) {
	$erfansanat_specs[ __( 'تاریخ تکمیل و بهره‌برداری', 'erfansanat' ) ] = (string) $erfansanat_date['display'];
}
if ( array() !== $erfansanat_cities ) {
	$erfansanat_specs[ __( 'محل اجرا (آرشیو شهر)', 'erfansanat' ) ] = implode( ' · ', $erfansanat_cities );
}
if ( array() !== $erfansanat_types ) {
	$erfansanat_specs[ __( 'نوع سازه و کاربری (آرشیو)', 'erfansanat' ) ] = implode( ' · ', $erfansanat_types );
}
foreach ( $erfansanat_facts as $erfansanat_fact_label => $erfansanat_fact_value ) {
	if ( '' !== trim( (string) $erfansanat_fact_value ) && ! isset( $erfansanat_specs[ $erfansanat_fact_label ] ) ) {
		$erfansanat_specs[ (string) $erfansanat_fact_label ] = (string) $erfansanat_fact_value;
	}
}

$erfansanat_tabs = array(
	'desc'    => __( 'معرفی و دستاوردهای فنی', 'erfansanat' ),
	'specs'   => __( 'شناسنامه کامل اجرایی', 'erfansanat' ),
	'process' => __( 'فرآیند مهندسی و اجرا', 'erfansanat' ),
);

$erfansanat_icons = array(
	'desc'    => '<path d="M12 3v3M5 6l2 2M19 6l-2 2M4 14h16l-2 6H6z"/>',
	'specs'   => '<path d="M9 3h6a2 2 0 0 1 2 2v16l-5-3-5 3V5a2 2 0 0 1 2-2z"/>',
	'process' => '<path d="M4 6h16M4 12h10M4 18h7"/>',
);

$erfansanat_steps = array(
	array(
		'title' => __( 'بازدید میدانی و طراحی سه‌بعدی', 'erfansanat' ),
		'text'  => __( 'اندازه‌گیری محل، بررسی سازه و نورپردازی موجود، و ارائهٔ طرح سه‌بعدی پیش از تولید.', 'erfansanat' ),
	),
	array(
		'title' => __( 'تولید سفارشی در کارخانه', 'erfansanat' ),
		'text'  => __( 'ساخت تجهیزات بر اساس طرح تأییدشده، با آزمون آب‌بندی و روشنایی پیش از ارسال.', 'erfansanat' ),
	),
	array(
		'title' => __( 'نصب سریع و راه‌اندازی هوشمند', 'erfansanat' ),
		'text'  => __( 'نصب توسط تیم اجرایی، تنظیم سناریوهای نوری و تحویل با آموزش بهره‌برداری.', 'erfansanat' ),
	),
);
?>
<section id="project-details" data-prod-tabs-container="true" class="mx-auto max-w-6xl scroll-mt-28 px-4 pb-14 sm:px-6 lg:px-8">
	<div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs">
		<div class="no-scrollbar flex gap-2 overflow-x-auto border-b border-slate-100 bg-slate-50/70 p-3 sm:flex-wrap sm:px-6" role="tablist">
			<?php foreach ( $erfansanat_tabs as $erfansanat_key => $erfansanat_label ) : ?>
				<?php $erfansanat_active = 'desc' === $erfansanat_key; ?>
				<button
					type="button"
					data-prod-tab-btn="<?php echo esc_attr( $erfansanat_key ); ?>"
					role="tab"
					aria-selected="<?php echo $erfansanat_active ? 'true' : 'false'; ?>"
					class="flex shrink-0 cursor-pointer items-center gap-2 whitespace-nowrap rounded-2xl px-3.5 py-2 text-xs font-extrabold transition-all sm:px-4 sm:py-2.5 sm:text-[13px] <?php echo $erfansanat_active ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900'; ?>"
				>
					<span data-prod-tab-icon="true" class="<?php echo $erfansanat_active ? 'text-white' : 'text-emerald-700'; ?>">
						<?php
						printf(
							'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">%s</svg>',
							$erfansanat_icons[ $erfansanat_key ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed whitelist above.
						);
						?>
					</span>
					<span><?php echo esc_html( $erfansanat_label ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="p-6 sm:p-8 lg:p-10">
			<div data-prod-tab-panel="desc" role="tabpanel" class="block">
				<div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10">
					<div class="space-y-4 lg:col-span-7">
						<p class="text-xs font-extrabold text-emerald-700"><?php esc_html_e( 'گزارش فنی و روایت اجرایی پروژه', 'erfansanat' ); ?></p>
						<h2 class="text-xl font-extrabold text-slate-900 sm:text-2xl">
							<?php
							printf(
								/* translators: %s: project title. */
								esc_html__( 'درباره %s', 'erfansanat' ),
								esc_html( $erfansanat_title )
							);
							?>
						</h2>
						<div class="space-y-4 text-[14.5px] leading-9 text-slate-600">
							<?php if ( '' !== $erfansanat_body ) : ?>
								<?php echo wp_kses_post( $erfansanat_body ); ?>
							<?php else : ?>
								<p><?php esc_html_e( 'توضیحات تکمیلی این پروژه هنوز ثبت نشده است. برای دریافت گزارش فنی و تصاویر اجرا با واحد فروش تماس بگیرید.', 'erfansanat' ); ?></p>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( array() !== $erfansanat_highlights ) : ?>
						<div class="lg:col-span-5">
							<div class="rounded-2xl border border-emerald-100 bg-emerald-50/35 p-5 sm:p-6">
								<div class="mb-4 flex items-center gap-2 text-sm font-extrabold text-slate-900">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5 text-emerald-600" aria-hidden="true"><path d="M12 3v3M5 6l2 2M19 6l-2 2M4 14h16l-2 6H6z"></path></svg>
									<span><?php esc_html_e( 'دستاوردها و شاخصه‌های فنی این پروژه', 'erfansanat' ); ?></span>
								</div>
								<ul class="space-y-2.5">
									<?php foreach ( $erfansanat_highlights as $erfansanat_point ) : ?>
										<li class="flex items-start gap-2.5 rounded-xl border border-emerald-100/80 bg-white px-3.5 py-3 text-[13px] font-semibold leading-6 text-slate-700 shadow-2xs">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-1 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
											<span><?php echo esc_html( $erfansanat_point ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div data-prod-tab-panel="specs" role="tabpanel" class="hidden">
				<div class="mb-5">
					<h2 class="text-xl font-extrabold text-slate-900">
						<?php
						printf(
							/* translators: %s: project title. */
							esc_html__( 'شناسنامه کامل اجرایی %s', 'erfansanat' ),
							esc_html( $erfansanat_title )
						);
						?>
					</h2>
					<p class="mt-1 text-xs font-medium text-slate-500"><?php esc_html_e( 'مشخصات قراردادی، کارفرما، موقعیت جغرافیایی و استانداردهای فنی پروژه', 'erfansanat' ); ?></p>
				</div>
				<?php if ( array() === $erfansanat_specs ) : ?>
					<p class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 px-5 py-6 text-sm text-slate-600"><?php esc_html_e( 'شناسنامهٔ اجرایی این پروژه تکمیل نشده است.', 'erfansanat' ); ?></p>
				<?php else : ?>
					<dl class="overflow-hidden rounded-2xl border border-slate-200">
						<?php foreach ( array_values( $erfansanat_specs ) as $erfansanat_index => $erfansanat_value ) : ?>
							<?php $erfansanat_label = array_keys( $erfansanat_specs )[ $erfansanat_index ]; ?>
							<div class="grid grid-cols-1 gap-1 border-b border-slate-100 px-5 py-4 last:border-b-0 sm:grid-cols-3 sm:gap-4 <?php echo 0 === $erfansanat_index % 2 ? 'bg-slate-50/70' : 'bg-white'; ?>">
								<dt class="flex items-center gap-2 text-xs font-bold text-slate-500">
									<span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
									<?php echo esc_html( (string) $erfansanat_label ); ?>
								</dt>
								<dd class="text-sm font-extrabold text-slate-900 sm:col-span-2"><?php echo esc_html( (string) $erfansanat_value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

			<div data-prod-tab-panel="process" role="tabpanel" class="hidden">
				<h2 class="text-xl font-extrabold text-slate-900 mb-1"><?php esc_html_e( 'فرآیند مهندسی، تولید و اجرای پروژه', 'erfansanat' ); ?></h2>
				<p class="mb-6 text-xs font-medium text-slate-500"><?php esc_html_e( 'همان مسیری که هر پروژهٔ نورپردازی عرفان صنعت از تماس اولیه تا تحویل طی می‌کند.', 'erfansanat' ); ?></p>
				<ol class="grid gap-4 sm:grid-cols-3">
					<?php foreach ( $erfansanat_steps as $erfansanat_step_index => $erfansanat_step ) : ?>
						<li class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
							<span class="mb-3 grid h-9 w-9 place-items-center rounded-xl bg-emerald-100 text-sm font-black text-emerald-800"><?php echo esc_html( SectionParts::fa_digits( (string) ( $erfansanat_step_index + 1 ) ) ); ?></span>
							<h3 class="text-sm font-extrabold text-slate-900"><?php echo esc_html( (string) $erfansanat_step['title'] ); ?></h3>
							<p class="mt-2 text-[12.5px] leading-7 text-slate-600"><?php echo esc_html( (string) $erfansanat_step['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</div>
	</div>
</section>
