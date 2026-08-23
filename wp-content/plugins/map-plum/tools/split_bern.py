# -*- coding: utf-8 -*-
"""Split sorts/sw/bern.php into front assets."""
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
bern = (ROOT / "sorts/sw/bern.php").read_text(encoding="utf-8")

i0 = bern.index("<style>") + len("<style>")
i1 = bern.index("</style>", i0)
css = bern[i0:i1].strip()

i2 = bern.index('<div class="bern-map-widget"')
i3 = bern.rindex("</aside>") + len("</aside>")
# close two wrapping divs after aside
rest = bern[i3:]
close = 0
pos = 0
for m in re.finditer(r"</div>", rest):
    pos = m.end()
    close += 1
    if close >= 2:
        break
html = bern[i2 : i3 + pos].strip()

marker = "<script>\n    (function () {"
j0 = bern.index(marker) + len("<script>\n")
j1 = bern.rindex("</script>")
js_inner = bern[j0:j1].strip()
if js_inner.startswith("(function () {"):
    js_inner = js_inner[len("(function () {") :].strip()
if js_inner.endswith("})();"):
    js_inner = js_inner[: -len("})();")].strip()
js_inner = re.sub(
    r"// Ваша информация[\s\S]*?var BERN_DISTRICTS = \{[\s\S]*?\};\s*",
    "",
    js_inner,
    count=1,
)
js_inner = re.sub(
    r"\s*\$\(function \(\) \{[\s\S]*?\}\);\s*$",
    "",
    js_inner,
).strip()

front = ROOT / "front"
front.mkdir(parents=True, exist_ok=True)
(front / "map.css").write_text(css + "\n", encoding="utf-8")
(front / "bern-map.php").write_text(html + "\n", encoding="utf-8")

map_js = """(function ($) {
  'use strict';

  function boot() {
    var cfg = window.mapPlumBern || {};
    var MY_REGION_INFO = cfg.regionInfo || {};
    var MY_POI_POINTS = cfg.poiPoints || [];
    var MY_POI_INFO = cfg.poiInfo || {};
    var FALLBACK_PHOTOS = cfg.fallbackPhotos || [];
    var BERN_DISTRICTS = cfg.districts || { type: 'FeatureCollection', features: [] };

"""
map_js += js_inner
map_js += """

    $(function () {
      initMap(BERN_DISTRICTS);
      setTimeout(refreshMapSize, 50);
      setTimeout(refreshMapSize, 300);
      $(window).on('resize', refreshMapSize);
      if (window.ResizeObserver) {
        var el = document.getElementById('bernMapWidget');
        if (el) {
          new ResizeObserver(refreshMapSize).observe(el);
        }
      }
      $('#panelClose, #overlay').on('click', closePanel);
      $('#mapToolbar').on('click', '.map-toolbar-btn', function () {
        togglePoiCategory($(this).data('category'));
      });
      $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
          closePanel();
        }
      });
    });
  }

  $(boot);
})(jQuery);
"""
(front / "map.js").write_text(map_js, encoding="utf-8")

# Extract JS object literals for PHP data file (simple approach: eval in node not available)
# Write data.php generator from regex on MY_REGION_INFO block
region_m = re.search(
    r"var MY_REGION_INFO = (\{[\s\S]*?\});\s*\n\s*// Точки",
    bern,
)
poi_points_m = re.search(
    r"var MY_POI_POINTS = (\[[\s\S]*?\]);\s*\n\s*// Шаблоны",
    bern,
)
poi_info_m = re.search(
    r"var MY_POI_INFO = (\{[\s\S]*?\});\s*\n\s*var FALLBACK",
    bern,
)
fallback_m = re.search(r"var FALLBACK_PHOTOS = (\[[\s\S]*?\]);", bern)
districts_m = re.search(r"var BERN_DISTRICTS = (\{[\s\S]*?\});\s*\n\s*var map", bern)

import json

def js_to_json(js):
    # quote keys loosely - use json5 not available; use demjson or manual
    # For geojson line it's valid JSON already
    js = js.strip()
    if js.startswith("{") or js.startswith("["):
        # fix trailing commas
        js = re.sub(r",\s*([}\]])", r"\1", js)
        # unquoted keys -> quoted (simple)
        js = re.sub(r"(\{|,)\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*:", r'\1 "\2":', js)
        js = js.replace("'", '"')
    return json.loads(js)

data_php = """<?php
/**
 * Данные карты Берн (Швейцария).
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
\texit;
}

return array(
\t'region_info' => %s,
\t'poi_points' => %s,
\t'poi_info' => %s,
\t'fallback_photos' => %s,
\t'districts' => %s,
);
""" % (
    "json_decode( file_get_contents( __DIR__ . '/bern-districts.json' ), true )",  # placeholder
    "array()",
    "array()",
    "array()",
    "array()",
)

print("Extracted. region:", bool(region_m), "districts:", bool(districts_m))
print("CSS", len(css), "HTML", len(html), "JS inner", len(js_inner))
