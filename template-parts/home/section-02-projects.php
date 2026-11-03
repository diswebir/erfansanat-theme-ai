<?php
/**
 * Home section: projects (cut from static index.html, DOM preserved)
 * @package ErfanSanat\\Theme
 */

if (!defined('ABSPATH')) { exit; }
?>
<section id="projects" class="relative z-10 mx-auto max-w-[1300px] scroll-mt-24 px-4 pt-12 pb-8 sm:px-6 sm:pt-16 sm:pb-10 lg:px-8"><div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>نمونه‌کارهای شاخص</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">پروژه‌های <span class="text-emerald-700">نورپردازی شهری</span></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">اجرای تخصصی پروژه‌های نورپردازی برای شهرداری‌ها و سازمان‌های زیباسازی؛ تلفیق مهندسی سازه، استاندارد حفاظتی IP68 و زیبایی‌شناسی شهری.</p></div></div><div class="mt-8 sm:mt-10 grid gap-4 sm:gap-5 lg:grid-cols-5 lg:items-stretch"><?php
$q = \ErfanSanat\Theme\Queries::latest_projects(6);
if ($q->have_posts()) :
    while ($q->have_posts()) : $q->the_post();
        get_template_part('template-parts/cards/card', 'project');
    endwhile;
    wp_reset_postdata();
endif;
?></section>