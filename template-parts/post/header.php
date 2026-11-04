<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) get_the_ID();
if ($postId < 1) return;
$authorId = (int) get_post_field('post_author', $postId);
$author = (string) get_the_author_meta('display_name', $authorId);
$views = \ErfanSanat\Theme\Helpers\ViewCounter::get($postId);
$categories = get_the_category($postId);
?>
<header class="mb-8 rounded-3xl bg-white p-6 md:p-9">
<nav aria-label="breadcrumb" class="mb-5 text-xs text-slate-500"><ol class="flex flex-wrap items-center gap-2"><li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-emerald-700">خانه</a></li><li aria-hidden="true">/</li><?php if (is_array($categories) && isset($categories[0])) : ?><li><a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="hover:text-emerald-700"><?php echo esc_html($categories[0]->name); ?></a></li><li aria-hidden="true">/</li><?php endif; ?><li aria-current="page" class="font-bold text-slate-700"><?php echo esc_html(get_the_title($postId)); ?></li></ol></nav>
<?php if (is_array($categories) && isset($categories[0])) : ?><p class="mb-3 text-xs font-extrabold text-emerald-700"><?php echo esc_html($categories[0]->name); ?></p><?php endif; ?>
<h1 class="text-2xl font-black leading-relaxed text-slate-900 md:text-4xl"><?php echo esc_html(get_the_title($postId)); ?></h1>
<div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500"><span data-post-author="<?php echo esc_attr((string) $authorId); ?>">نویسنده: <strong class="text-slate-700"><?php echo esc_html($author !== '' ? $author : 'تیم عرفان صنعت'); ?></strong></span><time data-post-date="<?php echo esc_attr(get_the_date('Y-m-d', $postId)); ?>" datetime="<?php echo esc_attr(get_the_date('c', $postId)); ?>">تاریخ انتشار: <?php echo esc_html(get_the_date('', $postId)); ?></time><span data-post-views="<?php echo esc_attr((string) $views); ?>">بازدید: <?php echo esc_html(number_format_i18n($views)); ?></span></div>
</header>
