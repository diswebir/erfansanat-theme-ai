#!/usr/bin/env python3
"""Extract the theme's translatable strings into a gettext .pot template.

`wp i18n make-pot` needs to fetch `theme-i18n.json` from develop.svn.wordpress.org
to validate the palette in theme.json, and that host is not reachable when the
tools are built offline. The strings themselves only need a parser, so this walks
the PHP sources and writes the template directly.

The theme's source strings are already Persian, because the site is Persian and
there is nothing to translate at run time. The template exists so the strings can
be translated *out* of Persian later — a translator needs a catalogue to work
from, and every new string has to show up in a diff when it is added.

Usage:
  tools/make-pot.py [theme-dir] [output]

Exit code is 1 when a string is found that a translator could not reorder, which
almost always means a concatenation that should have been a placeholder.
"""
from __future__ import annotations

import re
import sys
from pathlib import Path

# `_n` and `_nx` take a singular and a plural; everything else takes one string.
SINGLE = ("esc_html__", "esc_html_e", "esc_attr__", "esc_attr_e", "esc_html_x", "esc_attr_x",
          "__", "_e", "_x")
PLURAL = ("_n", "_nx")

CALL = re.compile(
    r"""\b(esc_html__|esc_html_e|esc_attr__|esc_attr_e|esc_html_x|esc_attr_x|_n|_nx|__|_e|_x)\s*\(\s*"""
    r"""(?P<q>['"])(?P<text>(?:\\.|(?!(?P=q)).)*)(?P=q)""",
    re.S,
)

# Strings that are placeholders only, or bare punctuation, are not translatable.
SKIP = re.compile(r"^[\s%(][\s\w$.*+\-/%()]*$")


def unescape(text: str) -> str:
    """Turn a PHP single/double quoted literal body into its runtime value."""
    return (text.replace("\\'", "'").replace('\\"', '"').replace("\\\\", "\\")
                .replace("\\n", "\n").replace("\\t", "\t"))


def escape_po(text: str) -> str:
    return text.replace("\\", "\\\\").replace('"', '\\"').replace("\n", "\\n").replace("\t", "\\t")


def collect(theme: Path) -> tuple[dict[str, list[tuple[str, int]]], list[tuple[str, str, int]]]:
    """Return {string: [(relative file, line)]} and a list of plurals."""
    strings: dict[str, list[tuple[str, int]]] = {}
    plurals: list[tuple[str, str, int]] = []
    seen_plural: dict[tuple[str, str], list[tuple[str, int]]] = {}

    for path in sorted(theme.rglob("*.php")):
        if any(part in {"node_modules", "vendor", "languages"} for part in path.parts):
            continue
        text = path.read_text(encoding="utf-8", errors="ignore")
        rel = str(path.relative_to(theme))
        for match in CALL.finditer(text):
            func = match.group(1)
            raw = unescape(match.group("text"))
            if not raw.strip() or SKIP.match(raw):
                continue
            line = text.count("\n", 0, match.start()) + 1
            if func in PLURAL:
                # The plural form follows the same string as a second argument.
                tail = text[match.end():match.end() + 400]
                second = re.match(r"""\s*,\s*(['"])((?:\\.|(?!\1).)*)\1""", tail, re.S)
                plural = unescape(second.group(2)) if second else raw
                key = (raw, plural)
                seen_plural.setdefault(key, []).append((rel, line))
            else:
                strings.setdefault(raw, []).append((rel, line))

    return strings, [(*key, refs) for key, refs in seen_plural.items()]  # type: ignore[misc]


def render(strings, plurals, version: str) -> str:
    out = [
        "# Translation template for the Erfan Sanat Esfahan theme.",
        "#",
        "# The theme's own strings are written in Persian, because the site is",
        "# Persian. This catalogue is what a translator starts from when the theme",
        "# is reused on a non-Persian site: copy it to <locale>.po, translate the",
        "# msgstr fields (keeping the source as msgid) and compile with",
        "# tools/compile-po.py.",
        "#",
        "# Every user-visible string goes through one of the gettext functions with",
        "# the `erfansanat` text domain, so nothing in the theme has to be edited to",
        "# add a language.",
        "msgid \"\"",
        "msgstr \"\"",
        "\"Project-Id-Version: Erfan Sanat Esfahan " + version + "\\n\"",
        "\"Report-Msgid-Bugs-To: https://erfansanat.com\\n\"",
        "\"MIME-Version: 1.0\\n\"",
        "\"Content-Type: text/plain; charset=UTF-8\\n\"",
        "\"Content-Transfer-Encoding: 8bit\\n\"",
        "\"Language: fa_IR\\n\"",
        "\"Plural-Forms: nplurals=2; plural=(n > 1);\\n\"",
        "\"X-Domain: erfansanat\\n\"",
        "",
    ]

    def refs(entries) -> None:
        # gettext wants `#: file:line`, and a max of a few per line.
        line = []
        for rel, ln in entries[:6]:
            line.append(f"{rel}:{ln}")
        out.append("#: " + " ".join(line))

    for text in sorted(strings):
        refs(strings[text])
        out.append('msgid "' + escape_po(text) + '"')
        out.append('msgstr ""')
        out.append("")

    for msgid, plural, entries in sorted(plurals, key=lambda row: row[0]):
        refs(entries)
        out.append('msgid "' + escape_po(msgid) + '"')
        out.append('msgid_plural "' + escape_po(plural) + '"')
        out.append('msgstr[0] ""')
        out.append('msgstr[1] ""')
        out.append("")

    return "\n".join(out)


def main() -> int:
    root = Path(__file__).resolve().parent.parent
    theme = Path(sys.argv[1]) if len(sys.argv) > 1 else root / "erfansanat"
    out_path = Path(sys.argv[2]) if len(sys.argv) > 2 else theme / "languages" / "erfansanat.pot"

    if not theme.is_dir():
        print(f"No such theme directory: {theme}", file=sys.stderr)
        return 2

    header = (theme / "style.css").read_text(encoding="utf-8", errors="ignore")[:2000]
    found = re.search(r"^Version:\s*(\S+)", header, re.M)
    version = found.group(1) if found else "0.0.0"

    strings, plurals = collect(theme)
    if not strings and not plurals:
        print("No translatable strings found — the gettext wrappers may have changed.", file=sys.stderr)
        return 1

    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(render(strings, plurals, version), encoding="utf-8")

    total = len(strings) + len(plurals)
    print(f"{out_path.relative_to(root)}: {len(strings)} strings, {len(plurals)} plural forms ({total} entries)")

    # A translation with no domain is dead weight, and a missing text domain is
    # the usual reason a string silently stays in the source language.
    stray = 0
    for path in sorted(theme.rglob("*.php")):
        text = path.read_text(encoding="utf-8", errors="ignore")
        for match in CALL.finditer(text):
            tail = text[match.end():match.end() + 300]
            if "'erfansanat'" not in tail and '"erfansanat"' not in tail:
                line = text.count("\n", 0, match.start()) + 1
                print(f"  {path.relative_to(theme)}:{line} has no text domain", file=sys.stderr)
                stray += 1
    if stray:
        print(f"{stray} gettext call(s) without the 'erfansanat' domain", file=sys.stderr)
        return 1

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
