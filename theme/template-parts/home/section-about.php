<?php
/** Render the schema-backed static-reference about section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'about', isset( $args ) && is_array( $args ) ? $args : array() );
