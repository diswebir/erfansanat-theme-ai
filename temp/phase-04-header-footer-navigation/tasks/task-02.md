# Task 04.02 — Nav Walkers چهارگانه

**فاز:** phase-04-header-footer-navigation · **وضعیت:** `[PENDING]`

## شرح تسک
- `inc/Nav/MegaMenuWalker.php` برای `main-mega-menu` — تبیب محصولات/پروژه‌ها (`mega-prod-tab/panel`, `mega-proj-tab/panel`, `desktop-mega-trigger/panel`) با زیرمنوهای چندسطحی منطبق بر مارک‌آپ میرور
- `inc/Nav/MobileWalker.php` برای `mobile-menu` — سایدبار off-canvas (`mobile-sidebar`, `mobile-prod/proj-panel`, `mobile-acc-*`)
- Walker ساده `tolidat-sherkat` (فوتر) و `landing` (اسکرول لندینگ)
- حفظ کلاس‌ها/کلاس‌های فعال (current-menu-item و…) با فرمت کلاس‌های میرور

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] خروجی HTML منو با مارک‌آپ مرجع میرور match است (تست DOM walker با آیتم‌های fixture)
- [ ] باز/بسته شدن مگامنو و منوی موبایل با main.js موجود کار کند (hookها حفظ شده)

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
