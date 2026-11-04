<?php
/** Reusable product card; pass a product post, term, or explicit presentation args. */
if ( ! defined( 'ABSPATH' ) ) { return; }
\ErfanSanat\Theme\Sections\HomeCardMarkup::render( 'product', is_array( $args ?? null ) ? $args : array() );
