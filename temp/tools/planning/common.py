"""Shared helpers for the atomic Stage-1 plan. No theme code is generated here."""
from __future__ import annotations

def T(title, legacy, goal, targets, sources, deps, accept, tests, keys=(), out=(), notes=""):
    return dict(title=title, legacy=legacy, goal=goal, targets=list(targets),
                sources=list(sources), deps=list(deps), accept=list(accept),
                tests=list(tests), keys=list(keys), out=list(out), notes=notes)

PHASE_META = {
    1: ("phase-01-core-and-assets", "هسته قالب، Autoloader و Assets"),
    2: ("phase-02-custom-admin-dashboard", "داشبورد اختصاصی ادمین و Section Builder"),
    3: ("phase-03-cpt-and-legacy-meta", "CPTها، تکسونومی‌ها و متاباکس‌های Legacy"),
    4: ("phase-04-header-footer-navigation", "هدر، مگامنو، منوی موبایل و فوتر"),
    5: ("phase-05-homepage-and-sections", "صفحه اصلی و کامپوننت‌های ماژولار"),
    6: ("phase-06-woocommerce-and-products", "ووکامرس، محصول تکی و آرشیو فروشگاه"),
    7: ("phase-07-projects-blog-and-pages", "پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها"),
    8: ("phase-08-ajax-filters-and-audit", "ایجکس، فرم‌ها، ممیزی و استقرار"),
}

COMMON_DOD = [
    "php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.",
    "هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.",
    "کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.",
    "تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.",
    "PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.",
]
