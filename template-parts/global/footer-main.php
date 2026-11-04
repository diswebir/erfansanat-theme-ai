<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSocials = ['social_instagram' => 'اینستاگرام', 'social_telegram' => 'تلگرام', 'social_whatsapp' => 'واتساپ', 'social_linkedin' => 'لینکدین', 'social_aparat' => 'آپارات'];
?>
<footer class="relative overflow-hidden border-t border-emerald-100 bg-white"><div class="pointer-events-none absolute inset-0 grid-Floor opacity-50"></div><div class="pointer-events-none absolute -top-40 right-1/3 h-80 w-80 rounded-full bg-emerald-400/10 blur-[130px]"></div><div class="relative mx-auto max-w-[1300px] px-5 pt-20 pb-10 lg:px-8"><div class="grid gap-10 pb-14 md:grid-cols-2 lg:grid-cols-12">
<div class="space-y-4 lg:col-span-4"><div class="flex items-center gap-3"><div class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-200 bg-emerald-50"><span class="text-sm font-black text-emerald-700">ESE</span></div><div><div class="text-base font-extrabold text-slate-900"><?php echo esc_html((string) es_option('brand_name', '')); ?></div><div class="text-xs text-emerald-700">شرکت دانش‌بنیان نورپردازی شهری و صنعتی</div></div></div>
<p class="text-[13px] leading-7 text-slate-600"><?php echo esc_html((string) es_option('footer_about', 'طراح و تولیدکننده تجهیزات نورپردازی پیشرفته، المان‌های نوری تزئینی، درختان نوری هوشمند، خورشیدنماها، ریسه‌های LED و پوینت لایت پیکسل با ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش.')); ?></p>
<div class="flex flex-wrap gap-2 pt-1 text-[11px] font-bold text-emerald-800"><span class="rounded-full bg-emerald-50 px-3 py-1">ISO 9001:2015</span><span class="rounded-full bg-emerald-50 px-3 py-1">IP67 / IP68</span><span class="rounded-full bg-emerald-50 px-3 py-1">کد بین‌المللی GS1</span><span class="rounded-full bg-emerald-50 px-3 py-1">عرضه در سامانه ستاد</span></div>
<div class="flex gap-2"><?php foreach ($esSocials as $key => $label) : $url = (string) es_option($key, ''); if ($url === '') continue; ?><a class="rounded-full border border-emerald-100 px-3 py-1.5 text-[11px] font-bold text-emerald-800 hover:bg-emerald-50" href="<?php echo esc_url($url); ?>" rel="noopener" target="_blank"><?php echo esc_html($label); ?></a><?php endforeach; ?></div></div>
<div class="lg:col-span-2"><h3 class="mb-4 text-sm font-extrabold text-slate-900">دسترسی سریع</h3><ul class="space-y-2.5 text-[13px] text-slate-600">
<?php foreach ([['فروشگاه محصولات', '/shop/'], ['پروژه‌های اجراشده', '/projects/'], ['خدمات نورپردازی', '/smartelement/'], ['مجوزها و افتخارات', '/licenses-and-honors/'], ['درباره ما', '/about/']] as [$l, $p]) : ?><li><a class="transition-colors hover:text-emerald-700" href="<?php echo esc_url(home_url($p)); ?>"><?php echo esc_html($l); ?></a></li><?php endforeach; ?>
</ul></div>
<div class="lg:col-span-3"><h3 class="mb-4 text-sm font-extrabold text-slate-900">دسته‌بندی محصولات</h3>
<?php if (has_nav_menu('tolidat-sherkat')) { wp_nav_menu(['theme_location' => 'tolidat-sherkat', 'container' => false, 'menu_class' => 'space-y-2.5 text-[13px] text-slate-600', 'fallback_cb' => false]); } else { echo '<ul class="space-y-2.5 text-[13px] text-slate-600">'; $t = taxonomy_exists('product_cat') ? 'product_cat' : 'category'; foreach ((taxonomy_exists($t) ? get_terms(['taxonomy' => $t, 'hide_empty' => false, 'number' => 8]) : []) as $term) { if (is_wp_error($term)) continue; $l = get_term_link($term); echo '<li><a class="transition-colors hover:text-emerald-700" href="' . esc_url(is_wp_error($l) ? '#' : $l) . '">' . esc_html($term->name) . '</a></li>'; } echo '</ul>'; } ?>
</div>
<div class="lg:col-span-3"><h3 class="mb-4 text-sm font-extrabold text-slate-900">اطلاعات تماس کارخانه</h3><ul class="space-y-2.5 text-[13px] leading-6 text-slate-600">
<?php if ((string) es_option('contact_address', '') !== '') : ?><li><?php echo esc_html((string) es_option('contact_address', '')); ?></li><?php endif; ?>
<li><a href="tel:<?php echo esc_attr((string) es_option('contact_phone', '')); ?>"><?php echo esc_html((string) es_option('contact_phone', '')); ?></a></li>
<li><a href="tel:<?php echo esc_attr((string) es_option('contact_mobile', '')); ?>"><?php echo esc_html((string) es_option('contact_mobile', '')); ?></a></li>
<?php if ((string) es_option('contact_email', '') !== '') : ?><li><a href="mailto:<?php echo esc_attr((string) es_option('contact_email', '')); ?>"><?php echo esc_html((string) es_option('contact_email', '')); ?></a></li><?php endif; ?>
<?php if ((string) es_option('contact_working_hours', '') !== '') : ?><li><?php echo esc_html((string) es_option('contact_working_hours', '')); ?></li><?php endif; ?>
</ul></div>
</div>
