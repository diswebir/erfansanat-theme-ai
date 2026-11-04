<?php
/** Render the schema-backed static-reference contact-cta section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'contact-cta', isset( $args ) && is_array( $args ) ? $args : array() );
