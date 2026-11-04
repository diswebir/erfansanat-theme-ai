<?php
/** Home 15 Contact card — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

?>
<section class="mx-auto max-w-[1300px] px-5 py-20 lg:px-8"><div class="grid gap-5 rounded-[2rem] border border-emerald-100 bg-white p-6 shadow-sm sm:p-10 md:grid-cols-3"><div><h3 class="text-sm font-black text-slate-900">کارخانه</h3><p class="mt-2 text-xs leading-7 text-slate-600"><?php echo esc_html((string) es_option('contact_address', 'اصفهان، شهرک صنعتی')); ?></p></div><div><h3 class="text-sm font-black text-slate-900">تماس</h3><p class="mt-2 text-xs leading-7 text-slate-600"><a href="tel:<?php echo esc_attr((string) es_option('contact_phone', '')); ?>"><?php echo esc_html((string) es_option('contact_phone', '')); ?></a><br /><a href="tel:<?php echo esc_attr((string) es_option('contact_mobile', '')); ?>"><?php echo esc_html((string) es_option('contact_mobile', '')); ?></a></p></div><div><h3 class="text-sm font-black text-slate-900">مسیریابی</h3><a class="mt-2 inline-block rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-extrabold text-white" href="<?php echo esc_url(home_url('/contact/')); ?>">مشاهده نقشه و مسیر ←</a></div></div></section>
