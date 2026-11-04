<?php
/**
 * Home section: clients.
 *
 * Structure only: Phase 5 lays the approved static design over these slots.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : ErfanSanat\Theme\Sections::default_fields( ErfanSanat\Theme\Sections::registry()['clients']['fields'] );
?>
<header class="erfansanat-section__head">
	<h2 class="erfansanat-section__title"><?php echo esc_html( (string) ( $erfansanat_fields['title'] ?? '' ) ); ?></h2>
	<p class="erfansanat-section__subtitle"><?php echo esc_html( (string) ( $erfansanat_fields['subtitle'] ?? '' ) ); ?></p>
</header>
<ul class="erfansanat-list erfansanat-list--items">
	<?php foreach ( (array) ( $erfansanat_fields['items'] ?? array() ) as $erfansanat_item ) : ?>
		<li class="erfansanat-list__item">
			<span><?php echo esc_html( (string) ( $erfansanat_item['title'] ?? '' ) ); ?></span>
		</li>
	<?php endforeach; ?>
</ul>
