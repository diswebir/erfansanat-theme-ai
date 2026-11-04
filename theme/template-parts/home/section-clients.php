<?php
/** Render the schema-backed static-reference clients section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'clients', isset( $args ) && is_array( $args ) ? $args : array() );
