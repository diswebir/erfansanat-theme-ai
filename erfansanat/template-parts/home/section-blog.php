<?php
/**
 * Home section S13: latest posts, newest first.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;
use ErfanSanat\Theme\Queries;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_link   = (array) SectionParts::field( $erfansanat_fields, 'link', array() );
$erfansanat_posts  = Queries::posts( 4 );

if ( array() === $erfansanat_posts ) {
	return;
}
?>
<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
	<div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
		<?php
		SectionParts::heading(
			array(
				'eyebrow'  => (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ),
				'title'    => (string) SectionParts::field( $erfansanat_fields, 'title', '' ),
				'subtitle' => (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ),
			)
		);
		?>
		<?php if ( ! empty( $erfansanat_link['url'] ) ) : ?>
			<a href="<?php echo esc_url( (string) $erfansanat_link['url'] ); ?>" class="group inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200/90 bg-white px-5 py-2.5 text-xs font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800">
				<span><?php echo esc_html( (string) ( $erfansanat_link['title'] ?? '' ) ); ?></span>
				<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="mt-8 sm:mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
		<?php
		foreach ( $erfansanat_posts as $erfansanat_post ) {
			include get_template_directory() . '/template-parts/cards/card-post.php';
		}
		?>
	</div>
</div>
