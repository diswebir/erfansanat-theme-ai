<?php
/** Render the schema-backed static-reference services section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'services', isset( $args ) && is_array( $args ) ? $args : array() );
