<?php
/** Home 01 Hero — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esScenes = [['تونل نور شهری', 'light-tunnel.jpg'], ['المان نوری خورشیدی', 'hero.jpg'], ['ریسه‌های نورپردازی شهری', 'string-lights.jpg'], ['المان نوری تعاملی', 'light-element.jpg'], ['درخت نور هوشمند', 'light-tree.jpg']];
$esTax = taxonomy_exists('product_cat') ? 'product_cat' : 'category';
$esCats = taxonomy_exists($esTax) ? get_terms(['taxonomy' => $esTax, 'hide_empty' => false, 'number' => 8]) : [];
if (is_wp_error($esCats)) $esCats = [];
?>
<section class="relative overflow-hidden pt-28 lg:pt-36" data-home1-hero>
<div class="mx-auto max-w-[1300px] px-5 lg:px-8"><div class="relative overflow-hidden rounded-[2rem] bg-slate-900 text-white">
<?php foreach ($esScenes as $i => [$title, $img]) : ?><img data-home1-scene-img="<?php echo esc_attr((string) $i); ?>" src="<?php echo esc_url(es_image_uri($img)); ?>" alt="<?php echo esc_attr($title); ?>" class="h-[520px] w-full object-cover <?php echo $i === 0 ? '' : 'hidden'; ?>" /><?php endforeach; ?>
<div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent" data-home1-atmosphere></div>
<div class="absolute bottom-0 right-0 left-0 p-6 sm:p-10"><span class="rounded-full bg-emerald-600/90 px-3 py-1 text-[11px] font-bold">شرکت دانش‌بنیان عرفان صنعت اصفهان</span>
<h1 data-home1-scene-title class="mt-4 max-w-2xl text-3xl font-black leading-snug sm:text-5xl"><?php echo esc_html($esField('title', 'نورپردازی شهری و المان‌های نوری هوشمند')); ?></h1>
<p class="mt-3 max-w-xl text-sm leading-8 text-slate-200"><?php echo esc_html($esField('subtitle', 'طراحی، تولید و اجرای پروژه‌های نورپردازی شهری در سراسر ایران و منطقه')); ?></p>
<div class="mt-5 flex flex-wrap gap-2"><a data-home1-scene-cta href="<?php echo esc_url(home_url('/projects/')); ?>" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-extrabold text-white">مشاهده پروژه‌ها</a><button type="button" data-home1-play-trigger class="rounded-xl bg-white/15 px-5 py-3 text-sm font-extrabold backdrop-blur">▶ <span data-home1-inline-video-label>تماشای ویدیوی کارخانه</span></button><a data-home1-scene-link href="<?php echo esc_url(home_url('/shop/')); ?>" class="rounded-xl bg-white/15 px-5 py-3 text-sm font-extrabold backdrop-blur">فروشگاه محصولات</a></div>
<div class="mt-6 flex flex-wrap gap-2"><?php foreach ($esScenes as $i => [$title, $img]) : ?><button type="button" data-home1-scene-btn="<?php echo esc_attr((string) $i); ?>" class="rounded-full px-4 py-2 text-[11px] font-bold <?php echo $i === 0 ? 'bg-white text-slate-900' : 'bg-white/15 text-white'; ?>"><?php echo esc_html($title); ?></button><?php endforeach; ?></div>
</div>
<div data-home1-inline-video class="absolute inset-0 hidden items-center justify-center bg-slate-950/90"><video class="max-h-full w-full" controls preload="none" poster="<?php echo esc_url(es_image_uri('factory.jpg')); ?>"><source src="<?php echo esc_url(es_image_uri('hero-video.mp4')); ?>" type="video/mp4" /></video><div data-home1-video-controls class="absolute top-4 left-4 flex gap-2"><button type="button" data-home1-video-mute class="rounded-xl bg-white/15 px-3 py-2 text-xs font-bold text-white">صدا</button><button type="button" data-home1-video-close class="rounded-xl bg-white/15 px-3 py-2 text-xs font-bold text-white">بستن ×</button></div></div>
<div data-home1-bottom-fillet></div>
</div>
<div data-home1-categories-grid class="mt-5 grid grid-cols-2 gap-3 pb-4 sm:grid-cols-4"><?php foreach ($esCats as $term) : $l = get_term_link($term); ?><a href="<?php echo esc_url(is_wp_error($l) ? '#' : $l); ?>" class="rounded-2xl border border-emerald-100 bg-white px-4 py-4 text-center text-xs font-extrabold text-slate-700 shadow-sm hover:text-emerald-700"><?php echo esc_html($term->name); ?></a><?php endforeach; ?></div>
<div class="flex gap-2 pb-2"><?php foreach ($esScenes as $i => [$t, $im]) : ?><button type="button" data-home1-bottom-tab="<?php echo esc_attr((string) $i); ?>" class="h-1.5 flex-1 rounded-full <?php echo $i === 0 ? 'bg-emerald-600' : 'bg-slate-200'; ?>" aria-label="<?php echo esc_attr($t); ?>"></button><?php endforeach; ?></div>
</div></section>
