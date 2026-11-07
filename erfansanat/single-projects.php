<?php
/**
 * Single project.
 *
 * Mirrors `projects/*.html`: hero with breadcrumb, a gallery with the reference's
 * lightbox hooks, a sticky summary card, the three-tab detail panel, the
 * products used and the related projects strip.
 *
 * The featured projects page (`/project/`) is a separate page template, so this
 * file only renders one project. Every value comes from the post and its
 * historical meta keys; the mirror's sample numbers are not reproduced.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\LegacyMeta;
use ErfanSanat\Theme\Data\ProjectData;
use ErfanSanat\Theme\Frontend\SectionParts;

get_header( 'projects' );

while ( have_posts() ) :
	the_post();

	$erfansanat_project    = get_post( (int) get_the_ID() );
	$erfansanat_project_id = $erfansanat_project instanceof \WP_Post ? (int) $erfansanat_project->ID : 0;
	$erfansanat_title      = (string) get_the_title( $erfansanat_project_id );
	$erfansanat_gallery    = ProjectData::gallery( $erfansanat_project_id );
	$erfansanat_date       = ProjectData::completion_parts( $erfansanat_project_id );
	$erfansanat_cities     = ProjectData::cities( $erfansanat_project_id );
	$erfansanat_types      = ProjectData::types( $erfansanat_project_id );
	$erfansanat_products   = ProjectData::products( $erfansanat_project_id );
	$erfansanat_related    = ProjectData::related( $erfansanat_project_id, 3 );
	$erfansanat_views      = ProjectData::views( $erfansanat_project_id );

	// The excerpt is the lead; a project without one falls back to the first
	// paragraph of its content, and finally to nothing at all rather than a
	// templated sentence that says nothing about this project.
	$erfansanat_lead = trim( wp_strip_all_tags( (string) get_the_excerpt() ) );

	// Quick facts: only what the project actually stores.
	$erfansanat_facts = array();
	if ( array() !== $erfansanat_cities ) {
		$erfansanat_facts[ __( 'محل اجرا', 'erfansanat' ) ] = (string) $erfansanat_cities[0];
	}
	if ( array() !== $erfansanat_types ) {
		$erfansanat_facts[ __( 'نوع پروژه', 'erfansanat' ) ] = (string) $erfansanat_types[0];
	}
	if ( '' !== $erfansanat_date['display'] ) {
		$erfansanat_facts[ __( 'تاریخ تکمیل', 'erfansanat' ) ] = $erfansanat_date['display'];
	}
	$erfansanat_client = trim( LegacyMeta::text( $erfansanat_project_id, 'project-client' ) );
	if ( '' !== $erfansanat_client ) {
		$erfansanat_facts = array( __( 'کارفرما', 'erfansanat' ) => $erfansanat_client ) + $erfansanat_facts;
	}
	$erfansanat_facts = array_slice( $erfansanat_facts, 0, 3, true );

	// Highlights are the project's own repeater rows when present.
	$erfansanat_highlights = array();
	foreach ( LegacyMeta::read_repeater( $erfansanat_project_id, 'project-highlights' ) as $erfansanat_row ) {
		$erfansanat_value = trim( (string) ( $erfansanat_row['add-new-feature'] ?? reset( $erfansanat_row ) ) );
		if ( '' !== $erfansanat_value ) {
			$erfansanat_highlights[] = $erfansanat_value;
		}
	}

	$erfansanat_crumb_trail  = array(
		array(
			'label' => __( 'صفحه اصلی', 'erfansanat' ),
			'url'   => home_url( '/' ),
		),
	);
	$erfansanat_projects_url = (string) get_post_type_archive_link( 'projects' );
	if ( '' !== $erfansanat_projects_url ) {
		$erfansanat_crumb_trail[] = array(
			'label' => __( 'پروژه‌ها', 'erfansanat' ),
			'url'   => $erfansanat_projects_url,
		);
	}
	if ( array() !== $erfansanat_types ) {
		$erfansanat_type_terms = wp_get_post_terms( $erfansanat_project_id, 'project_type' );
		if ( is_array( $erfansanat_type_terms ) && array() !== $erfansanat_type_terms ) {
			$erfansanat_crumb_trail[] = array(
				'label' => (string) $erfansanat_type_terms[0]->name,
				'url'   => \ErfanSanat\Theme\term_url( $erfansanat_type_terms[0] ),
			);
		}
	}
	$erfansanat_crumb_trail[] = array(
		'label' => $erfansanat_title,
		'url'   => '',
	);

	$erfansanat_crumbs      = $erfansanat_crumb_trail;
	$erfansanat_eyebrow     = implode( ' · ', array_filter( array( (string) ( $erfansanat_types[0] ?? '' ), (string) ( $erfansanat_cities[0] ?? '' ) ) ) );
	$erfansanat_badge       = (string) ( $erfansanat_types[0] ?? '' );
	$erfansanat_contact     = get_page_by_path( 'contact' );
	$erfansanat_contact_url = $erfansanat_contact instanceof \WP_Post ? (string) get_permalink( $erfansanat_contact ) : '';

	require get_template_directory() . '/template-parts/project/part-hero.php';
	?>

	<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
		<div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10">
			<div class="space-y-4 lg:col-span-7">
				<?php require get_template_directory() . '/template-parts/project/part-gallery.php'; ?>
				<?php if ( array() !== $erfansanat_facts ) : ?>
					<?php
					// The quick facts sit under the gallery on small screens only;
					// on desktop the sticky summary already carries them.
					$erfansanat_facts = $erfansanat_facts;
					require get_template_directory() . '/template-parts/project/part-facts.php';
					?>
				<?php endif; ?>
			</div>
			<div class="lg:col-span-5 lg:sticky lg:top-28">
				<?php require get_template_directory() . '/template-parts/project/part-summary.php'; ?>
			</div>
		</div>
	</section>

	<?php require get_template_directory() . '/template-parts/project/part-details.php'; ?>
	<?php require get_template_directory() . '/template-parts/project/part-products.php'; ?>
	<?php require get_template_directory() . '/template-parts/project/part-related.php'; ?>

	<?php
	$erfansanat_section_fields = array();
	?>
	<section id="contact" class="erfansanat-section erfansanat-section--contact-cta" data-section="contact-cta">
		<?php require get_template_directory() . '/template-parts/home/section-contact-cta.php'; ?>
	</section>
	<?php
endwhile;

get_footer( 'projects' );
