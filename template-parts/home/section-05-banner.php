<?php
/** Home 05 Banner — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

?>
<section class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><div class="relative overflow-hidden rounded-[2rem] bg-emerald-800 px-6 py-12 text-white sm:px-10"><h2 class="max-w-2xl text-2xl font-black leading-snug sm:text-3xl"><?php echo esc_html($esField('title', 'مرجع بومی‌سازی سازه‌های نوری و تجهیزات روشنایی شهری در ایران')); ?></h2><p class="mt-3 max-w-xl text-sm leading-8 text-emerald-100"><?php echo esc_html($esField('subtitle', 'از ایده و طراحی سه‌بعدی تا تولید، نصب و گارانتی سراسری')); ?></p><a href="<?php echo esc_url($esField('url', home_url('/smartelement/'))); ?>" class="mt-6 inline-block rounded-xl bg-white px-5 py-3 text-sm font-extrabold text-emerald-900">خدمات ما ←</a><img src="<?php echo esc_url(es_image_uri('light-sphere.jpg')); ?>" alt="" class="pointer-events-none absolute -left-10 top-0 h-full w-1/3 object-cover opacity-25" loading="lazy" decoding="async" /></div></section>
