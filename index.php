<?php
/**
 * The main template fallback.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<main id="primary" class="site-main">
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : ?>
			<?php the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php echo esc_html(get_the_title()); ?></h1>
				</header>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p><?php esc_html_e('No content was found.', 'erfansanat-theme'); ?></p>
	<?php endif; ?>
</main>
<?php wp_footer(); ?>
</body>
</html>
