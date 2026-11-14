<?php
/**
 * Technical specifications table (P06-T010). Builds rows from the product's
 * real attribute terms (the 18 mapped pa_* taxonomies), preserving the map's
 * order and Persian labels (AC-03). `pa_color` renders as colour swatches
 * (AC-02). Attributes with no terms add no row (AC-01 / SC-02).
 *
 * @package ErfanSanat
 * @var WC_Product|ES_Shim_Product $product
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\AttributeMap;

$es_product = isset( $product ) && is_object( $product ) ? $product : null;
if ( ! $es_product && isset( $args['product'] ) && is_object( $args['product'] ) ) {
	$es_product = $args['product'];
}
if ( ! $es_product || ! method_exists( $es_product, 'get_id' ) ) {
	return;
}
$es_id    = (int) $es_product->get_id();
$es_title = get_the_title( $es_id );

$es_color_map = apply_filters(
	'es_product_color_swatch_map',
	array(
		'فول‌کالر (هفت رنگ خودکار)' => 'bg-gradient-to-r from-red-500 via-emerald-500 to-blue-500',
		'فول کالر'                  => 'bg-gradient-to-r from-red-500 via-emerald-500 to-blue-500',
		'سبز'                       => 'bg-emerald-500',
		'قرمز'                      => 'bg-red-500',
		'سفید'                      => 'bg-white border border-slate-200',
		'آبی'                       => 'bg-blue-500',
		'زرد'                       => 'bg-amber-400',
		'بنفش'                      => 'bg-purple-500',
		'نارنجی'                    => 'bg-orange-500',
		'صورتی'                     => 'bg-pink-500',
		'مشکی'                      => 'bg-slate-900',
		'آفتابی'                    => 'bg-amber-300',
		'مهتابی'                    => 'bg-slate-100 border border-slate-200',
	),
	$es_id
);

$es_rows = array();
foreach ( AttributeMap::all() as $es_tax => $es_def ) {
	if ( ! taxonomy_exists( $es_tax ) ) {
		continue;
	}
	$es_terms = get_the_terms( $es_id, $es_tax );
	if ( ! $es_terms || is_wp_error( $es_terms ) ) {
		continue; // SC-02: empty attribute → no row.
	}
	$es_rows[] = array(
		'label' => $es_def['label'],
		'type'  => $es_def['type'],
		'terms' => $es_terms,
	);
}

if ( ! $es_rows ) {
	return;
}
?>
<div data-product-specs-table>
	<div class="mb-5 flex flex-wrap items-center justify-between gap-2">
		<div>
			<h2 class="text-xl font-extrabold text-slate-900"><?php echo esc_html( sprintf( 'جدول کامل مشخصات فنی و مهندسی %s', $es_title ) ); ?></h2>
			<p class="mt-1 text-xs font-medium text-slate-500"><?php esc_html_e( 'تولید و تست‌شده مطابق استانداردهای کنترل کیفیت کارخانه عرفان صنعت اصفهان', 'erfansanat' ); ?></p>
		</div>
	</div>
	<div class="overflow-hidden rounded-2xl border border-slate-200">
		<?php foreach ( $es_rows as $es_i => $es_row ) : ?>
			<div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 <?php echo 0 === $es_i % 2 ? 'bg-slate-50/70' : 'bg-white'; ?> border-b border-slate-100 last:border-b-0">
				<div class="text-xs font-bold text-slate-500"><?php echo esc_html( $es_row['label'] ); ?></div>
				<div class="text-sm font-extrabold text-slate-900 sm:col-span-2">
					<?php if ( 'color' === $es_row['type'] ) : ?>
						<span class="flex flex-wrap items-center gap-2">
							<?php foreach ( $es_row['terms'] as $es_term ) : ?>
								<span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-bold text-slate-700">
									<span class="h-3.5 w-3.5 rounded-full <?php echo esc_attr( $es_color_map[ $es_term->name ] ?? 'bg-slate-300' ); ?>" aria-hidden="true"></span>
									<?php echo esc_html( $es_term->name ); ?>
								</span>
							<?php endforeach; ?>
						</span>
					<?php else : ?>
						<?php echo esc_html( implode( '، ', wp_list_pluck( $es_row['terms'], 'name' ) ) ); ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
