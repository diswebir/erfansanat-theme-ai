<?php
$WP = "/home/user/wp-test";
@unlink($WP."/wp-content/database/.ht.sqlite");
define("WP_INSTALLING", true);
require $WP."/wp-load.php";
require_once ABSPATH."wp-admin/includes/upgrade.php";
wp_install("t","admin","a@b.c",true,"","admin123");
add_filter("locale", fn() => "fa_IR");
switch_theme("erfansanat-theme-ai");
require_once get_theme_file_path("functions.php");
do_action("after_setup_theme"); do_action("init");
// set custom options BEFORE render
update_option("erfansanat_theme_options", [
  "general" => ["site_name" => "تست سایت", "meta_description" => "توضیح تست"],
  "header" => ["announcement" => "IMPLEMENTED announcement تست", "cta_text" => "تماس با ما تست", "sticky_header" => "0"],
  "footer" => ["phone" => "031-99999999", "email" => "test@erfansanat.com", "footer_text" => "کپی‌رایت تست"],
  "brand" => ["brand_color" => "#ff0000", "custom_css" => ".x{color:red}"],
  "advanced" => ["maintenance_mode" => "0"],
], false);
query_posts(["post_type" => "page", "name" => "__none__"]);
$home = wp_insert_post(["post_type"=>"page","post_title"=>"خانه","post_status"=>"publish"]);
update_option("page_on_front", $home); update_option("show_on_front", "page");
query_posts(["post_type" => "post", "posts_per_page" => 3]);
include get_theme_file_path("home.php");
