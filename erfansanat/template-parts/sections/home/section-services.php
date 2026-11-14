<?php
/**
 * Services section (P05-T015). DOM parity with static-mirror (id=services,
 * data-services-section). Rows come from published `services` CPT entries
 * (AC-02); every initHomeServices hook is emitted so hover/click switches
 * the preview panel (AC-03 / SC-02): data-service-row/-title/-desc-text/
 * -img/-counter, data-service-indicator/-num/-heading/-row-icon/-desc/
 * -mobile-img, data-service-preview-img/-title/-desc/-counter/-icon.
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'services' );
$es_type = (string) ( $es_section['type'] ?? 'services' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'خدمات تخصصی نورپردازی' );
$es_subheading = (string) ( $es_fields['subheading'] ?? 'از طراحی سه‌بعدی تا تولید صنعتی و اجرای کامل در محل؛ راهکارهای جامع برای شهرداری‌ها و سازمان‌های عمرانی' );
$es_eyebrow    = (string) apply_filters( 'es_services_eyebrow', 'خدمات مهندسی و اجرایی' );

// Row/preview icons (filterable; cycled when services outnumber icons).
$es_icons = (array) apply_filters( 'es_services_icons', array(
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-landmark h-6 w-6 text-emerald-300" aria-hidden="true"><path d="M10 18v-7"></path><path d="M11.12 2.198a2 2 0 0 1 1.76.006l7.866 3.847c.476.233.31.949-.22.949H3.474c-.53 0-.695-.716-.22-.949z"></path><path d="M14 18v-7"></path><path d="M18 18v-7"></path><path d="M3 22h18"></path><path d="M6 18v-7"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-milestone h-6 w-6 text-emerald-300" aria-hidden="true"><path d="M12 13v8"></path><path d="M12 3v3"></path><path d="M4 6a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h13a2 2 0 0 0 1.152-.365l3.424-2.317a1 1 0 0 0 0-1.635l-3.424-2.318A2 2 0 0 0 17 6z"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-waypoints h-6 w-6 text-emerald-300" aria-hidden="true"><circle cx="12" cy="4.5" r="2.5"></circle><path d="m10.2 6.3-3.9 3.9"></path><circle cx="4.5" cy="12" r="2.5"></circle><path d="M7 12h10"></path><circle cx="19.5" cy="12" r="2.5"></circle><path d="m13.8 17.7 3.9-3.9"></path><circle cx="12" cy="19.5" r="2.5"></circle></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-6 w-6 text-emerald-300" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 h-6 w-6 text-emerald-300" aria-hidden="true"><path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-3a2 2 0 0 0-4 0v3"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path></svg>',
) );

$es_fallback_img = (string) apply_filters( 'es_services_fallback_image', ES_ASSETS_URI . 'images/light-sphere.jpg' );

$es_fa_digits = static function ( $value ) {
	return strtr( (string) $value, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
};

$es_query = new WP_Query( array(
	'post_type'      => 'services',
	'post_status'    => 'publish',
	'posts_per_page' => max( 1, min( 12, (int) apply_filters( 'es_services_section_count', 10 ) ) ),
	'no_found_rows'  => true,
	'orderby'        => 'menu_order date',
	'order'          => 'ASC',
) );

$es_rows = array();
while ( $es_query->have_posts() ) {
	$es_query->the_post();
	$es_pid  = get_the_ID();
	$es_desc = has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : wp_trim_words( wp_strip_all_tags( get_the_content() ), 18, '…' );
	$es_img  = get_the_post_thumbnail_url( $es_pid, 'large' );
	$es_rows[] = array(
		'title' => get_the_title(),
		'desc'  => $es_desc,
		'img'   => $es_img ? $es_img : $es_fallback_img,
		'icon'  => (string) $es_icons[ count( $es_rows ) % max( 1, count( $es_icons ) ) ],
	);
}
wp_reset_postdata();
$es_total = count( $es_rows );
?>
<section id="services" class="es-section es-section--services relative z-10 scroll-mt-20 py-12 sm:py-16 lg:py-20" style="scroll-margin-top:5rem" data-services-section="true" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
		<div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto">
			<div>
				<div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]">
					<span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( $es_eyebrow ); ?></span>
				</div>
			</div>
			<div>
				<h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]"><?php echo esc_html( $es_heading ); ?></h2>
			</div>
			<?php if ( '' !== trim( $es_subheading ) ) : ?>
				<div>
					<p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo esc_html( $es_subheading ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $es_total > 0 ) : ?>
			<div class="mt-8 sm:mt-10 grid items-stretch gap-6 lg:grid-cols-12 lg:gap-8">
				<div class="flex flex-col justify-between gap-2.5 lg:col-span-6">
					<?php foreach ( $es_rows as $es_i => $es_row ) :
						$es_active   = ( 0 === $es_i );
						$es_counter  = $es_fa_digits( $es_i + 1 ) . ' / ' . $es_fa_digits( $es_total );
						$es_num      = $es_fa_digits( str_pad( (string) ( $es_i + 1 ), 2, '0', STR_PAD_LEFT ) );
						$es_row_icon = str_replace( 'h-6 w-6 text-emerald-300', 'h-5 w-5 shrink-0 transition-colors ' . ( $es_active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-emerald-600' ), $es_row['icon'] );
						$es_row_icon = str_replace( '<svg ', '<svg data-service-row-icon="' . esc_attr( (string) $es_i ) . '" ', $es_row_icon );
						?>
						<button type="button" data-service-row="<?php echo esc_attr( (string) $es_i ); ?>" data-service-title="<?php echo esc_attr( $es_row['title'] ); ?>" data-service-desc-text="<?php echo esc_attr( $es_row['desc'] ); ?>" data-service-img="<?php echo esc_url( $es_row['img'] ); ?>" data-service-counter="<?php echo esc_attr( $es_counter ); ?>" class="group relative flex items-start gap-4 rounded-2xl border p-4 text-right transition-colors duration-200 sm:p-5 cursor-pointer <?php echo $es_active ? 'border-emerald-500/40 bg-white shadow-sm' : 'border-slate-200/80 bg-white/60 hover:border-emerald-200 hover:bg-white'; ?>">
							<span data-service-indicator="<?php echo esc_attr( (string) $es_i ); ?>" class="absolute right-0 top-1/2 w-1 -translate-y-1/2 rounded-l-full bg-emerald-600 transition-all duration-200 <?php echo $es_active ? 'h-10' : 'h-0'; ?>"></span>
							<span data-service-num="<?php echo esc_attr( (string) $es_i ); ?>" class="mt-0.5 text-xs font-black tabular-nums transition-colors <?php echo $es_active ? 'text-emerald-700' : 'text-slate-400'; ?>"><?php echo esc_html( $es_num ); ?></span>
							<div class="flex flex-1 flex-col gap-1">
								<span class="flex items-center justify-between gap-3">
									<span data-service-heading="<?php echo esc_attr( (string) $es_i ); ?>" class="text-sm sm:text-base font-extrabold transition-colors <?php echo $es_active ? 'text-emerald-950' : 'text-slate-800'; ?>"><?php echo esc_html( $es_row['title'] ); ?></span>
									<?php echo $es_row_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG. ?>
								</span>
								<p data-service-desc="<?php echo esc_attr( (string) $es_i ); ?>" class="text-xs sm:text-[13px] font-medium leading-6 text-slate-600"><?php echo esc_html( $es_row['desc'] ); ?></p>
								<div data-service-mobile-img="<?php echo esc_attr( (string) $es_i ); ?>" class="<?php echo $es_active ? 'block' : 'hidden'; ?> overflow-hidden lg:hidden">
									<img src="<?php echo esc_url( $es_row['img'] ); ?>" alt="<?php echo esc_attr( $es_row['title'] ); ?>" loading="lazy" class="mt-3 h-40 w-full rounded-xl border border-slate-200 object-cover"/>
								</div>
							</div>
						</button>
					<?php endforeach; ?>
				</div>
				<div class="hidden lg:col-span-6 lg:block">
					<div class="relative h-full min-h-[520px] overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 shadow-sm">
						<img data-service-preview-img="true" src="<?php echo esc_url( $es_rows[0]['img'] ); ?>" alt="<?php echo esc_attr( $es_rows[0]['title'] ); ?>" class="absolute inset-0 h-full w-full object-cover transition-opacity duration-300"/>
						<div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/25 to-transparent"></div>
						<span data-service-preview-counter="true" class="absolute left-6 top-6 rounded-xl border border-white/20 bg-slate-950/65 px-3.5 py-1.5 text-xs font-extrabold text-emerald-300 tabular-nums backdrop-blur-md"><?php echo esc_html( $es_fa_digits( 1 ) . ' / ' . $es_fa_digits( $es_total ) ); ?></span>
						<div class="absolute inset-x-0 bottom-0 flex items-center gap-4 p-7">
							<span class="grid h-13 w-13 shrink-0 place-items-center rounded-2xl border border-emerald-400/40 bg-emerald-950/80 backdrop-blur-md">
								<?php foreach ( $es_rows as $es_i => $es_row ) : ?>
									<span data-service-preview-icon="<?php echo esc_attr( (string) $es_i ); ?>" class="<?php echo 0 === $es_i ? 'block' : 'hidden'; ?>"><?php echo $es_row['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG. ?></span>
								<?php endforeach; ?>
							</span>
							<div class="flex flex-col gap-1">
								<span data-service-preview-title="true" class="text-xl font-black text-white"><?php echo esc_html( $es_rows[0]['title'] ); ?></span>
								<span data-service-preview-desc="true" class="text-xs sm:text-[13px] font-medium leading-6 text-slate-200"><?php echo esc_html( $es_rows[0]['desc'] ); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php else : ?>
			<p class="mt-8 text-center text-sm font-medium text-slate-500">خدمتی برای نمایش وجود ندارد.</p>
		<?php endif; ?>
	</div>
</section>
