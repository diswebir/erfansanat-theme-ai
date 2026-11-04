<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1) return;
$ids = \ErfanSanat\Theme\Helpers\LegacyMeta::productGalleryIds($postId);
$featured = (int) get_post_thumbnail_id($postId);
if ($featured > 0) array_unshift($ids, $featured);
$ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
$slides = [];
foreach ($ids as $attachmentId) {
    $src = wp_get_attachment_image_url($attachmentId, 'large');
    if (!is_string($src) || $src === '') continue;
    $thumb = wp_get_attachment_image_url($attachmentId, 'thumbnail');
    $alt = (string) get_post_meta($attachmentId, '_wp_attachment_image_alt', true);
    $slides[] = ['id' => $attachmentId, 'src' => $src, 'thumb' => is_string($thumb) && $thumb !== '' ? $thumb : $src, 'alt' => $alt !== '' ? $alt : get_the_title($postId)];
}
if ($slides === []) $slides[] = ['id' => 0, 'src' => es_image_uri('hero.jpg'), 'thumb' => es_image_uri('hero.jpg'), 'alt' => get_the_title($postId)];
?>
<div class="space-y-5" data-gallery-root="true">
    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <img data-gallery-main-img="true" src="<?php echo esc_url($slides[0]['src']); ?>" alt="<?php echo esc_attr($slides[0]['alt']); ?>" class="aspect-square w-full object-contain p-5" loading="eager">
        <span data-gallery-counter="true" class="absolute bottom-4 left-4 rounded-full bg-slate-900/75 px-3 py-1 text-xs font-bold text-white">۱ / <?php echo esc_html((string) count($slides)); ?></span>
        <button type="button" data-gallery-prev="true" aria-label="تصویر قبلی" class="absolute right-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 shadow">‹</button>
        <button type="button" data-gallery-next="true" aria-label="تصویر بعدی" class="absolute left-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 shadow">›</button>
        <button type="button" data-gallery-expand="true" aria-label="نمایش بزرگ تصویر" class="absolute left-4 top-4 grid h-10 w-10 place-items-center rounded-full bg-white/90 shadow">⤢</button>
    </div>
    <div class="flex gap-3 overflow-x-auto pb-1" aria-label="تصاویر محصول">
        <?php foreach ($slides as $index => $slide) : ?><button type="button" data-gallery-thumb="<?php echo esc_attr((string) $index); ?>" data-gallery-src="<?php echo esc_url($slide['src']); ?>" class="relative shrink-0 overflow-hidden rounded-xl border border-emerald-500 p-1"><img src="<?php echo esc_url($slide['thumb']); ?>" alt="<?php echo esc_attr($slide['alt']); ?>" class="h-16 w-16 rounded-lg object-cover"><span data-gallery-thumb-ring class="pointer-events-none absolute inset-0 rounded-xl ring-2 ring-emerald-500"></span></button><?php endforeach; ?>
    </div>
    <div data-gallery-lightbox="true" class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-950/95 p-4" role="dialog" aria-modal="true" aria-label="نمایش تصاویر محصول">
        <button type="button" data-gallery-lightbox-close="true" aria-label="بستن" class="absolute right-5 top-5 grid h-11 w-11 place-items-center rounded-full bg-white text-2xl">×</button>
        <button type="button" data-gallery-lightbox-prev="true" aria-label="تصویر قبلی" class="absolute right-4 top-1/2 grid h-11 w-11 place-items-center rounded-full bg-white">‹</button>
        <img data-gallery-lightbox-img="true" src="<?php echo esc_url($slides[0]['src']); ?>" alt="<?php echo esc_attr($slides[0]['alt']); ?>" class="max-h-[85vh] max-w-[90vw] object-contain">
        <button type="button" data-gallery-lightbox-next="true" aria-label="تصویر بعدی" class="absolute left-4 top-1/2 grid h-11 w-11 place-items-center rounded-full bg-white">›</button>
        <div class="absolute bottom-5 flex gap-2"><?php foreach ($slides as $index => $_slide) : ?><button type="button" data-gallery-lightbox-dot="<?php echo esc_attr((string) $index); ?>" aria-label="تصویر <?php echo esc_attr((string) ($index + 1)); ?>" class="h-2 w-2 rounded-full bg-white/40"></button><?php endforeach; ?></div>
    </div>
    <ul class="grid grid-cols-2 gap-2 text-xs font-bold text-slate-700 sm:grid-cols-4"><li class="rounded-xl bg-emerald-50 p-3">۲۴ ماه گارانتی</li><li class="rounded-xl bg-emerald-50 p-3">۵ سال خدمات</li><li class="rounded-xl bg-emerald-50 p-3">ارسال سراسری</li><li class="rounded-xl bg-emerald-50 p-3">فاکتور رسمی</li></ul>
</div>
