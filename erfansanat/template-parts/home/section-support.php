<?php
/**
 * Home section: support.
 *
 * Structure only: Phase 5 lays the approved static design over these slots.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : ErfanSanat\Theme\Sections::default_fields( ErfanSanat\Theme\Sections::registry()['support']['fields'] );
?>
<div class="erfansanat-section__icon">
	<?php echo ErfanSanat\Theme\Sections::icon_svg( (string) ( $erfansanat_fields['icon'] ?? 'support' ), 'h-8 w-8' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Whitelisted inline SVG. ?>
</div>
<header class="erfansanat-section__head">
	<h2 class="erfansanat-section__title"><?php echo esc_html( (string) ( $erfansanat_fields['title'] ?? '' ) ); ?></h2>
	<p class="erfansanat-section__subtitle"><?php echo esc_html( (string) ( $erfansanat_fields['subtitle'] ?? '' ) ); ?></p>
</header>
