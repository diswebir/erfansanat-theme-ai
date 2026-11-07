<?php
/**
 * Site footer, cut from the approved static reference.
 *
 * Column proportions, headings and the scroll-top control follow
 * `static-mirror/`; the four content columns read from theme settings so the
 * footer follows the panel instead of being hard-coded.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SiteParts;

?>
	</main>

	<footer class="relative overflow-hidden border-t border-emerald-100 bg-white">
		<div class="pointer-events-none absolute inset-0 grid-Floor opacity-50"></div>
		<div class="pointer-events-none absolute -top-40 right-1/3 h-80 w-80 rounded-full bg-emerald-400/10 blur-[130px]"></div>

		<div class="relative mx-auto max-w-[1300px] px-5 pt-12 pb-8 lg:px-8">
			<div class="grid gap-10 pb-10 md:grid-cols-2 lg:grid-cols-12">

				<div class="space-y-4 lg:col-span-4">
					<div class="flex items-center gap-3">
						<?php
						$erfansanat_logo = SiteParts::logo_url();
						if ( '' !== $erfansanat_logo ) :
							?>
							<img src="<?php echo esc_url( $erfansanat_logo ); ?>" alt="<?php echo esc_attr( (string) \ErfanSanat\Theme\Settings::get( 'brand_name', '' ) ); ?>" class="h-11 w-auto" loading="lazy" decoding="async" />
						<?php else : ?>
							<div class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-200 bg-emerald-50">
								<span class="text-sm font-black text-emerald-700"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_mark', 'ESE' ) ); ?></span>
							</div>
						<?php endif; ?>
						<div>
							<div class="text-base font-extrabold text-slate-900"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_name', '' ) ); ?></div>
							<div class="text-xs text-emerald-700"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_tagline', '' ) ); ?></div>
						</div>
					</div>

					<p class="text-[13px] leading-7 text-slate-600">
						<?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'footer_about', '' ) ); ?>
					</p>

					<?php SiteParts::footer_badges( 'flex flex-wrap gap-2 pt-1 text-[11px] font-bold text-emerald-800 rounded-full bg-emerald-50 px-3 py-1' ); ?>
					<?php SiteParts::social_links(); ?>
				</div>

				<div class="lg:col-span-2">
					<h2 class="mb-4 text-sm font-extrabold text-slate-900"><?php esc_html_e( 'دسترسی سریع', 'erfansanat' ); ?></h2>
					<?php SiteParts::link_list( 'footer_quick_links', 'space-y-2.5 text-[13px] text-slate-600' ); ?>
				</div>

				<div class="lg:col-span-3">
					<h2 class="mb-4 text-sm font-extrabold text-slate-900"><?php esc_html_e( 'دسته‌بندی محصولات', 'erfansanat' ); ?></h2>
					<?php SiteParts::footer_categories( 'space-y-2.5 text-[13px] text-slate-600' ); ?>
				</div>

				<div class="lg:col-span-3">
					<h2 class="mb-4 text-sm font-extrabold text-slate-900"><?php esc_html_e( 'اطلاعات تماس کارخانه', 'erfansanat' ); ?></h2>
					<?php SiteParts::contact_details( 'space-y-2.5 text-[13px] text-slate-600' ); ?>
				</div>
			</div>

			<div class="flex flex-col items-center justify-between gap-4 border-t border-slate-200 pt-6 text-xs text-slate-500 sm:flex-row">
				<p>© <?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'footer_copyright', '' ) ); ?></p>
				<button type="button" data-scroll-top="true"
					class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-[11px] font-bold text-emerald-800 transition-colors hover:bg-emerald-100">
					<span><?php esc_html_e( 'بازگشت به بالا', 'erfansanat' ); ?></span>
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="m18 15-6-6-6 6"></path></svg>
				</button>
			</div>
		</div>
	</footer>
</div>

<?php
wp_footer();
$erfansanat_footer_scripts = SiteParts::custom_scripts( 'footer' );
if ( '' !== $erfansanat_footer_scripts ) {
	echo $erfansanat_footer_scripts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Stored only for holders of unfiltered_html and re-checked at print time.
}
?>
</body>
</html>
