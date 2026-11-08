<?php
/**
 * page.php — generic page fallback (editable via SectionManager).
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }
get_header();
while (have_posts()) : the_post();
?>
<main id="main" class="site-main">
    <section class="relative overflow-hidden bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <h1 class="text-2xl font-black text-slate-900 sm:text-3xl lg:text-4xl"><?php the_title(); ?></h1>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
        <div class="prose max-w-none text-slate-700"><?php the_content(); ?></div>
    </section>
</main>
<?php endwhile; ?>
<?php get_footer();
