<?php
/** Render the schema-backed static-reference projects section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'projects', isset( $args ) && is_array( $args ) ? $args : array() );
