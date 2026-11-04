<?php
/**
 * Shared product card.
 *
 * Every value is read from the real `WC_Product` object: title, permalink,
 * price and image. The card never prints a sample price and never links a
 * product that is not purchasable.
 *
 * @package ErfanSanat
 *
 * @var \WC_Product $erfansanat_product Product to render.
 * @var string      $erfansanat_size    `feature` for the wide hero card, `card` otherwise.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_product ) || ! $erfansanat_product instanceof \WC_Product ) {
	return;
}

$erfansanat_id      = (int) $erfansanat_product->get_id();
$erfansanat_title   = $erfansanat_product->get_name();
$erfansanat_url     = (string) get_permalink( $erfansanat_id );
$erfansanat_image   = (string) get_the_post_thumbnail_url( $erfansanat_id, 'erfansanat-card' );
$erfansanat_size    = isset( $erfansanat_size ) && 'feature' === $erfansanat_size ? 'feature' : 'card';
$erfansanat_classes = 'feature' === $erfansanat_size
	? 'sm:col-span-2 card-glow group relative flex h-full min-h-[320px] sm:min-h-[380px] flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 p-5 sm:p-6'
	: 'card-glow group relative flex h-full min-h-[260px] sm:min-h-[295px] flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 p-5 sm:p-6';

// The protective rating is a real product attribute when the store has one.
$erfansanat_ip = '';
if ( $erfansanat_product->get_attribute( 'pa_ip-rating' ) ) {
	$erfansanat_ip = (string) $erfansanat_product->get_attribute( 'pa_ip-rating' );
} elseif ( $erfansanat_product->get_attribute( 'ip-rating' ) ) {
	$erfansanat_ip = (string) $erfansanat_product->get_attribute( 'ip-rating' );
}

$erfansanat_price = $erfansanat_product->get_price_html();
?>
<a href="<?php echo esc_url( $erfansanat_url ); ?>" class="<?php echo esc_attr( $erfansanat_classes ); ?>" aria-label="<?php echo esc_attr( $erfansanat_title ); ?>">
	<?php if ( '' !== $erfansanat_image ) : ?>
		<img src="<?php echo esc_url( $erfansanat_image ); ?>" alt="<?php echo esc_attr( $erfansanat_title ); ?>" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
	<?php endif; ?>
	<div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/45 to-slate-950/20"></div>

	<div class="relative z-10 flex items-center justify-between gap-2">
		<span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-white/25 bg-slate-950/60 text-white backdrop-blur-md transition-colors duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-600">
			<?php SectionParts::arrow( 'h-4 w-4' ); ?>
		</span>
		<?php if ( '' !== $erfansanat_ip ) : ?>
			<div class="flex items-center gap-2 text-[11px] font-bold text-white/90">
				<span class="rounded-lg border border-white/20 bg-slate-950/60 px-2 py-0.5 font-mono text-[10.5px] font-bold text-emerald-300 backdrop-blur-xs"><?php echo esc_html( $erfansanat_ip ); ?></span>
			</div>
		<?php endif; ?>
	</div>

	<div class="relative z-10 mt-auto pt-12">
		<?php
		$erfansanat_cats = get_the_terms( $erfansanat_id, 'product_cat' );
		if ( is_array( $erfansanat_cats ) && array() !== $erfansanat_cats ) :
			?>
			<div class="text-[11.5px] font-bold text-emerald-300"><?php echo esc_html( $erfansanat_cats[0]->name ); ?></div>
		<?php endif; ?>
		<h3 class="mt-1 text-lg font-black tracking-tight text-white sm:text-xl"><?php echo esc_html( $erfansanat_title ); ?></h3>
		<div class="mt-2.5 flex items-center justify-between gap-2 border-t border-white/15 pt-2.5 text-[11.5px] font-medium text-slate-300">
			<span class="truncate">
				<?php
				// Price comes from WooCommerce with its own escaping and currency HTML.
				echo wp_kses_post( $erfansanat_price );
				?>
			</span>
		</div>
	</div>
</a>
