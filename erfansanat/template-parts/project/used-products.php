<?php
/**
 * Products used in a project (P07-T004). Connects the `mortabet_mahsole_2`
 * relation to real product cards. Only published, valid products render
 * (AC-01), in the stored relation order (AC-02). When the relation is empty the
 * part outputs nothing so no empty grid is left behind (AC-03).
 *
 * @package ErfanSanat
 *
 * @var array $args { project_id: int }
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Admin\ProjectMetaboxes;

$es_pid = isset( $args['project_id'] ) ? (int) $args['project_id'] : (int) get_the_ID();
if ( ! $es_pid || 'projects' !== get_post_type( $es_pid ) ) {
	return;
}

// Stored relation order; keep only published, existing products (AC-01/AC-02).
$es_ids = array();
foreach ( ProjectMetaboxes::get_related_products( $es_pid ) as $es_product_id ) {
	$es_product_id = (int) $es_product_id;
	if ( $es_product_id > 0 && 'product' === get_post_type( $es_product_id ) && 'publish' === get_post_status( $es_product_id ) && ! in_array( $es_product_id, $es_ids, true ) ) {
		$es_ids[] = $es_product_id;
	}
}

/** Filter the used-product IDs (order preserved). */
$es_ids = apply_filters( 'es_project_used_product_ids', $es_ids, $es_pid );

if ( empty( $es_ids ) ) {
	return; // AC-03: no empty section content.
}
?>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3" data-project-used-products="true">
	<?php
	foreach ( $es_ids as $es_product_id ) {
		get_template_part( 'template-parts/cards/card', 'product', array( 'product_id' => $es_product_id ) );
	}
	?>
</div>
