<?php
/**
 * Category description + FAQ (P06-T018 / P06-T019). Renders the product_cat
 * term description (wpautop + kses) and its FAQ repeater as the reference
 * accordion, in stored order (AC-01). When the term has neither description nor
 * FAQ, no section is rendered (AC-03). The accordion uses the same data-faq-*
 * contract the immutable main.js drives.
 *
 * @package ErfanSanat
 * @var array $args { @type WP_Term $term }
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Admin\TermMetaboxes;

$es_term = isset( $args['term'] ) && $args['term'] instanceof WP_Term ? $args['term'] : null;
if ( ! $es_term ) {
	$es_queried = get_queried_object();
	$es_term    = ( $es_queried instanceof WP_Term ) ? $es_queried : null;
}
if ( ! $es_term ) {
	return;
}

$es_description = trim( (string) $es_term->description );
$es_faq         = TermMetaboxes::get_faq( $es_term->term_id );

if ( '' === $es_description && ! $es_faq ) {
	return; // AC-03: nothing to render.
}
?>
<section class="mx-auto max-w-4xl px-5 pb-14 lg:px-8" data-category-faq>
	<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs sm:p-8">
		<div class="mb-6 flex items-center gap-2.5">
			<span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
				<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
			</span>
			<div>
				<h2 class="text-lg font-extrabold text-slate-900"><?php echo esc_html( sprintf( 'راهنمای تخصصی خرید %s', $es_term->name ) ); ?></h2>
				<p class="text-xs text-slate-500"><?php esc_html_e( 'مشخصات فنی، نکات خرید و پاسخ به سوالات پرتکرار خریداران', 'erfansanat' ); ?></p>
			</div>
		</div>

		<?php if ( '' !== $es_description ) : ?>
			<div class="prose-es mb-6 max-w-none text-[13.5px] leading-8 text-slate-600" data-category-description>
				<?php echo wp_kses_post( wpautop( $es_description ) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $es_faq ) : ?>
			<div data-faq-list="true" class="divide-y divide-slate-100">
				<?php foreach ( $es_faq as $es_i => $es_row ) : ?>
					<?php $es_open = 0 === $es_i; ?>
					<div class="py-3.5 first:pt-0 last:pb-0">
						<button type="button" data-faq-toggle="<?php echo (int) $es_i; ?>" class="flex w-full items-center justify-between gap-4 py-2 text-right cursor-pointer" aria-expanded="<?php echo $es_open ? 'true' : 'false'; ?>">
							<span class="text-sm font-extrabold text-slate-800"><?php echo esc_html( $es_row['name_question'] ); ?></span>
							<span data-faq-icon="<?php echo (int) $es_i; ?>" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-all <?php echo $es_open ? 'bg-emerald-800 text-white rotate-45' : 'bg-emerald-100 text-emerald-800'; ?>">
								<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
							</span>
						</button>
						<div data-faq-body="<?php echo (int) $es_i; ?>" class="<?php echo $es_open ? 'block' : 'hidden'; ?> pt-2 pb-1">
							<p class="text-[13.5px] leading-7 text-slate-600"><?php echo wp_kses_post( $es_row['answer_question'] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
