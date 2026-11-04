<?php
/** Template Name: درباره ما */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
$postId = (int) get_the_ID();
$defaults = [
    'about-hero' => ['title' => 'درباره عرفان صنعت اصفهان', 'body' => 'طراح، تولیدکننده و مجری سازه‌های نورپردازی شهری در ایران.', 'order' => 0],
    'about-story' => ['title' => 'داستان بیش از ۲۵ سال تجربه', 'body' => 'از آغاز فعالیت تا امروز، تجربه و دانش فنی را برای ساخت فضاهای شهری ماندگار به کار گرفته‌ایم.', 'order' => 1],
    'about-iso' => ['title' => 'کیفیت و گواهینامه‌های ISO', 'body' => 'فرآیندهای طراحی و تولید با تکیه بر استانداردهای کیفیت و بهبود مستمر انجام می‌شوند.', 'order' => 2],
    'about-values' => ['title' => 'اصول بنیادین ما', 'body' => 'ایمنی، کیفیت ساخت، نوآوری و پاسخ‌گویی به نیازهای کارفرما در مرکز تصمیم‌های ما قرار دارد.', 'order' => 3],
    'about-contact' => ['title' => 'برای همکاری با ما در تماس باشید', 'body' => 'کارشناسان ما آماده گفت‌وگو درباره نیازها و پروژه‌های نورپردازی شما هستند.', 'order' => 4],
];
$saved = es_get_sections($postId);
$byType = [];
foreach ($saved as $section) if (isset($section['type'], $defaults[$section['type']])) $byType[$section['type']] = $section;
$sections = [];
foreach ($defaults as $type => $fields) {
    $section = $byType[$type] ?? ['type' => $type, 'enabled' => true, 'order' => $fields['order'], 'fields' => []];
    $section['fields'] = array_merge($fields, is_array($section['fields'] ?? null) ? $section['fields'] : []);
    $sections[] = $section;
}
usort($sections, static fn(array $a, array $b): int => (int) ($a['order'] ?? 0) <=> (int) ($b['order'] ?? 0));
?>
<main id="primary" class="site-main mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
<?php foreach ($sections as $section) : if (empty($section['enabled'])) continue; $type = (string) $section['type']; $fields = $section['fields']; ?>
<?php if ($type === 'about-hero') : ?><section data-about-section="about-hero" class="overflow-hidden rounded-3xl bg-slate-900 p-7 text-white md:p-12"><p class="text-xs font-bold text-emerald-300">عرفان صنعت اصفهان</p><h1 class="mt-3 text-3xl font-black leading-relaxed md:text-5xl"><?php echo esc_html((string) $fields['title']); ?></h1><div class="mt-4 max-w-3xl text-sm leading-8 text-slate-200"><?php echo wp_kses_post((string) $fields['body']); ?></div></section>
<?php elseif ($type === 'about-story') : ?><section data-about-section="about-story" class="rounded-3xl bg-white p-6 md:p-9"><p class="text-xs font-bold text-emerald-700">تجربه و مسیر ما</p><h2 class="mt-2 text-2xl font-black text-slate-900"><?php echo esc_html((string) $fields['title']); ?></h2><div class="mt-4 text-sm leading-8 text-slate-600"><?php echo wp_kses_post((string) $fields['body']); ?></div></section>
<?php elseif ($type === 'about-iso') : ?><section data-about-section="about-iso" class="rounded-3xl border border-emerald-100 bg-emerald-50 p-6 md:p-9"><p class="text-xs font-bold text-emerald-700">استاندارد و کیفیت</p><h2 class="mt-2 text-2xl font-black text-slate-900"><?php echo esc_html((string) $fields['title']); ?></h2><div class="mt-4 text-sm leading-8 text-slate-700"><?php echo wp_kses_post((string) $fields['body']); ?></div></section>
<?php elseif ($type === 'about-values') : ?><section data-about-section="about-values" class="rounded-3xl bg-white p-6 md:p-9"><p class="text-xs font-bold text-emerald-700">آنچه به آن پایبندیم</p><h2 class="mt-2 text-2xl font-black text-slate-900"><?php echo esc_html((string) $fields['title']); ?></h2><div class="mt-4 text-sm leading-8 text-slate-600"><?php echo wp_kses_post((string) $fields['body']); ?></div></section>
<?php elseif ($type === 'about-contact') : ?><section data-about-section="about-contact" class="rounded-3xl bg-slate-100 p-6 text-center md:p-9"><h2 class="text-2xl font-black text-slate-900"><?php echo esc_html((string) $fields['title']); ?></h2><div class="mt-3 text-sm leading-7 text-slate-600"><?php echo wp_kses_post((string) $fields['body']); ?></div><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="mt-5 inline-block rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white">تماس با کارشناسان</a></section><?php endif; ?>
<?php endforeach; ?>
</main>
<?php get_footer(); ?>
