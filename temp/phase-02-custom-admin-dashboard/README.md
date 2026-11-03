# Phase 2 — داشبورد اختصاصی ادمین و Section Builder

تعداد تسک اتمیک: **18** · وضعیت: `[PENDING]`

| شناسه | تسک | تسک قبلی | وابستگی | وضعیت |
|---|---|---|---|---|
| `P02-T001` | Schema و ذخیره‌گاه آپشن‌ها | `2.1` | P01-T004 | `[PASSED]` |
| `P02-T002` | Helper es_option و کش | `2.1` | P02-T001 | `[PASSED]` |
| `P02-T003` | منوی ادمین و دسترسی | `2.1` | P02-T001 | `[PASSED]` |
| `P02-T004` | پوسته UI تب‌بندی RTL | `2.1` | P02-T003 | `[PASSED]` |
| `P02-T005` | Renderer فیلدهای پایه | `2.1` | P02-T001, P02-T004 | `[PASSED]` |
| `P02-T006` | فیلدهای Media/Link/Icon | `2.2` | P02-T005 | `[PASSED]` |
| `P02-T007` | فیلد Repeater | `2.3` | P02-T005 | `[PASSED]` |
| `P02-T008` | AJAX Save: امنیت | `2.1` | P02-T003, P02-T005 | `[PASSED]` |
| `P02-T009` | AJAX Save: sanitize و validation | `2.1` | P02-T001, P02-T008 | `[PASSED]` |
| `P02-T010` | تب هدر/اعلان/لوگو | `2.2` | P02-T005, P02-T006 | `[PASSED]` |
| `P02-T011` | تب مگامنو | `2.2` | P02-T010 | `[PASSED]` |
| `P02-T012` | تب فوتر/تماس/شبکه‌های اجتماعی | `2.2` | P02-T010 | `[PASSED]` |
| `P02-T013` | تب برند/رنگ و اسکریپت سفارشی | `2.2` | P02-T009 | `[PASSED]` |
| `P02-T014` | تب تنظیمات آرشیو | `2.2` | P02-T005 | `[PASSED]` |
| `P02-T015` | SectionManager: schema و ذخیره متا | `2.3` | P02-T001, P02-T007 | `[PASSED]` |
| `P02-T016` | SectionManager: UI ترتیب و فعال‌سازی | `2.3` | P02-T015 | `[PASSED]` |
| `P02-T017` | SectionManager: ویرایش محتوای سکشن | `2.3` | P02-T006, P02-T007, P02-T016 | `[PASSED]` |
| `P02-T018` | SectionManager: API رندر و کش | `2.3` | P02-T015, P02-T017 | `[PASSED]` |

اجرای همه تست‌ها: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
