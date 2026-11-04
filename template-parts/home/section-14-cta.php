<?php
/** Home 14 CTA — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

?>
<section id="contact" class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><div class="rounded-[2rem] bg-gradient-to-l from-emerald-800 to-teal-700 px-6 py-12 text-center text-white sm:px-10"><h2 class="text-2xl font-black sm:text-3xl"><?php echo esc_html($esField('title', 'برای پروژه بعدی‌تان مشاوره رایگان بگیرید')); ?></h2><p class="mx-auto mt-3 max-w-lg text-sm leading-8 text-emerald-100">کارشناسان ما نقشه، برآورد و پیشنهاد فنی را بدون هزینه آماده می‌کنند.</p><div class="mt-6 flex flex-wrap justify-center gap-2"><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="rounded-xl bg-white px-6 py-3 text-sm font-extrabold text-emerald-900">درخواست مشاوره</a><a href="tel:<?php echo esc_attr((string) es_option('header_phone', '')); ?>" class="rounded-xl bg-white/15 px-6 py-3 text-sm font-extrabold"><?php echo esc_html((string) es_option('header_phone', '')); ?></a></div></div></section>
