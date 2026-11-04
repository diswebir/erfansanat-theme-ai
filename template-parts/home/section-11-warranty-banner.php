<?php
/** Home 11 Warranty — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

?>
<section class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><div class="rounded-[2rem] bg-slate-900 px-6 py-12 text-center text-white sm:px-10"><h2 class="text-2xl font-black sm:text-3xl"><?php echo esc_html($esField('title', 'پشتیبانی فنی و گارانتی تعویض در سراسر ایران')); ?></h2><p class="mx-auto mt-3 max-w-xl text-sm leading-8 text-slate-300">۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش برای همه محصولات</p><a href="<?php echo esc_url(home_url('/refund_returns/')); ?>" class="mt-6 inline-block rounded-xl bg-emerald-600 px-6 py-3 text-sm font-extrabold text-white">شرایط گارانتی ←</a></div></section>
