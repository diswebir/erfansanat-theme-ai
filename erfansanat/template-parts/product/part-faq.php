<?php
/**
 * Product FAQ accordion.
 *
 * Uses the same `data-faq-*` contract as every other FAQ on the site, so
 * `initProductDetailAndFaqs()` toggles it without extra JavaScript. The section
 * disappears entirely when the product has no FAQ rows.
 *
 * @package ErfanSanat
 *
 * @var array<int, array{question: string, answer: string}> $erfansanat_faq   Rows.
 * @var string                                             $erfansanat_title Product name.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_faq = isset( $erfansanat_faq ) && is_array( $erfansanat_faq ) ? $erfansanat_faq : array();

if ( array() === $erfansanat_faq ) {
	return;
}
?>
<section class="mx-auto max-w-4xl px-5 pb-14 lg:px-8">
	<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs sm:p-8">
		<div class="mb-6 flex items-center gap-2.5">
			<span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M9.5 9a2.5 2.5 0 1 1 3.4 2.3c-.6.3-.9.9-.9 1.6v.4"></path><path d="M12 17h.01"></path></svg>
			</span>
			<div>
				<h2 class="text-lg font-extrabold text-slate-900">
					<?php
					printf(
						/* translators: %s: product name. */
						esc_html__( 'سوالات متداول درباره %s', 'erfansanat' ),
						esc_html( isset( $erfansanat_title ) ? (string) $erfansanat_title : '' )
					);
					?>
				</h2>
				<p class="text-xs text-slate-500"><?php esc_html_e( 'پاسخ به پرتکرارترین سوالات خریداران و کارفرمایان', 'erfansanat' ); ?></p>
			</div>
		</div>

		<div data-faq-list="true" class="divide-y divide-slate-100">
			<?php foreach ( $erfansanat_faq as $erfansanat_index => $erfansanat_row ) : ?>
				<?php $erfansanat_open = 0 === (int) $erfansanat_index; ?>
				<div class="py-3.5 first:pt-0 last:pb-0">
					<button type="button" data-faq-toggle="<?php echo (int) $erfansanat_index; ?>"
						aria-expanded="<?php echo $erfansanat_open ? 'true' : 'false'; ?>"
						class="flex w-full items-center justify-between gap-4 py-2 text-right cursor-pointer">
						<span class="text-sm font-extrabold text-slate-800"><?php echo esc_html( $erfansanat_row['question'] ); ?></span>
						<span data-faq-icon="<?php echo (int) $erfansanat_index; ?>" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-all <?php echo $erfansanat_open ? 'bg-emerald-800 text-white rotate-45' : 'bg-emerald-50 text-emerald-700'; ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
						</span>
					</button>
					<div data-faq-body="<?php echo (int) $erfansanat_index; ?>" class="<?php echo $erfansanat_open ? 'block' : 'hidden'; ?> pt-2 pb-1">
						<p class="text-[13.5px] leading-7 text-slate-600"><?php echo esc_html( $erfansanat_row['answer'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
