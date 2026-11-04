<?php
/** Front page — SectionManager orchestrator (default: 15 static sections). @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
$esPostId = (int) get_the_ID();
$esSections = es_get_sections($esPostId);
if ($esSections === []) {
    $esSections = array_map(static fn(string $t, int $i): array => ['id' => $t, 'type' => $t, 'enabled' => true, 'order' => $i, 'fields' => []], \ErfanSanat\Theme\Home\DefaultSections::types(), array_keys(\ErfanSanat\Theme\Home\DefaultSections::types()));
}
?>
<main>
<?php foreach ($esSections as $esSection) : if (empty($esSection['enabled'])) continue; $GLOBALS['es_section'] = $esSection; get_template_part('template-parts/home/' . $esSection['type']); endforeach; unset($GLOBALS['es_section']); ?>
</main>
<?php get_footer(); ?>
