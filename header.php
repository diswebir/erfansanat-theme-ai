<?php
/** Global header shell sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php wp_head(); ?>
<?php $esHeadCode = (string) es_option('custom_head_code',''); if ($esHeadCode !== '' && current_user_can('manage_options')) echo $esHeadCode . "\n"; ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="root">
<div id="home" class="relative min-h-screen overflow-x-clip selection:bg-emerald-600 selection:text-white">
<div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 overflow-hidden"><div class="absolute -top-36 right-[5%] h-[36rem] w-[36rem] rounded-full bg-emerald-200/40 blur-[130px]"></div><div class="absolute top-[32%] -left-32 h-[32rem] w-[32rem] rounded-full bg-teal-100/50 blur-[130px]"></div><div class="absolute bottom-[10%] right-[18%] h-[34rem] w-[34rem] rounded-full bg-green-100/60 blur-[140px]"></div><div class="light-dot-grid absolute inset-0 opacity-60"></div></div>
<div class="relative z-10">
<?php get_template_part('template-parts/global/header-bar'); ?>
<?php if (locate_template('template-parts/global/mobile-menu.php', false, false) !== '') get_template_part('template-parts/global/mobile-menu'); ?>
<?php if (locate_template('template-parts/global/cart-modal.php', false, false) !== '') get_template_part('template-parts/global/cart-modal'); ?>
