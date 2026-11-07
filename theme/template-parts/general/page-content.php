<?php
/** Safe general page content renderer for pages without a specialized template. */

defined( 'ABSPATH' ) || exit;
if ( ! have_posts() ) { return; }
?>
<main class="bg-slate-50/50 pb-28 lg:pb-0"><div class="mx-auto max-w-[1300px] px-4 py-16 sm:px-6 lg:px-8">
	<?php while ( have_posts() ) : the_post(); $content = trim( (string) get_the_content() ); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10' ); ?>><header class="mb-7 border-b border-slate-100 pb-5"><h1 class="text-3xl font-black text-slate-950 sm:text-4xl"><?php the_title(); ?></h1></header><?php if ( '' !== $content ) : ?><div class="entry-content text-sm leading-8 text-slate-700"><?php the_content(); ?></div><?php else : ?><p class="text-sm leading-7 text-slate-500"><?php esc_html_e( 'محتوایی برای نمایش در این برگه ثبت نشده است.', 'erfansanat-theme' ); ?></p><?php endif; ?></article>
	<?php endwhile; ?>
</div></main>
