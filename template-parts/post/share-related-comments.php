<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) get_the_ID();
if ($postId < 1) return;
$categories = get_the_category($postId);
$categoryIds = is_array($categories) ? array_values(array_filter(array_map(static fn($category): int => $category instanceof WP_Term ? (int) $category->term_id : 0, $categories))) : [];
$related = $categoryIds !== [] ? get_posts(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'post__not_in' => [$postId], 'category__in' => $categoryIds, 'orderby' => 'date', 'order' => 'DESC']) : [];
$showComments = comments_open($postId) || get_comments_number($postId) > 0;
?>
<div class="mt-8 flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-white p-5"><p class="text-sm font-bold text-slate-700">این مقاله برایتان مفید بود؟ آن را به اشتراک بگذارید.</p><button type="button" data-share-btn="true" class="rounded-xl bg-emerald-700 px-4 py-3 text-sm font-extrabold text-white transition hover:bg-emerald-800"><span data-share-label="true">اشتراک‌گذاری مقاله</span></button></div>
<?php if ($related !== []) : ?><section class="mt-12" data-related-posts="true"><h2 class="mb-5 text-xl font-black text-slate-900">مقالات مرتبط</h2><div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($related as $relatedPost) : $GLOBALS['es_card_post'] = $relatedPost; get_template_part('template-parts/cards/card-post'); endforeach; unset($GLOBALS['es_card_post']); ?></div></section><?php endif; ?>
<?php if ($showComments) : ?><section id="comments" class="mt-12 rounded-3xl bg-white p-5 md:p-8" data-post-comments="true"><h2 class="mb-5 text-xl font-black text-slate-900">دیدگاه‌ها</h2><?php comments_template(); ?></section><?php endif; ?>
