<?php
/**
 * Product FAQ accordion (P06-T013). Builds the reference accordion from the
 * `faq` repeater (`name_question` / `answer_question`). The first item is open;
 * main.js toggles the rest via data-faq-* hooks (AC-02). Answers allow safe
 * HTML (wp_kses_post); questions are escaped. Empty items / empty repeater add
 * no DOM (AC-03).
 *
 * @package ErfanSanat
 * @var WC_Product|ES_Shim_Product $product
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\LegacyMeta;

$es_product = isset( $product ) && is_object( $product ) ? $product : null;
if ( ! $es_product && isset( $args['product'] ) && is_object( $args['product'] ) ) {
	$es_product = $args['product'];
}
if ( ! $es_product || ! method_exists( $es_product, 'get_id' ) ) {
	return;
}
$es_id    = (int) $es_product->get_id();
$es_title = get_the_title( $es_id );

$es_items = array();
foreach ( LegacyMeta::get( $es_id, 'faq' ) as $es_item ) {
	if ( ! is_array( $es_item ) ) {
		continue;
	}
	$es_q = trim( (string) ( $es_item['name_question'] ?? '' ) );
	$es_a = trim( (string) ( $es_item['answer_question'] ?? '' ) );
	if ( '' !== $es_q && '' !== $es_a ) {
		$es_items[] = array( 'q' => $es_q, 'a' => $es_a );
	}
}

if ( ! $es_items ) {
	return; // AC-03.
}
?>
<section class="mx-auto max-w-4xl px-5 pb-14 lg:px-8" data-product-faq>
	<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs sm:p-8">
		<div class="mb-6 flex items-center gap-2.5">
			<span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
				<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
			</span>
			<div>
				<h2 class="text-lg font-extrabold text-slate-900"><?php echo esc_html( sprintf( 'سوالات متداول درباره %s', $es_title ) ); ?></h2>
				<p class="text-xs text-slate-500"><?php esc_html_e( 'پاسخ به پرتکرارترین سوالات خریداران و کارفرمایان', 'erfansanat' ); ?></p>
			</div>
		</div>
		<div data-faq-list="true" class="divide-y divide-slate-100">
			<?php foreach ( $es_items as $es_i => $es_item ) : ?>
				<?php $es_open = 0 === $es_i; ?>
				<div class="py-3.5 first:pt-0 last:pb-0">
					<button type="button" data-faq-toggle="<?php echo (int) $es_i; ?>" class="flex w-full items-center justify-between gap-4 py-2 text-right cursor-pointer" aria-expanded="<?php echo $es_open ? 'true' : 'false'; ?>">
						<span class="text-sm font-extrabold text-slate-800"><?php echo esc_html( $es_item['q'] ); ?></span>
						<span data-faq-icon="<?php echo (int) $es_i; ?>" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-all <?php echo $es_open ? 'bg-emerald-800 text-white rotate-45' : 'bg-emerald-100 text-emerald-800'; ?>">
							<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
						</span>
					</button>
					<div data-faq-body="<?php echo (int) $es_i; ?>" class="<?php echo $es_open ? 'block' : 'hidden'; ?> pt-2 pb-1">
						<p class="text-[13.5px] leading-7 text-slate-600"><?php echo wp_kses_post( $es_item['a'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
