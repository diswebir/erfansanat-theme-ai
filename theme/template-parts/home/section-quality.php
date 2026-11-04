<?php
/** Render the schema-backed static-reference quality section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'quality', isset( $args ) && is_array( $args ) ? $args : array() );
