<?php
/**
 * نتایج جستجو — کامپوننت‌های بایت‌دقیق میرور + WP_Query استاندارد
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
echo '<main class="min-h-screen bg-[#f8faf9]">';
es_get_template('template-parts/search/search-shell.php');
echo '</main>';
get_footer();
