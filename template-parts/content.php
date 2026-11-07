<?php
/**
 * content.php — default loop card wrapper (used by index.php fallback).
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('rounded-3xl border border-slate-200 bg-white p-6'); ?>>
    <h2 class="text-lg font-black text-slate-900"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="mt-2 text-sm leading-relaxed text-slate-600"><?php the_excerpt(); ?></div>
</article>
