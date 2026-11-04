<?php
/** Render the schema-backed static-reference hero section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'hero', isset( $args ) && is_array( $args ) ? $args : array() );
