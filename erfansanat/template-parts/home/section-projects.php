<?php
/**
 * Home section S03: tabbed project gallery driven by real project types.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;
use ErfanSanat\Theme\Queries;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_link   = (array) SectionParts::field( $erfansanat_fields, 'link', array() );
$erfansanat_groups = Queries::project_groups( 5 );

if ( empty( $erfansanat_groups['all'] ) ) {
	return;
}

// Tab labels: the "all" bucket keeps the reference wording, the rest are real terms.
$erfansanat_labels = array( 'all' => __( 'همه سازه‌های نوری', 'erfansanat' ) );
$erfansanat_terms  = get_terms(
	array(
		'taxonomy'   => 'project_type',
		'hide_empty' => true,
		'number'     => 3,
	)
);
if ( is_array( $erfansanat_terms ) ) {
	foreach ( $erfansanat_terms as $erfansanat_term ) {
		$erfansanat_labels[ (string) $erfansanat_term->slug ] = (string) $erfansanat_term->name;
	}
}
?>
<div class="mx-auto max-w-[1300px] px-4 pt-2 pb-12 sm:px-6 sm:pb-16 lg:px-8">
	<div class="flex flex-col items-start justify-between gap-4 rounded-2xl border border-slate-200/85 bg-white p-5 sm:flex-row sm:items-center sm:p-6">
		<div>
			<?php if ( '' !== (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ) ) : ?>
				<div class="inline-flex items-center gap-2 text-xs font-extrabold text-emerald-700">
					<span aria-hidden="true" class="h-3 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ) ); ?></span>
				</div>
			<?php endif; ?>
			<h3 class="mt-1 text-lg font-black text-slate-900 sm:text-xl">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by title_markup().
				echo SectionParts::title_markup( (string) SectionParts::field( $erfansanat_fields, 'title', '' ) );
				?>
			</h3>
		</div>

		<div role="tablist" aria-label="<?php esc_attr_e( 'دسته‌بندی نمونه‌کارها', 'erfansanat' ); ?>" class="inline-flex flex-wrap items-center gap-1.5 rounded-2xl border border-slate-200/85 bg-[#f6f8f7] p-1.5">
			<?php foreach ( $erfansanat_labels as $erfansanat_slug => $erfansanat_label ) : ?>
				<?php if ( empty( $erfansanat_groups[ $erfansanat_slug ] ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<?php $erfansanat_active = 'all' === $erfansanat_slug; ?>
				<button type="button" role="tab"
					aria-selected="<?php echo $erfansanat_active ? 'true' : 'false'; ?>"
					data-home2-port-tab="<?php echo esc_attr( $erfansanat_slug ); ?>"
					class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap <?php echo $erfansanat_active ? 'bg-emerald-700 text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-emerald-800'; ?>">
					<span><?php echo esc_html( $erfansanat_label ); ?></span>
					<span class="rounded-md px-1.5 py-0.5 text-[10px] font-black tabular-nums <?php echo $erfansanat_active ? 'bg-white/20 text-white' : 'bg-slate-200/75 text-slate-600'; ?>"><?php echo esc_html( (string) count( (array) $erfansanat_groups[ $erfansanat_slug ] ) ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>
	</div>

	<?php foreach ( $erfansanat_groups as $erfansanat_slug => $erfansanat_projects ) : ?>
		<?php if ( array() === $erfansanat_projects ) : ?>
			<?php continue; ?>
		<?php endif; ?>
		<div data-home2-port-panel="<?php echo esc_attr( $erfansanat_slug ); ?>" class="<?php echo 'all' === $erfansanat_slug ? 'grid' : 'hidden'; ?> mt-5 grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
			<?php
			foreach ( $erfansanat_projects as $erfansanat_project ) {
				include get_template_directory() . '/template-parts/cards/card-project.php';
			}
			?>
		</div>
	<?php endforeach; ?>

	<?php if ( ! empty( $erfansanat_link['url'] ) ) : ?>
		<div class="mt-6 flex justify-center">
			<a href="<?php echo esc_url( (string) $erfansanat_link['url'] ); ?>" class="group inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800">
				<span><?php echo esc_html( (string) ( $erfansanat_link['title'] ?? '' ) ); ?></span>
				<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>
