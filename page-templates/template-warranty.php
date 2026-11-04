<?php
/** Template Name: شرایط گارانتی و بازگشت */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
$postId = (int) get_the_ID();
$defaults = [
    'warranty-terms' => ['title' => 'شرایط گارانتی تعویض ۲۴ ماهه', 'body' => 'شرایط پوشش هر محصول بر اساس مشخصات و مدارک خرید آن بررسی می‌شود.', 'items' => [], 'order' => 0],
    'warranty-process' => ['title' => 'فرآیند خدمات پس از فروش', 'body' => 'برای پیگیری، مشخصات محصول و شرح مشکل را در اختیار تیم پشتیبانی قرار دهید.', 'items' => [], 'order' => 1],
    'warranty-contact' => ['title' => 'ارتباط با پشتیبانی', 'body' => 'کارشناسان عرفان صنعت برای راهنمایی درباره خدمات و شرایط گارانتی پاسخ‌گوی شما هستند.', 'items' => [], 'order' => 2],
];
$byType = [];
foreach (es_get_sections($postId) as $section) if (isset($section['type'], $defaults[$section['type']])) $byType[$section['type']] = $section;
$sections = [];
foreach ($defaults as $type => $fields) {
    $section = $byType[$type] ?? ['type' => $type, 'enabled' => true, 'order' => $fields['order'], 'fields' => []];
    $section['fields'] = array_merge($fields, is_array($section['fields'] ?? null) ? $section['fields'] : []);
    $sections[] = $section;
}
usort($sections, static fn(array $a, array $b): int => (int) ($a['order'] ?? 0) <=> (int) ($b['order'] ?? 0));
?>
<main id="primary" class="site-main mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6 lg:px-8"><header class="rounded-3xl bg-slate-900 p-7 text-white md:p-10"><p class="text-xs font-bold text-emerald-300">خدمات پس از فروش</p><h1 class="mt-2 text-3xl font-black">گارانتی و شرایط بازگشت</h1><p class="mt-3 text-sm leading-7 text-slate-200">اطلاعات ثبت‌شده درباره پوشش گارانتی، مسیر پیگیری و پشتیبانی محصولات.</p></header>
<?php foreach ($sections as $section) : if (empty($section['enabled'])) continue; $type = (string) $section['type']; $key = substr($type, strlen('warranty-')); $fields = $section['fields']; $items = is_array($fields['items'] ?? null) ? array_values(array_filter($fields['items'], static fn($item): bool => is_scalar($item) && trim((string) $item) !== '')) : []; ?>
<section data-warranty-section="<?php echo esc_attr($key); ?>" class="rounded-3xl bg-white p-6 md:p-8"><h2 class="text-xl font-black text-slate-900"><?php echo esc_html((string) $fields['title']); ?></h2><?php if (trim((string) $fields['body']) !== '') : ?><p class="mt-3 text-sm leading-7 text-slate-600"><?php echo esc_html((string) $fields['body']); ?></p><?php endif; ?>
<?php if ($key === 'terms' && $items !== []) : ?><ul class="mt-5 space-y-3"><?php foreach ($items as $item) : ?><li class="flex gap-3 text-sm leading-7 text-slate-700"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-emerald-600"></span><span><?php echo esc_html((string) $item); ?></span></li><?php endforeach; ?></ul><?php elseif ($key === 'process' && $items !== []) : ?><ol class="mt-5 space-y-3"><?php foreach ($items as $index => $item) : ?><li class="flex items-start gap-3 text-sm leading-7 text-slate-700"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-extrabold text-emerald-800"><?php echo esc_html(number_format_i18n($index + 1)); ?></span><span><?php echo esc_html((string) $item); ?></span></li><?php endforeach; ?></ol><?php endif; ?>
<?php if ($key === 'contact') : ?><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="mt-5 inline-flex rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white">تماس با پشتیبانی</a><?php endif; ?></section>
<?php endforeach; ?>
</main>
<?php get_footer(); ?>
