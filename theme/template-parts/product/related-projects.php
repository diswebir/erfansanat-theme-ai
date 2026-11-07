<?php
/** Related case studies selected in the product editor, preserving saved order. */

defined( 'ABSPATH' ) || exit;
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $args['product'] ?? null );
if ( ! $product_id ) { return; }
$raw = \ErfanSanat\Theme\Woo\ProductTemplate::meta( $product_id, 'project-ids', array() );
if ( ! is_array( $raw ) ) { $raw = array(); }
$ids = array();
foreach ( $raw as $value ) {
	if ( is_array( $value ) ) { $value = $value['id'] ?? $value['value'] ?? ''; }
	$id = absint( $value );
	if ( $id > 0 && ! in_array( $id, $ids, true ) ) { $ids[] = $id; }
}
$projects = array();
if ( $ids && class_exists( 'WP_Query' ) ) {
	$project_query = new WP_Query( array( 'post_type' => 'projects', 'post_status' => 'publish', 'posts_per_page' => count( $ids ), 'post__in' => $ids, 'orderby' => 'post__in', 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
	$projects = is_array( $project_query->posts ?? null ) ? $project_query->posts : array();
} else {
	foreach ( $ids as $id ) {
		$post = function_exists( 'get_post' ) ? get_post( $id ) : null;
		if ( is_object( $post ) && 'projects' === ( $post->post_type ?? '' ) && 'publish' === ( $post->post_status ?? 'publish' ) ) { $projects[] = $post; }
	}
}
$projects = array_values( array_filter( $projects, static function ( $post ) { return is_object( $post ) && 'projects' === ( $post->post_type ?? '' ) && 'publish' === ( $post->post_status ?? 'publish' ); } ) );
if ( ! $projects ) { return; }
$product = \ErfanSanat\Theme\Woo\ProductTemplate::product( $args['product'] ?? null );
$title = $product && method_exists( $product, 'get_name' ) ? (string) $product->get_name() : (string) get_the_title( $product_id );
?>
<section class="mx-auto max-w-6xl px-5 pb-14 lg:px-8" aria-labelledby="es-product-projects-title"><div class="mb-7 flex flex-wrap items-end justify-between gap-4"><div><p class="mb-1.5 text-xs font-bold text-emerald-700">نمونه‌کارهای واقعی اجراشده با این محصول</p><h2 id="es-product-projects-title" class="text-2xl font-extrabold text-slate-900">پروژه‌های مرتبط با <?php echo esc_html( $title ); ?></h2></div><a href="<?php echo esc_url( get_post_type_archive_link( 'projects' ) ); ?>" class="text-xs font-extrabold text-emerald-700">مشاهده همه پروژه‌ها</a></div><div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"><?php foreach ( $projects as $project ) : $project_id_item = (int) $project->ID; $terms = function_exists( 'get_the_terms' ) ? get_the_terms( $project_id_item, 'project_type' ) : array(); $type = is_array( $terms ) && isset( $terms[0]->name ) ? (string) $terms[0]->name : ''; $city_terms = function_exists( 'get_the_terms' ) ? get_the_terms( $project_id_item, 'city' ) : array(); $city = is_array( $city_terms ) && isset( $city_terms[0]->name ) ? (string) $city_terms[0]->name : ''; get_template_part( 'template-parts/cards/card-project', null, array( 'post' => $project, 'post_id' => $project_id_item, 'title' => get_the_title( $project_id_item ), 'url' => get_permalink( $project_id_item ), 'image' => get_the_post_thumbnail_url( $project_id_item, 'large' ), 'excerpt' => get_the_excerpt( $project_id_item ), 'category' => $type, 'city' => $city ) ); endforeach; ?></div></section>
