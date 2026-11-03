<?php
/**
 * Fallback template.
 *
 * @package ErfanSanat\Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="bg-slate-50/50 pb-28 lg:pb-0">
	<div class="mx-auto max-w-[1300px] px-4 py-16 sm:px-6 lg:px-8">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'card-light rounded-[1.5rem] bg-white p-6' ); ?>>
					<h2 class="text-xl font-extrabold text-slate-900">
						<a href="<?php the_permalink(); ?>" class="hover:text-emerald-700"><?php the_title(); ?></a>
					</h2>
					<div class="mt-3 text-sm leading-7 text-slate-600"><?php the_excerpt(); ?></div>
				</article>
				<?php
			endwhile;
			the_posts_pagination();
		else :
			?>
			<p class="text-center text-sm font-bold text-slate-500"><?php esc_html_e( 'محتوایی یافت نشد.', 'erfansanat' ); ?></p>
			<?php
		endif;
		?>
	</div>
</main>
<?php
get_footer();
