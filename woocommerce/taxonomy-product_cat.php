<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
do_action('woocommerce_before_main_content');
$term = get_queried_object();
$GLOBALS['es_product_category_term'] = $term instanceof WP_Term ? $term : null;
$GLOBALS['es_archive_title'] = $term instanceof WP_Term ? $term->name : 'دسته محصولات';
$GLOBALS['es_archive_hide_header'] = true; $GLOBALS['es_archive_description'] = '';
$GLOBALS['es_archive_query'] = $GLOBALS['wp_query'] ?? null;
?>
<main id="primary" class="site-main">
    <?php get_template_part('template-parts/product/category-intro'); get_template_part('template-parts/product/archive-layout'); ?>
</main>
<?php unset($GLOBALS['es_product_category_term'], $GLOBALS['es_product_archive_term'], $GLOBALS['es_archive_hide_header'], $GLOBALS['es_archive_title'], $GLOBALS['es_archive_description'], $GLOBALS['es_archive_query']); do_action('woocommerce_after_main_content'); get_footer(); ?>
