<?php
/** Render the schema-backed static-reference blog-preview section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'blog-preview', isset( $args ) && is_array( $args ) ? $args : array() );
