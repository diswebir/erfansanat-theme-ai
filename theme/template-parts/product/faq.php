<?php
/** Product FAQ accordion using the existing data-faq-* hooks. */

defined( 'ABSPATH' ) || exit;
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $args['product'] ?? null );
if ( ! $product_id ) { return; }
$rows = \ErfanSanat\Theme\Woo\ProductTemplate::rows( $product_id, 'faq' );
$items = array();
foreach ( $rows as $row ) {
	$question = $row['name_question'] ?? '';
	$answer = $row['answer_question'] ?? '';
	if ( ! is_scalar( $question ) || ! is_scalar( $answer ) || '' === trim( (string) $question ) || '' === trim( wp_strip_all_tags( (string) $answer ) ) ) { continue; }
	$items[] = array( 'question' => (string) $question, 'answer' => (string) $answer );
}
if ( ! $items ) { return; }
?>
<section class="mx-auto max-w-4xl px-5 pb-14 lg:px-8" aria-labelledby="es-product-faq-title"><div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs sm:p-8"><div class="mb-6"><h2 id="es-product-faq-title" class="text-lg font-extrabold text-slate-900">پرسش‌های متداول این محصول</h2><p class="mt-1 text-xs text-slate-500">پاسخ به پرسش‌های پرتکرار درباره محصول</p></div><div data-faq-list="true" class="divide-y divide-slate-100"><?php foreach ( $items as $index => $item ) : $open = 0 === $index; ?><div class="py-3.5 first:pt-0 last:pb-0"><button type="button" data-faq-toggle="<?php echo esc_attr( (string) $index ); ?>" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="es-product-faq-body-<?php echo esc_attr( (string) $index ); ?>" class="flex w-full items-center justify-between gap-4 py-2 text-right"><span class="text-sm font-extrabold text-slate-800"><?php echo esc_html( $item['question'] ); ?></span><span data-faq-icon="<?php echo esc_attr( (string) $index ); ?>" aria-hidden="true" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg <?php echo $open ? 'bg-emerald-800 text-white rotate-45' : 'bg-emerald-50 text-emerald-700'; ?>">+</span></button><div id="es-product-faq-body-<?php echo esc_attr( (string) $index ); ?>" data-faq-body="<?php echo esc_attr( (string) $index ); ?>" class="<?php echo $open ? 'block pt-2 pb-1' : 'hidden'; ?>"><div class="text-[13.5px] leading-7 text-slate-600"><?php echo wp_kses_post( $item['answer'] ); ?></div></div></div><?php endforeach; ?></div></div></section>
