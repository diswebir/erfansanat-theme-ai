<?php
/**
 * Temporary compatibility header.
 *
 * Phase 2 wires the theme settings into this shell so every saved value is
 * visible on the front end. Phase 4 replaces the markup with the exact
 * static-reference header DOM and the real mega menu.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SiteParts;

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
	<?php
	$erfansanat_header_scripts = SiteParts::custom_scripts( 'header' );
	if ( '' !== $erfansanat_header_scripts ) {
		echo $erfansanat_header_scripts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Stored only for holders of unfiltered_html and re-checked at print time.
	}
	?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php SiteParts::notice_bar(); ?>
<header class="erfansanat-header border-b border-slate-200/70 bg-white/95" data-navbar-header="true">
	<?php SiteParts::topbar(); ?>
	<div class="mx-auto flex max-w-[1300px] items-center justify-between gap-3 px-4 py-2.5 sm:px-5 lg:px-8">
		<?php SiteParts::brand( home_url( '/' ) ); ?>
		<nav class="erfansanat-header__nav hidden items-center gap-1 lg:flex" aria-label="<?php esc_attr_e( 'منوی اصلی', 'erfansanat' ); ?>">
			<?php SiteParts::primary_navigation(); ?>
		</nav>
	</div>
</header>
<main id="primary" class="site-main">
