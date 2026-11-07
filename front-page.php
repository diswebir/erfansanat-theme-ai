<?php
/**
 * Homepage template. The source sections remain discrete and are rendered in the
 * order and visibility configured by the native page Section Builder.
 *
 * @package ErfanSanat\Theme
 */

global $post;
$postId = (int) get_queried_object_id();
if (!$postId && $post instanceof WP_Post) {
    $postId = (int) $post->ID;
}
if (!$postId) {
    $postId = (int) get_option('page_on_front');
}

get_header();
?><main><?php
if ($postId > 0) {
    \ErfanSanat\Theme\Admin\SectionManager::render_sections($postId);
}
?></main><?php
get_footer();
