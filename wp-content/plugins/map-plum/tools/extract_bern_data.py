# -*- coding: utf-8 -*-
import json
import re
import subprocess
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
bern = (ROOT / "sorts/sw/bern.php").read_text(encoding="utf-8")
out_dir = ROOT / "sorts/sw"
out_dir.mkdir(parents=True, exist_ok=True)

districts_m = re.search(r"var BERN_DISTRICTS = (\{[\s\S]*?\});\s*\n\s*var map", bern)
if districts_m:
    (out_dir / "bern-districts.json").write_text(
        districts_m.group(1) + "\n", encoding="utf-8"
    )

snippet = """
var MY_REGION_INFO = {REGION};
var MY_POI_POINTS = {POI};
var MY_POI_INFO = {INFO};
var FALLBACK_PHOTOS = {FB};
JSON.stringify({
  regionInfo: MY_REGION_INFO,
  poiPoints: MY_POI_POINTS,
  poiInfo: MY_POI_INFO,
  fallbackPhotos: FALLBACK_PHOTOS
});
"""

def grab(name, until_pattern):
    m = re.search(
        rf"var {name} = ([\s\S]*?);\s*\n\s*{until_pattern}",
        bern,
    )
    return m.group(1) if m else "null"

region = grab("MY_REGION_INFO", "//")
poi = grab("MY_POI_POINTS", "//")
info = grab("MY_POI_INFO", "var FALLBACK")
fb = grab("FALLBACK_PHOTOS", "// GeoJSON")

js = (
    snippet.replace("{REGION}", region)
    .replace("{POI}", poi)
    .replace("{INFO}", info)
    .replace("{FB}", fb)
)
try:
    raw = subprocess.check_output(["node", "-e", js], text=True, encoding="utf-8")
    config = json.loads(raw)
    (out_dir / "bern-config.json").write_text(
        json.dumps(config, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    print("bern-config.json OK")
except Exception as e:
    print("node failed:", e)
