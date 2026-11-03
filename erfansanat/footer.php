<?php
/**
 * Temporary compatibility footer.
 *
 * Phase 2 prints the configured contact block, links and badges so the settings
 * are observable. Phase 4 replaces the markup with the static-reference footer.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SiteParts;

?>
</main>
<footer class="erfansanat-footer border-t border-emerald-100 bg-white">
	<div class="mx-auto max-w-[1300px] px-5 py-14 lg:px-8">
		<div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
			<div class="erfansanat-footer__about space-y-4">
				<?php SiteParts::brand(); ?>
				<p class="text-[13px] leading-7 text-slate-600"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'footer_about', '' ) ); ?></p>
				<?php SiteParts::footer_badges(); ?>
				<?php SiteParts::social_links(); ?>
			</div>
			<div>
				<h2 class="mb-4 text-sm font-extrabold text-slate-900"><?php esc_html_e( 'دسترسی سریع', 'erfansanat' ); ?></h2>
				<?php SiteParts::link_list( 'footer_quick_links' ); ?>
			</div>
			<div>
				<h2 class="mb-4 text-sm font-extrabold text-slate-900"><?php esc_html_e( 'دسته‌بندی محصولات', 'erfansanat' ); ?></h2>
				<?php SiteParts::footer_categories(); ?>
			</div>
			<div>
				<h2 class="mb-4 text-sm font-extrabold text-slate-900"><?php esc_html_e( 'اطلاعات تماس کارخانه', 'erfansanat' ); ?></h2>
				<?php SiteParts::contact_details(); ?>
			</div>
		</div>
		<div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-slate-200 pt-6 text-xs text-slate-500 sm:flex-row">
			<p>© <?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'footer_copyright', '' ) ); ?></p>
			<button type="button" data-scroll-top="true" class="erfansanat-scroll-top rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-emerald-800 hover:bg-emerald-100">
				<?php esc_html_e( 'بازگشت به بالا', 'erfansanat' ); ?>
			</button>
		</div>
	</div>
</footer>
<?php
wp_footer();
$erfansanat_footer_scripts = SiteParts::custom_scripts( 'footer' );
if ( '' !== $erfansanat_footer_scripts ) {
	echo $erfansanat_footer_scripts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Stored only for holders of unfiltered_html and re-checked at print time.
}
?>
</body>
</html>
