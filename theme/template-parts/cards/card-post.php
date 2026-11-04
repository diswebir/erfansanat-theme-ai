<?php
/** Reusable article card; pass a post or explicit presentation args. */
if ( ! defined( 'ABSPATH' ) ) { return; }
\ErfanSanat\Theme\Sections\HomeCardMarkup::render( 'post', is_array( $args ?? null ) ? $args : array() );
