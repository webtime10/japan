/*
 * Leaflet-карта: районы, категории из админки, маркеры по координатам из БД.
 */
(function () {
  "use strict";

  function initMapWidget(widget) {
    var slug = widget.getAttribute("data-map-slug");
    var allConfigs = window.mapPlumMaps || {};
    var cfg = allConfigs[slug];
    if (!cfg || !window.L) {
      return;
    }

    var mapEl = widget.querySelector("[data-role='map']");
    var toolbar = widget.querySelector("[data-role='toolbar']");
    var panel = widget.querySelector("[data-role='panel']");
    var panelClose = widget.querySelector("[data-role='panel-close']");
    var panelTitle = widget.querySelector("[data-role='panel-title']");
    var panelPhotoWrap = widget.querySelector("[data-role='panel-photo-wrap']");
    var panelDesc = widget.querySelector("[data-role='panel-desc']");
    var panelLinkWrap = widget.querySelector("[data-role='panel-link-wrap']");
    var panelLink = widget.querySelector("[data-role='panel-link']");
    var panelPhoto = widget.querySelector("[data-role='panel-photo']");

    if (!mapEl || !panel || !panelClose) {
      return;
    }

    var categories = cfg.categories || [];
    var markersByCategory = cfg.markersByCategory || {};
    var fallbackPhotos = cfg.fallbackPhotos || [];
    var districts = cfg.districts || { type: "FeatureCollection", features: [] };
    var mapCenter = cfg.mapCenter || [46.95, 7.6];
    var mapZoom = cfg.mapZoom || 9;
    var i18n = cfg.i18n || { readMore: "Read more", panelEmpty: "", panelClose: "Close" };

    var map;
    var geoLayer;
    var poiLayerGroup = null;
    var activeCategoryId = null;

    function createPoiIcon() {
      var iconCfg = cfg.markerIcon || {};
      var iconUrl = iconCfg.url || "";
      if (iconUrl) {
        var iconW = iconCfg.width || 32;
        var iconH = iconCfg.height || 40;
        return L.icon({
          iconUrl: iconUrl,
          iconSize: [iconW, iconH],
          iconAnchor: [iconW / 2, iconH],
          tooltipAnchor: [0, -iconH + 4],
          className: "map-plum-poi-icon",
        });
      }
      return L.divIcon({
        className: "poi-marker",
        html: "<div class='poi-marker-dot'></div>",
        iconSize: [12, 12],
        iconAnchor: [6, 6],
      });
    }

    var poiIcon = createPoiIcon();

    var styleDefault = { fillColor: "#3d8bfd", fillOpacity: 0.35, color: "#5eead4", weight: 1.5, opacity: 0.85 };
    var styleHover = { fillColor: "#5ba8ff", fillOpacity: 0.52, color: "#7ee8f7", weight: 2, opacity: 1 };
    function pickRandom(arr) {
      if (!arr || !arr.length) {
        return "";
      }
      return arr[Math.floor(Math.random() * arr.length)];
    }

    function getDistrictName(props) {
      return (props && (props.v_kreis || props.name || props.NAME)) || "Unbekannt";
    }

    function clearPoiMarkers() {
      if (poiLayerGroup) {
        poiLayerGroup.clearLayers();
      }
    }

    function markerPanelData(point) {
      return {
        title: point.title || "",
        description: point.description || "",
        photo: point.photo || "",
        link: point.link || "",
      };
    }

    function addPoiMarker(lat, lng, data) {
      var marker = L.marker([lat, lng], { icon: poiIcon, poiData: data, riseOnHover: true });
      var title = (data.title || "").trim();
      if (title) {
        var iconH = (cfg.markerIcon && cfg.markerIcon.height) || 40;
        marker.bindTooltip(title, {
          direction: "top",
          offset: [0, -iconH - 6],
          className: "map-plum-marker-tooltip",
          opacity: 1,
          sticky: true,
        });
      }
      marker.on("click", function (e) {
        openPanel(e.target.options.poiData);
        L.DomEvent.stopPropagation(e);
      });
      marker.addTo(poiLayerGroup);
    }

    function placeCategoryMarkers(categoryId) {
      if (!map || !geoLayer) {
        return;
      }
      clearPoiMarkers();
      var key = String(categoryId);
      var points = markersByCategory[key] || [];
      points.forEach(function (point) {
        if (typeof point.lat !== "number" || typeof point.lng !== "number") {
          return;
        }
        addPoiMarker(point.lat, point.lng, markerPanelData(point));
      });
    }

    function buildToolbar() {
      if (!toolbar) {
        return;
      }
      toolbar.innerHTML = "";
      categories.forEach(function (cat) {
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "map-toolbar-btn";
        btn.setAttribute("data-category-id", String(cat.id));
        btn.textContent = cat.name || "";
        toolbar.appendChild(btn);
      });
    }

    function updateToolbarActive(categoryId) {
      if (!toolbar) {
        return;
      }
      var active = categoryId != null ? String(categoryId) : "";
      var buttons = toolbar.querySelectorAll(".map-toolbar-btn");
      buttons.forEach(function (btn) {
        var current = btn.getAttribute("data-category-id");
        btn.classList.toggle("active", current === active);
      });
    }

    function toggleCategory(categoryId) {
      var id = String(categoryId);
      if (activeCategoryId === id) {
        activeCategoryId = null;
        clearPoiMarkers();
        updateToolbarActive(null);
        return;
      }
      activeCategoryId = id;
      updateToolbarActive(id);
      placeCategoryMarkers(id);
    }

    function refreshMapSize() {
      if (!map) {
        return;
      }
      map.invalidateSize({ animate: false });
    }

    function openPanel(info) {
      if (panelTitle) {
        panelTitle.textContent = info.title || "";
      }

      var photoSrc = info.photo || pickRandom(fallbackPhotos);
      if (panelPhotoWrap && panelPhoto) {
        if (photoSrc) {
          panelPhoto.setAttribute("src", photoSrc);
          panelPhoto.setAttribute("alt", info.title || "");
          panelPhoto.setAttribute("loading", "lazy");
          panelPhotoWrap.hidden = false;
        } else {
          panelPhoto.removeAttribute("src");
          panelPhotoWrap.hidden = true;
        }
      }

      if (panelDesc) {
        if (info.description) {
          panelDesc.innerHTML = info.description;
          panelDesc.hidden = false;
        } else {
          panelDesc.innerHTML = "";
          panelDesc.hidden = true;
        }
      }

      if (panelLinkWrap && panelLink) {
        if (info.link) {
          panelLink.href = info.link;
          panelLink.textContent = i18n.readMore || "Read more";
          panelLinkWrap.hidden = false;
        } else {
          panelLinkWrap.hidden = true;
          panelLink.removeAttribute("href");
        }
      }
      panel.classList.add("open", "has-selection");
      setTimeout(refreshMapSize, 400);
    }

    function closePanel() {
      panel.classList.remove("open", "has-selection");
      setTimeout(refreshMapSize, 400);
    }

    function initMap() {
      map = L.map(mapEl, { zoomControl: true, attributionControl: true }).setView(mapCenter, mapZoom);

      var cartoConfig = window.mapPlumCartoConfig || {};
      var cartoKey = cartoConfig.apiKey || window.mapPlumCartoApiKey || "";
      var tileUrl =
        cartoConfig.tileUrlTemplate ||
        "https://{s}.basemaps.cartocdn.com/rastertiles/dark_all/{z}/{x}/{y}{r}.png";

      if (cartoKey && tileUrl.indexOf("key=") === -1) {
        tileUrl += (tileUrl.indexOf("?") >= 0 ? "&" : "?") + "key=" + encodeURIComponent(cartoKey);
      }

      L.tileLayer(tileUrl, {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: "abcd",
        maxZoom: 19,
      }).addTo(map);

      geoLayer = L.geoJSON(districts, {
        style: styleDefault,
        onEachFeature: function (feature, layer) {
          var name = getDistrictName(feature.properties);
          layer.bindTooltip(name, { permanent: false, direction: "center", className: "district-label" });
          layer.on({
            mouseover: function (e) {
              e.target.setStyle(styleHover);
              e.target.bringToFront();
            },
            mouseout: function (e) {
              geoLayer.resetStyle(e.target);
            },
          });
        },
      }).addTo(map);

      map.fitBounds(geoLayer.getBounds(), { padding: [12, 12] });
      map.zoomControl.setPosition("topright");
      poiLayerGroup = L.layerGroup().addTo(map);
      refreshMapSize();
    }

    buildToolbar();
    initMap();
    setTimeout(refreshMapSize, 50);
    setTimeout(refreshMapSize, 300);
    window.addEventListener("resize", refreshMapSize);
    if (window.ResizeObserver) {
      new ResizeObserver(refreshMapSize).observe(widget);
    }

    panelClose.addEventListener("click", closePanel);

    if (toolbar) {
      toolbar.addEventListener("click", function (e) {
        var button = e.target.closest(".map-toolbar-btn");
        if (!button) {
          return;
        }
        var categoryId = button.getAttribute("data-category-id");
        if (categoryId) {
          toggleCategory(categoryId);
        }
      });
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        closePanel();
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    var widgets = document.querySelectorAll(".map-plum-map-widget[data-map-slug]");
    widgets.forEach(initMapWidget);
  });
})();
