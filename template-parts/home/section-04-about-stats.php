<?php
/** Home 04 About stats — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esStats = [['25', '+', 'سال تجربه'], ['850', '+', 'پروژه اجراشده'], ['120', '+', 'شهر تحت پوشش'], ['98', '%', 'رضایت کارفرمایان']];
if (isset($esF['stats']) && is_array($esF['stats'])) $esStats = array_map(static fn($s): array => [(string)($s['value'] ?? ''), (string)($s['suffix'] ?? ''), (string)($s['label'] ?? '')], array_values($esF['stats']));
?>
<section id="about" class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><div class="grid items-center gap-10 lg:grid-cols-2"><div><span class="text-xs font-extrabold text-emerald-700">درباره عرفان صنعت</span><h2 class="mt-2 text-2xl font-black leading-snug text-slate-900 sm:text-3xl"><?php echo esc_html($esField('title', 'بیش از ۲۵ سال تجربه در تولید سازه‌های نوری شهری')); ?></h2><p class="mt-4 text-sm leading-8 text-slate-600"><?php echo esc_html($esField('text', 'عرفان صنعت اصفهان با تکیه بر دانش فنی بومی، خط تولید پیشرفته و تیم اجرایی متخصص، مرجع طراحی و تولید تجهیزات نورپردازی شهری در ایران است.')); ?></p><a href="<?php echo esc_url(home_url('/about/')); ?>" class="mt-5 inline-block rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white">درباره ما ←</a></div>
<div class="grid grid-cols-2 gap-4"><?php foreach ($esStats as [$v, $suf, $label]) : ?><div class="rounded-3xl border border-emerald-100 bg-white p-6 text-center shadow-sm"><div class="text-3xl font-black text-emerald-700"><span data-counter-target="<?php echo esc_attr($v); ?>" data-counter-suffix="<?php echo esc_attr($suf); ?>">۰</span></div><div class="mt-1 text-xs font-bold text-slate-500"><?php echo esc_html($label); ?></div></div><?php endforeach; ?></div></div></section>
