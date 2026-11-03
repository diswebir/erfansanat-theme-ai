<?php
/**
 * Fallback template.
 *
 * Phase 1 provides a safe fallback. Static-derived header, homepage, WooCommerce,
 * project, blog and page templates are introduced in later phases. The opening
 * <main> element lives in header.php and is closed by footer.php.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header();
?>
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
<?php
get_footer();
