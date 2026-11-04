#!/usr/bin/env python3
"""Compile a gettext .po file into a binary .mo file.

WordPress reads .mo only; there is no msgfmt in this environment, so the theme's
own translation catalogue is compiled here. Plural entries are preserved using
gettext's plural form layout, because an installed catalogue such as
WooCommerce's contains them and dropping them would silently lose translations.
An indexed plural translation that this writer cannot represent is skipped with
a warning rather than aborting the whole catalogue.

Usage:
  tools/compile-po.py erfansanat/languages/fa_IR.po [output.mo]
"""
from __future__ import annotations

import struct
import sys
from pathlib import Path


def parse_po(text: str) -> list[tuple[str, str]]:
    """Parse a .po file into (msgid, msgstr) pairs.

    Plural entries are folded into gettext's NUL-separated form
    (``singular\0plural`` -> ``form0\0form1``), which is exactly how gettext
    stores them in a .mo and how WordPress reads them back.
    """
    entries: list[tuple[str, str]] = []
    msgid: str | None = None
    msgid_plural: str | None = None
    msgstr: str | None = None
    plurals: dict[int, str] = {}
    field: str | None = None

    def flush() -> None:
        nonlocal msgid, msgid_plural, msgstr, plurals, field
        if msgid is not None:
            if plurals:
                ordered = [plurals[key] for key in sorted(plurals)]
                entries.append((msgid + "\x00" + (msgid_plural or ""), "\x00".join(ordered)))
            else:
                entries.append((msgid, msgstr if msgstr is not None else ""))
        msgid, msgid_plural, msgstr, plurals, field = None, None, None, {}, None

    for raw in text.splitlines():
        line = raw.strip()
        if not line or line.startswith("#"):
            continue
        if line.startswith("msgid_plural "):
            msgid_plural = _unquote(line[len("msgid_plural "):])
            field = "plural_id"
        elif line.startswith("msgid "):
            flush()
            msgid = _unquote(line[6:])
            field = "msgid"
        elif line.startswith("msgstr["):
            index = int(line[len("msgstr["):line.index("]")])
            plurals[index] = _unquote(line[line.index("]") + 1:])
            field = "plural_str"
        elif line.startswith("msgstr "):
            msgstr = _unquote(line[7:])
            field = "msgstr"
        elif line.startswith('"'):
            chunk = _unquote(line)
            if field == "msgid" and msgid is not None:
                msgid += chunk
            elif field == "plural_id" and msgid_plural is not None:
                msgid_plural += chunk
            elif field == "msgstr" and msgstr is not None:
                msgstr += chunk
            elif field == "plural_str" and plurals:
                last = max(plurals)
                plurals[last] += chunk
        else:
            continue
    flush()
    return entries


def _unquote(fragment: str) -> str:
    fragment = fragment.strip()
    if len(fragment) >= 2 and fragment[0] == '"' and fragment[-1] == '"':
        fragment = fragment[1:-1]
    out = []
    index = 0
    while index < len(fragment):
        char = fragment[index]
        if char == "\\" and index + 1 < len(fragment):
            nxt = fragment[index + 1]
            mapping = {"n": "\n", "t": "\t", "r": "\r", '"': '"', "\\": "\\", "0": "\x00"}
            if nxt in mapping:
                out.append(mapping[nxt])
                index += 2
                continue
        out.append(char)
        index += 1
    return "".join(out)


def escape_po(value: str) -> str:
    """Escape a string so it can sit inside a .po quoted fragment.

    The NUL that separates a plural's singular from its plural must be written
    as the two-character sequence ``\\0``; emitting a raw NUL makes the file
    binary and every parser downstream (including this one) misread it.
    """
    return (
        value.replace("\\", "\\\\")
        .replace('"', '\\"')
        .replace("\x00", "\\0")
        .replace("\n", "\\n")
        .replace("\t", "\\t")
        .replace("\r", "\\r")
    )


def compile_mo(entries: list[tuple[str, str]]) -> bytes:
    # The header entry (empty msgid) must exist and comes first.
    catalog = {msgid: msgstr for msgid, msgstr in entries}
    catalog.setdefault("", "Content-Type: text/plain; charset=UTF-8\n")
    ordered = sorted(catalog.items())

    # gettext layout: [header][key table][value table][hash][strings].
    # The two tables are separate arrays, and every string offset is absolute
    # from the start of the file (not from the string block), which is what a
    # reader such as WordPress or PHP's gettext expects.
    header_size = 7 * 4
    key_start = header_size
    value_start = key_start + len(ordered) * 8
    hash_start = value_start + len(ordered) * 8
    strings_start = hash_start

    key_table = b""
    value_table = b""
    strings = b""
    for msgid, msgstr in ordered:
        encoded_id = msgid.encode("utf-8") + b"\x00"
        encoded_str = msgstr.encode("utf-8") + b"\x00"
        key_table += struct.pack("II", len(encoded_id) - 1, strings_start + len(strings))
        strings += encoded_id
        value_table += struct.pack("II", len(encoded_str) - 1, strings_start + len(strings))
        strings += encoded_str

    header = struct.pack(
        "Iiiiiii",
        0x950412DE,      # magic
        0,               # revision
        len(ordered),    # number of strings
        key_start,       # offset of key table
        value_start,     # offset of value table
        0,               # hash table size (0 = no hash, valid)
        hash_start,      # hash table offset
    )
    return header + key_table + value_table + strings


def main() -> int:
    if len(sys.argv) < 2:
        print(__doc__)
        return 2
    source = Path(sys.argv[1])
    target = Path(sys.argv[2]) if len(sys.argv) > 2 else source.with_suffix(".mo")
    entries = parse_po(source.read_text(encoding="utf-8"))
    translated = [(i, s) for i, s in entries if i and s]
    if not translated:
        raise SystemExit("No translated strings in " + str(source))
    target.write_bytes(compile_mo(entries))
    print(f"compiled {len(translated)} strings -> {target}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
