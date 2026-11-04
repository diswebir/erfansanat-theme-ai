<?php
/**
 * Footer: shared contact CTA, info grid, social links, copyright, wp_footer.
 *
 * Social links carry a safe rel/target and empty URLs are skipped (AC-02,
 * SC-02). Copyright is editable from options (AC-03). wp_footer is called
 * (AC-01). DOM mirrors static-mirror/index.html (SC-01).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\FooterTab;

$es_copyright = FooterTab::copyright();
$es_social    = FooterTab::social_links();

$es_network_labels = array(
	'instagram' => 'اینستاگرام',
	'telegram'  => 'تلگرام',
	'linkedin'  => 'لینکدین',
	'whatsapp'  => 'واتس‌اپ',
	'aparat'    => 'آپارات',
);

$es_arrow_up = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up h-3.5 w-3.5" aria-hidden="true"><path d="m5 12 7-7 7 7"></path><path d="M12 19V5"></path></svg>';

// Shared contact CTA (P04-T016) renders before the footer when enabled.
get_template_part( 'template-parts/global/cta-contact' );
?>
		<footer class="relative overflow-hidden border-t border-emerald-100 bg-white">
			<div class="pointer-events-none absolute inset-0 grid-Floor opacity-50"></div>
			<div class="pointer-events-none absolute -top-40 right-1/3 h-80 w-80 rounded-full bg-emerald-400/10 blur-[130px]"></div>
			<div class="relative mx-auto max-w-[1300px] px-5 pt-20 pb-10 lg:px-8">

				<?php get_template_part( 'template-parts/global/footer-info' ); ?>

				<div class="flex flex-col items-center justify-between gap-4 border-t border-slate-200 pt-6 text-xs text-slate-500 sm:flex-row">
					<div><?php echo esc_html( $es_copyright ); ?></div>

					<?php if ( ! empty( $es_social ) ) : ?>
						<div class="flex items-center gap-2">
							<?php foreach ( $es_social as $es_social_link ) : ?>
								<?php $es_label = $es_network_labels[ $es_social_link['network'] ] ?? $es_social_link['network']; ?>
								<a href="<?php echo esc_url( $es_social_link['url'] ); ?>" target="<?php echo esc_attr( $es_social_link['target'] ); ?>" rel="<?php echo esc_attr( $es_social_link['rel'] ); ?>" aria-label="<?php echo esc_attr( $es_label ); ?>" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 font-bold text-slate-600 transition-colors hover:border-emerald-300 hover:text-emerald-700"><?php echo esc_html( $es_label ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<button type="button" data-scroll-top="true" class="flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-emerald-800 transition-colors hover:bg-emerald-100 cursor-pointer"><span>بازگشت به بالا</span><?php echo $es_arrow_up; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				</div>

			</div>
		</footer>

	</div><!-- .relative.z-10 -->
</div><!-- .min-h-screen -->
</div><!-- #root -->
<?php wp_footer(); ?>
</body>
</html>
