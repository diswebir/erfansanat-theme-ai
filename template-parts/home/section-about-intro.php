<?php
/**
 * سکشن معرفی تاسیس شرکت (about-intro) — برش بایت‌دقیق میرور
 * فرگمنت با temp/gen_gi5.py از refs/about-intro.html برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

/** فیلتر es_about_intro_args — توسعه‌پذیری سکشن معرفی */
$es_args = apply_filters('es_about_intro_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'about-intro')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}

$ES_ABOUT_INTRO = <<<'ESI12'
<section aria-label="درباره تاسیس شرکت دانش‌بنیان عرفان صنعت اصفهان" class="mx-auto max-w-[1300px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8"><div class="relative overflow-hidden rounded-[2.25rem] border-0 bg-gradient-to-l from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-[0_24px_60px_-24px_rgba(4,120,87,0.38)] sm:rounded-[2.5rem]"><img src="{ES_BG}" alt="{ES_BG_ALT}" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-14 mix-blend-luminosity"/><div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(52,211,153,0.22),transparent_55%)]"></div><div class="absolute -top-0.5 -right-0.5 z-20"><div class="relative rounded-bl-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-bl-[2rem] sm:px-8 sm:py-3"><div class="flex items-center gap-2 text-xs sm:text-sm font-black text-emerald-700"><span class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_BADGE}</span></div><svg viewBox="0 0 32 32" aria-hidden="true" class="pointer-events-none absolute top-0.5 -left-[31px] h-8 w-8 text-[#f6f8f7]"><path d="M32 0H0C17.673 0 32 14.327 32 32V0Z" fill="currentColor"></path></svg><svg viewBox="0 0 32 32" aria-hidden="true" class="pointer-events-none absolute right-0.5 -bottom-[31px] h-8 w-8 text-[#f6f8f7]"><path d="M32 0V32C32 14.327 17.673 0 0 0H32Z" fill="currentColor"></path></svg></div></div><div class="relative z-10 border-0 px-6 pt-15 pb-15 sm:px-10 sm:pt-16 sm:pb-16 lg:px-12"><div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-10"><div class="text-right lg:col-span-6"><div class="inline-flex flex-wrap items-center gap-2.5 rounded-2xl bg-white/10 px-3.5 py-1.5 backdrop-blur-xs"><span dir="ltr" class="font-mono text-lg font-black tracking-[0.14em] text-white sm:text-xl">{ES_MONO}</span><span class="h-3.5 w-px bg-white/25"></span><span class="text-xs font-extrabold text-emerald-100 sm:text-sm">{ES_SINCE}</span></div><h3 class="mt-4 text-xl font-black leading-snug text-white sm:text-2xl [text-wrap:balance]">{ES_TITLE}</h3><p class="mt-3 text-right text-xs leading-7 font-medium text-emerald-50/90 sm:text-[13.5px] sm:leading-7">{ES_DESC}</p><div class="mt-5 grid grid-cols-1 gap-2 xs:grid-cols-3 xs:gap-2.5"><div class="rounded-xl bg-white/10 px-2.5 py-2 backdrop-blur-xs xs:px-3 xs:py-2.5"><div class="text-[11.5px] xs:text-xs font-black text-white tabular-nums">{ES_S1T}</div><div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85">{ES_S1D}</div></div><div class="rounded-xl bg-white/10 px-2.5 py-2 backdrop-blur-xs xs:px-3 xs:py-2.5"><div class="text-[11.5px] xs:text-xs font-black text-white tabular-nums">{ES_S2T}</div><div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85">{ES_S2D}</div></div><div class="rounded-xl bg-white/10 px-2.5 py-2 backdrop-blur-xs xs:px-3 xs:py-2.5"><div class="text-[11.5px] xs:text-xs font-black text-white tabular-nums">{ES_S3T}</div><div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85">{ES_S3D}</div></div></div></div><div class="grid grid-cols-12 gap-4 lg:col-span-6"><div class="group relative col-span-7 h-48 overflow-hidden rounded-2xl bg-emerald-950/40 shadow-lg sm:h-56"><img src="{ES_G1}" alt="{ES_G1_ALT}"پروژه‌های تونل نوری عرفان صنعت" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-transparent"></div><div class="absolute right-3.5 bottom-3 text-right"><div class="text-[10.5px] font-bold text-emerald-300">{ES_G1_CHIP}</div><div class="text-xs font-extrabold text-white">{ES_G1_TITLE}</div></div></div><div class="group relative col-span-5 h-48 overflow-hidden rounded-2xl bg-emerald-950/40 shadow-lg sm:h-56"><img src="{ES_G2}" alt="{ES_G2_ALT}"تولید درختان نوری هوشمند" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-transparent"></div><div class="absolute right-3.5 bottom-3 text-right"><div class="text-[10.5px] font-bold text-emerald-300">{ES_G2_CHIP}</div><div class="text-xs font-extrabold text-white">{ES_G2_TITLE}</div></div></div></div></div></div><div class="absolute -bottom-0.5 -left-0.5 z-20"><div class="relative rounded-tr-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-tr-[2rem] sm:px-8 sm:py-3"><a href="{ES_CTA_URL}" class="inline-flex items-center gap-2 text-xs font-black text-emerald-700 transition-colors hover:text-emerald-900 sm:text-sm"><span>{ES_CTA}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a><svg viewBox="0 0 32 32" aria-hidden="true" class="pointer-events-none absolute -top-[31px] left-0.5 h-8 w-8 text-[#f6f8f7]"><path d="M0 32V0C0 17.673 14.327 32 32 32H0Z" fill="currentColor"></path></svg><svg viewBox="0 0 32 32" aria-hidden="true" class="pointer-events-none absolute -right-[31px] bottom-0.5 h-8 w-8 text-[#f6f8f7]"><path d="M0 32H32C14.327 32 0 17.673 0 0V32Z" fill="currentColor"></path></svg></div></div></div></section>
ESI12;


echo strtr($ES_ABOUT_INTRO, [
    '{ES_BADGE}'     => (string) ($es_f['badge'] ?? ''),
    '{ES_BG}'        => es_media_url((string) ($es_f['bg_image'] ?? 'assets/images/hero.jpg')),
    '{ES_BG_ALT}'    => (string) ($es_f['bg_alt'] ?? ''),
    '{ES_MONO}'      => (string) ($es_f['mono'] ?? ''),
    '{ES_SINCE}'     => (string) ($es_f['since'] ?? ''),
    '{ES_TITLE}'     => (string) ($es_f['title'] ?? ''),
    '{ES_DESC}'      => (string) ($es_f['desc'] ?? ''),
    '{ES_S1T}'       => (string) ($es_f['s1_title'] ?? ''),
    '{ES_S1D}'       => (string) ($es_f['s1_desc'] ?? ''),
    '{ES_S2T}'       => (string) ($es_f['s2_title'] ?? ''),
    '{ES_S2D}'       => (string) ($es_f['s2_desc'] ?? ''),
    '{ES_S3T}'       => (string) ($es_f['s3_title'] ?? ''),
    '{ES_S3D}'       => (string) ($es_f['s3_desc'] ?? ''),
    '{ES_G1}'        => es_media_url((string) ($es_f['g1_image'] ?? 'assets/images/light-tunnel.jpg')),
    '{ES_G1_ALT}'    => (string) ($es_f['g1_alt'] ?? ''),
    '{ES_G1_CHIP}'   => (string) ($es_f['g1_chip'] ?? ''),
    '{ES_G1_TITLE}'  => (string) ($es_f['g1_title'] ?? ''),
    '{ES_G2}'        => es_media_url((string) ($es_f['g2_image'] ?? 'assets/images/light-tree.jpg')),
    '{ES_G2_ALT}'    => (string) ($es_f['g2_alt'] ?? ''),
    '{ES_G2_CHIP}'   => (string) ($es_f['g2_chip'] ?? ''),
    '{ES_G2_TITLE}'  => (string) ($es_f['g2_title'] ?? ''),
    '{ES_CTA}'       => (string) ($es_f['cta_text'] ?? ''),
    '{ES_CTA_URL}'   => es_link_url((string) ($es_f['cta_url'] ?? '')),
]);
