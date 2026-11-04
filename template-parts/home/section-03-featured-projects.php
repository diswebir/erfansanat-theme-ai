<?php
/** Home 03 Featured projects — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esQ = new WP_Query(['post_type' => 'projects', 'post_status' => 'publish', 'posts_per_page' => 6, 'orderby' => 'date', 'order' => 'DESC']);
?>
<section class="mx-auto max-w-[1300px] px-5 pt-12 lg:px-8"><div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-home-featured-projects>
<?php while ($esQ->have_posts()) : $esQ->the_post(); $GLOBALS['es_card_post'] = get_post(); get_template_part('template-parts/cards/card-project'); endwhile; wp_reset_postdata(); unset($GLOBALS['es_card_post']); ?>
</div></section>
