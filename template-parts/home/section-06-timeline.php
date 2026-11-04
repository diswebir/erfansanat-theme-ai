<?php
/** Home 06 Timeline — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esItems = [['۱۳۸۰', 'آغاز فعالیت'], ['۱۳۸۸', 'تولید انبوه ریسه‌های LED'], ['۱۳۹۵', 'شرکت دانش‌بنیان'], ['۱۴۰۰', 'صادرات منطقه‌ای'], ['۱۴۰۵', 'نسل هوشمند المان‌های نوری']];
if (isset($esF['items']) && is_array($esF['items'])) $esItems = array_map(static fn($s): array => [(string)($s['year'] ?? ''), (string)($s['title'] ?? '')], array_values($esF['items']));
?>
<section class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><h2 class="text-center text-2xl font-black text-slate-900 sm:text-3xl"><?php echo esc_html($esField('title', 'مسیر رشد عرفان صنعت اصفهان')); ?></h2><div class="mt-10 grid gap-4 sm:grid-cols-3 lg:grid-cols-5"><?php foreach ($esItems as [$y, $t]) : ?><div class="relative rounded-3xl border border-slate-200/80 bg-white p-5 text-center shadow-sm"><div class="text-xl font-black text-emerald-700"><?php echo esc_html($y); ?></div><div class="mt-2 text-xs font-bold leading-6 text-slate-600"><?php echo esc_html($t); ?></div></div><?php endforeach; ?></div></section>
