<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) get_the_ID();
unset($GLOBALS['es_post_toc_headings'], $GLOBALS['es_post_toc_post_id']);
$content = (string) get_post_field('post_content', $postId);
if ($postId < 1 || !preg_match_all('/<(h[2-4])\b([^>]*)>(.*?)<\/\1\s*>/is', $content, $matches, PREG_SET_ORDER)) return;
$headings = [];
$usedIds = [];
foreach ($matches as $index => $match) {
    $attrs = (string) $match[2];
    $label = trim(wp_strip_all_tags((string) $match[3]));
    if ($label === '') continue;
    $id = '';
    if (preg_match('/\bid\s*=\s*(["\'])(.*?)\1/i', $attrs, $idMatch)) $id = trim(sanitize_text_field((string) $idMatch[2]));
    if ($id === '') {
        $slug = preg_replace('/[^\\p{L}\\p{N}_-]+/u', '-', trim($label));
        $id = is_string($slug) ? trim($slug, '-') : '';
    }
    if ($id === '') $id = 'section-' . ($index + 1);
    $base = $id; $suffix = 2;
    while (isset($usedIds[$id])) $id = $base . '-' . $suffix++;
    $usedIds[$id] = true;
    $headings[] = ['id' => $id, 'label' => $label];
}
if ($headings === []) return;
$GLOBALS['es_post_toc_headings'] = $headings;
$GLOBALS['es_post_toc_post_id'] = $postId;
if (!function_exists('es_theme_apply_post_toc_ids')) {
    function es_theme_apply_post_toc_ids(string $content): string {
        $postId = (int) ($GLOBALS['es_post_toc_post_id'] ?? 0);
        $headings = $GLOBALS['es_post_toc_headings'] ?? [];
        if ($postId < 1 || get_the_ID() !== $postId || !is_array($headings)) return $content;
        $index = 0;
        return (string) preg_replace_callback('/<(h[2-4])\b([^>]*)>(.*?)<\/\1\s*>/is', static function (array $m) use (&$index, $headings): string {
            if (trim(wp_strip_all_tags((string) $m[3])) === '' || !isset($headings[$index])) return $m[0];
            $entry = $headings[$index++];
            $attrs = (string) $m[2];
            if (preg_match('/\bid\s*=\s*(["\'])(.*?)\1/i', $attrs)) {
                $attrs = (string) preg_replace('/\bid\s*=\s*(["\']).*?\1/i', 'id="' . esc_attr($entry['id']) . '"', $attrs, 1);
            } else {
                $attrs .= ' id="' . esc_attr($entry['id']) . '"';
            }
            return '<' . strtolower($m[1]) . $attrs . '>' . $m[3] . '</' . strtolower($m[1]) . '>';
        }, $content);
    }
}
add_filter('the_content', 'es_theme_apply_post_toc_ids', 20);
?>
<nav class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 md:p-7" data-post-toc="true" aria-label="فهرست مطالب"><h2 class="mb-4 text-lg font-black text-slate-900">فهرست مطالب</h2><ol class="space-y-2 border-r-2 border-emerald-100 pr-4"><?php foreach ($headings as $heading) : ?><li><a href="#<?php echo esc_attr($heading['id']); ?>" class="text-sm text-slate-600 transition hover:text-emerald-700"><?php echo esc_html($heading['label']); ?></a></li><?php endforeach; ?></ol></nav>
