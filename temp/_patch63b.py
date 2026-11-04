import re

p = 'temp/gen_p63.py'
s = open(p, encoding='utf-8').read()

# ۱) برش کاتالوگ از baloti (قبل از frags)
if 'BTN_CATALOG' not in s.split('frags = nowdoc')[0]:
    anchor = "frags = nowdoc('ES_HEAD_A', HEAD_A, 'ESP63A')"
    assert anchor in s, 'frags anchor'
    catalog = open('temp/_catalog_block.py', encoding='utf-8').read()
    s = s.replace(anchor, catalog + '\n' + anchor)

# ۲) nowdocهای کاتالوگ در frags
old2 = "      + nowdoc('ES_CARD', CARD, 'ESP63I') + nowdoc('ES_SPECS_TAIL', CARDS_CLOSE, 'ESP63J')"
new2 = ("      + nowdoc('ES_CARD', CARD, 'ESP63I') + nowdoc('ES_SPECS_TAIL', CARDS_CLOSE, 'ESP63J') \\\n"
        "      + nowdoc('ES_BTN_CATALOG', BTN_CATALOG, 'ESP63K') + nowdoc('ES_DOC_HEAD', DOC_HEAD, 'ESP63L') \\\n"
        "      + nowdoc('ES_DOC_CARD', DOC_CARD, 'ESP63M') + nowdoc('ES_DOC_TAIL', DOC_TAIL, 'ESP63N')")
assert old2 in s, 'frags2 anchor'
s = s.replace(old2, new2)

# ۳) خواندن متای کاتالوگ
old3 = '"$es_rev_count = (int) get_comments_number($es_pid);\\n"'
assert old3 in s, 'meta anchor'
new3 = ('"$es_catalog_raw = trim((string) get_post_meta($es_pid, \'product-catalog-file\', true));\\n"\n'
        '"$es_docs_url = $es_catalog_raw !== \'\' ? es_media_url($es_catalog_raw) : \'\';\\n"\n'
        + old3)
s = s.replace(old3, new3)

# ۴) docs_url در فیلتر args
old4 = '"    \'has_reviews\' => false,\\n"'
assert old4 in s, 'args anchor'
s = s.replace(old4, old4 + '\n"    \'docs_url\'   => $es_docs_url,\\n"')

# ۵) خواندن بعد از فیلتر
old5 = "$es_rev_count = (int) ($es_data['rev_count'] ?? 0);"
assert old5 in s, 'postfilter anchor'
s = s.replace('"$es_rev_count = (int) ($es_data[\'rev_count\'] ?? 0);\\n\\n"',
              '"$es_rev_count = (int) ($es_data[\'rev_count\'] ?? 0);\\n"\n"$es_docs_url = (string) ($es_data[\'docs_url\'] ?? \'\');\\n\\n"')

# ۶) تب docs
old6 = '"if ($es_rows !== [] || $es_legal !== []) {\\n"\n"    $es_tabs[] = \'specs\';\\n"\n"}\\n"'
assert old6 in s, 'tabs anchor'
new6 = ('"if ($es_rows !== [] || $es_legal !== []) {\\n"\n'
        '"    $es_tabs[] = \'specs\';\\n"\n'
        '"}\\n"\n'
        '"if ($es_docs_url !== \'\') {\\n"\n'
        '"    $es_tabs[] = \'docs\';\\n"\n'
        '"}\\n"')
s = s.replace(old6, new6)

open(p, 'w', encoding='utf-8').write(s)
print('remaining catalog patches applied')
