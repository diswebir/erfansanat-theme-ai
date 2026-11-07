<?php
/**
 * Quick facts under the gallery.
 *
 * Every value comes from the project itself; a missing value drops its card
 * instead of printing an empty box, which is what the mirror's hard-coded
 * "کارفرما / محل اجرا / مدت اجرا" trio turns into on a real site.
 *
 * @package ErfanSanat
 *
 * @var array<string, string> $erfansanat_facts Sorted label => value pairs.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_facts = isset( $erfansanat_facts ) && is_array( $erfansanat_facts ) ? $erfansanat_facts : array();
$erfansanat_facts = array_filter( $erfansanat_facts, static fn( $value ): bool => '' !== trim( (string) $value ) );

if ( array() === $erfansanat_facts ) {
	return;
}

$erfansanat_columns = 3 === count( $erfansanat_facts ) ? 'grid-cols-3' : 'grid-cols-2';
?>
<dl class="grid <?php echo esc_attr( $erfansanat_columns ); ?> gap-1.5 sm:gap-2">
	<?php foreach ( $erfansanat_facts as $erfansanat_label => $erfansanat_value ) : ?>
		<div class="rounded-xl border border-slate-100 bg-slate-50/80 px-1.5 py-2 text-center sm:px-2.5 sm:py-2.5">
			<dt class="truncate text-[9.5px] font-semibold text-slate-400 sm:text-[10px]"><?php echo esc_html( (string) $erfansanat_label ); ?></dt>
			<dd class="mt-0.5 truncate text-[11px] font-extrabold text-slate-800 sm:text-[11.5px]"><?php echo esc_html( (string) $erfansanat_value ); ?></dd>
		</div>
	<?php endforeach; ?>
</dl>
