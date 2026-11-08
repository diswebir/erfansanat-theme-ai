<?php
/**
 * Closing call to action under the projects archives.
 *
 * Reuses the home page's contact section so the phone, the e-mail and the
 * buttons stay in one place, and keeps the `#contact` wrapper the rest of the
 * theme links to.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_section_fields = array();
?>
<section id="contact" class="erfansanat-section erfansanat-section--contact-cta" data-section="contact-cta">
	<?php require get_template_directory() . '/template-parts/home/section-contact-cta.php'; ?>
</section>
