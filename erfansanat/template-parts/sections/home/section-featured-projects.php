<?php
/**
 * Featured projects section (P05-T009). DOM parity with the reference
 * portfolio gallery (header bar + data-home2-port-tab/panel hooks consumed by
 * the immutable main.js). Projects are rendered with the standard project
 * card (P05-T002) via WP_Query: only published projects (AC-01/SC-02),
 * no_found_rows since the gallery is not paginated (AC-02), count from the
 * section setting (SC-01), and a safe empty state (AC-03).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'featured-projects' );
$es_type = (string) ( $es_section['type'] ?? 'featured-projects' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'نمونه‌کارهای اجراشده در کلان‌شهرها' );
$es_subheading = (string) ( $es_fields['subheading'] ?? '' );
$es_count      = (int) ( $es_fields['count'] ?? 6 );
$es_count      = max( 1, min( 24, $es_count ) );
$es_eyebrow    = (string) apply_filters( 'es_featured_projects_eyebrow', 'آرشیو تفکیکی پروژه‌های اجرایی' );

$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );

// Tabs: "all" + the top project types (filterable). Each tab maps to a panel.
$es_total     = (int) ( wp_count_posts( 'projects' )->publish ?? 0 );
$es_tabs      = array(
	array( 'key' => 'all', 'label' => 'همه سازه‌های نوری', 'type' => '', 'count' => $es_total ),
);
$es_type_terms = get_terms(
	array(
		'taxonomy'   => 'project_type',
		'number'     => 2,
		'hide_empty' => false,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
if ( ! is_wp_error( $es_type_terms ) ) {
	foreach ( $es_type_terms as $es_term ) {
		$es_tabs[] = array(
			'key'   => $es_term->slug,
			'label' => $es_term->name,
			'type'  => $es_term->slug,
			'count' => (int) $es_term->count,
		);
	}
}
$es_tabs = (array) apply_filters( 'es_featured_projects_tabs', $es_tabs );
?>
<section aria-label="گالری نمونه‌کارها و پروژه‌های اجراشده عرفان صنعت" class="es-section es-section--featured-projects relative z-10 mx-auto max-w-[1300px] px-4 pt-2 pb-12 sm:px-6 sm:pb-16 lg:px-8" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="flex flex-col items-start justify-between gap-4 rounded-2xl border border-slate-200/85 bg-white p-5 sm:flex-row sm:items-center sm:p-6">
		<div>
			<div class="inline-flex items-center gap-2 text-xs font-extrabold text-emerald-700">
				<span aria-hidden="true" class="h-3 w-1 rounded-full bg-emerald-600"></span>
				<span><?php echo esc_html( $es_eyebrow ); ?></span>
			</div>
			<h3 class="mt-1 text-lg font-black text-slate-900 sm:text-xl"><?php echo esc_html( $es_heading ); ?></h3>
			<?php if ( '' !== trim( $es_subheading ) ) : ?>
				<p class="mt-1 text-sm leading-7 font-medium text-slate-600"><?php echo esc_html( $es_subheading ); ?></p>
			<?php endif; ?>
		</div>
		<div role="tablist" aria-label="دسته‌بندی نمونه‌کارها" class="inline-flex flex-wrap items-center gap-1.5 rounded-2xl border border-slate-200/85 bg-[#f6f8f7] p-1.5">
			<?php foreach ( $es_tabs as $es_i => $es_tab ) : ?>
				<?php $es_active = ( 0 === $es_i ); ?>
				<button type="button" role="tab" aria-selected="<?php echo $es_active ? 'true' : 'false'; ?>" data-home2-port-tab="<?php echo esc_attr( $es_tab['key'] ); ?>" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap <?php echo $es_active ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 hover:bg-white/80 hover:text-slate-900'; ?>">
					<span><?php echo esc_html( $es_tab['label'] ); ?></span>
					<span class="rounded-md px-1.5 py-0.5 text-[10px] font-black tabular-nums <?php echo $es_active ? 'bg-white/20 text-white' : 'bg-slate-200/75 text-slate-600'; ?>"><?php echo esc_html( strtr( (string) (int) $es_tab['count'], $es_fa_digits ) ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>
	</div>
	<?php foreach ( $es_tabs as $es_i => $es_tab ) : ?>
		<?php
		$es_query_args = array(
			'post_type'      => 'projects',
			'post_status'    => 'publish',
			'posts_per_page' => $es_count,
			'no_found_rows'  => true, // AC-02: the gallery is not paginated.
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		if ( '' !== $es_tab['type'] ) {
			$es_query_args['tax_query'] = array(
				array(
					'taxonomy' => 'project_type',
					'field'    => 'slug',
					'terms'    => $es_tab['type'],
				),
			);
		}
		$es_query = new WP_Query( $es_query_args );
		?>
		<div data-home2-port-panel="<?php echo esc_attr( $es_tab['key'] ); ?>" class="<?php echo 0 === $es_i ? 'grid' : 'hidden'; ?> mt-5 grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
			<?php if ( $es_query->have_posts() ) : ?>
				<?php
				while ( $es_query->have_posts() ) :
					$es_query->the_post();
					get_template_part( 'template-parts/cards/card-project', null, array( 'project_id' => get_the_ID() ) );
				endwhile;
				wp_reset_postdata();
				?>
			<?php else : ?>
				<p class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm font-semibold text-slate-500">پروژه‌ای برای نمایش در این دسته وجود ندارد.</p>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</section>
