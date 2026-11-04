<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$flOutput = apply_filters('erfansanat_fl_panel_auth_output', '');
if (!is_string($flOutput) || $flOutput === '') $flOutput = shortcode_exists('fl-panel') ? do_shortcode('[fl-panel]') : (shortcode_exists('fl_panel') ? do_shortcode('[fl_panel]') : '');
$voOutput = apply_filters('erfansanat_voorodak_auth_output', '');
if (!is_string($voOutput) || $voOutput === '') $voOutput = shortcode_exists('voorodak') ? do_shortcode('[voorodak]') : (shortcode_exists('voorodak_login') ? do_shortcode('[voorodak_login]') : '');
?>
<div class="space-y-6"><section class="rounded-2xl bg-slate-50 p-5" data-auth-provider="fl-panel"><h2 class="mb-3 text-base font-black">پنل کاربری</h2><?php if ($flOutput !== '') : ?><?php echo $flOutput; // Trusted plugin shortcode output must preserve form fields and authentication markup. ?><?php else : do_action('erfansanat_fl_panel_auth'); endif; ?></section><section class="rounded-2xl bg-slate-50 p-5" data-auth-provider="voorodak"><h2 class="mb-3 text-base font-black">ورود پیامکی</h2><?php if ($voOutput !== '') : ?><?php echo $voOutput; // Trusted plugin shortcode output must preserve form fields and authentication markup. ?><?php else : do_action('erfansanat_voorodak_auth'); endif; ?></section></div>
