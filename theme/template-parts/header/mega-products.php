<?php
/** Optional Appearance → Menus content for the product mega panel. */
defined( 'ABSPATH' ) || exit;
echo \ErfanSanat\Theme\Nav\MegaMenuWalker::for_location( 'tolidat-sherkat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Renderer escapes text, attributes, and URLs.
