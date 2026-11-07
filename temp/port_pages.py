#!/usr/bin/env python3
"""Port static mirror <main> content into theme page templates (WP-ified)."""
import re, os, sys

MIRROR = '/home/user/static-mirror/static-mirror'
THEME = '/home/user/erfansanat-theme-ai'

PAIRS = [
    ('about.html', 'page-about.php'),
    ('contact.html', 'page-contact.php'),
    ('faq.html', 'page-faq.php'),
    ('recruitment.html', 'page-recruitment.php'),
    ('refund_returns.html', 'page-refund_returns.php'),
    ('licenses-and-honors.html', 'page-licenses-and-honors.php'),
    ('services.html', 'page-services.php'),
    ('shop.html', 'archive-product.php'),
    ('projects.html', 'archive-projects.php'),
    ('blog.html', 'home.php'),
    ('cart.html', 'page-cart.php'),
    ('checkout.html', 'page-checkout.php'),
]

def port(mirror_file, tpl):
    html = open(os.path.join(MIRROR, mirror_file), encoding='utf-8').read()
    m = re.search(r'<main[^>]*>(.*)</main>', html, re.S)
    if not m:
        print('NO MAIN:', mirror_file); return
    body = m.group(1)
    # drop scripts (theme loads its own JS)
    body = re.sub(r'<script\b.*?</script>', '', body, flags=re.S)
    # assets: images/... -> assets/images/... (theme keeps all under assets/images)
    body = re.sub(r'(src|poster)="images/', r'\1="<?php echo ERFANSANAT_THEME_URI; ?>assets/images/', body)
    body = re.sub(r'(src|poster)="assets/', r'\1="<?php echo ERFANSANAT_THEME_URI; ?>assets/', body)
    # static .html links -> es_static_url
    body = re.sub(r'href="([a-zA-Z0-9_./-]+\.html[^"]*)"',
                  lambda mm: 'href="<?php echo esc_url(es_static_url(\'%s\')); ?>"' % mm.group(1), body)
    out = ("<?php\n/** %s — sliced from static mirror %s (100%% classes/IDs/data-*). */\n"
           "if (!defined('ABSPATH')) { exit; }\nget_header(); ?>\n%s\n<?php get_footer(); ?>\n"
           % (tpl, mirror_file, body))
    open(os.path.join(THEME, tpl), 'w', encoding='utf-8').write(out)
    print('ported %s -> %s (%d bytes)' % (mirror_file, tpl, len(out)))

for mf, tpl in PAIRS:
    port(mf, tpl)
