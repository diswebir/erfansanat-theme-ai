<?php
/** Home 08 Catalog — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

?>
<section class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><div class="flex flex-wrap items-center justify-between gap-5 rounded-[2rem] border border-emerald-100 bg-emerald-50/70 px-6 py-10 sm:px-10"><div><h2 class="text-xl font-black text-slate-900 sm:text-2xl"><?php echo esc_html($esField('title', 'کاتالوگ جامع محصولات عرفان صنعت اصفهان')); ?></h2><p class="mt-2 text-sm text-slate-600">مشخصات فنی، ابعاد و راهنمای انتخاب همه محصولات در یک فایل</p></div><a href="<?php echo esc_url($esField('catalog_url', '#')); ?>" class="rounded-xl bg-emerald-700 px-6 py-3.5 text-sm font-extrabold text-white" download>دانلود کاتالوگ ↓</a></div></section>
