<?php
/**
 * Products used in the project.
 *
 * The relation is the historical `mortabet_mahsole_2` id list, resolved through
 * ProjectData so a deleted or unpublished product disappears instead of leaving
 * a dead card.
 *
 * @package ErfanSanat
 *
 * @var array<int, string> $erfansanat_products Product id => title.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_products = isset( $erfansanat_products ) && is_array( $erfansanat_products ) ? $erfansanat_products : array();

if ( array() === $erfansanat_products ) {
	return;
}
?>
<section aria-labelledby="project-products-title" class="mx-auto max-w-6xl px-4 pb-14 sm:px-6 lg:px-8">
	<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
		<div>
			<p class="text-xs font-extrabold text-emerald-700"><?php esc_html_e( 'تجهیزات به‌کاررفته', 'erfansanat' ); ?></p>
			<h2 id="project-products-title" class="mt-1 text-xl font-black text-slate-900 sm:text-2xl"><?php esc_html_e( 'محصولات و تجهیزات استفاده‌شده', 'erfansanat' ); ?></h2>
		</div>
		<a href="<?php echo esc_url( (string) ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ) ); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-[12px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-50">
			<span><?php esc_html_e( 'مشاهده فروشگاه', 'erfansanat' ); ?></span>
			<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
		</a>
	</div>
	<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
		<?php foreach ( $erfansanat_products as $erfansanat_product_id => $erfansanat_product_title ) : ?>
			<?php
			$erfansanat_product = wc_get_product( (int) $erfansanat_product_id );
			if ( ! $erfansanat_product instanceof \WC_Product ) {
				continue;
			}
			$erfansanat_link = (string) get_permalink( (int) $erfansanat_product_id );
			?>
			<a href="<?php echo esc_url( $erfansanat_link ); ?>" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xs transition-all hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-lg">
				<div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100">
					<?php if ( has_post_thumbnail( (int) $erfansanat_product_id ) ) : ?>
						<?php
						echo get_the_post_thumbnail(
							(int) $erfansanat_product_id,
							'erfansanat-card',
							array(
								'class'   => 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-105',
								'loading' => 'lazy',
							)
						);
						?>
					<?php endif; ?>
				</div>
				<div class="flex flex-1 flex-col justify-between gap-2 p-4">
					<h3 class="line-clamp-2 text-[13px] font-extrabold leading-6 text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( (string) $erfansanat_product_title ); ?></h3>
					<span class="text-[12px] font-bold text-slate-600"><?php echo wp_kses_post( $erfansanat_product->get_price_html() ); ?></span>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>
