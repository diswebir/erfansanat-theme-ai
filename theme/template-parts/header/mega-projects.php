<?php
/** Dynamic project-type and city mega panel. */
defined( 'ABSPATH' ) || exit;
echo \ErfanSanat\Theme\Nav\ProjectsMegaWalker::render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Renderer escapes term names and URLs.
