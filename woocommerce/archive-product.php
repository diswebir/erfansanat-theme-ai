<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
do_action('woocommerce_before_main_content');
$GLOBALS['es_archive_title'] = function_exists('is_shop') && is_shop() ? 'فروشگاه محصولات' : 'فروشگاه محصولات';
$GLOBALS['es_archive_description'] = '';
$GLOBALS['es_archive_query'] = $GLOBALS['wp_query'] ?? null;
get_template_part('template-parts/product/archive-layout');
unset($GLOBALS['es_archive_title'], $GLOBALS['es_archive_description'], $GLOBALS['es_archive_query']);
do_action('woocommerce_after_main_content');
get_footer();
