<?php
/**
 * Fallback template.
 *
 * Phase 1 only provides a safe fallback. Static-derived header/footer, homepage,
 * WooCommerce, project, blog and page templates are introduced in later phases.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="site-main">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<article class="no-results">
			<h1><?php esc_html_e( 'چیزی یافت نشد', 'erfansanat' ); ?></h1>
		</article>
	<?php endif; ?>
</main>
<?php
get_footer();
