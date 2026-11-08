<?php
/**
 * آرشیو دستهٔ نوشته‌ها — مطابق میرور category/*.html
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
es_get_template('template-parts/archive/blog-shell.php', ['mode' => 'category', 'term' => get_queried_object()]);
get_footer();
