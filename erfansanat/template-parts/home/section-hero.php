<?php
/**
 * Home section: hero.
 *
 * Structure only: Phase 5 lays the approved static design over these slots.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : ErfanSanat\Theme\Sections::default_fields( ErfanSanat\Theme\Sections::registry()['hero']['fields'] );
?>

<section class="erfansanat-hero__inner" aria-labelledby="erfansanat-hero-title">
	<h1 id="erfansanat-hero-title" class="erfansanat-hero__title"><?php echo esc_html( (string) ( $erfansanat_fields['title'] ?? '' ) ); ?></h1>
	<p class="erfansanat-hero__subtitle"><?php echo esc_html( (string) ( $erfansanat_fields['subtitle'] ?? '' ) ); ?></p>
	<?php if ( (int) ( $erfansanat_fields['media_id'] ?? 0 ) > 0 ) : ?>
		<?php echo wp_get_attachment_image( (int) $erfansanat_fields['media_id'], 'erfansanat-hero', false, array( 'class' => 'erfansanat-hero__media' ) ); ?>
	<?php endif; ?>
	<?php
	$erfansanat_cta = is_array( $erfansanat_fields['cta'] ?? null ) ? $erfansanat_fields['cta'] : array();
	?>
	<?php if ( ! empty( $erfansanat_cta['url'] ) ) : ?>
		<a class="erfansanat-button" href="<?php echo esc_url( (string) $erfansanat_cta['url'] ); ?>"><?php echo esc_html( (string) ( $erfansanat_cta['title'] ?? '' ) ); ?></a>
	<?php endif; ?>
	<?php if ( ! empty( $erfansanat_fields['chips'] ) && is_array( $erfansanat_fields['chips'] ) ) : ?>
		<ul class="erfansanat-hero__chips">
			<?php foreach ( $erfansanat_fields['chips'] as $erfansanat_chip ) : ?>
				<li class="erfansanat-chip">
					<?php echo ErfanSanat\Theme\Sections::icon_svg( (string) ( $erfansanat_chip['icon'] ?? 'lamp' ), 'erfansanat-chip__icon h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Whitelisted inline SVG. ?>
					<span><?php echo esc_html( (string) ( $erfansanat_chip['title'] ?? '' ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</section>
