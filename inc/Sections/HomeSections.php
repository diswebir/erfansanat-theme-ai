<?php
/**
 * ثبت سکشن‌های صفحه اصلی در SectionManager — ترتیب پیش‌فرض عین میرور index.html
 *
 * refها: temp/phase-05-homepage-and-sections/refs/*.html (۱۵ سکشن سطح‌بالا، byte-exact)
 * حالت متای خالی = رندر کامل میرور (پیش‌فرض‌های اسکیم = مقادیر میرور)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Sections;

final class HomeSections {

    /** ترتیب سکشن‌ها — عین ترتیب <section>های سطح‌بالای index.html */
    public const ORDER = [
        'hero-home1',
        'projects',
        'projects-gallery',
        'about',
        'about-intro',
        'history',
        'categories',
        'catalog',
        'services',
        'quality',
        'aftersale',
        'customers',
        'blog-preview',
        'contact',
        'map',
    ];

    public static function init(): void {
        add_filter('es_sections_registry', [self::class, 'register']);
    }

    /**
     * ثبت همه سکشن‌ها با اسکیم فیلدها (مقادیر پیش‌فرض = میرور)
     *
     * @param array<string,array> $registry
     * @return array<string,array>
     */
    public static function register(array $registry): array {
        $items = [];
        $registry['page'] ??= [];

        // ── تعریف سکشن‌ها (به ترتیب میرور) ──
        $items['hero-home1'] = [
            'label'  => __('هیرو — طرح ۱ (نوتچ و صحنه‌ها)', 'erfansanat'),
            'schema' => self::hero_schema(),
        ];
        $items['projects'] = [
            'label'  => __('پروژه‌های شاخص', 'erfansanat'),
            'schema' => [
                ['key' => 'kicker', 'type' => 'text', 'label' => __('بالانویس', 'erfansanat'), 'default' => 'نمونه‌کارهای شاخص'],
                ['key' => 'title_highlight', 'type' => 'text', 'label' => __('عنوان (بخش سبز بعد از «پروژه‌های»)', 'erfansanat'), 'default' => 'نورپردازی شهری'],
                ['key' => 'excerpt', 'type' => 'textarea', 'label' => __('متن معرفی', 'erfansanat'), 'default' => 'اجرای تخصصی پروژه‌های نورپردازی برای شهرداری‌ها و سازمان‌های زیباسازی؛ تلفیق مهندسی سازه، استاندارد حفاظتی IP68 و زیبایی‌شناسی شهری.'],
                ['key' => 'count', 'type' => 'number', 'label' => __('تعداد پروژه', 'erfansanat'), 'default' => 3],
                ['key' => 'handpicked', 'type' => 'text', 'label' => __('آیدی پروژه‌های انتخابی (با کاما — اختیاری)', 'erfansanat'), 'default' => ''],
            ],
        ];
        $items['projects-gallery'] = [
            'label'  => __('گالری نمونه‌کارها', 'erfansanat'),
            'schema' => [
                ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => 'گالری نمونه‌کارها'],
                ['key' => 'count', 'type' => 'number', 'label' => __('تعداد پروژه', 'erfansanat'), 'default' => 6],
            ],
        ];
        $items['about'] = [
            'label'  => __('درباره شرکت + آمار', 'erfansanat'),
            'schema' => self::about_schema(),
        ];
        $items['about-intro'] = [
            'label'  => __('معرفی تاسیس شرکت', 'erfansanat'),
            'schema' => [
                ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => ''],
                ['key' => 'body', 'type' => 'textarea', 'label' => __('متن معرفی', 'erfansanat'), 'default' => ''],
                ['key' => 'image', 'type' => 'media', 'label' => __('تصویر', 'erfansanat'), 'default' => ''],
                ['key' => 'link_text', 'type' => 'text', 'label' => __('متن لینک', 'erfansanat'), 'default' => ''],
                ['key' => 'link_url', 'type' => 'url', 'label' => __('لینک', 'erfansanat'), 'default' => ''],
            ],
        ];
        $items['history'] = [
            'label'  => __('تایم‌لاین تاریخچه', 'erfansanat'),
            'schema' => self::history_schema(),
        ];
        $items['categories'] = [
            'label'  => __('دسته‌بندی محصولات', 'erfansanat'),
            'schema' => [
                ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => 'دسته‌بندی تولیدات'],
                ['key' => 'count', 'type' => 'number', 'label' => __('تعداد دسته', 'erfansanat'), 'default' => 12],
                ['key' => 'terms', 'type' => 'text', 'label' => __('اسلاگ دسته‌های انتخابی (با کاما — خالی = همه)', 'erfansanat'), 'default' => ''],
            ],
        ];
        $items['catalog'] = [
            'label'  => __('دانلود کاتالوگ جامع', 'erfansanat'),
            'schema' => [
                ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => ''],
                ['key' => 'body', 'type' => 'textarea', 'label' => __('متن دعوت', 'erfansanat'), 'default' => ''],
                ['key' => 'file', 'type' => 'media', 'label' => __('فایل PDF کاتالوگ', 'erfansanat'), 'default' => ''],
                ['key' => 'button_text', 'type' => 'text', 'label' => __('متن دکمه', 'erfansanat'), 'default' => ''],
            ],
        ];
        $items['services'] = [
            'label'  => __('خدمات تخصصی (تب‌دار)', 'erfansanat'),
            'schema' => self::services_schema(),
        ];
        $items['quality'] = [
            'label'  => __('مزیت‌های رقابتی کارخانه', 'erfansanat'),
            'schema' => self::quality_schema(),
        ];
        $items['aftersale'] = [
            'label'  => __('خدمات پس از فروش', 'erfansanat'),
            'schema' => [
                ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => ''],
                ['key' => 'body', 'type' => 'textarea', 'label' => __('متن', 'erfansanat'), 'default' => ''],
                ['key' => 'link_text', 'type' => 'text', 'label' => __('متن لینک گارانتی', 'erfansanat'), 'default' => ''],
                ['key' => 'link_url', 'type' => 'url', 'label' => __('لینک گارانتی', 'erfansanat'), 'default' => ''],
            ],
        ];
        $items['customers'] = [
            'label'  => __('مشتریان و سازمان‌های همکار', 'erfansanat'),
            'schema' => [
                ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => ''],
                [
                    'key' => 'logos', 'type' => 'repeater', 'label' => __('لوگوی مشتریان', 'erfansanat'), 'add_label' => __('افزودن لوگو', 'erfansanat'),
                    'fields' => [
                        ['key' => 'name', 'type' => 'text', 'label' => __('نام سازمان', 'erfansanat')],
                        ['key' => 'image', 'type' => 'media', 'label' => __('لوگو', 'erfansanat')],
                        ['key' => 'url', 'type' => 'url', 'label' => __('لینک (اختیاری)', 'erfansanat')],
                    ],
                    'default' => [],
                ],
            ],
        ];
        $items['blog-preview'] = [
            'label'  => __('ویترین مقالات', 'erfansanat'),
            'schema' => [
                ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => ''],
                ['key' => 'count', 'type' => 'number', 'label' => __('تعداد مقاله', 'erfansanat'), 'default' => 3],
            ],
        ];
        $items['contact'] = [
            'label'  => __('تماس با ما', 'erfansanat'),
            'schema' => [
                ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => ''],
                ['key' => 'body', 'type' => 'textarea', 'label' => __('متن دعوت', 'erfansanat'), 'default' => ''],
                ['key' => 'button_text', 'type' => 'text', 'label' => __('متن دکمه استعلام', 'erfansanat'), 'default' => ''],
            ],
        ];
        $items['map'] = [
            'label'  => __('نقشه کارخانه و پشتیبانی', 'erfansanat'),
            'schema' => [
                ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => ''],
                ['key' => 'embed', 'type' => 'textarea', 'label' => __('کد iframe نقشه (گوگل/نشان)', 'erfansanat'), 'default' => ''],
                ['key' => 'image', 'type' => 'media', 'label' => __('تصویر جایگزین نقشه', 'erfansanat'), 'default' => ''],
            ],
        ];
        foreach ($items as $id => $def) {
            $registry['page'][] = [
                'id'     => (string) $id,
                'label'  => (string) $def['label'],
                'schema' => is_array($def['schema'] ?? null) ? $def['schema'] : [],
            ];
        }
        return $registry;
    }

    /** تعاریف مسطح (id => def) — برای تست‌ها و خوانش مستقیم */
    public static function definitions(): array {
        $items = [];
        foreach ((array) apply_filters('es_sections_registry', [])['page'] ?? [] as $item) {
            if (is_array($item) && isset($item['id'])) {
                $items[(string) $item['id']] = $item;
            }
        }
        return $items;
    }

    /** اسکیم هیرو: رپیتر صحنه‌ها + ویدیو */
    private static function hero_schema(): array {
        return [
            ['key' => 'video', 'type' => 'media', 'label' => __('ویدیوی درون‌خطی', 'erfansanat'), 'default' => 'assets/media/hero-video.mp4'],
            ['key' => 'kicker', 'type' => 'text', 'label' => __('بالانویس نوتچ', 'erfansanat'), 'default' => 'المان‌های نوری شهری · استاندارد IP68'],
            ['key' => 'title', 'type' => 'text', 'label' => __('عنوان نوتچ (h1)', 'erfansanat'), 'default' => 'وقتی کیفیت و ماندگاری مهمه'],
            ['key' => 'brand_line', 'type' => 'text', 'label' => __('نوار برند نوتچ', 'erfansanat'), 'default' => 'عرفان صنعت انتخاب توئه...'],
            ['key' => 'video_scene', 'type' => 'number', 'label' => __('اندیس صحنه ویدیویی', 'erfansanat'), 'default' => 0],
            [
                'key' => 'scenes', 'type' => 'repeater', 'label' => __('صحنه‌های هیرو', 'erfansanat'), 'add_label' => __('افزودن صحنه', 'erfansanat'),
                'fields' => [
                    ['key' => 'name', 'type' => 'text', 'label' => __('نام صحنه (دکمه)', 'erfansanat')],
                    ['key' => 'image', 'type' => 'media', 'label' => __('تصویر پس‌زمینه', 'erfansanat')],
                    ['key' => 'title', 'type' => 'text', 'label' => __('عنوان صحنه', 'erfansanat')],
                    ['key' => 'cta_text', 'type' => 'text', 'label' => __('متن CTA', 'erfansanat')],
                    ['key' => 'cta_url', 'type' => 'url', 'label' => __('لینک CTA', 'erfansanat')],
                ],
                'default' => [
                    ['name' => 'معرفی شرکت', 'image' => 'assets/images/hero.jpg', 'title' => 'تیزر رسمی خط تولید و پروژه‌های عرفان صنعت اصفهان', 'cta_text' => 'درباره کارخانه', 'cta_url' => '/about/'],
                    ['name' => 'استان البرز', 'image' => 'assets/images/light-tunnel.jpg', 'title' => 'تونل نوری نیم‌آرک پارک جهان‌نما', 'cta_text' => 'مشاهده پروژه', 'cta_url' => '/projects/half-arch-tunnel-alborz/'],
                    ['name' => 'استان اصفهان', 'image' => 'assets/images/sun-light.jpg', 'title' => 'آذین‌بندی نوری میدان و بلوار شهدا اصفهان', 'cta_text' => 'مشاهده پروژه', 'cta_url' => '/projects/esfahan-shohada-square/'],
                    ['name' => 'استان خراسان رضوی', 'image' => 'assets/images/light-tree.jpg', 'title' => 'درختان نوری هوشمند آرتام مشهد', 'cta_text' => 'مشاهده پروژه', 'cta_url' => '/projects/artam-tree-midan/'],
                    ['name' => 'استان خوزستان', 'image' => 'assets/images/light-sphere.jpg', 'title' => 'المان حجمی و نیم‌هرم نوری اهواز', 'cta_text' => 'مشاهده پروژه', 'cta_url' => '/projects/ahvaz-semi-pyramid-light/'],
                ],
            ],
            [
                'key' => 'strip_items', 'type' => 'repeater', 'label' => __('نوار پربازدیدترین دسته‌بندی‌ها', 'erfansanat'), 'add_label' => __('افزودن کارت', 'erfansanat'),
                'fields' => [
                    ['key' => 'icon', 'type' => 'text', 'label' => __('نام آیکون lucide', 'erfansanat')],
                    ['key' => 'name', 'type' => 'text', 'label' => __('نام دسته', 'erfansanat')],
                    ['key' => 'sub', 'type' => 'text', 'label' => __('زیرعنوان', 'erfansanat')],
                    ['key' => 'count_text', 'type' => 'text', 'label' => __('متن شمارنده', 'erfansanat')],
                    ['key' => 'url', 'type' => 'url', 'label' => __('لینک', 'erfansanat')],
                ],
                'default' => [
                    ['icon' => 'cpu', 'name' => 'پوینت لایت پیکسل', 'sub' => '۴ و ۶ سانتی پلی‌کربنات', 'count_text' => '۱۲ مدل', 'url' => '/product_cat/point-light/'],
                    ['icon' => 'sparkles', 'name' => 'ریسه سوزنی IP68', 'sub' => 'تزریق مستقیم ضدآب', 'count_text' => '۸ رنگ', 'url' => '/product_cat/needle-thread/'],
                    ['icon' => 'trees', 'name' => 'درخت نوری هوشمند', 'sub' => 'مدل‌های دلسا و آرتام', 'count_text' => '۱۰ مدل', 'url' => '/product_cat/light-tree/'],
                    ['icon' => 'lightbulb', 'name' => 'ریسه بلوطی و فندقی', 'sub' => 'هفت‌رنگ و فول‌کالر', 'count_text' => '۱۴ مدل', 'url' => '/product_cat/acorn-thread/'],
                    ['icon' => 'waves', 'name' => 'والواشر و لینیرلایت', 'sub' => 'نورپردازی نمای ساختمان و پل', 'count_text' => '۱۸ مدل', 'url' => '/product_cat/wall-washer/'],
                    ['icon' => 'sun', 'name' => 'المان خورشیدنما', 'sub' => '۳۲ و ۴۸ پره فول‌کالر', 'count_text' => '۶ مدل', 'url' => '/product_cat/sun-light-elements/'],
                    ['icon' => 'flame', 'name' => 'لوستر و المان شهری', 'sub' => 'آذین‌بندی بلوار و میادین', 'count_text' => '۱۵ مدل', 'url' => '/product_cat/light-chandelier/'],
                ],
            ],
            [
                'key' => 'quick_links', 'type' => 'repeater', 'label' => __('چیپ‌های ابری (حداکثر ۸)', 'erfansanat'), 'add_label' => __('افزودن چیپ', 'erfansanat'),
                'fields' => [
                    ['key' => 'label', 'type' => 'text', 'label' => __('برچسب', 'erfansanat')],
                    ['key' => 'url', 'type' => 'url', 'label' => __('لینک', 'erfansanat')],
                ],
                'default' => [
                    ['label' => 'نورپردازی شهری', 'url' => '/projects/'],
                    ['label' => 'تونل نوری', 'url' => '/project_type/light-tunnel/'],
                    ['label' => 'درخت نوری', 'url' => '/product_cat/light-tree/'],
                    ['label' => 'المان میادین', 'url' => '/product_cat/urban-lighting-elements/'],
                    ['label' => 'ریسه ضدآب IP68', 'url' => '/product_cat/needle-thread/'],
                    ['label' => 'پوینت لایت پیکسل', 'url' => '/product_cat/point-light/'],
                    ['label' => 'والواشر خطی', 'url' => '/product_cat/wall-washer/'],
                    ['label' => 'فول‌کالر هوشمند', 'url' => '/product_cat/hazelnut-thread/'],
                ],
            ],
        ];
    }

    /** اسکیم درباره: فیلدها + رپیتر آمار */
    private static function about_schema(): array {
        return [
            ['key' => 'kicker', 'type' => 'text', 'label' => __('بالانویس', 'erfansanat'), 'default' => 'درباره شرکت دانش‌بنیان عرفان صنعت اصفهان'],
            ['key' => 'title_highlight', 'type' => 'text', 'label' => __('بخش سبز عنوان', 'erfansanat'), 'default' => '۲۵ سال'],
            ['key' => 'excerpt', 'type' => 'textarea', 'label' => __('متن معرفی', 'erfansanat'), 'default' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE) با تمرکز بر طراحی و تولید صنعتی تجهیزات روشنایی و المان‌های نوری شهری، طیف کاملی از محصولات استاندارد را به شهرداری‌ها، سازمان‌های زیباسازی و پیمانکاران عمرانی ارائه می‌دهد. تمامی تولیدات کارخانه با عایق‌بندی تزریق مستقیم IP68 و سازگاری کامل با اقلیم‌های مختلف کشور تولید می‌شوند.'],
            [
                'key' => 'points', 'type' => 'repeater', 'label' => __('چک‌لیست', 'erfansanat'), 'add_label' => __('افزودن مورد', 'erfansanat'),
                'fields' => [
                    ['key' => 'text', 'type' => 'text', 'label' => __('متن', 'erfansanat')],
                ],
                'default' => [
                    ['text' => 'طراحی، تولید و اجرای صفر تا صد پروژه‌های روشنایی شهری و میادین'],
                    ['text' => 'دارای گواهینامه بین‌المللی ISO 9001:2015 و ثبت رسمی در سامانه ستاد'],
                    ['text' => '۲۴ ماه گارانتی تعویض بی‌قیدوشرط کارخانه + ۵ سال خدمات پس از فروش'],
                ],
            ],
            ['key' => 'cta_text', 'type' => 'text', 'label' => __('متن دکمه', 'erfansanat'), 'default' => 'آشنایی با خط تولید کارخانه'],
            ['key' => 'cta_url', 'type' => 'url', 'label' => __('لینک دکمه', 'erfansanat'), 'default' => '/about/'],
            ['key' => 'image', 'type' => 'media', 'label' => __('تصویر کارخانه', 'erfansanat'), 'default' => 'assets/images/factory.jpg'],
            ['key' => 'image_alt', 'type' => 'text', 'label' => __('alt تصویر', 'erfansanat'), 'default' => 'کارخانه و خط تولید عرفان صنعت اصفهان'],
            ['key' => 'image_caption', 'type' => 'text', 'label' => __('زیرنویس تصویر', 'erfansanat'), 'default' => 'خط تولید صنعتی · شهرک صنعتی جی اصفهان'],
            ['key' => 'image_title', 'type' => 'text', 'label' => __('عنوان روی تصویر', 'erfansanat'), 'default' => 'تولید تخصصی المان‌های نوری و ریسه‌های تزریق مستقیم'],
            [
                'key' => 'stats', 'type' => 'repeater', 'label' => __('آمار شمارنده', 'erfansanat'), 'add_label' => __('افزودن آمار', 'erfansanat'),
                'fields' => [
                    ['key' => 'target', 'type' => 'text', 'label' => __('عدد هدف', 'erfansanat')],
                    ['key' => 'suffix', 'type' => 'text', 'label' => __('پسوند (+ یا ٪)', 'erfansanat')],
                    ['key' => 'label', 'type' => 'text', 'label' => __('برچسب', 'erfansanat')],
                    ['key' => 'sub', 'type' => 'text', 'label' => __('زیربرچسب', 'erfansanat')],
                ],
                'default' => [
                    ['target' => '850', 'suffix' => '+', 'label' => 'پروژه تکمیل‌شده', 'sub' => 'در کلان‌شهرهای ایران و عراق'],
                    ['target' => '100', 'suffix' => '+', 'label' => 'نیروی متخصص', 'sub' => 'واحد طراحی، قالب‌سازی و مونتاژ'],
                    ['target' => '289', 'suffix' => '+', 'label' => 'کارفرمای سازمانی', 'sub' => 'شهرداری‌ها و سازمان‌های عمرانی'],
                    ['target' => '125', 'suffix' => '+', 'label' => 'محصول تخصصی', 'sub' => 'دارای کد ملی کالا GS1'],
                ],
            ],
        ];
    }

    /** اسکیم تایم‌لاین: رپیتر مراحل */
    private static function history_schema(): array {
        return [
            ['key' => 'kicker', 'type' => 'text', 'label' => __('بالانویس', 'erfansanat'), 'default' => 'مسیر رشد و تکامل صنعتی'],
            ['key' => 'title_highlight', 'type' => 'text', 'label' => __('بخش سبز عنوان', 'erfansanat'), 'default' => 'عرفان صنعت اصفهان (۱۳۸۰ تا ۱۴۰۵)'],
            ['key' => 'excerpt', 'type' => 'textarea', 'label' => __('متن معرفی', 'erfansanat'), 'default' => 'بیش از دو دهه مهندسی و بومی‌سازی؛ از تاسیس کارخانه در سال ۱۳۸۰ تا عبور از ۸۵۰ پروژه شهری و ورود به صنعت نیروگاه‌های خورشیدی با برند «سانیرما» در سال ۱۴۰۵'],
            [
                'key' => 'stages', 'type' => 'repeater', 'label' => __('مراحل تایم‌لاین', 'erfansanat'), 'add_label' => __('افزودن مرحله', 'erfansanat'),
                'fields' => [
                    ['key' => 'year', 'type' => 'text', 'label' => __('سال', 'erfansanat')],
                    ['key' => 'name', 'type' => 'text', 'label' => __('نام کوتاه (دکمه)', 'erfansanat')],
                    ['key' => 'badge', 'type' => 'text', 'label' => __('نشان', 'erfansanat')],
                    ['key' => 'title', 'type' => 'text', 'label' => __('عنوان مرحله', 'erfansanat')],
                    ['key' => 'subline', 'type' => 'text', 'label' => __('زیرعنوان', 'erfansanat')],
                    ['key' => 'body', 'type' => 'textarea', 'label' => __('متن', 'erfansanat')],
                    ['key' => 's1_label', 'type' => 'text', 'label' => __('ویژگی ۱ — برچسب', 'erfansanat')],
                    ['key' => 's1_value', 'type' => 'text', 'label' => __('ویژگی ۱ — مقدار', 'erfansanat')],
                    ['key' => 's2_label', 'type' => 'text', 'label' => __('ویژگی ۲ — برچسب', 'erfansanat')],
                    ['key' => 's2_value', 'type' => 'text', 'label' => __('ویژگی ۲ — مقدار', 'erfansanat')],
                    ['key' => 'hint', 'type' => 'text', 'label' => __('راهنمای پیل', 'erfansanat')],
                    ['key' => 'caption', 'type' => 'text', 'label' => __('کپشن تصویر', 'erfansanat')],
                    ['key' => 'image', 'type' => 'media', 'label' => __('تصویر', 'erfansanat')],
                    ['key' => 'image_alt', 'type' => 'text', 'label' => __('alt تصویر', 'erfansanat')],
                ],
                'default' => [
                    ['year' => '۱۳۸۰', 'name' => 'فاز تاسیس صنعتی', 'badge' => 'فاز تاسیس صنعتی', 'title' => 'تاسیس مجموعه و آغاز تولید صنعتی', 'subline' => 'شروع یک حرکت مهندسی هدفمند در صنعت روشنایی اصفهان', 'body' => 'شرکت عرفان صنعت اصفهان فعالیت تخصصی خود را با هدف بومی‌سازی تجهیزات روشنایی و سازه‌های نوری شهری آغاز کرد. تمرکز بر کیفیت عایق‌بندی و طول عمر بالا از همان روز نخست، پایه‌گذار اعتماد شهرداری‌های سراسر کشور به محصولات ESE شد.', 's1_label' => 'ظرفیت خط تولید', 's1_value' => 'فاز نخست صنعتی', 's2_label' => 'حوزه تخصصی', 's2_value' => 'المان‌های نوری شهری', 'hint' => 'با اسکرول به پایین سال بعد را ببینید', 'caption' => 'دستاوردهای اجرایی سال ۱۳۸۰', 'image' => 'assets/images/hero.jpg', 'image_alt' => 'تاسیس مجموعه و آغاز تولید صنعتی'],
                    ['year' => '۱۳۹۲', 'name' => 'استاندارد IP68', 'badge' => 'استاندارد IP68', 'title' => 'راه‌اندازی خط تزریق مستقیم ریسه‌های IP68', 'subline' => 'تولید انبوه ریسه‌های سوزنی، بلوطی و فندقی ضدآب', 'body' => 'با تجهیز کارخانه به ماشین‌آلات تمام‌اتوماتیک تزریق PVC و پلی‌کربنات، تولید ریسه‌های ال‌ای‌دی با استاندارد واقعی IP68 آغاز گردید تا مشکل نفوذ آب و سوختن لامپ‌ها در فضای باز شهری برای همیشه برطرف شود.', 's1_label' => 'درجه حفاظت', 's1_value' => 'استاندارد واقعی IP68', 's2_label' => 'تکنولوژی ساخت', 's2_value' => 'تزریق مستقیم پلیمری', 'hint' => 'با اسکرول به پایین سال بعد را ببینید', 'caption' => 'دستاوردهای اجرایی سال ۱۳۹۲', 'image' => 'assets/images/light-tunnel.jpg', 'image_alt' => 'راه‌اندازی خط تزریق مستقیم ریسه‌های IP68'],
                    ['year' => '۱۳۹۷', 'name' => 'هوشمندسازی NRF', 'badge' => 'هوشمندسازی NRF', 'title' => 'تولید درختان نوری هوشمند و پوینت‌لایت پیکسلی', 'subline' => 'ورود به عرصه نورپردازی هوشمند فول‌کالر (RGB / DMX)', 'body' => 'طراحی و ساخت درختان نوری هوشمند مدل دلسا و آرتام با تنه کامپوزیت نشکن و بردهای کنترل مرکزی بی‌سیم (NRF)، تحولی بزرگ در زیباسازی میادین و پارک‌های شهری ایجاد نمود.', 's1_label' => 'کنترلر مرکزی', 's1_value' => 'همگام‌سازی بی‌سیم NRF', 's2_label' => 'طول عمر مفید', 's2_value' => 'بیش از ۵۰,۰۰۰ ساعت', 'hint' => 'با اسکرول به پایین سال بعد را ببینید', 'caption' => 'دستاوردهای اجرایی سال ۱۳۹۷', 'image' => 'assets/images/light-tree.jpg', 'image_alt' => 'تولید درختان نوری هوشمند و پوینت‌لایت پیکسلی'],
                    ['year' => '۱۴۰۱', 'name' => 'گواهینامه‌های رسمی', 'badge' => 'گواهینامه‌های رسمی', 'title' => 'اخذ گواهینامه ISO 9001:2015 و کدهای GS1', 'subline' => 'تثبیت جایگاه دانش‌بنیان و تامین‌کننده رسمی سامانه ستاد', 'body' => 'دریافت گواهینامه‌های بین‌المللی مدیریت کیفیت، ثبت ملی کالاها در سامانه ایران‌کد (GS1) و حضور فعال در سامانه تدارکات الکترونیکی دولت (ستاد) جهت تسهیل خریدهای سازمانی شهرداری‌ها.', 's1_label' => 'استاندارد کیفیت', 's1_value' => 'ISO 9001:2015', 's2_label' => 'شناسه کالا', 's2_value' => 'ثبت رسمی ایران‌کد GS1', 'hint' => 'با اسکرول به پایین سال بعد را ببینید', 'caption' => 'دستاوردهای اجرایی سال ۱۴۰۱', 'image' => 'assets/images/light-sphere.jpg', 'image_alt' => 'اخذ گواهینامه ISO 9001:2015 و کدهای GS1'],
                    ['year' => '۱۴۰۴', 'name' => '+۸۵۰ پروژه شهری', 'badge' => '+۸۵۰ پروژه شهری', 'title' => 'عبور از مرز ۸۵۰ پروژه موفق در ایران و عراق', 'subline' => 'توسعه سبد محصولات به بیش از ۱۲۵ تجهیز تخصصی نوری', 'body' => 'عرفان صنعت اصفهان به عنوان مرجع نخست طراحی و تولید المان‌های نوری شهری، علاوه بر پوشش کامل کلان‌شهرهای ایران، اجرای پروژه‌های شاخص نورپردازی در شهرهای مذهبی و تجاری عراق را نیز در کارنامه خود ثبت کرد.', 's1_label' => 'پروژه‌های اجرا شده', 's1_value' => '+۸۵۰ پروژه شهری', 's2_label' => 'تنوع محصولات', 's2_value' => '+۱۲۵ محصول تخصصی', 'hint' => 'با اسکرول به پایین سال بعد را ببینید', 'caption' => 'دستاوردهای اجرایی سال ۱۴۰۴', 'image' => 'assets/images/light-tunnel.jpg', 'image_alt' => 'عبور از مرز ۸۵۰ پروژه موفق در ایران و عراق'],
                    ['year' => '۱۴۰۵', 'name' => 'برند سانیرما (Sunirma)', 'badge' => 'برند سانیرما (Sunirma)', 'title' => 'ورود به حوزه تولید نیروگاه‌های خورشیدی با برند سانیرما', 'subline' => 'توسعه زیرساخت‌های انرژی پاک و سامانه‌های فتوولتائیک صنعتی', 'body' => 'در سال ۱۴۰۵، شرکت دانش‌بنیان عرفان صنعت اصفهان با راه‌اندازی برند تخصصی «سانیرما» (Sunirma) وارد عرصه طراحی، تولید سازه‌ها و احداث نیروگاه‌های خورشیدی و سامانه‌های روشنایی سولار شد تا تجربه دو دهه مهندسی برق و سازه را در خدمت تولید انرژی تجدیدپذیر قرار دهد.', 's1_label' => 'برند تخصصی انرژی خورشیدی', 's1_value' => 'سانیرما (Sunirma)', 's2_label' => 'حوزه فعالیت جدید', 's2_value' => 'تولید و احداث نیروگاه خورشیدی', 'hint' => 'آخرین دستاورد (۱۴۰۵ — برند سانیرما)', 'caption' => 'نیروگاه‌های خورشیدی برند سانیرما — ۱۴۰۵', 'image' => 'assets/images/sun-light.jpg', 'image_alt' => 'ورود به حوزه تولید نیروگاه‌های خورشیدی با برند سانیرما'],
                ],
            ],
        ];
    }

    /** اسکیم خدمات: رپیتر ردیف‌های تب‌دار */
    private static function services_schema(): array {
        return [
            ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => ''],
            [
                'key' => 'rows', 'type' => 'repeater', 'label' => __('خدمات', 'erfansanat'), 'add_label' => __('افزودن خدمت', 'erfansanat'),
                'fields' => [
                    ['key' => 'num', 'type' => 'text', 'label' => __('شماره (۰۱…)', 'erfansanat')],
                    ['key' => 'title', 'type' => 'text', 'label' => __('عنوان خدمت', 'erfansanat')],
                    ['key' => 'body', 'type' => 'textarea', 'label' => __('توضیح', 'erfansanat')],
                    ['key' => 'image', 'type' => 'media', 'label' => __('تصویر پیش‌نمایش', 'erfansanat')],
                    ['key' => 'url', 'type' => 'url', 'label' => __('لینک', 'erfansanat')],
                ],
                'default' => [],
            ],
        ];
    }

    /** اسکیم مزیت‌ها: رپیتر کارت‌ها */
    private static function quality_schema(): array {
        return [
            ['key' => 'kicker', 'type' => 'text', 'label' => __('سرنویس', 'erfansanat'), 'default' => ''],
            ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat'), 'default' => ''],
            ['key' => 'body', 'type' => 'textarea', 'label' => __('متن', 'erfansanat'), 'default' => ''],
            [
                'key' => 'cards', 'type' => 'repeater', 'label' => __('کارت‌های مزیت', 'erfansanat'), 'add_label' => __('افزودن کارت', 'erfansanat'),
                'fields' => [
                    ['key' => 'title', 'type' => 'text', 'label' => __('عنوان', 'erfansanat')],
                    ['key' => 'body', 'type' => 'textarea', 'label' => __('متن', 'erfansanat')],
                ],
                'default' => [],
            ],
        ];
    }
}
