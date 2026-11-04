<?php
/**
 * فوتر — بازسازی ساختار میرور با داده داینامیک
 *
 * منبع مارک‌آپ: temp/phase-04-header-footer-navigation/refs/footer-ref.html (byte-exact)
 * ستون دسترسی سریع: منوی «footer» → fallback آپشن footer.quick_links (۸ لینک فاز ۲)
 * ستون دسته‌بندی: منوی «tolidat-sherkat» → fallback product_cat
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Nav;

final class Footer {

    private const F_OPEN = <<<'ESFRAG5T'
<footer class="relative overflow-hidden border-t border-emerald-100 bg-white"><div class="pointer-events-none absolute inset-0 grid-Floor opacity-50"></div><div class="pointer-events-none absolute -top-40 right-1/3 h-80 w-80 rounded-full bg-emerald-400/10 blur-[130px]"></div><div class="relative mx-auto max-w-[1300px] px-5 pt-20 pb-10 lg:px-8"><div class="grid gap-10 pb-14 md:grid-cols-2 lg:grid-cols-12">
ESFRAG5T;

    private const BRAND = <<<'ESFRAG5T'
<div class="space-y-4 lg:col-span-4"><div class="flex items-center gap-3"><div class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-200 bg-emerald-50"><span class="text-sm font-black text-emerald-700">ESE</span></div><div><div class="text-base font-extrabold text-slate-900">عرفان صنعت اصفهان</div><div class="text-xs text-emerald-700">شرکت دانش‌بنیان نورپردازی شهری و صنعتی</div></div></div><p class="text-[13px] leading-7 text-slate-600">طراح و تولیدکننده تجهیزات نورپردازی پیشرفته، المان‌های نوری تزئینی، درختان نوری هوشمند، خورشیدنماها، ریسه‌های LED (بلوطی، فندقی، سوزنی IP68) و پوینت لایت پیکسل با ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش.</p><div class="flex flex-wrap gap-2 pt-1 text-[11px] font-bold text-emerald-800"><span class="rounded-full bg-emerald-50 px-3 py-1">ISO 9001:2015</span><span class="rounded-full bg-emerald-50 px-3 py-1">IP67 / IP68</span><span class="rounded-full bg-emerald-50 px-3 py-1">کد بین‌المللی GS1</span><span class="rounded-full bg-emerald-50 px-3 py-1">عرضه در سامانه ستاد</span></div></div>
ESFRAG5T;

    private const F_QUICK = <<<'ESFRAG5T'
<div class="lg:col-span-2"><h3 class="mb-4 text-sm font-extrabold text-slate-900">دسترسی سریع</h3><ul class="space-y-2.5 text-[13px] text-slate-600">{ES_QUICK_LINKS}</ul></div>
ESFRAG5T;

    private const QLI = <<<'ESFRAG5T'
<li><a href="{ES_HREF}" class="transition-colors hover:text-emerald-700">{ES_LABEL}</a></li>
ESFRAG5T;

    private const F_CATS = <<<'ESFRAG5T'
<div class="lg:col-span-3"><h3 class="mb-4 text-sm font-extrabold text-slate-900">دسته‌بندی محصولات</h3><ul class="space-y-2.5 text-[13px] text-slate-600">{ES_CAT_LINKS}</ul></div>
ESFRAG5T;

    private const CLI = <<<'ESFRAG5T'
<li><a href="{ES_HREF}" class="transition-colors hover:text-emerald-700">{ES_LABEL}</a></li>
ESFRAG5T;

    private const F_CONTACT = <<<'ESFRAG5T'
<div class="space-y-3 text-[13px] text-slate-600 lg:col-span-3"><h3 class="mb-4 text-sm font-extrabold text-slate-900">اطلاعات تماس کارخانه</h3><div class="flex items-start gap-2.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin mt-1 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg><span class="leading-6">{ES_ADDRESS}</span></div><div class="flex items-center gap-2.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><a href="{ES_TEL_URL}" class="font-bold text-slate-800 hover:text-emerald-700">{ES_PHONE_DISPLAY}</a></div><div class="flex items-center gap-2.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><a href="{ES_SALES_URL}" class="hover:text-emerald-700">{ES_SALES_DISPLAY}</a></div><div class="flex items-center gap-2.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg><a href="{ES_MAIL_URL}" class="hover:text-emerald-700">{ES_EMAIL}</a></div><div class="flex items-center gap-2.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M12 6v6l4 2"></path><circle cx="12" cy="12" r="10"></circle></svg><span>{ES_HOURS}</span></div></div></div>
ESFRAG5T;

    private const F_BAR = <<<'ESFRAG5T'
<div class="flex flex-col items-center justify-between gap-4 border-t border-slate-200 pt-6 text-xs text-slate-500 sm:flex-row"><div>{ES_COPYRIGHT}</div><button type="button" data-scroll-top="true" class="flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-emerald-800 transition-colors hover:bg-emerald-100 cursor-pointer"><span>بازگشت به بالا</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up h-3.5 w-3.5" aria-hidden="true"><path d="m5 12 7-7 7 7"></path><path d="M12 19V5"></path></svg></button></div></div></footer>
ESFRAG5T;


    /** خروجی کامل فوتر (تگ <footer>…</footer>) */
    public static function output(): void {
        echo self::render();
    }

    public static function render(): string {
        $contact = \ErfanSanat\Theme\Admin\OptionsOutput::contact_args();

        $out = self::F_OPEN;
        $out .= self::BRAND;

        // ── دسترسی سریع ──
        $quick = self::menu_links('footer');
        if ($quick === []) {
            $quick = self::quick_links_from_options();
        }
        $quick_html = '';
        foreach ($quick as $link) {
            $quick_html .= strtr(self::QLI, ['{ES_HREF}' => esc_url((string) $link['href']), '{ES_LABEL}' => esc_html((string) $link['label'])]);
        }
        $out .= str_replace('{ES_QUICK_LINKS}', $quick_html, self::F_QUICK);

        // ── دسته‌بندی محصولات ──
        $cats = self::menu_links('tolidat-sherkat');
        if ($cats === []) {
            foreach (get_terms(['taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => false]) as $term) {
                if (!is_object($term)) {
                    continue;
                }
                $cats[] = ['label' => (string) $term->name, 'href' => (string) get_term_link($term)];
            }
        }
        $cats_html = '';
        foreach ($cats as $link) {
            $cats_html .= strtr(self::CLI, ['{ES_HREF}' => esc_url((string) $link['href']), '{ES_LABEL}' => esc_html((string) $link['label'])]);
        }
        $out .= str_replace('{ES_CAT_LINKS}', $cats_html, self::F_CATS);

        // ── اطلاعات تماس ──
        $address = (string) ($contact['address'] ?? '');
        $out .= strtr(self::F_CONTACT, [
            '{ES_ADDRESS}'        => esc_html($address !== '' ? $address : 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹'),
            '{ES_TEL_URL}'        => 'tel:' . rawurlencode((string) ($contact['phone'] ?? '')),
            '{ES_PHONE_DISPLAY}'  => esc_html((string) ($contact['phone_display'] ?? '')),
            '{ES_SALES_URL}'      => 'tel:' . rawurlencode((string) ($contact['sales_phone'] ?? '')),
            '{ES_SALES_DISPLAY}'  => esc_html((string) ($contact['sales_display'] ?? '')),
            '{ES_MAIL_URL}'       => 'mailto:' . rawurlencode((string) ($contact['email'] ?? '')),
            '{ES_EMAIL}'          => esc_html((string) ($contact['email'] ?? '')),
            '{ES_HOURS}'          => esc_html((string) ($contact['hours'] ?? '')),
        ]);

        // ── نوار پایانی ──
        $copyright = (string) es_option('footer.copyright', '');
        $out .= str_replace('{ES_COPYRIGHT}', esc_html($copyright !== '' ? $copyright : '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.'), self::F_BAR);

        return $out;
    }

    /**
     * لینک‌های یک موقعیت منو (تک‌سطح)
     *
     * @return list<array{label:string,href:string}>
     */
    private static function menu_links(string $location): array {
        $items = wp_get_nav_menu_items($location);
        if (!is_array($items)) {
            return [];
        }
        $out = [];
        foreach ($items as $item) {
            if ((int) ($item->menu_item_parent ?? 0) === 0) {
                $out[] = ['label' => (string) $item->title, 'href' => (string) $item->url];
            }
        }
        return $out;
    }

    /**
     * fallback آپشن footer.quick_links (Repeater فاز ۲)
     *
     * @return list<array{label:string,href:string}>
     */
    private static function quick_links_from_options(): array {
        $raw = es_option('footer.quick_links', []);
        $out = [];
        if (is_array($raw)) {
            foreach ($raw as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $label = (string) ($row['label'] ?? '');
                $href  = (string) ($row['url'] ?? '');
                if ($label !== '' && $href !== '') {
                    // مسیرهای نسبی ذخیره‌شده در داشبورد → URL کامل
                    if (str_starts_with($href, '/')) {
                        $href = home_url($href);
                    }
                    $out[] = ['label' => $label, 'href' => $href];
                }
            }
        }
        return $out;
    }
}
