# Task 5.9 — سکشن ویترین مقالات (blog-preview)

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-09.php`

## 🎯 هدف
آخرین مقالات با WP_Query و کارت مقاله + لینک وبلاگ

## 🔗 وابستگی‌ها
- Task 5.1
- Task 5.11

## 📎 مراجع (منابع برش/استخراج)
- index.html سکشن #blog-preview

## 📤 فایل‌های خروجی
- `template-parts/home/section-blog.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. WP_Query post (posts_per_page از فیلد، پیش‌فرض میرور ۳/۶) + ignore_sticky_posts
2. حلقه با card-post.php
3. فیلد عنوان سکشن + لینک مشاهده همه به برگه blog (page_for_posts)
4. خالی → مخفی

## ⛔ محدودیت‌ها و خط‌قرمزها
- دقت به page_for_posts=7426 (لینک از get_permalink(get_option))

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] کوئری صحیح
- [ ] لینک وبلاگ داینامیک
- [ ] کارت‌ها استاندارد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-09.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
