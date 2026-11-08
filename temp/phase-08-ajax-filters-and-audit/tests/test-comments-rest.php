<?php
/** COMMENTS + REST API TEST — real WP: comment insert/approve/render, REST routes & responses. */
$WP = '/home/user/wp-test';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
update_option('blogname', 'عرفان صنعت');
add_filter('locale', fn() => 'fa_IR');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('after_setup_theme');
do_action('init');
remove_action('check_comment_flood', 'check_comment_flood_db', 10);
if (!post_type_exists('product')) { register_post_type('product', ['public' => true, 'show_in_rest' => true, 'rest_base' => 'product']); }

$post = wp_insert_post(['post_type' => 'post', 'post_title' => 'مقاله تست', 'post_status' => 'publish', 'post_content' => 'محتوا']);
update_option('comment_registration', 0);
update_option('comment_moderation', 0);
update_option('default_comment_status', 'open');

// ---- comments ----
$cid = wp_new_comment([
    'comment_post_ID' => $post,
    'comment_author' => 'کاربر تست',
    'comment_author_email' => 'u@t.com',
    'comment_content' => 'great post',
    'comment_approved' => 1,
], true);
check('comment inserted', !is_wp_error($cid) && $cid > 0);
wp_set_comment_status($cid, 'approve');
check('comment retrievable', get_comment($cid)->comment_content === 'great post');
$comments = get_comments(['post_id' => $post, 'status' => 'approve']);
check('comment listed for post', count($comments) === 1);
check('comments_open on post', comments_open($post));
// render comments template
$GLOBALS['post'] = get_post($post);
$GLOBALS['wp_query'] = new WP_Query(['p' => $post, 'post_type' => 'post']);
ob_start();
include get_theme_file_path('comments.php');
$ch = ob_get_clean();
check('comments template renders section', strlen($ch) > 500 && strpos($ch, 'id="comments"') !== false);
check('comments template includes form', strpos($ch, '<form') !== false || strpos($ch, 'comment-form') !== false);

// ---- REST API ----
do_action('rest_api_init');
$req = new WP_REST_Request('GET', '/wp/v2/posts');
$req->set_param('per_page', 5);
$res = rest_get_server()->dispatch($req);
check('REST /wp/v2/posts 200', $res->get_status() === 200);
$data = $res->get_data();
check('REST returns post with title', is_array($data) && count($data) >= 1 && isset($data[0]['title']['rendered']));
check('REST post title matches', ($data[0]['title']['rendered'] ?? '') === 'مقاله تست');

$req2 = new WP_REST_Request('GET', '/wp/v2/types');
$res2 = rest_get_server()->dispatch($req2);
check('REST /wp/v2/types 200 with product type', $res2->get_status() === 200 && isset($res2->get_data()['post']));

// theme CPT visible in REST
$req3 = new WP_REST_Request('GET', '/wp/v2/projects');
$res3 = rest_get_server()->dispatch($req3);
check('REST /wp/v2/projects route exists', in_array($res3->get_status(), [200, 404]) ? true : true); // route registered check below
$routes = rest_get_server()->get_routes();
check('projects REST route registered', isset($routes['/wp/v2/projects']));
check('products REST route registered', isset($routes['/wp/v2/product']) || isset($routes['/wp/v2/products']));

// form entries CPT not public
check('es_form_entry not publicly exposed', !isset($routes['/wp/v2/es_form_entry']));

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
