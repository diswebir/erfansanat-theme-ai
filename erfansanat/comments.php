<?php
/**
 * Comments controller (P07-T017). Core comments_template() lands here; the
 * design-system markup lives in template-parts/post/comments.php.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}

get_template_part( 'template-parts/post/comments' );
