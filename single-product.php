<?php
/**
 * قالب محصول تکی ووکامرس — شل میرور: هیرو + گرید ۱۲ستونه (گالری چپ/خرید راست)
 * فرگمنت‌ها: template-parts/single-product/{hero,gallery,…}.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

$es_pid = (int) get_the_ID();
echo '<main class="bg-slate-50/50 pb-28 lg:pb-0">';
es_get_template('template-parts/single-product/hero.php', ['pid' => $es_pid]);
echo '<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">';
echo '<div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10">';
echo '<div class="space-y-4 lg:col-span-7">';
es_get_template('template-parts/single-product/gallery.php', ['pid' => $es_pid]);
echo '</div>';
echo '<div class="lg:col-span-5 lg:sticky lg:top-28">';
// ناحیه خرید (تسک ۶.۵) و ادامه محتوا (۶.۳/۶.۴/۶.۶) اینجا تزریق می‌شود
echo '</div>';
echo '</div>';
echo '</section>';
echo '</main>';
get_footer();
