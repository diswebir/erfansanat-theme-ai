<?php
/** Render the authorized, schema-backed company differentiators section. */

defined( 'ABSPATH' ) || exit;
\ErfanSanat\Theme\Sections\HomeSectionMarkup::render( 'why-ese', isset( $args ) && is_array( $args ) ? $args : array() );
