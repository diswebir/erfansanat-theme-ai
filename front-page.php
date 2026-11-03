<?php
/**
 * front-page.php — home page: renders each section part, ordered/enabled
 * via the admin SectionManager (es_sections).
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }

get_header();

$es_parts = [
    'hero' => 'template-parts/home/section-01-hero.php',
    'projects' => 'template-parts/home/section-02-projects.php',
    'categories' => 'template-parts/home/section-03-categories.php',
    'about' => 'template-parts/home/section-04-about.php',
    'history' => 'template-parts/home/section-05-history.php',
    'stats' => 'template-parts/home/section-06-stats.php',
    'products' => 'template-parts/home/section-07-products.php',
    'brands' => 'template-parts/home/section-08-brands.php',
    'services' => 'template-parts/home/section-09-services.php',
    'quality' => 'template-parts/home/section-10-quality.php',
    'support' => 'template-parts/home/section-11-support.php',
    'clients' => 'template-parts/home/section-12-clients.php',
    'blog-preview' => 'template-parts/home/section-13-blog-preview.php',
    'contact-cta' => 'template-parts/home/section-14-contact-cta.php',
    'newsletter' => 'template-parts/home/section-15-newsletter.php',
];

$configured = function_exists('es_sections') ? es_sections(get_the_ID()) : [];

// Build ordered list: configured order wins; otherwise static order.
$ordered = [];
if ($configured) {
    foreach ($configured as $cfg) {
        if (isset($es_parts[$cfg['id']])) { $ordered[$cfg['id']] = $es_parts[$cfg['id']]; }
    }
} else {
    $ordered = $es_parts;
}

?>
<main id="main" class="site-main">
<?php foreach ($ordered as $sid => $part) : ?>
    <?php get_template_part($part); ?>
<?php endforeach; ?>
</main>
<?php
get_footer();
