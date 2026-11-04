<?php
/** Home 12 Clients — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esClients = ['شهرداری اصفهان', 'شهرداری تهران', 'شهرداری مشهد', 'شهرداری قم', 'شهرداری اهواز', 'شهرداری کرج'];
if (isset($esF['items']) && is_array($esF['items'])) $esClients = array_map(static fn($s): string => (string)($s['title'] ?? $s), array_values($esF['items']));
?>
<section class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><h2 class="text-center text-2xl font-black text-slate-900"><?php echo esc_html($esField('title', 'مشتریان و سازمان‌های همکار')); ?></h2><div class="mt-8 flex flex-wrap justify-center gap-3"><?php foreach ($esClients as $c) : ?><span class="rounded-2xl border border-slate-200 bg-white px-6 py-4 text-sm font-extrabold text-slate-600 shadow-sm"><?php echo esc_html($c); ?></span><?php endforeach; ?></div></section>
