# میرور استاتیک کامل وب‌سایت شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)

این پوشه شامل نسخه **میرور استاتیک کامل (Complete Static Website Mirror)** وب‌سایت «عرفان صنعت اصفهان» است که به‌صورت ۱۰۰٪ آفلاین و تنها با استفاده از **HTML5، CSS3 و Vanilla JavaScript** (بدون هیچ فریم‌ورک یا وابستگی خارجی) تولید شده است.

## ویژگی‌های کلیدی
- **۳۷ صفحه مستقل HTML**: تمامی مسیرهای ثابت و داینامیک (پروژه‌ها، محصولات، خدمات و مقالات وبلاگ) با `slug` واقعی خودشان pre-render شده‌اند.
- **کارکرد ۱۰۰٪ آفلاین (`file://`)**: تمامی مسیرهای لینک‌ها، تصاویر، فونت‌ها، استایل‌ها و اسکریپت‌ها به‌صورت نسبی (Relative) تنظیم شده‌اند؛ کافی است روی `index.html` دابل‌کلیک کنید تا در مرورگر باز شود.
- **محلی‌سازی کامل منابع**: تمامی ۲۱ تصویر (۱۰ تصویر محلی + ۱۱ تصویر خارجی از Pexels) و ۳ فایل فونت `Vazirmatn` (`woff2`) دانلود و در ساختار پوشه ذخیره شده‌اند. هیچ درخواستی به اینترنت ارسال نمی‌شود.
- **تعامل‌های کامل با Vanilla JS**: منوی موبایل، تب‌های تعاملی خدمات و مشخصات فنی، فیلتر دسته‌بندی و جستجوی زنده پروژه‌ها/محصولات/مقالات، گالری تصاویر و لایت‌باکس صفحات جزئیات، آکاردئون سوالات متداول (FAQ)، دکمه اشتراک‌گذاری، فرم تماس و شمارنده‌های آماری.

## ساختار پوشه‌ها و فایل‌ها (`static-mirror/`)

```text
static-mirror/
├── index.html                          # صفحه اصلی (خانه)
├── projects.html                       # آرشیو پروژه‌های اجرا شده
├── products.html                       # کاتالوگ محصولات و تجهیزات
├── services.html                       # صفحه خدمات مهندسی و اجرایی
├── blog.html                           # وبلاگ و دانشنامه تخصصی
├── about.html                          # درباره عرفان صنعت اصفهان
├── contact.html                        # تماس با ما و استعلام قیمت
├── projects/                           # ۶ صفحه جزئیات پروژه (Case Studies)
│   ├── esfahan-shohada-square.html
│   ├── half-arch-tunnel-alborz.html
│   ├── artam-tree-midan.html
│   ├── foroogh-boulevard-project.html
│   ├── crystal-globe-plaza.html
│   └── bridge-fullcolor-lighting.html
├── products/                           # ۸ صفحه جزئیات فنی محصولات (Datasheets)
│   ├── delsa-light-tree.html
│   ├── artam-light-tree.html
│   ├── sun-light-32.html
│   ├── sun-light-48.html
│   ├── foroogh-chandelier.html
│   ├── fandoghi-string.html
│   ├── balooti-string.html
│   └── point-light-pixel.html
├── services/                           # ۵ صفحه جزئیات خدمات تخصصی
│   ├── square-lighting.html
│   ├── street-lighting.html
│   ├── bridge-lighting.html
│   ├── element-lighting.html
│   └── building-lighting.html
├── blog/                               # ۱۱ صفحه مقاله تخصصی وبلاگ
│   ├── guide-urban-lighting-municipality.html
│   ├── delsa-vs-artam-light-tree.html
│   ├── what-is-ip67-ip68-outdoor-lighting.html
│   ├── string-lights-fandoghi-vs-balooti.html
│   ├── bridge-lighting-engineering.html
│   ├── roi-of-urban-beautification.html
│   ├── nowruz-ramadan-urban-elements.html
│   ├── beauty-of-light-tunnel.html
│   ├── sun-display-32-vs-48.html
│   ├── led-wall-washer-guide.html
│   ├── point-light-pixel-led.html
│   └── frame-holder-plt.html
├── assets/
│   ├── css/
│   │   └── styles.css                  # استایل‌های کامپایل‌شده و کامل سایت + @font-face محلی
│   ├── js/
│   │   └── main.js                     # اسکریپت Vanilla JS برای تمامی تعامل‌های سایت
│   └── fonts/
│       ├── vazirmatn-arabic.woff2      # فونت وزیرمتن (بازه فارسی و عربی)
│       ├── vazirmatn-latin.woff2       # فونت وزیرمتن (بازه لاتین)
│       └── vazirmatn-latin-ext.woff2   # فونت وزیرمتن (بازه لاتین تکمیلی)
└── images/
    ├── hero.jpg                        # ۱۰ تصویر اختصاصی پروژه‌ها و محصولات
    ├── light-tree.jpg
    ├── ...
    └── pexels/                         # ۱۱ تصویر دانلودشده و محلی‌شده از Pexels
        ├── pexels-10951256.jpg
        └── ...
```

## روش اجرا
1. **اجرای مستقیم بدون سرور (آفلاین - `file://`)**:
   فایل ZIP را استخراج کرده و مستقیماً روی فایل `index.html` (یا هر یک از ۳۷ فایل HTML) دابل‌کلیک کنید تا در مرورگر باز شود.
2. **اجرا با وب‌سرور محلی دلخواه**:
   می‌توانید پوشه `static-mirror` را با هر وب‌سرور استاتیک (مانند `npx serve static-mirror` یا `python3 -m http.server`) نیز اجرا نمایید.

تعداد کل صفحات تولیدشده: **128 صفحه HTML مستقل**.
