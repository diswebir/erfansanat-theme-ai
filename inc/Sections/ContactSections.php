<?php
/**
 * ثبت سکشن‌های قالب برگه «تماس با ما» در SectionManager — ترتیب عین میرور
 * ref: uploads/static-mirror/contact.html (تسک ۷.۷)
 * حالت متای خالی = رندر کامل میرور (پیش‌فرض‌های اسکیم = مقادیر میرور)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Sections;

final class ContactSections {

    /** ترتیب سکشن‌ها — عین ترتیب <section>های میرور */
    public const ORDER = [
            'contact-hero',
            'contact-form',
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
        $registry['contact'] ??= [];

        // ── تعریف سکشن‌ها (به ترتیب میرور) ──
        $items = [];
        $items['contact-hero'] = [
            'label'  => __('هیرو تماس با ما (کرامب، چیپ‌های تماس، آمار)', 'erfansanat'),
            'schema' => [
                ['key' => 'crumb', 'type' => 'text', 'label' => __('برچسب مسیر — صفحه فعلی', 'erfansanat'), 'default' => 'تماس با ما'],
                ['key' => 'kicker', 'type' => 'text', 'label' => __('بالانویس', 'erfansanat'), 'default' => 'مشاوره و استعلام قیمت رایگان'],
                ['key' => 'h1_pre', 'type' => 'text', 'label' => __('عنوان — بخش اول', 'erfansanat'), 'default' => 'تماس با ما؛'],
                ['key' => 'h1_hl', 'type' => 'text', 'label' => __('عنوان — بخش سبز', 'erfansanat'), 'default' => 'پاسخگوی شما هستیم'],
                ['key' => 'desc', 'type' => 'textarea', 'label' => __('متن معرفی', 'erfansanat'), 'default' => 'راه‌های ارتباط و ثبت سفارش در شرکت دانش‌بنیان عرفان صنعت اصفهان؛ مشاوره رایگان، استعلام قیمت روز و راهنمایی کامل نصب و اجرا'],
                ['key' => 'chip1', 'type' => 'text', 'label' => __('چیپ تلفن', 'erfansanat'), 'default' => '۰۳۱ - ۹۱۰۹۱۰۱۱ (۱۰ خط ویژه)'],
                ['key' => 'chip2', 'type' => 'text', 'label' => __('چیپ همراه فروش', 'erfansanat'), 'default' => 'همراه فروش (۰۹۱۳۷۹۷۶۹۱۵)'],
                ['key' => 'brand', 'type' => 'text', 'label' => __('نام شرکت', 'erfansanat'), 'default' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان'],
                ['key' => 'since', 'type' => 'text', 'label' => __('بج «از سال»', 'erfansanat'), 'default' => 'از ۱۳۷۸'],
                ['key' => 'l1', 'type' => 'text', 'label' => __('ردیف ۱ — برچسب', 'erfansanat'), 'default' => '۱۰ خط ویژه فروش'],
                ['key' => 'l2', 'type' => 'text', 'label' => __('ردیف ۱ — مقدار', 'erfansanat'), 'default' => '۰۳۱-۹۱۰۹۱۰۱۱'],
                ['key' => 'l3', 'type' => 'text', 'label' => __('ردیف ۲ — برچسب', 'erfansanat'), 'default' => 'شنبه تا پنجشنبه'],
                ['key' => 'l4', 'type' => 'text', 'label' => __('ردیف ۲ — مقدار', 'erfansanat'), 'default' => '۷ صبح تا ۴:۳۰ بعدازظهر'],
                ['key' => 'st1_v', 'type' => 'text', 'label' => __('آمار ۱ — عدد', 'erfansanat'), 'default' => '۱۰'],
                ['key' => 'st1_l', 'type' => 'text', 'label' => __('آمار ۱ — برچسب', 'erfansanat'), 'default' => 'خط ویژه پاسخگویی'],
                ['key' => 'st2_v', 'type' => 'text', 'label' => __('آمار ۲ — عدد', 'erfansanat'), 'default' => '۲۸۹+'],
                ['key' => 'st2_l', 'type' => 'text', 'label' => __('آمار ۲ — برچسب', 'erfansanat'), 'default' => 'مشتری فعال'],
                ['key' => 'st3_v', 'type' => 'text', 'label' => __('آمار ۳ — عدد', 'erfansanat'), 'default' => '۲۴'],
                ['key' => 'st3_l', 'type' => 'text', 'label' => __('آمار ۳ — برچسب', 'erfansanat'), 'default' => 'ماه گارانتی'],
                ['key' => 'st4_v', 'type' => 'text', 'label' => __('آمار ۴ — عدد', 'erfansanat'), 'default' => 'رایگان'],
                ['key' => 'st4_l', 'type' => 'text', 'label' => __('آمار ۴ — برچسب', 'erfansanat'), 'default' => 'مشاوره تخصصی'],
            ],
        ];
        $items['contact-form'] = [
            'label'  => __('اطلاعات تماس، فرم مشاوره، کارت کارشناس و سوال متداول', 'erfansanat'),
            'schema' => [
                ['key' => 'ic1_l', 'type' => 'text', 'label' => __('کارت تلفن — برچسب', 'erfansanat'), 'default' => 'تماس مستقیم با کارشناسان فروش'],
                ['key' => 'ic1_v', 'type' => 'text', 'label' => __('کارت تلفن — مقدار', 'erfansanat'), 'default' => '۰۳۱ - ۹۱۰۹۱۰۱۱'],
                ['key' => 'ic1_s', 'type' => 'text', 'label' => __('کارت تلفن — زیرنویس', 'erfansanat'), 'default' => '۱۰ خط ویژه'],
                ['key' => 'ic1_url', 'type' => 'url', 'label' => __('کارت تلفن — لینک', 'erfansanat'), 'default' => 'tel:031-91091011'],
                ['key' => 'ic2_l', 'type' => 'text', 'label' => __('کارت ایمیل — برچسب', 'erfansanat'), 'default' => 'ایمیل شرکت'],
                ['key' => 'ic2_v', 'type' => 'text', 'label' => __('کارت ایمیل — مقدار', 'erfansanat'), 'default' => 'info@erfansanat.com'],
                ['key' => 'ic2_s', 'type' => 'text', 'label' => __('کارت ایمیل — زیرنویس', 'erfansanat'), 'default' => 'پاسخگویی در سریع‌ترین زمان'],
                ['key' => 'ic2_url', 'type' => 'url', 'label' => __('کارت ایمیل — لینک', 'erfansanat'), 'default' => 'mailto:info@erfansanat.com'],
                ['key' => 'ic3_l', 'type' => 'text', 'label' => __('کارت ساعات — برچسب', 'erfansanat'), 'default' => 'ساعات کاری'],
                ['key' => 'ic3_v', 'type' => 'text', 'label' => __('کارت ساعات — مقدار', 'erfansanat'), 'default' => '۷ صبح تا ۴:۳۰ بعدازظهر'],
                ['key' => 'ic3_s', 'type' => 'text', 'label' => __('کارت ساعات — زیرنویس', 'erfansanat'), 'default' => 'شنبه تا پنجشنبه'],
                ['key' => 'ic4_l', 'type' => 'text', 'label' => __('کارت آدرس — برچسب', 'erfansanat'), 'default' => 'آدرس کارخانه'],
                ['key' => 'ic4_v', 'type' => 'text', 'label' => __('کارت آدرس — مقدار', 'erfansanat'), 'default' => 'خمینی‌شهر، شهرک صنعتی برق و الکترونیک'],
                ['key' => 'ic4_s', 'type' => 'text', 'label' => __('کارت آدرس — زیرنویس', 'erfansanat'), 'default' => 'بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹ — کدپستی ۸۴۱۸۱۴۸۶۷۸'],
                ['key' => 'form_title', 'type' => 'text', 'label' => __('عنوان فرم', 'erfansanat'), 'default' => 'فرم درخواست مشاوره رایگان'],
                ['key' => 'form_desc', 'type' => 'textarea', 'label' => __('توضیح فرم', 'erfansanat'), 'default' => 'اطلاعات پروژه خود را ثبت کنید؛ کارشناسان ما در سریع‌ترین زمان با شما تماس می‌گیرند. اگر طرح خاص و موردنظر خودتان را دارید، می‌توانیم آن را به صورت کامل پیاده‌سازی کنیم.'],
                ['key' => 'ok_t', 'type' => 'text', 'label' => __('پیام موفقیت — عنوان', 'erfansanat'), 'default' => 'درخواست شما با موفقیت ثبت شد!'],
                ['key' => 'ok_d', 'type' => 'text', 'label' => __('پیام موفقیت — متن', 'erfansanat'), 'default' => 'کارشناسان فروش عرفان صنعت به‌زودی با شما تماس خواهند گرفت.'],
                ['key' => 'ok_btn', 'type' => 'text', 'label' => __('پیام موفقیت — دکمه بازنشانی', 'erfansanat'), 'default' => 'ارسال پیام جدید'],
                ['key' => 'l_name', 'type' => 'text', 'label' => __('برچسب نام و نام خانوادگی', 'erfansanat'), 'default' => 'نام و نام خانوادگی *'],
                ['key' => 'ph_name', 'type' => 'text', 'label' => __('جانگهدار نام', 'erfansanat'), 'default' => 'نام شما'],
                ['key' => 'l_tel', 'type' => 'text', 'label' => __('برچسب شماره تماس', 'erfansanat'), 'default' => 'شماره تماس *'],
                ['key' => 'ph_tel', 'type' => 'text', 'label' => __('جانگهدار شماره تماس', 'erfansanat'), 'default' => '۰۹۱۲ ...'],
                ['key' => 'l_org', 'type' => 'text', 'label' => __('برچسب سازمان', 'erfansanat'), 'default' => 'سازمان / شهرداری'],
                ['key' => 'ph_org', 'type' => 'text', 'label' => __('جانگهدار سازمان', 'erfansanat'), 'default' => 'نام سازمان یا شرکت (اختیاری)'],
                ['key' => 'l_msg', 'type' => 'text', 'label' => __('برچسب توضیحات', 'erfansanat'), 'default' => 'توضیحات پروژه *'],
                ['key' => 'ph_msg', 'type' => 'text', 'label' => __('جانگهدار توضیحات', 'erfansanat'), 'default' => 'نوع پروژه (میدان، خیابان، تونل نوری و...)، محل اجرا و توضیحات لازم'],
                ['key' => 'submit', 'type' => 'text', 'label' => __('متن دکمه ارسال', 'erfansanat'), 'default' => 'ارسال درخواست مشاوره'],
                ['key' => 'mgr_kicker', 'type' => 'text', 'label' => __('کارت کارشناس — بالانویس', 'erfansanat'), 'default' => 'کارشناسان حرفه‌ای فروش پاسخگوی شما هستند!'],
                ['key' => 'mgr_name', 'type' => 'text', 'label' => __('کارت کارشناس — نام', 'erfansanat'), 'default' => 'آقای محمدعلی قربانی'],
                ['key' => 'mgr_role', 'type' => 'text', 'label' => __('کارت کارشناس — سمت', 'erfansanat'), 'default' => 'مدیر فروش'],
                ['key' => 'mgr_phone', 'type' => 'text', 'label' => __('کارت کارشناس — شماره', 'erfansanat'), 'default' => '۰۹۱۳۷۹۷۶۹۱۵'],
                ['key' => 'mgr_phone_url', 'type' => 'url', 'label' => __('کارت کارشناس — لینک شماره', 'erfansanat'), 'default' => 'tel:09137976915'],
                ['key' => 'mgr_b1', 'type' => 'text', 'label' => __('دکمه تماس با دفتر', 'erfansanat'), 'default' => 'تماس با دفتر'],
                ['key' => 'mgr_b2', 'type' => 'text', 'label' => __('دکمه تماس همراه', 'erfansanat'), 'default' => 'تماس همراه'],
                ['key' => 'map_img', 'type' => 'media', 'label' => __('تصویر موقعیت کارخانه', 'erfansanat'), 'default' => 'assets/images/factory.jpg'],
                ['key' => 'map_img_alt', 'type' => 'text', 'label' => __('alt تصویر موقعیت', 'erfansanat'), 'default' => 'موقعیت کارخانه عرفان صنعت'],
                ['key' => 'addr', 'type' => 'textarea', 'label' => __('نشانی روی تصویر', 'erfansanat'), 'default' => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹'],
                ['key' => 'postal', 'type' => 'text', 'label' => __('کدپستی', 'erfansanat'), 'default' => 'کدپستی: ۸۴۱۸۱۴۸۶۷۸'],
                ['key' => 'faq_q', 'type' => 'text', 'label' => __('سوال متداول — پرسش', 'erfansanat'), 'default' => 'آیا خدمات طراحی روشنایی هم ارائه می‌دهید؟'],
                ['key' => 'faq_a1', 'type' => 'textarea', 'label' => __('سوال متداول — پاسخ (اول)', 'erfansanat'), 'default' => 'بله، تیم متخصص ما خدمات طراحی روشنایی را برای پروژه‌های مختلف شهری، تجاری و صنعتی ارائه می‌دهد. ما با در نظر گرفتن نیازهای شما و اصول طراحی روشنایی، بهترین راهکارها را پیشنهاد می‌دهیم.'],
                ['key' => 'faq_hl', 'type' => 'text', 'label' => __('سوال متداول — بخش پررنگ پاسخ', 'erfansanat'), 'default' => ' البته'],
                ['key' => 'faq_a2', 'type' => 'textarea', 'label' => __('سوال متداول — پاسخ (دوم)', 'erfansanat'), 'default' => ' اگر شما خود نیز طرحی خاص و موردنظرتان را دارید، می‌توانیم به صورت کامل آن طرح را پیاده‌سازی کنیم.'],
            ],
        ];
        foreach ($items as $id => $def) {
            $registry['contact'][] = [
                'id'     => (string) $id,
                'label'  => (string) $def['label'],
                'schema' => is_array($def['schema'] ?? null) ? $def['schema'] : [],
            ];
        }
        return $registry;
    }
}
