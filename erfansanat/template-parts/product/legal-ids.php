<?php
/**
 * Legal / industrial product identifiers (P06-T005). Renders the six official
 * identifiers (product_id, gs1_id, national_code, hs_id, isic_id, cpc_id) in
 * the specs tab panel, matching the reference card grid. Empty values add no
 * card (AC-02); if all six are empty the whole section is omitted (SC-02).
 * Values are read as strings so leading zeros are preserved (AC-03) and are
 * escaped + shown LTR in a mono font. Hooked into es_product_specs_panel.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : false;
if ( ! $es_product ) {
	return;
}
$es_id = $es_product->get_id();

// Six identifiers in reference order: label, meta key, value colour.
$es_defs = array(
	array( 'product_id', 'شناسه فنی محصول در کارخانه', false ),
	array( 'gs1_id', 'کد بین‌الملل GS1 (GTIN)', true ),
	array( 'national_code', 'کد ملی کالا (ایران‌کد)', false ),
	array( 'hs_id', 'کد تعرفه هماهنگ گمرکی (HS Code)', false ),
	array( 'isic_id', 'کد آیسیک صنعتی (ISIC)', false ),
	array( 'cpc_id', 'کد طبقه‌بندی مرکزی محصول (CPC)', false ),
);

$es_rows = array();
foreach ( $es_defs as $es_def ) {
	list( $es_key, $es_label, $es_emerald ) = $es_def;
	$es_value = (string) get_post_meta( $es_id, $es_key, true ); // string: keep leading zeros (AC-03).
	// The factory technical id falls back to the SKU when not set separately.
	if ( '' === trim( $es_value ) && 'product_id' === $es_key ) {
		$es_value = (string) $es_product->get_sku();
	}
	if ( '' === trim( $es_value ) ) {
		continue; // AC-02: empty value -> no card.
	}
	$es_rows[] = array( $es_label, $es_value, $es_emerald );
}

if ( array() === $es_rows ) {
	return; // SC-02: no identifiers -> no empty section.
}

$es_barcode = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-barcode h-5 w-5 text-emerald-700" aria-hidden="true"><path d="M3 5v14"></path><path d="M8 5v14"></path><path d="M12 5v14"></path><path d="M17 5v14"></path><path d="M21 5v14"></path></svg>';
?>
<div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-6">
	<div class="mb-4">
		<h3 class="flex items-center gap-2 text-base font-black text-emerald-950"><?php echo $es_barcode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>شناسه‌های رسمی کالا (ویژه سامانه ستاد، تدارکات دولتی و ایران‌کد)</span></h3>
		<p class="mt-1 text-xs font-medium text-slate-600">کدهای ثبت‌شده محصول جهت درج در اسناد مناقصات، فاکتور رسمی و سامانه تدارکات الکترونیکی دولت</p>
	</div>
	<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
		<?php foreach ( $es_rows as $es_row ) :
			list( $es_label, $es_value, $es_emerald ) = $es_row;
			$es_vcls = 'mt-1 block font-mono text-sm font-black tabular-nums ' . ( $es_emerald ? 'text-emerald-800' : 'text-slate-900' );
			?>
			<div class="rounded-xl border border-emerald-100 bg-white p-3.5">
				<span class="block text-[11px] font-bold text-slate-500"><?php echo esc_html( $es_label ); ?></span>
				<span dir="ltr" class="<?php echo esc_attr( $es_vcls ); ?>"><?php echo esc_html( $es_value ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</div>
