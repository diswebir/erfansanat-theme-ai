<?php
/**
 * Project-type archive.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header( 'projects' );

$erfansanat_term     = get_queried_object();
$erfansanat_taxonomy = 'project_type';
require get_template_directory() . '/template-parts/archive/part-projects-archive.php';

get_footer( 'projects' );
