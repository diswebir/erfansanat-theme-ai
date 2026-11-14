<?php
/**
 * Arya landing technical specs (P07-T028). Editable label/value rows in the
 * shared striped-table card vocabulary (product specs-table style).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'specs' );

$es_heading = (string) ( $es_fields['heading'] ?? 'مشخصات فنی آریا' );
$es_rows    = ! empty( $es_fields['rows'] ) && is_array( $es_fields['rows'] ) ? $es_fields['rows'] : array(
	array( 'label' => 'ولتاژ تغذیه', 'value' => '۲۲۰ ولت AC — ۵۰ هرتز' ),
	array( 'label' => 'توان موتور', 'value' => '۲۵۰ وات — گیربکس صنعتی' ),
	array( 'label' => 'حداکثر وزن درب', 'value' => 'تا ۸۰۰ کیلوگرم' ),
	array( 'label' => 'سرعت باز شدن', 'value' => '۱۲ تا ۱۸ ثانیه (قابل تنظیم)' ),
	array( 'label' => 'روش‌های کنترل', 'value' => 'ریموت ۴۳۳ مگاهرتز، اپلیکیشن موبایل، کلید فشاری' ),
	array( 'label' => 'ایمنی', 'value' => 'سنسور تشخیص مانع + خلاص‌کن دستی هنگام قطعی برق' ),
	array( 'label' => 'دمای کاری', 'value' => 'منفی ۲۰ تا مثبت ۵۵ درجه سانتی‌گراد' ),
	array( 'label' => 'گارانتی', 'value' => '۲۴ ماه گارانتی تعویض + ۵ سال خدمات پس از فروش' ),
);
?>
<section id="<?php echo esc_attr( $es_id ); ?>" class="border-y border-emerald-100 bg-white py-14 scroll-mt-28 lg:py-18" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="specs">
	<div class="mx-auto max-w-7xl px-5 lg:px-8">
		<h2 class="mb-8 flex items-center gap-3 text-xl font-extrabold text-slate-900 lg:text-2xl"><span class="h-7 w-1.5 rounded-full bg-gradient-to-b from-emerald-500 to-emerald-700"></span><?php echo esc_html( $es_heading ); ?></h2>
		<div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm">
			<table class="w-full text-right text-[13.5px]">
				<tbody>
					<?php foreach ( $es_rows as $es_i => $es_row ) : ?>
						<?php
						$es_label = (string) ( $es_row['label'] ?? '' );
						$es_value = (string) ( $es_row['value'] ?? '' );
						if ( '' === trim( $es_label ) ) { continue; }
						?>
						<tr class="<?php echo 0 === $es_i % 2 ? 'bg-slate-50/60' : 'bg-white'; ?> border-b border-slate-100 last:border-b-0">
							<th scope="row" class="w-40 whitespace-nowrap px-5 py-4 align-top font-extrabold text-slate-800 sm:w-56"><?php echo esc_html( $es_label ); ?></th>
							<td class="px-5 py-4 font-medium leading-7 text-slate-600"><?php echo esc_html( $es_value ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
