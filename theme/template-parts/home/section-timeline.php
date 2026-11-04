<?php
/** Render the schema-backed static-reference timeline section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'timeline', isset( $args ) && is_array( $args ) ? $args : array() );
