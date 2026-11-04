<?php
/**
 * Home section S10: the four quality cards.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_items  = (array) SectionParts::field( $erfansanat_fields, 'items', array() );

if ( array() === $erfansanat_items ) {
	return;
}
?>
<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
	<?php
	SectionParts::heading(
		array(
			'eyebrow'  => (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ),
			'title'    => (string) SectionParts::field( $erfansanat_fields, 'title', '' ),
			'subtitle' => (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ),
			'align'    => 'center',
		)
	);
	?>
	<div class="mt-8 sm:mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
		<?php foreach ( $erfansanat_items as $erfansanat_item ) : ?>
			<?php $erfansanat_item = is_array( $erfansanat_item ) ? $erfansanat_item : array(); ?>
			<div class="card-light flex h-full flex-col justify-between rounded-2xl p-5 sm:p-6">
				<div>
					<div class="flex items-center justify-between gap-2">
						<span class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-100 bg-emerald-50/70 text-emerald-700">
							<?php SectionParts::icon( (string) ( $erfansanat_item['icon'] ?? 'badge' ), 'h-5 w-5' ); ?>
						</span>
						<?php if ( '' !== (string) ( $erfansanat_item['code'] ?? '' ) ) : ?>
							<span class="font-mono text-xs font-extrabold text-emerald-700 tabular-nums"><?php echo esc_html( (string) $erfansanat_item['code'] ); ?></span>
						<?php endif; ?>
					</div>
					<h3 class="mt-4 text-base font-black text-slate-900"><?php echo esc_html( (string) ( $erfansanat_item['title'] ?? '' ) ); ?></h3>
					<p class="mt-2 text-xs sm:text-[13px] font-medium leading-6 text-slate-600"><?php echo esc_html( (string) ( $erfansanat_item['description'] ?? '' ) ); ?></p>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
