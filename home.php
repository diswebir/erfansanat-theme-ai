<?php
/**
 * صفحه نوشته‌ها (وبلاگ) — مطابق میرور blog.html؛ فقط برای page_for_posts
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
es_get_template('template-parts/archive/blog-shell.php', ['mode' => 'home']);
get_footer();
