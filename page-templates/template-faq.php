<?php
/** Template Name: سوالات متداول */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
$postId = (int) get_the_ID();
$defaults = [
    'faq-products' => ['title' => 'سوالات محصولات', 'order' => 0],
    'faq-projects' => ['title' => 'سوالات پروژه‌ها', 'order' => 1],
    'faq-services' => ['title' => 'سوالات خدمات و پشتیبانی', 'order' => 2],
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
$schemaQuestions = [];
?>
<main id="primary" class="site-main mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8"><header class="mb-8 rounded-3xl bg-white p-6 text-center md:p-9"><p class="text-xs font-bold text-emerald-700">پاسخ پرسش‌های شما</p><h1 class="mt-2 text-3xl font-black text-slate-900">سوالات متداول</h1><p class="mt-3 text-sm leading-7 text-slate-600">پاسخ پرسش‌های پرتکرار درباره محصولات، پروژه‌ها و خدمات عرفان صنعت.</p></header>
<?php foreach ($sections as $section) : if (empty($section['enabled'])) continue; $type = (string) $section['type']; $key = substr($type, strlen('faq-')); $fields = $section['fields']; $questions = is_array($fields['questions'] ?? null) ? $fields['questions'] : []; $validQuestions = []; foreach ($questions as $item) { if (!is_array($item)) continue; $question = trim(sanitize_text_field((string) ($item['question'] ?? ''))); $answer = trim(sanitize_textarea_field((string) ($item['answer'] ?? ''))); if ($question === '' || $answer === '') continue; $validQuestions[] = ['question' => $question, 'answer' => $answer]; $schemaQuestions[] = ['@type' => 'Question', 'name' => $question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer]]; } ?>
<section data-faq-section="<?php echo esc_attr($key); ?>" class="mb-6 rounded-3xl bg-white p-6 md:p-8"><h2 class="mb-5 text-xl font-black text-slate-900"><?php echo esc_html((string) $fields['title']); ?></h2><div data-faq-list="true" class="space-y-3"><?php if ($validQuestions !== []) : foreach ($validQuestions as $index => $item) : ?><article class="overflow-hidden rounded-2xl border border-slate-200"><button type="button" data-faq-toggle="<?php echo esc_attr((string) $index); ?>" class="flex w-full items-center justify-between gap-4 p-4 text-right text-sm font-bold text-slate-800" aria-expanded="false"><span><?php echo esc_html($item['question']); ?></span><span data-faq-icon="<?php echo esc_attr((string) $index); ?>" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-lg text-emerald-800">+</span></button><div data-faq-body="<?php echo esc_attr((string) $index); ?>" class="hidden px-4 pb-4 text-sm leading-7 text-slate-600"><?php echo esc_html($item['answer']); ?></div></article><?php endforeach; else : ?><p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">پرسشی برای این دسته ثبت نشده است.</p><?php endif; ?></div></section>
<?php endforeach; ?>
</main>
<?php if ($schemaQuestions !== []) : $schema = wp_json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $schemaQuestions], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); if (is_string($schema)) : ?><script type="application/ld+json"><?php echo $schema; ?></script><?php endif; endif; get_footer(); ?>
