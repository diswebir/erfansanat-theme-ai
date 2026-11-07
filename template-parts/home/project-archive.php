<?php
$archiveFields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$archiveTitle = trim((string) ($archiveFields['title'] ?? '')) ?: 'نمونه‌کارهای اجراشده در کلان‌شهرهای کشور';
$archiveDescription = trim((string) ($archiveFields['description'] ?? ''));
$archiveProjectTypes = \ErfanSanat\Theme\HomepageData::get_project_types(4);
$archiveProjectCount = \ErfanSanat\Theme\HomepageData::get_published_count('projects');
$archiveUrl = get_post_type_archive_link('projects') ?: home_url('/projects/');
?>
<section aria-label="گالری نمونه‌کارها و پروژه‌های اجراشده عرفان صنعت" class="relative z-10 mx-auto max-w-[1300px] px-4 pt-2 pb-12 sm:px-6 sm:pb-16 lg:px-8"><div class="flex flex-col items-start justify-between gap-4 rounded-2xl border border-slate-200/85 bg-white p-5 sm:flex-row sm:items-center sm:p-6"><div><div class="inline-flex items-center gap-2 text-xs font-extrabold text-emerald-700"><span aria-hidden="true" class="h-3 w-1 rounded-full bg-emerald-600"></span><span>آرشیو تفکیکی پروژه‌های اجرایی</span></div><h3 class="mt-1 text-lg font-black text-slate-900 sm:text-xl"><?php echo esc_html($archiveTitle); ?></h3><p class="mt-2 text-sm leading-6 text-slate-600"><?php echo esc_html($archiveDescription); ?></p></div><div role="tablist" aria-label="دسته‌بندی نمونه‌کارها" class="inline-flex flex-wrap items-center gap-1.5 rounded-2xl border border-slate-200/85 bg-[#f6f8f7] p-1.5">
<button type="button" role="tab" aria-selected="true" data-home2-port-tab="all" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap bg-emerald-700 text-white shadow-xs"><span><?php echo esc_html__('همه سازه‌های نوری', 'erfansanat-theme'); ?></span><span class="rounded-md px-1.5 py-0.5 text-[10px] font-black tabular-nums bg-white/20 text-white"><?php echo esc_html(number_format_i18n($archiveProjectCount)); ?></span></button>
<?php foreach ($archiveProjectTypes as $projectType) : $typeKey = sanitize_key($projectType->slug); ?>
<button type="button" role="tab" aria-selected="false" data-home2-port-tab="<?php echo esc_attr($typeKey); ?>" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-600 hover:bg-white/80 hover:text-slate-900"><span><?php echo esc_html($projectType->name); ?></span><span class="rounded-md px-1.5 py-0.5 text-[10px] font-black tabular-nums bg-slate-200/75 text-slate-600"><?php echo esc_html(number_format_i18n((int) $projectType->count)); ?></span></button>
<?php endforeach; ?>
</div></div><?php
$archivePanels = [['key' => 'all', 'term' => null]];
foreach ($archiveProjectTypes as $projectType) {
    $archivePanels[] = ['key' => sanitize_key($projectType->slug), 'term' => $projectType];
}
foreach ($archivePanels as $panelIndex => $archivePanel) :
    $panelArgs = [];
    if ($archivePanel['term'] instanceof WP_Term) {
        $panelArgs['tax_query'] = [[
            'taxonomy' => 'project_type',
            'field' => 'term_id',
            'terms' => [(int) $archivePanel['term']->term_id],
        ]];
    }
    $panelProjectIds = \ErfanSanat\Theme\HomepageData::get_projects(5, $panelArgs);
    ?>
<div data-home2-port-panel="<?php echo esc_attr($archivePanel['key']); ?>" class="grid mt-5 grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5<?php echo 0 === $panelIndex ? '' : ' hidden'; ?>">
<?php foreach ($panelProjectIds as $projectIndex => $projectId) : get_template_part('template-parts/cards/card-project', null, ['post_id' => $projectId, 'variant' => 'archive', 'index' => $projectIndex]); endforeach; ?>
</div>
<?php endforeach; ?><div class="mt-6 flex justify-center"><a href="<?php echo esc_url($archiveUrl); ?>" class="group inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200/90 bg-white px-6 py-3 text-xs sm:text-sm font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800"><span>مشاهده آرشیو کامل پروژه‌ها</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 text-emerald-700 transition-transform group-hover:-translate-y-0.5 group-hover:-translate-x-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div></section>