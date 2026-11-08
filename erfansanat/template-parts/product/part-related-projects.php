<?php
/**
 * Projects this product was used in.
 *
 * Reads the real `project-ids` relation through ProductData, then renders the
 * shared project card so the rail and the home page can never drift apart. The
 * whole section is skipped when the relation is empty rather than printing an
 * empty heading.
 *
 * @package ErfanSanat
 *
 * @var array<int, \WP_Post> $erfansanat_related_projects Primed project posts.
 * @var string               $erfansanat_product_name   Product name, for the heading.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_projects = isset( $erfansanat_related_projects ) && is_array( $erfansanat_related_projects ) ? $erfansanat_related_projects : array();

if ( array() === $erfansanat_projects ) {
	return;
}

$erfansanat_archive = get_post_type_archive_link( 'projects' );
?>
<section class="mx-auto max-w-6xl px-5 pb-14 lg:px-8">
	<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
		<div>
			<div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="M4 21V9l8-5 8 5v12M9 21v-6h6v6"></path><path d="M4 21h16"></path></svg>
				<span><?php esc_html_e( 'نمونه‌کارهای واقعی اجراشده با این محصول', 'erfansanat' ); ?></span>
			</div>
			<h2 class="text-2xl font-extrabold text-slate-900">
				<?php
				printf(
					/* translators: %s: product name. */
					esc_html__( 'پروژه‌های اجراشده با %s', 'erfansanat' ),
					esc_html( isset( $erfansanat_product_name ) ? (string) $erfansanat_product_name : '' )
				);
				?>
			</h2>
		</div>
		<?php if ( is_string( $erfansanat_archive ) && '' !== $erfansanat_archive ) : ?>
			<a href="<?php echo esc_url( $erfansanat_archive ); ?>" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900">
				<span><?php esc_html_e( 'مشاهده همه پروژه‌ها', 'erfansanat' ); ?></span>
				<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
		<?php foreach ( $erfansanat_projects as $erfansanat_project ) : ?>
			<div class="flex">
				<?php
				include get_template_directory() . '/template-parts/cards/card-project.php';
				?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
