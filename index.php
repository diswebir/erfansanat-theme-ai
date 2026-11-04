<?php
/**
 * Main fallback template file (WordPress Template Hierarchy root fallback).
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$esHasCustomHeader = locate_template('header.php', false, false) !== '';
$esHasCustomFooter = locate_template('footer.php', false, false) !== '';

if ($esHasCustomHeader) {
    get_header();
} else {
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <div id="root">
        <div id="home" class="relative min-h-screen overflow-x-clip">
            <div class="relative z-10">
    <?php
}
?>

<main class="mx-auto max-w-7xl px-5 py-28 lg:px-8 lg:py-36">
    <?php if (have_posts()) : ?>
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm transition hover:shadow-md'); ?>>
                    <h2 class="mb-3 text-lg font-bold text-slate-900">
                        <a href="<?php the_permalink(); ?>" class="hover:text-emerald-700">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                    <div class="text-sm leading-7 text-slate-600">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="mt-10">
            <?php the_posts_pagination(); ?>
        </div>
    <?php else : ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
            <h1 class="text-xl font-bold text-slate-900"><?php esc_html_e('محتوایی برای نمایش یافت نشد.', 'erfansanat'); ?></h1>
        </div>
    <?php endif; ?>
</main>

<?php
if ($esHasCustomFooter) {
    get_footer();
} else {
    ?>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
    </body>
    </html>
    <?php
}
