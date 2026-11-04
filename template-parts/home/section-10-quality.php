<?php
/** Home 10 Quality — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esItems = [['IP68', 'مقاومت کامل در برابر آب و گردوغبار'], ['تست دمایی', 'عملکرد پایدار از ‎-۲۵ تا ‎+۶۰ درجه'], ['بدنه مقاوم', 'آلومینیوم و پلی‌کربنات صنعتی'], ['دانش‌بنیان', 'تولید داخلی با گارانتی تعویض']];
if (isset($esF['items']) && is_array($esF['items'])) $esItems = array_map(static fn($s): array => [(string)($s['title'] ?? ''), (string)($s['desc'] ?? '')], array_values($esF['items']));
?>
<section class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><h2 class="text-center text-2xl font-black text-slate-900 sm:text-3xl"><?php echo esc_html($esField('title', 'استانداردهای کیفیت و ماندگاری')); ?></h2><div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"><?php foreach ($esItems as [$t, $d]) : ?><div class="rounded-3xl border border-slate-200/80 bg-white p-6 text-center shadow-sm"><div class="text-lg font-black text-emerald-700"><?php echo esc_html($t); ?></div><p class="mt-2 text-xs leading-6 text-slate-500"><?php echo esc_html($d); ?></p></div><?php endforeach; ?></div></section>
