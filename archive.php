<?php
/**
 * آرشیو عمومی نوشته‌ها (تاریخ/نویسنده) — همان شل بلاگ
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
es_get_template('template-parts/archive/blog-shell.php', ['mode' => 'home']);
get_footer();
