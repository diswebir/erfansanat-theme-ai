<?php
/**
 * Document shell (opening part).
 *
 * Phase 1 ships the minimal valid shell so the theme renders a well-formed
 * document and no core deprecation notices fire. Phase 4 replaces the body of
 * these templates with the pixel-accurate static markup (top bar, branding,
 * primary navigation, mega menu, mobile menu).
 *
 * @package ErfanSanat\Theme
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="es-page" class="es-site">
