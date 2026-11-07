<?php
/**
 * Other projects of the same type, using the shared project card.
 *
 * @package ErfanSanat
 *
 * @var array<int, \WP_Post> $erfansanat_related Related project posts.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_related = isset( $erfansanat_related ) && is_array( $erfansanat_related ) ? $erfansanat_related : array();

if ( array() === $erfansanat_related ) {
	return;
}

$erfansanat_projects_url = (string) get_post_type_archive_link( 'projects' );
?>
<section aria-labelledby="project-related-title" class="border-t border-slate-200/80 bg-white py-14">
	<div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
		<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
			<div>
				<p class="text-xs font-extrabold text-emerald-700"><?php esc_html_e( 'نمونه‌های مشابه', 'erfansanat' ); ?></p>
				<h2 id="project-related-title" class="mt-1 text-xl font-black text-slate-900 sm:text-2xl"><?php esc_html_e( 'سایر پروژه‌های شاخص عرفان صنعت', 'erfansanat' ); ?></h2>
			</div>
			<?php if ( '' !== $erfansanat_projects_url ) : ?>
				<a href="<?php echo esc_url( $erfansanat_projects_url ); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-[12px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-50">
					<span><?php esc_html_e( 'همه پروژه‌ها', 'erfansanat' ); ?></span>
					<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $erfansanat_related as $erfansanat_project_item ) : ?>
				<?php
				if ( ! $erfansanat_project_item instanceof \WP_Post ) {
					continue;
				}
				$erfansanat_project = $erfansanat_project_item;
				include get_template_directory() . '/template-parts/cards/card-project.php';
				?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
