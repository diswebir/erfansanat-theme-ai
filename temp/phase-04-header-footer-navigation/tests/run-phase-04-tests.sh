#!/usr/bin/env bash
# اجرای همه تست‌های فاز ۴ — هدر/مگامنو/موبایل/فوتر
set -u
cd "$(dirname "$0")"
total=0; passed=0
for f in "$PWD"/test-task-*.php; do
  out=$(/home/user/tools/php -f "$f" 2>/dev/null | tail -1)
  p=$(echo "$out" | grep -oE '[0-9]+ پاس' | grep -oE '[0-9]+')
  r=$(echo "$out" | grep -oE '[0-9]+ رد' | grep -oE '[0-9]+')
  p=${p:-0}; r=${r:-0}
  echo "$f → $out"
  total=$((total + p + r)); passed=$((passed + p))
done
echo "────────────────────"
echo "مجموع فاز ۴: $passed/$total"
[ "$passed" -eq "$total" ]
