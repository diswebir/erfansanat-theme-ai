<?php
/** Render the schema-backed static-reference map-contact section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'map-contact', isset( $args ) && is_array( $args ) ? $args : array() );
