<?php
/** Render the schema-backed static-reference products section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'products', isset( $args ) && is_array( $args ) ? $args : array() );
