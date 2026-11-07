<?php
/**
 * City archive: every project executed in one city.
 *
 * The reference keeps a parallel `projects/city-*.html` copy of each city page,
 * so this term template also serves as that mirror page.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header( 'projects' );

$erfansanat_term     = get_queried_object();
$erfansanat_taxonomy = 'city';
require get_template_directory() . '/template-parts/archive/part-projects-archive.php';

get_footer( 'projects' );
