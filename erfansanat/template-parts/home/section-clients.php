<?php
/**
 * Home section S12: the client / partner wall.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_items  = (array) SectionParts::field( $erfansanat_fields, 'items', array() );
$erfansanat_icons  = array( 'city', 'lamp', 'star', 'factory', 'badge', 'shield' );

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
	<div class="mt-8 sm:mt-10 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-6">
		<?php foreach ( $erfansanat_items as $erfansanat_index => $erfansanat_item ) : ?>
			<?php
			$erfansanat_item = is_array( $erfansanat_item ) ? $erfansanat_item : array();
			$erfansanat_icon = $erfansanat_icons[ (int) $erfansanat_index % count( $erfansanat_icons ) ];
			?>
			<div class="card-light flex flex-col items-start justify-between rounded-2xl p-4 text-right">
				<div class="flex w-full items-center justify-between">
					<span class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-100 bg-emerald-50/70 text-emerald-700">
						<?php SectionParts::icon( $erfansanat_icon, 'h-4 w-4' ); ?>
					</span>
					<span class="font-mono text-[11px] font-bold text-slate-400 tabular-nums"><?php echo esc_html( sprintf( '%02d', (int) $erfansanat_index + 1 ) ); ?></span>
				</div>
				<div class="mt-3">
					<div class="text-xs sm:text-[13px] font-extrabold text-slate-900"><?php echo esc_html( (string) ( $erfansanat_item['title'] ?? '' ) ); ?></div>
					<div class="mt-0.5 line-clamp-1 text-[10.5px] font-medium text-slate-500"><?php echo esc_html( (string) ( $erfansanat_item['description'] ?? '' ) ); ?></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
