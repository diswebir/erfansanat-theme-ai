<?php
/** Reusable project card; pass a project post or explicit presentation args. */
if ( ! defined( 'ABSPATH' ) ) { return; }
\ErfanSanat\Theme\Sections\HomeCardMarkup::render( 'project', is_array( $args ?? null ) ? $args : array() );
