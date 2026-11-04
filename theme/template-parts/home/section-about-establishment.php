<?php
/** Render the schema-backed static-reference establishment section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'establishment', isset( $args ) && is_array( $args ) ? $args : array() );
