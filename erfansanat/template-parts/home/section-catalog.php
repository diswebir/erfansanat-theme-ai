<?php
/**
 * Home section: catalog.
 *
 * Structure only: Phase 5 lays the approved static design over these slots.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : ErfanSanat\Theme\Sections::default_fields( ErfanSanat\Theme\Sections::registry()['catalog']['fields'] );
?>
<header class="erfansanat-section__head">
	<h2 class="erfansanat-section__title"><?php echo esc_html( (string) ( $erfansanat_fields['title'] ?? '' ) ); ?></h2>
	<p class="erfansanat-section__subtitle"><?php echo esc_html( (string) ( $erfansanat_fields['subtitle'] ?? '' ) ); ?></p>
</header>
<?php if ( (int) ( $erfansanat_fields['media_id'] ?? 0 ) > 0 ) : ?>
	<?php echo wp_get_attachment_image( (int) $erfansanat_fields['media_id'], 'medium', false, array( 'class' => 'erfansanat-section__media' ) ); ?>
<?php endif; ?>
<?php $erfansanat_link = is_array( $erfansanat_fields['link'] ?? null ) ? $erfansanat_fields['link'] : array(); ?>
<?php if ( ! empty( $erfansanat_link['url'] ) ) : ?>
	<a class="erfansanat-link" href="<?php echo esc_url( (string) $erfansanat_link['url'] ); ?>"><?php echo esc_html( (string) ( $erfansanat_link['title'] ?? '' ) ); ?></a>
<?php endif; ?>
