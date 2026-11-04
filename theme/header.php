<?php
/** Shared document head and site-wide static-compatible navigation. */

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
<?php
ob_start();
require __DIR__ . '/template-parts/header/announcement.php';
require __DIR__ . '/template-parts/header/static-header.html';
require __DIR__ . '/template-parts/header/mobile-menu.html';
$es_header_markup = (string) ob_get_clean();
echo \ErfanSanat\Theme\Nav\HeaderMarkup::render( $es_header_markup ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup is trusted; announcement text and href values are escaped.
?>
