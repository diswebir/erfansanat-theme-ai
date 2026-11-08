<?php
/**
 * Product category FAQ, below the grid.
 *
 * Same `data-faq-*` contract as every other accordion on the site, so the
 * existing script drives it. The term's rows come from the historical `faq`
 * term meta, so the migration needs no new field.
 *
 * @package ErfanSanat
 *
 * @var array<int, array{question: string, answer: string}> $erfansanat_faq_rows  Rows.
 * @var string                                             $erfansanat_faq_title Heading.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_faq_rows  = isset( $erfansanat_faq_rows ) && is_array( $erfansanat_faq_rows ) ? $erfansanat_faq_rows : array();
$erfansanat_faq_title = isset( $erfansanat_faq_title ) ? (string) $erfansanat_faq_title : '';

if ( array() === $erfansanat_faq_rows ) {
	return;
}

$erfansanat_faq_key = 'term';
?>
<div class="mt-10 border-t border-slate-100 pt-8" data-faq-list="<?php echo esc_attr( $erfansanat_faq_key ); ?>">
	<div class="mb-5 flex items-center gap-2.5">
		<span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M9.5 9a2.5 2.5 0 1 1 3.4 2.3c-.6.3-.9.9-.9 1.6v.4"></path><path d="M12 17h.01"></path></svg>
		</span>
		<h3 class="text-lg font-black text-slate-950"><?php echo esc_html( $erfansanat_faq_title ); ?></h3>
	</div>

	<div class="grid gap-3 md:grid-cols-2">
		<?php foreach ( $erfansanat_faq_rows as $erfansanat_index => $erfansanat_row ) : ?>
			<?php $erfansanat_open = 0 === $erfansanat_index; ?>
			<div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-slate-50/40">
				<button
					type="button"
					data-faq-toggle="<?php echo esc_attr( (string) $erfansanat_index ); ?>"
					aria-expanded="<?php echo $erfansanat_open ? 'true' : 'false'; ?>"
					class="flex w-full cursor-pointer items-center justify-between gap-3 px-5 py-4 text-right"
				>
					<span class="text-[13.5px] font-extrabold leading-6 text-slate-900"><?php echo esc_html( (string) $erfansanat_row['question'] ); ?></span>
					<span data-faq-icon="<?php echo esc_attr( (string) $erfansanat_index ); ?>" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-transform <?php echo $erfansanat_open ? 'rotate-45 bg-emerald-800 text-white' : 'bg-emerald-100 text-emerald-800'; ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
					</span>
				</button>
				<div data-faq-body="<?php echo esc_attr( (string) $erfansanat_index ); ?>" class="<?php echo $erfansanat_open ? 'block' : 'hidden'; ?> border-t border-slate-200/60 bg-white px-5 py-4 text-[13px] leading-7 text-slate-600">
					<?php echo esc_html( (string) $erfansanat_row['answer'] ); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
