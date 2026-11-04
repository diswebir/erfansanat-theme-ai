<?php
/**
 * Front page: the fifteen reference sections in the saved order.
 *
 * Phase 2 renders the registry order with plain structural markup; Phase 5
 * supplies the approved static design inside each section part.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header();

\ErfanSanat\Theme\Sections::render( \ErfanSanat\Theme\Sections::home_layout() );

get_footer();
