<?php
/**
 * Home section: blog-preview (cut from static index.html, DOM preserved)
 * @package ErfanSanat\\Theme
 */

if (!defined('ABSPATH')) { exit; }
?>
<section id="blog-preview" aria-label="ویترین مقالات کاربردی عرفان صنعت" class="mx-auto max-w-[1300px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8"><div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end"><div class="flex max-w-2xl flex-col gap-3 items-start text-right"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>دانشنامه و مجله فنی</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">جدیدترین مقالات و <span class="text-emerald-700">راهنماهای تخصصی نورپردازی</span></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">بررسی فنی تجهیزات LED، استانداردهای حفاظتی IP68 و راهنمای خرید المان‌های نوری شهری</p></div></div><a href="blog.html" class="group inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200/90 bg-white px-5 py-2.5 text-xs font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800 sm:text-sm"><span>مشاهده آرشیو مقالات</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4 text-emerald-700 transition-transform group-hover:-translate-x-0.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div><div class="mt-8 sm:mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"><?php
$q = \ErfanSanat\Theme\Queries::latest_posts(4);
if ($q->have_posts()) :
    while ($q->have_posts()) : $q->the_post();
        get_template_part('template-parts/cards/card', 'post');
    endwhile;
    wp_reset_postdata();
endif;
?></section>