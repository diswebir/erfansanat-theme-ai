<?php
/**
 * Single project executive metadata (P07-T003). Renders the completion date
 * (finish-pro-time, shown verbatim with no forced calendar conversion — AC-01),
 * the city and project-type terms with their real taxonomy archive links
 * (AC-02) and the view count read from `number2-viewc` (AC-03).
 *
 * @package ErfanSanat
 *
 * @var array $args { project_id: int }
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\ViewCounter;

$es_pid = isset( $args['project_id'] ) ? (int) $args['project_id'] : (int) get_the_ID();
if ( ! $es_pid || 'projects' !== get_post_type( $es_pid ) ) {
	return;
}

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};

// AC-01: raw stored completion date, no forced conversion.
$es_finish = (string) get_post_meta( $es_pid, 'finish-pro-time', true );

$es_cities = get_the_terms( $es_pid, 'city' );
$es_types  = get_the_terms( $es_pid, 'project_type' );
$es_city   = ( $es_cities && ! is_wp_error( $es_cities ) ) ? $es_cities[0] : null;
$es_type   = ( $es_types && ! is_wp_error( $es_types ) ) ? $es_types[0] : null;

// AC-03: primary view store number2-viewc (legacy fallback handled by ViewCounter).
$es_views = ViewCounter::get( $es_pid );

// Optional legacy fields (filterable; rendered only when present).
$es_client   = (string) apply_filters( 'es_project_client', (string) get_post_meta( $es_pid, 'karfarma', true ), $es_pid );
$es_duration = (string) apply_filters( 'es_project_duration', (string) get_post_meta( $es_pid, 'modat-zaman', true ), $es_pid );
$es_warranty = (string) apply_filters( 'es_project_warranty', (string) get_post_meta( $es_pid, 'garanti', true ), $es_pid );

$es_rows = array();
if ( '' !== $es_client ) {
	$es_rows[] = array( 'label' => 'کارفرمای محترم', 'value' => esc_html( $es_client ) );
}
if ( $es_city ) {
	$es_city_link = get_term_link( $es_city );
	$es_rows[]    = array(
		'label' => 'محل اجرا (آرشیو شهر)',
		'value' => is_wp_error( $es_city_link ) ? esc_html( $es_city->name ) : '<a href="' . esc_url( $es_city_link ) . '" class="text-emerald-700 hover:underline" data-project-city-link>' . esc_html( $es_city->name ) . '</a>',
	);
}
if ( $es_type ) {
	$es_type_link = get_term_link( $es_type );
	$es_rows[]    = array(
		'label' => 'نوع سازه و کاربری (آرشیو)',
		'value' => is_wp_error( $es_type_link ) ? esc_html( $es_type->name ) : '<a href="' . esc_url( $es_type_link ) . '" class="text-emerald-700 hover:underline" data-project-type-link>' . esc_html( $es_type->name ) . '</a>',
	);
}
if ( '' !== $es_duration ) {
	$es_rows[] = array( 'label' => 'مدت زمان طراحی و اجرا', 'value' => esc_html( $es_duration ) );
}
if ( '' !== trim( $es_finish ) ) {
	$es_rows[] = array( 'label' => 'تاریخ تکمیل و بهره‌برداری', 'value' => '<span data-project-finish-time>' . esc_html( $es_finish ) . '</span>' );
}
if ( '' !== $es_warranty ) {
	$es_rows[] = array( 'label' => 'استاندارد حفاظتی و گارانتی', 'value' => esc_html( $es_warranty ) );
}
$es_rows[] = array( 'label' => 'بازدید پروژه', 'value' => '<span data-project-views>' . esc_html( $es_fa( number_format( $es_views, 0, '.', ',' ) ) ) . '</span> بازدید' );

/** Filter the executive metadata rows. */
$es_rows = apply_filters( 'es_project_meta_rows', $es_rows, $es_pid );
?>

<p class="mt-1 text-xs font-bold text-slate-500"><?php esc_html_e( 'مشخصات قراردادی، کارفرما، موقعیت جغرافیایی و استانداردهای فنی پروژه', 'erfansanat' ); ?></p>
<div class="mt-5 grid grid-cols-1 gap-1.5 sm:grid-cols-2" data-project-meta="true">
	<?php foreach ( $es_rows as $es_row ) : ?>
		<div class="flex flex-col gap-1 rounded-2xl border border-slate-100 bg-slate-50/60 px-4 py-3">
			<span class="text-[11px] font-bold text-slate-500"><?php echo esc_html( $es_row['label'] ); ?></span>
			<span class="text-[13px] font-extrabold text-slate-900"><?php echo wp_kses_post( $es_row['value'] ); ?></span>
		</div>
	<?php endforeach; ?>
</div>
