<?php
/**
 * Related projects (P06-T012). Reads the `project-ids` legacy relation, keeps
 * the stored order (AC-02), renders only published `projects` posts (AC-01)
 * through the standard project card (AC-03). No valid projects → no section.
 *
 * @package ErfanSanat
 * @var WC_Product|ES_Shim_Product $product
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\LegacyMeta;

$es_product = isset( $product ) && is_object( $product ) ? $product : null;
if ( ! $es_product && isset( $args['product'] ) && is_object( $args['product'] ) ) {
	$es_product = $args['product'];
}
if ( ! $es_product || ! method_exists( $es_product, 'get_id' ) ) {
	return;
}
$es_id    = (int) $es_product->get_id();
$es_title = get_the_title( $es_id );

$es_projects = array();
foreach ( LegacyMeta::get( $es_id, 'project-ids' ) as $es_project_id ) {
	$es_project_id = (int) $es_project_id;
	if ( $es_project_id && 'projects' === get_post_type( $es_project_id ) && 'publish' === get_post_status( $es_project_id ) ) {
		$es_projects[] = $es_project_id;
	}
}

if ( ! $es_projects ) {
	return;
}

$es_archive = get_post_type_archive_link( 'projects' );
if ( ! $es_archive ) {
	$es_archive = home_url( '/projects/' );
}
?>
<section class="mx-auto max-w-6xl px-5 pb-14 lg:px-8" data-product-related-projects>
	<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
		<div>
			<div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700">
				<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
				<span><?php esc_html_e( 'نمونه‌کارهای واقعی اجراشده با این محصول', 'erfansanat' ); ?></span>
			</div>
			<h2 class="text-2xl font-extrabold text-slate-900"><?php echo esc_html( sprintf( 'پروژه‌های اجراشده با %s', $es_title ) ); ?></h2>
		</div>
		<a href="<?php echo esc_url( $es_archive ); ?>" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900">
			<span><?php esc_html_e( 'مشاهده همه پروژه‌ها', 'erfansanat' ); ?></span>
			<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
		</a>
	</div>
	<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
		<?php foreach ( $es_projects as $es_project_id ) : ?>
			<?php get_template_part( 'template-parts/cards/card', 'project', array( 'project_id' => $es_project_id ) ); ?>
		<?php endforeach; ?>
	</div>
</section>
