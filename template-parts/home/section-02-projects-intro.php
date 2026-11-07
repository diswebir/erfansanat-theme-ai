<?php
/** Home 02 Projects intro — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esTypes = taxonomy_exists('project_type') ? get_terms(['taxonomy' => 'project_type', 'hide_empty' => false]) : [];
if (is_wp_error($esTypes)) $esTypes = [];
?>
<section id="projects" class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><div class="flex flex-wrap items-end justify-between gap-4"><div><span class="text-xs font-extrabold text-emerald-700">نمونه‌کارهای ما</span><h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl"><?php echo esc_html($esField('title', 'پروژه‌های نورپردازی شهری عرفان صنعت')); ?></h2><p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600"><?php echo esc_html($esField('subtitle', 'اجرای پروژه‌های شاخص نورپردازی در کلان‌شهرهای ایران')); ?></p></div><a href="<?php echo esc_url(home_url('/projects/')); ?>" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-extrabold text-emerald-800">همه پروژه‌ها ←</a></div>
<div class="mt-6 flex flex-wrap gap-2"><?php foreach ($esTypes as $term) : $l = get_term_link($term); ?><a class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold hover:border-emerald-300 hover:text-emerald-700" href="<?php echo esc_url(is_wp_error($l) ? '#' : $l); ?>"><?php echo esc_html($term->name); ?></a><?php endforeach; ?></div></section>
