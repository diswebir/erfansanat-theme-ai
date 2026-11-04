<?php
/** Render the schema-backed static-reference catalog section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'catalog', isset( $args ) && is_array( $args ) ? $args : array() );
