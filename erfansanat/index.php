<?php
/**
 * Fallback template.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main bg-[#f6f8f7]">
	<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
		<?php if ( have_posts() ) : ?>
			<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'rounded-2xl border border-slate-200 bg-white p-6 shadow-sm' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="mb-4 block overflow-hidden rounded-xl">
								<?php the_post_thumbnail( 'medium_large', array( 'class' => 'h-auto w-full object-cover' ) ); ?>
							</a>
						<?php endif; ?>

						<h2 class="mb-2 text-lg font-extrabold text-slate-800">
							<a href="<?php the_permalink(); ?>" class="transition-colors hover:text-emerald-700">
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
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => esc_html__( 'قبلی', 'erfansanat' ),
						'next_text' => esc_html__( 'بعدی', 'erfansanat' ),
					)
				);
				?>
			</div>
		<?php else : ?>
			<p class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-600">
				<?php echo esc_html__( 'هیچ محتوایی یافت نشد.', 'erfansanat' ); ?>
			</p>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
