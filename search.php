<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
$query = $GLOBALS['wp_query'] ?? null;
$searchTerm = get_search_query(false);
$posts = $query instanceof WP_Query && is_array($query->posts) ? $query->posts : [];
$groups = ['product' => [], 'projects' => [], 'post' => []];
foreach ($posts as $result) {
    if (!$result instanceof WP_Post) continue;
    $type = get_post_type($result);
    if (isset($groups[$type])) $groups[$type][] = $result;
}
$hasResults = count($groups['product']) + count($groups['projects']) + count($groups['post']) > 0;
?>
<main id="primary" class="site-main"><section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" data-search-results="true" data-search-query="<?php echo esc_attr($searchTerm); ?>">
<header class="mb-8 rounded-3xl bg-white p-6 md:p-8"><p class="mb-2 text-xs font-bold text-emerald-700">جستجو در عرفان صنعت</p><h1 class="text-2xl font-black text-slate-900 md:text-4xl">نتایج جستجو برای «<?php echo esc_html($searchTerm); ?>»</h1><p class="mt-3 text-sm text-slate-600"><?php echo esc_html(number_format_i18n((int) ($query instanceof WP_Query ? $query->found_posts : count($posts)))); ?> نتیجه</p></header>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="mb-8 flex gap-3 rounded-3xl border border-slate-200 bg-white p-4"><label class="sr-only" for="site-search-results">عبارت جستجو</label><input id="site-search-results" type="search" name="s" value="<?php echo esc_attr($searchTerm); ?>" class="min-w-0 flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm" placeholder="جستجو در محصولات، پروژه‌ها و مقالات"><button type="submit" class="rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white">جستجو</button></form>
<?php if ($hasResults) : ?>
<?php foreach (['product' => ['محصولات', 'template-parts/cards/card-product'], 'projects' => ['پروژه‌ها', 'template-parts/cards/card-project'], 'post' => ['مقالات', 'template-parts/cards/card-post']] as $type => [$title, $card]) : if ($groups[$type] === []) continue; ?>
<section class="mb-10" data-search-group="<?php echo esc_attr($type); ?>"><h2 class="mb-5 text-xl font-black text-slate-900"><?php echo esc_html($title); ?></h2><div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($groups[$type] as $result) : $GLOBALS['es_card_post'] = $result; get_template_part($card); endforeach; unset($GLOBALS['es_card_post']); ?></div></section>
<?php endforeach; ?>
<?php else : ?><div data-search-empty="true" class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center"><h2 class="text-xl font-black text-slate-900">نتیجه‌ای پیدا نشد</h2><p class="mt-3 text-sm leading-7 text-slate-600">عبارت جستجو را کوتاه‌تر یا متفاوت وارد کنید.</p></div><?php endif; ?>
</section></main>
<?php get_footer(); ?>
