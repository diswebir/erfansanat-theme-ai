<?php
/** Render the schema-backed static-reference gallery section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'gallery', isset( $args ) && is_array( $args ) ? $args : array() );
