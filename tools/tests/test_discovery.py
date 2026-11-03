#!/usr/bin/env python3
"""Acceptance checks for Stage 0. No WordPress implementation is asserted."""
from collections import Counter
from pathlib import Path
import hashlib
import json
import re
import unittest
import zipfile

ROOT = Path(__file__).resolve().parents[2]
SOURCE = ROOT / "static-mirror"
INVENTORY = json.loads((ROOT / "temp/static-inventory.json").read_text())
UPLOAD = Path("/tmp/arena-coding-uploads/erfansanat-static-mirror.zip.png")


class DiscoveryAcceptanceTest(unittest.TestCase):
    def test_01_complete_source_manifest_and_unchanged_files(self):
        files = sorted(p for p in SOURCE.rglob("*") if p.is_file())
        self.assertEqual(156, len(files))
        self.assertEqual(156, len(INVENTORY["files"]))
        self.assertEqual(40018239, sum(p.stat().st_size for p in files))
        for record in INVENTORY["files"]:
            path = SOURCE / record["path"]
            self.assertTrue(path.is_file(), record["path"])
            self.assertEqual(record["bytes"], path.stat().st_size, record["path"])
            self.assertEqual(record["sha256"], hashlib.sha256(path.read_bytes()).hexdigest(), record["path"])

    def test_02_every_html_page_has_one_complete_mapping(self):
        paths = {p.relative_to(SOURCE).as_posix() for p in SOURCE.rglob("*.html")}
        pages = INVENTORY["pages"]
        self.assertEqual(128, len(pages))
        self.assertEqual(paths, {p["path"] for p in pages})
        table = (ROOT / "temp/STATIC_TEMPLATE_MAP.md").read_text()
        for page in pages:
            self.assertIn(f"| `{page['path']}` |", table)
            self.assertTrue(page["proposed_template"].endswith(".php"))
            self.assertTrue(page["sections"])
            self.assertTrue(page["mapping_note"])

    def test_03_landmarks_headings_and_sections(self):
        self.assertEqual(528, sum(len(p["sections"]) for p in INVENTORY["pages"]))
        for page in INVENTORY["pages"]:
            for tag in ("header", "main", "footer"):
                self.assertEqual(1, page["landmarks"][tag]["count"], f"{page['path']}: {tag}")
            self.assertEqual(1, page["tag_counts"].get("h1"), page["path"])
            self.assertEqual("fa", page["language"])
            self.assertEqual("rtl", page["direction"])
            self.assertEqual([], page["mismatched_end_tags"], page["path"])
        home = next(p for p in INVENTORY["pages"] if p["path"] == "index.html")
        self.assertEqual(15, len(home["sections"]))
        self.assertEqual({"home": 2}, home["duplicate_ids"])
        self.assertIn("بیش از ۲۵ سال تجربه", home["sections"][3]["headings"][0]["text"])

    def test_04_resource_link_and_font_integrity(self):
        stats = INVENTORY["statistics"]
        self.assertEqual([], stats["missing_local_links"])
        self.assertEqual([], stats["missing_local_resources"])
        self.assertEqual([], stats["external_resource_dependencies"])
        self.assertEqual(3, len(stats["font_urls"]))
        for url in stats["font_urls"]:
            self.assertTrue((SOURCE / "assets/css" / url).is_file())
        self.assertEqual(25061, stats["local_links_checked"])

    def test_05_jsonld_syntax_and_duplicate_disclosure(self):
        self.assertEqual(128, INVENTORY["statistics"]["jsonld_documents"])
        self.assertEqual([], INVENTORY["statistics"]["invalid_jsonld"])
        self.assertEqual(36, len(INVENTORY["statistics"]["duplicate_main_groups"]))
        table = (ROOT / "temp/STATIC_TEMPLATE_MAP.md").read_text()
        for group in INVENTORY["statistics"]["duplicate_main_groups"]:
            for path in group:
                self.assertIn(path, table)

    def test_06_exact_legacy_keys_and_data_uncertainty_recorded(self):
        blueprint = (ROOT / "temp/DATA_BLUEPRINT.md").read_text()
        keys = [
            "gs1_id", "national_code", "hs_id", "isic_id", "cpc_id", "product_id", "zireonvan",
            "english_titl", "before-sale-c", "true_sale", "product-catalog-file", "galerry_produ",
            "gallery-filed", "image-2", "image-6", "project-ids", "usage_countr", "product-feature",
            "product-feature2_copy", "add-new-feature", "vaule_of_variable", "usage_product", "add-new-usage",
            "faq", "name_question", "answer_question", "second-image", "three-image", "foure-image",
            "five-image", "six-image", "finish-pro-time", "mortabet_mahsole_2", "hrefmeta_en", "hrefmeta_ar",
            "hrefmeta_fa", "same_key_hrefl", "number2-viewc", "number-viewc", "thumbnail", "rank_math_*",
        ]
        for key in keys:
            self.assertIn(key, blueprint)
        self.assertIn("نه از اتصال مستقیم به دیتابیس", blueprint)
        for slug in ["projects", "project_type", "city", "product_cat", "product-tag", "product_brand", "IRT"]:
            self.assertIn(slug, blueprint)

    def test_07_source_matches_original_upload_when_available(self):
        if not UPLOAD.exists():
            self.skipTest("Original upload not present; committed source manifest still checked in test 01")
        self.assertTrue(zipfile.is_zipfile(UPLOAD))
        with zipfile.ZipFile(UPLOAD) as archive:
            self.assertIsNone(archive.testzip())
            records = [i for i in archive.infolist() if not i.is_dir()]
            self.assertEqual(156, len(records))
            for record in records:
                relative = record.filename.removeprefix("static-mirror/")
                self.assertEqual(archive.read(record), (SOURCE / relative).read_bytes(), relative)

    def test_08_gate_documented_and_no_theme_implementation(self):
        memory = (ROOT / "temp/PROJECT_MEMORY.md").read_text()
        self.assertIn("STAGE 0", memory)
        self.assertIn("پرسش", memory)
        for filename in ["functions.php", "style.css", "index.php", "header.php", "footer.php", "front-page.php"]:
            self.assertFalse((ROOT / filename).exists(), f"Premature theme file: {filename}")


if __name__ == "__main__":
    unittest.main(verbosity=2)
