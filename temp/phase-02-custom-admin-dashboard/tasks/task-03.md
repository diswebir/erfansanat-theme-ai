# Task 02.03 — موتور SectionManager برای برگه‌ها

**فاز:** phase-02-custom-admin-dashboard · **وضعیت:** `[PENDING]`

## شرح تسک
- ساخت `inc/Admin/SectionManager.php` — متاباکس روی برگه‌ها: لیست سکشن‌ها با toggle فعال/غیرفعال، Drag & Drop ترتیب، ویرایش فیلدها
- پشتیبانی فیلدهای: media, url, text, textarea, color, icon, select, repeater
- ذخیره ساختارمند در post meta (کلید اختصاصی `es_sections_{page_slug}`) + سازگار با JSON
- API رندر: تابع `es_render_sections($post_id)` که بر اساس ترتیب/وضعیت، template-parts را include می‌کند
- پیکربندی سکشن‌های صفحه اصلی، درباره ما، تماس با ما و لندینگ‌ها

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] ذخیره/خواندن متا بدون از دست رفتن Repeaterها
- [ ] تغییر ترتیب در ادمین → ترتیب رندر فرانت عوض می‌شود (تست واحد روی آرایه سکشن)
- [ ] سکشن غیرفعال رندر نمی‌شود

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
