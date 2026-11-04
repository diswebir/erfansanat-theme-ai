<?php
/**
 * Global footer and closing document hooks.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;

$companyName = (string) es_option('footer.company_name', 'شرکت دانش‌بنیان عرفان صنعت اصفهان');
$siteName = (string) es_option('branding.site_name', 'عرفان صنعت اصفهان');
$brandLogo = (string) es_option('branding.logo_url', get_theme_file_uri('assets/images/ese-logo.svg'));
$footerDescription = (string) es_option('footer.description', 'طراح و تولیدکننده تجهیزات نورپردازی پیشرفته، المان‌های نوری تزئینی، درختان نوری هوشمند و ریسه‌های LED.');
$badges = \ErfanSanat\Theme\TemplateParts::parse_lines(
    (string) es_option('footer.badges', ''),
    ['ISO 9001:2015', 'IP67 / IP68', 'کد بین‌المللی GS1', 'عرضه در سامانه ستاد']
);
$quickLinks = \ErfanSanat\Theme\TemplateParts::parse_links(
    (string) es_option('footer.quick_links', ''),
    [
        ['label' => 'فروشگاه محصولات', 'url' => '/shop/'],
        ['label' => 'پروژه‌های اجراشده', 'url' => '/projects/'],
        ['label' => 'خدمات نورپردازی', 'url' => '/services/'],
        ['label' => 'مجوزها و افتخارات', 'url' => '/licenses-and-honors/'],
        ['label' => 'سوالات متداول', 'url' => '/faq/'],
        ['label' => 'شرایط گارانتی محصولات', 'url' => '/refund_returns/'],
        ['label' => 'فرصت‌های شغلی و استخدام', 'url' => '/recruitment/'],
        ['label' => 'اخبار و مقالات', 'url' => '/blog/'],
    ]
);
$productCategories = [];
if (taxonomy_exists('product_cat')) {
    $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 8, 'orderby' => 'name', 'order' => 'ASC']);
    if (!is_wp_error($terms) && is_array($terms)) {
        foreach ($terms as $term) {
            $termUrl = get_term_link($term);
            if (!is_wp_error($termUrl)) {
                $productCategories[] = ['label' => $term->name, 'url' => $termUrl, 'is_term' => true];
            }
        }
    }
}
if (!$productCategories) {
    $productCategories = [
        ['label' => 'پوینت لایت پیکسل LED (IP68)', 'url' => 'product_cat/point-light.html'],
        ['label' => 'ریسه و تزئینات نورپردازی شهری', 'url' => 'product_cat/strings-and-lighting-decorations.html'],
        ['label' => 'ریسه بلوطی LED هفت‌رنگ', 'url' => 'product_cat/acorn-thread.html'],
        ['label' => 'ریسه سوزنی شهری ضدآب (IP68)', 'url' => 'product_cat/needle-thread.html'],
        ['label' => 'درخت نوری هوشمند (دلسا و آرتام)', 'url' => 'product_cat/light-tree.html'],
        ['label' => 'المان‌های نورپردازی و خورشیدنما', 'url' => 'product_cat/urban-lighting-elements.html'],
        ['label' => 'لینیرلایت و والواشر نما', 'url' => 'product_cat/wall-washer.html'],
        ['label' => 'باکس کنترل هوشمند نورپردازی', 'url' => 'product_cat/control-box.html'],
    ];
}
$phone = (string) es_option('contact.phone', '031-91091011');
$salesPhone = (string) es_option('contact.sales_phone', '09137976915');
$email = sanitize_email((string) es_option('contact.email', 'info@erfansanat.com'));
$address = (string) es_option('contact.address', 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹');
$hours = (string) es_option('contact.business_hours', 'شنبه تا پنجشنبه · ۷:۰۰ الی ۱۶:۳۰');
$copyright = (string) es_option('footer.copyright', 'تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.');
$productsMenu = \ErfanSanat\Theme\Navigation\Navigation::render_location('tolidat-sherkat', 'products');
?>
<footer class="relative overflow-hidden border-t border-emerald-100 bg-white">
    <div class="pointer-events-none absolute inset-0 grid-Floor opacity-50"></div>
    <div class="pointer-events-none absolute -top-40 right-1/3 h-80 w-80 rounded-full bg-emerald-400/10 blur-[130px]"></div>
    <div class="relative mx-auto max-w-[1300px] px-5 pt-20 pb-10 lg:px-8">
        <div class="grid gap-10 pb-14 md:grid-cols-2 lg:grid-cols-12">
            <div class="space-y-4 lg:col-span-4">
                <div class="flex items-center gap-3">
                    <div class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-200 bg-emerald-50">
                        <?php if ($brandLogo) : ?>
                            <img src="<?php echo esc_url($brandLogo); ?>" alt="" class="h-7 w-7 object-contain" />
                        <?php else : ?>
                            <span class="text-sm font-black text-emerald-700">ESE</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="text-base font-extrabold text-slate-900"><?php echo esc_html($siteName); ?></div>
                        <div class="text-xs text-emerald-700"><?php echo esc_html($companyName); ?></div>
                    </div>
                </div>
                <p class="text-[13px] leading-7 text-slate-600"><?php echo esc_html($footerDescription); ?></p>
                <div class="flex flex-wrap gap-2 pt-1 text-[11px] font-bold text-emerald-800">
                    <?php foreach ($badges as $badge) : ?>
                        <span class="rounded-full bg-emerald-50 px-3 py-1"><?php echo esc_html($badge); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="lg:col-span-2">
                <h3 class="mb-4 text-sm font-extrabold text-slate-900"><?php echo esc_html__('دسترسی سریع', 'erfansanat-theme'); ?></h3>
                <ul class="space-y-2.5 text-[13px] text-slate-600">
                    <?php foreach ($quickLinks as $link) : ?>
                        <li><a href="<?php echo esc_url(\ErfanSanat\Theme\TemplateParts::resolve_static_url($link['url'])); ?>" class="transition-colors hover:text-emerald-700"><?php echo esc_html($link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="lg:col-span-3">
                <h3 class="mb-4 text-sm font-extrabold text-slate-900"><?php echo esc_html__('دسته‌بندی محصولات', 'erfansanat-theme'); ?></h3>
                <?php if ('' !== $productsMenu) : ?>
                    <?php echo $productsMenu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered by the escaped WordPress navigation walker. ?>
                <?php else : ?>
                    <ul class="space-y-2.5 text-[13px] text-slate-600">
                        <?php foreach ($productCategories as $category) : ?>
                            <?php $categoryUrl = !empty($category['is_term']) ? $category['url'] : \ErfanSanat\Theme\TemplateParts::resolve_static_url($category['url']); ?>
                            <li><a href="<?php echo esc_url($categoryUrl); ?>" class="transition-colors hover:text-emerald-700"><?php echo esc_html($category['label']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="space-y-3 text-[13px] text-slate-600 lg:col-span-3">
                <h3 class="mb-4 text-sm font-extrabold text-slate-900"><?php echo esc_html__('اطلاعات تماس کارخانه', 'erfansanat-theme'); ?></h3>
                <div class="flex items-start gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin mt-1 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span class="leading-6"><?php echo nl2br(esc_html($address)); ?></span>
                </div>
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>
                    <a href="<?php echo esc_attr(\ErfanSanat\Theme\TemplateParts::contact_href($phone, 'tel')); ?>" class="font-bold text-slate-800 hover:text-emerald-700"><?php echo esc_html($phone); ?></a>
                </div>
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>
                    <a href="<?php echo esc_attr(\ErfanSanat\Theme\TemplateParts::contact_href($salesPhone, 'tel')); ?>" class="hover:text-emerald-700"><span><?php echo esc_html__('تلفن فروش:', 'erfansanat-theme'); ?></span> <?php echo esc_html($salesPhone); ?></a>
                </div>
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg>
                    <a href="<?php echo esc_attr(\ErfanSanat\Theme\TemplateParts::contact_href($email, 'mailto')); ?>" class="hover:text-emerald-700"><?php echo esc_html($email); ?></a>
                </div>
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M12 6v6l4 2"></path><circle cx="12" cy="12" r="10"></circle></svg>
                    <span><?php echo esc_html($hours); ?></span>
                </div>
            </div>
        </div>
        <div class="flex flex-col items-center justify-between gap-4 border-t border-slate-200 pt-6 text-xs text-slate-500 sm:flex-row">
            <div>© <?php echo esc_html($copyright); ?></div>
            <?php if (es_option('footer.show_back_to_top', true)) : ?>
                <button type="button" data-scroll-top="true" class="flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-emerald-800 transition-colors hover:bg-emerald-100 cursor-pointer">
                    <span><?php echo esc_html__('بازگشت به بالا', 'erfansanat-theme'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up h-3.5 w-3.5" aria-hidden="true"><path d="m5 12 7-7 7 7"></path><path d="M12 19V5"></path></svg>
                </button>
            <?php endif; ?>
        </div>
    </div>
</footer>
</div>
</div>
<?php wp_footer(); ?>
</body>
</html>
