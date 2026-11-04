# Phase 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده

**هدف:** ثبت Native تایپ‌ها/تکسونومی‌ها با URL یکسان، و خواندن/نوشتن بی‌نقص هر ۱۸ کلید متا و ۵ Repeater فیلد JetEngine.

**وضعیت:** Phase 3 تکمیل شد (2026-11-04). Tasks 3.1–3.18 همگی پاس شده‌اند؛ Gate 3 سبز است.

**18 تسک اتمیک:** `3.1`، `3.2`، `3.3`، `3.4`، `3.5`، `3.6`، `3.7`، `3.8`، `3.9`، `3.10`، `3.11`، `3.12`، `3.13`، `3.14`، `3.15`، `3.16`، `3.17`، `3.18`

**نتیجه:** 18 فایل تست، 66 PASS / 0 FAIL / 0 PENDING. Task 3.18 seed/round-trip/orphan checks از fixtureهای deterministic in-memory استفاده می‌کنند؛ WordPress/SQLite نصب‌شده برای اجرای DB integration در sandbox حاضر نیست.

**اجرای تست‌های فاز:**
```bash
bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-03-tests.sh
```
