<?php
/**
 * ثبت سکشن‌های قالب برگه «درباره ما» در SectionManager — ترتیب عین میرور
 * ref: uploads/static-mirror/about.html (تسک ۷.۷)
 * حالت متای خالی = رندر کامل میرور (پیش‌فرض‌های اسکیم = مقادیر میرور)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Sections;

final class AboutSections {

    /** ترتیب سکشن‌ها — عین ترتیب <section>های میرور */
    public const ORDER = [
            'page-about-hero',
            'page-about-intro',
            'page-about-iso',
            'page-about-values',
            'contact',
    ];

    public static function init(): void {
        add_filter('es_sections_registry', [self::class, 'register']);
    }

    /**
     * ثبت سکشن‌ها با اسکیم فیلدها (مقادیر پیش‌فرض = میرور)
     *
     * @param array<string,array> $registry
     * @return array<string,array>
     */
    public static function register(array $registry): array {
        $registry['about'] ??= [];

        // ── تعریف سکشن‌ها (به ترتیب میرور) ──
        $items = [];
        $items['page-about-hero'] = [
            'label'  => __('هیرو درباره ما (کرامب، معرفی، کارت‌های شناور، آمار)', 'erfansanat'),
            'schema' => [
                ['key' => 'crumb', 'type' => 'text', 'label' => __('برچسب مسیر — صفحه فعلی', 'erfansanat'), 'default' => 'درباره ما'],
                ['key' => 'badge', 'type' => 'text', 'label' => __('بج بالای عنوان', 'erfansanat'), 'default' => 'شرکت دانش‌بنیان — تأسیس ۱۳۷۸'],
                ['key' => 'h1_pre', 'type' => 'text', 'label' => __('عنوان — بخش اول', 'erfansanat'), 'default' => 'درباره شرکت'],
                ['key' => 'h1_hl', 'type' => 'text', 'label' => __('عنوان — بخش گرادیانی', 'erfansanat'), 'default' => 'عرفان صنعت اصفهان'],
                ['key' => 'quote', 'type' => 'textarea', 'label' => __('معرفی یک‌خطی (نقل‌قول)', 'erfansanat'), 'default' => '«درخشان‌تر از همیشه، آینده‌ای روشن را برای ایران می‌سازیم» — پیشگام طراحی، تولید و اجرای پروژه‌های نورپردازی شهری و صنعتی در ایران'],
                ['key' => 'brand', 'type' => 'text', 'label' => __('نام شرکت (کارت شناور پایین)', 'erfansanat'), 'default' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان'],
                ['key' => 'since', 'type' => 'text', 'label' => __('بج «از سال»', 'erfansanat'), 'default' => 'از ۱۳۷۸'],
                ['key' => 'c1_v', 'type' => 'text', 'label' => __('کارت ۱ — مقدار', 'erfansanat'), 'default' => 'ISO 9001:2015'],
                ['key' => 'c1_l', 'type' => 'text', 'label' => __('کارت ۱ — برچسب', 'erfansanat'), 'default' => 'مدیریت کیفیت بین‌المللی'],
                ['key' => 'c2_v', 'type' => 'text', 'label' => __('کارت ۲ — مقدار', 'erfansanat'), 'default' => '۱۴۰+ نیروی متخصص'],
                ['key' => 'c2_l', 'type' => 'text', 'label' => __('کارت ۲ — برچسب', 'erfansanat'), 'default' => 'تیم مهندسی و اجرایی'],
                ['key' => 'st1_v', 'type' => 'text', 'label' => __('آمار ۱ — عدد', 'erfansanat'), 'default' => '۲۵+'],
                ['key' => 'st1_l', 'type' => 'text', 'label' => __('آمار ۱ — برچسب', 'erfansanat'), 'default' => 'سال تجربه درخشان'],
                ['key' => 'st2_v', 'type' => 'text', 'label' => __('آمار ۲ — عدد', 'erfansanat'), 'default' => '۸۵۰+'],
                ['key' => 'st2_l', 'type' => 'text', 'label' => __('آمار ۲ — برچسب', 'erfansanat'), 'default' => 'پروژه تکمیل‌شده'],
                ['key' => 'st3_v', 'type' => 'text', 'label' => __('آمار ۳ — عدد', 'erfansanat'), 'default' => '۲۸۹+'],
                ['key' => 'st3_l', 'type' => 'text', 'label' => __('آمار ۳ — برچسب', 'erfansanat'), 'default' => 'مشتری فعال'],
                ['key' => 'st4_v', 'type' => 'text', 'label' => __('آمار ۴ — عدد', 'erfansanat'), 'default' => '۹۰+'],
                ['key' => 'st4_l', 'type' => 'text', 'label' => __('آمار ۴ — برچسب', 'erfansanat'), 'default' => 'محصول متنوع'],
            ],
        ];
        $items['page-about-intro'] = [
            'label'  => __('معرفی کارخانه و نوار آمار', 'erfansanat'),
            'schema' => [
                ['key' => 'fcard_v', 'type' => 'text', 'label' => __('کارت شناور — مقدار', 'erfansanat'), 'default' => 'تأسیس ۱۳۷۸'],
                ['key' => 'fcard_l', 'type' => 'text', 'label' => __('کارت شناور — برچسب', 'erfansanat'), 'default' => 'اولین تولیدکننده ریسه و تزئینات شهری در ایران'],
                ['key' => 'badge', 'type' => 'text', 'label' => __('بج «شرکت دانش‌بنیان»', 'erfansanat'), 'default' => 'شرکت دانش‌بنیان'],
                ['key' => 'h2_pre', 'type' => 'text', 'label' => __('عنوان — بخش اول', 'erfansanat'), 'default' => 'بیش از دو دهه پیشگامی در '],
                ['key' => 'h2_hl', 'type' => 'text', 'label' => __('عنوان — بخش سبز', 'erfansanat'), 'default' => 'صنعت روشنایی ایران'],
                ['key' => 'lead', 'type' => 'text', 'label' => __('سرخط معرفی', 'erfansanat'), 'default' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان'],
                ['key' => 'p1', 'type' => 'textarea', 'label' => __('پاراگراف اول', 'erfansanat'), 'default' => ' با بیش از دو دهه سابقه فعالیت، یکی از شرکت‌های پیشرو در حوزه طراحی، تولید و اجرای پروژه‌های نورپردازی شهری و المان‌های نوری در کشور به‌شمار می‌رود. ما با تکیه بر دانش فنی نیروهای متخصص، بهره‌گیری از فناوری‌های نوین و استفاده از متریال باکیفیت، در مسیر ارتقاء کیفیت بصری فضاهای شهری و ایجاد محیط‌هایی جذاب، ایمن و ماندگار گام برداشته‌ایم.'],
                ['key' => 'p2', 'type' => 'textarea', 'label' => __('پاراگراف دوم', 'erfansanat'), 'default' => 'حوزه فعالیت عرفان صنعت شامل طراحی و ساخت المان‌های نوری تزئینی، اجرای پروژه‌های نورپردازی محوطه‌ای، پارکی، خیابانی و میدانی، تولید تجهیزات تعاملی نوری و ارائه راهکارهای خلاقانه برای زیباسازی محیط‌های عمومی است.'],
                ['key' => 'st1_v', 'type' => 'text', 'label' => __('آمار ۱ — عدد', 'erfansanat'), 'default' => '۲۵+'],
                ['key' => 'st1_l', 'type' => 'text', 'label' => __('آمار ۱ — برچسب', 'erfansanat'), 'default' => 'سال تجربه درخشان'],
                ['key' => 'st2_v', 'type' => 'text', 'label' => __('آمار ۲ — عدد', 'erfansanat'), 'default' => '۸۵۰+'],
                ['key' => 'st2_l', 'type' => 'text', 'label' => __('آمار ۲ — برچسب', 'erfansanat'), 'default' => 'پروژه تکمیل‌شده'],
                ['key' => 'st3_v', 'type' => 'text', 'label' => __('آمار ۳ — عدد', 'erfansanat'), 'default' => '۲۸۹+'],
                ['key' => 'st3_l', 'type' => 'text', 'label' => __('آمار ۳ — برچسب', 'erfansanat'), 'default' => 'مشتری فعال'],
                ['key' => 'st4_v', 'type' => 'text', 'label' => __('آمار ۴ — عدد', 'erfansanat'), 'default' => '۱۴۰+'],
                ['key' => 'st4_l', 'type' => 'text', 'label' => __('آمار ۴ — برچسب', 'erfansanat'), 'default' => 'نیروی متخصص'],
            ],
        ];
        $items['page-about-iso'] = [
            'label'  => __('گواهینامه ISO 9001:2015', 'erfansanat'),
            'schema' => [
                ['key' => 'kicker', 'type' => 'text', 'label' => __('بالانویس', 'erfansanat'), 'default' => 'گواهینامه بین‌المللی'],
                ['key' => 'h2_pre', 'type' => 'text', 'label' => __('عنوان — بخش اول', 'erfansanat'), 'default' => 'گواهینامه ISO 9001:2015؛ '],
                ['key' => 'h2_hl', 'type' => 'text', 'label' => __('عنوان — بخش سبز', 'erfansanat'), 'default' => 'نشانه تعهد ما به کیفیت'],
                ['key' => 'text', 'type' => 'textarea', 'label' => __('متن توضیحی', 'erfansanat'), 'default' => 'شرکت ما با افتخار موفق به اخذ گواهینامه ISO 9001:2015 شده است. این گواهینامه بین‌المللی، مهر تأییدی است بر سیستم مدیریت کیفیت پیاده‌سازی‌شده در تمامی فرآیندهای سازمان ما؛ از طراحی و تولید تا ارائه خدمات پس از فروش، با رویکردی مشتری‌مدار و تمرکز بر بهبود مستمر.'],
                ['key' => 'b1', 'type' => 'text', 'label' => __('ویژگی ۱', 'erfansanat'), 'default' => 'سیستم مدیریت کیفیت در تمامی فرآیندها'],
                ['key' => 'b2', 'type' => 'text', 'label' => __('ویژگی ۲', 'erfansanat'), 'default' => 'رویکرد مشتری‌مدار و بهبود مستمر'],
                ['key' => 'b3', 'type' => 'text', 'label' => __('ویژگی ۳', 'erfansanat'), 'default' => 'انطباق با الزامات و استانداردهای جهانی'],
                ['key' => 'card_v', 'type' => 'text', 'label' => __('کارت ISO — مقدار', 'erfansanat'), 'default' => 'ISO 9001:2015'],
                ['key' => 'card_l', 'type' => 'text', 'label' => __('کارت ISO — برچسب', 'erfansanat'), 'default' => 'سیستم مدیریت کیفیت بین‌المللی'],
                ['key' => 'card_en', 'type' => 'text', 'label' => __('کارت ISO — عنوان انگلیسی', 'erfansanat'), 'default' => 'Quality Management System'],
                ['key' => 'card_badge', 'type' => 'text', 'label' => __('کارت ISO — بج پایین', 'erfansanat'), 'default' => 'دارای اعتبار رسمی'],
            ],
        ];
        $items['page-about-values'] = [
            'label'  => __('اصول بنیادین و نقشه ایران', 'erfansanat'),
            'schema' => [
                ['key' => 'h2_pre', 'type' => 'text', 'label' => __('عنوان — بخش اول', 'erfansanat'), 'default' => 'اصول بنیادین '],
                ['key' => 'h2_hl', 'type' => 'text', 'label' => __('عنوان — بخش سبز', 'erfansanat'), 'default' => 'عرفان صنعت'],
                ['key' => 'c1_t', 'type' => 'text', 'label' => __('ارزش ۱ — عنوان', 'erfansanat'), 'default' => 'تعهد به کیفیت'],
                ['key' => 'c1_d', 'type' => 'text', 'label' => __('ارزش ۱ — توضیح', 'erfansanat'), 'default' => 'رعایت سخت‌گیرانه استانداردهای فنی در تمام مراحل تولید'],
                ['key' => 'c2_t', 'type' => 'text', 'label' => __('ارزش ۲ — عنوان', 'erfansanat'), 'default' => 'نوآوری در طراحی'],
                ['key' => 'c2_d', 'type' => 'text', 'label' => __('ارزش ۲ — توضیح', 'erfansanat'), 'default' => 'خلق المان‌های نوری منحصربه‌فرد با فناوری‌های نوین'],
                ['key' => 'c3_t', 'type' => 'text', 'label' => __('ارزش ۳ — عنوان', 'erfansanat'), 'default' => 'استانداردهای زیست‌محیطی'],
                ['key' => 'c3_d', 'type' => 'text', 'label' => __('ارزش ۳ — توضیح', 'erfansanat'), 'default' => 'چیپ‌های LED پربازده و مصرف انرژی بهینه'],
                ['key' => 'c4_t', 'type' => 'text', 'label' => __('ارزش ۴ — عنوان', 'erfansanat'), 'default' => 'مشتری‌مداری'],
                ['key' => 'c4_d', 'type' => 'text', 'label' => __('ارزش ۴ — توضیح', 'erfansanat'), 'default' => 'از مشاوره رایگان تا ۵ سال خدمات پس از فروش'],
                ['key' => 'map_t', 'type' => 'text', 'label' => __('نقشه ایران — عنوان', 'erfansanat'), 'default' => 'درخشش در جای‌جای ایران و فراتر از مرزها'],
                ['key' => 'map_d', 'type' => 'textarea', 'label' => __('نقشه ایران — توضیح', 'erfansanat'), 'default' => 'درخشش محصولات عرفان صنعت در سراسر ایران، گواهی بر تعهد بی‌چون و چرای ما به کیفیت، نوآوری و زیبایی است. از کلان‌شهرهای پویا تا شهرهای کوچک و صمیمی، نور عرفان صنعت نمادی از پیشرفت، توسعه پایدار و تعهد به آینده‌ای روشن‌تر است. اعتماد شما بزرگ‌ترین سرمایه ماست و تمامی محصولات ما با ۲۴ ماه گارانتی و ۵ سال خدمات پس از فروش ارائه می‌شوند.'],
                ['key' => 'cta', 'type' => 'text', 'label' => __('متن دکمه', 'erfansanat'), 'default' => 'مشاهده پروژه‌ها'],
                ['key' => 'cta_url', 'type' => 'url', 'label' => __('لینک دکمه', 'erfansanat'), 'default' => '/projects/'],
            ],
        ];
        $items['contact'] = [
            'label'  => __('تماس با ما (دعوت به مشاوره — مشترک با صفحه اصلی)', 'erfansanat'),
            'schema' => \ErfanSanat\Theme\Sections\HomeSections::contact_schema(),
        ];
        foreach ($items as $id => $def) {
            $registry['about'][] = [
                'id'     => (string) $id,
                'label'  => (string) $def['label'],
                'schema' => is_array($def['schema'] ?? null) ? $def['schema'] : [],
            ];
        }
        return $registry;
    }
}
