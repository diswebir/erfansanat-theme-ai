# Phase 1 — هسته قالب، Autoloader و Assets

تعداد تسک اتمیک: **12** · وضعیت: `[PENDING]`

| شناسه | تسک | تسک قبلی | وابستگی | وضعیت |
|---|---|---|---|---|
| `P01-T001` | هدر قالب و style.css | `1.1` | — | `[PENDING]` |
| `P01-T002` | Bootstrap و ثابت‌های قالب | `1.1` | P01-T001 | `[PENDING]` |
| `P01-T003` | Autoloader اختصاصی | `1.1` | P01-T002 | `[PENDING]` |
| `P01-T004` | چرخه عمر و Theme Supports | `1.1` | P01-T003 | `[PENDING]` |
| `P01-T005` | Fallback index.php | `1.1` | P01-T004 | `[PENDING]` |
| `P01-T006` | DeBloat: صف‌های بلوک | `1.2` | P01-T004 | `[PENDING]` |
| `P01-T007` | DeBloat: head و ایموجی | `1.2` | P01-T006 | `[PENDING]` |
| `P01-T008` | انتقال CSS و فونت‌ها | `1.3` | P01-T001 | `[PENDING]` |
| `P01-T009` | انتقال JS و رسانه‌های پایه | `1.3` | P01-T008 | `[PENDING]` |
| `P01-T010` | AssetManager: ثبت و نسخه‌گذاری | `1.3` | P01-T008, P01-T009 | `[PENDING]` |
| `P01-T011` | AssetManager: enqueue شرطی | `1.3` | P01-T010 | `[PENDING]` |
| `P01-T012` | یکپارچگی asset و بسته‌بندی | `1.3` | P01-T011 | `[PENDING]` |

اجرای همه تست‌ها: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
