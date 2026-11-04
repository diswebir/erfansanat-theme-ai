<?php
/**
 * Closing call to action under the shop and category grids.
 *
 * The reference repeats the same contact card the home page ends with, so the
 * shop reuses that section instead of copying its markup: one source of truth
 * for the phone, the e-mail and the invoice links. The wrapper carries the same
 * `id` and `data-section` the home wrapper uses, because in-page links and the
 * scroll behaviour target `#contact`, not the inner markup.
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
