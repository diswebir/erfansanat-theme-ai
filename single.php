<?php
/**
 * سینگل مقاله — مطابق میرور blog/*.html
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
echo '<main class="min-h-screen bg-[#f8faf9]">';
$es_single_post = get_queried_object();
if (is_object($es_single_post) && isset($es_single_post->ID)) {
    es_get_template('template-parts/single/article-shell.php', ['post' => $es_single_post]);
}
echo '</main>';
get_footer();
