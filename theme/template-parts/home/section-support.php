<?php
/** Render the schema-backed static-reference support section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'support', isset( $args ) && is_array( $args ) ? $args : array() );
