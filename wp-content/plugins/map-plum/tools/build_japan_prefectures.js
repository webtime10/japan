/**
 * Build sorts/jp/* from tools/japan.geojson (dataofjapan/land).
 *
 * Usage: node tools/build_japan_prefectures.js
 */
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const srcPath = path.join(__dirname, 'japan.geojson');
const jpRoot = path.join(root, 'sorts', 'jp');

const PREFECTURES = {
	1: { slug: 'hokkaido', name: 'Hokkaido', nameJa: '北海道' },
	2: { slug: 'aomori', name: 'Aomori', nameJa: '青森県' },
	3: { slug: 'iwate', name: 'Iwate', nameJa: '岩手県' },
	4: { slug: 'miyagi', name: 'Miyagi', nameJa: '宮城県' },
	5: { slug: 'akita', name: 'Akita', nameJa: '秋田県' },
	6: { slug: 'yamagata', name: 'Yamagata', nameJa: '山形県' },
	7: { slug: 'fukushima', name: 'Fukushima', nameJa: '福島県' },
	8: { slug: 'ibaraki', name: 'Ibaraki', nameJa: '茨城県' },
	9: { slug: 'tochigi', name: 'Tochigi', nameJa: '栃木県' },
	10: { slug: 'gunma', name: 'Gunma', nameJa: '群馬県' },
	11: { slug: 'saitama', name: 'Saitama', nameJa: '埼玉県' },
	12: { slug: 'chiba', name: 'Chiba', nameJa: '千葉県' },
	13: { slug: 'tokyo', name: 'Tokyo', nameJa: '東京都' },
	14: { slug: 'kanagawa', name: 'Kanagawa', nameJa: '神奈川県' },
	15: { slug: 'niigata', name: 'Niigata', nameJa: '新潟県' },
	16: { slug: 'toyama', name: 'Toyama', nameJa: '富山県' },
	17: { slug: 'ishikawa', name: 'Ishikawa', nameJa: '石川県' },
	18: { slug: 'fukui', name: 'Fukui', nameJa: '福井県' },
	19: { slug: 'yamanashi', name: 'Yamanashi', nameJa: '山梨県' },
	20: { slug: 'nagano', name: 'Nagano', nameJa: '長野県' },
	21: { slug: 'gifu', name: 'Gifu', nameJa: '岐阜県' },
	22: { slug: 'shizuoka', name: 'Shizuoka', nameJa: '静岡県' },
	23: { slug: 'aichi', name: 'Aichi', nameJa: '愛知県' },
	24: { slug: 'mie', name: 'Mie', nameJa: '三重県' },
	25: { slug: 'shiga', name: 'Shiga', nameJa: '滋賀県' },
	26: { slug: 'kyoto', name: 'Kyoto', nameJa: '京都府' },
	27: { slug: 'osaka', name: 'Osaka', nameJa: '大阪府' },
	28: { slug: 'hyogo', name: 'Hyogo', nameJa: '兵庫県' },
	29: { slug: 'nara', name: 'Nara', nameJa: '奈良県' },
	30: { slug: 'wakayama', name: 'Wakayama', nameJa: '和歌山県' },
	31: { slug: 'tottori', name: 'Tottori', nameJa: '鳥取県' },
	32: { slug: 'shimane', name: 'Shimane', nameJa: '島根県' },
	33: { slug: 'okayama', name: 'Okayama', nameJa: '岡山県' },
	34: { slug: 'hiroshima', name: 'Hiroshima', nameJa: '広島県' },
	35: { slug: 'yamaguchi', name: 'Yamaguchi', nameJa: '山口県' },
	36: { slug: 'tokushima', name: 'Tokushima', nameJa: '徳島県' },
	37: { slug: 'kagawa', name: 'Kagawa', nameJa: '香川県' },
	38: { slug: 'ehime', name: 'Ehime', nameJa: '愛媛県' },
	39: { slug: 'kochi', name: 'Kochi', nameJa: '高知県' },
	40: { slug: 'fukuoka', name: 'Fukuoka', nameJa: '福岡県' },
	41: { slug: 'saga', name: 'Saga', nameJa: '佐賀県' },
	42: { slug: 'nagasaki', name: 'Nagasaki', nameJa: '長崎県' },
	43: { slug: 'kumamoto', name: 'Kumamoto', nameJa: '熊本県' },
	44: { slug: 'oita', name: 'Oita', nameJa: '大分県' },
	45: { slug: 'miyazaki', name: 'Miyazaki', nameJa: '宮崎県' },
	46: { slug: 'kagoshima', name: 'Kagoshima', nameJa: '鹿児島県' },
	47: { slug: 'okinawa', name: 'Okinawa', nameJa: '沖縄県' },
};

function walkCoords(coords, fn) {
	if (typeof coords[0] === 'number') {
		fn(coords[0], coords[1]);
		return;
	}
	coords.forEach((part) => walkCoords(part, fn));
}

function bboxFromGeometry(geometry) {
	let minLng = Infinity;
	let minLat = Infinity;
	let maxLng = -Infinity;
	let maxLat = -Infinity;
	walkCoords(geometry.coordinates, (lng, lat) => {
		if (lng < minLng) minLng = lng;
		if (lat < minLat) minLat = lat;
		if (lng > maxLng) maxLng = lng;
		if (lat > maxLat) maxLat = lat;
	});
	return [minLat, minLng, maxLat, maxLng];
}

function centerFromBbox(bbox) {
	return [
		(bbox[0] + bbox[2]) / 2,
		(bbox[1] + bbox[3]) / 2,
	];
}

function zoomFromBbox(bbox) {
	const latSpan = Math.abs(bbox[2] - bbox[0]);
	const lngSpan = Math.abs(bbox[3] - bbox[1]);
	const span = Math.max(latSpan, lngSpan);
	if (span > 8) return 5;
	if (span > 4) return 6;
	if (span > 2) return 7;
	if (span > 1) return 8;
	if (span > 0.5) return 9;
	if (span > 0.25) return 10;
	return 11;
}

function ensureDir(dir) {
	fs.mkdirSync(dir, { recursive: true });
}

function writeJson(filePath, data) {
	fs.writeFileSync(filePath, JSON.stringify(data));
}

function writeDataPhp(filePath, center, zoom) {
	const content = `<?php
/**
 * Map Plum — auto-generated prefecture map settings.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( ${center[0].toFixed(5)}, ${center[1].toFixed(5)} ),
	'map_zoom'   => ${zoom},
);
`;
	fs.writeFileSync(filePath, content);
}

function phpString(value) {
	return `'${String(value).replace(/\\/g, '\\\\').replace(/'/g, "\\'")}'`;
}

function buildRegistryEntries(metaBySlug) {
	const lines = [];
	Object.keys(PREFECTURES)
		.sort((a, b) => Number(a) - Number(b))
		.forEach((id) => {
			const pref = PREFECTURES[id];
			const meta = metaBySlug[pref.slug];
			const code = `JP${String(id).padStart(2, '0')}`;
			lines.push(`\t\t'${pref.slug}' => array(
\t\t\t'name'                 => ${phpString(pref.name)},
\t\t\t'code'                 => ${phpString(code)},
\t\t\t'aliases'              => array( ${phpString(code.toLowerCase())} ),
\t\t\t'manufacturer_needles' => array( ${phpString(pref.slug)}, ${phpString(pref.nameJa)} ),
\t\t\t'map_center'           => array( ${meta.center[0].toFixed(5)}, ${meta.center[1].toFixed(5)} ),
\t\t\t'map_zoom'             => ${meta.zoom},
\t\t\t'bbox'                 => array( ${meta.bbox.map((n) => n.toFixed(5)).join(', ')} ),
\t\t\t'title'                => ${phpString(`Prefecture of ${pref.name}`)},
\t\t\t'subtitle'             => 'Click on the map',
\t\t),`);
		});

	const japanBbox = metaBySlug.__japan__.bbox;
	const japanCenter = metaBySlug.__japan__.center;
	lines.push(`\t\t'japan' => array(
\t\t\t'name'                 => 'Japan',
\t\t\t'code'                 => 'JP',
\t\t\t'aliases'              => array( 'nippon', 'nihon', 'jp' ),
\t\t\t'manufacturer_needles' => array( 'japan', 'nippon', 'nihon', 'япония', '日本' ),
\t\t\t'map_center'           => array( ${japanCenter[0].toFixed(5)}, ${japanCenter[1].toFixed(5)} ),
\t\t\t'map_zoom'             => 5,
\t\t\t'bbox'                 => array( ${japanBbox.map((n) => n.toFixed(5)).join(', ')} ),
\t\t\t'title'                => 'Japan',
\t\t\t'subtitle'             => 'Click on a prefecture',
\t\t),`);

	return lines.join('\n');
}

function main() {
	if (!fs.existsSync(srcPath)) {
		console.error('Missing tools/japan.geojson — download it first.');
		process.exit(1);
	}

	const geo = JSON.parse(fs.readFileSync(srcPath, 'utf8'));
	const metaBySlug = {};
	const countryFeatures = [];

	geo.features.forEach((feature) => {
		const id = Number(feature.properties && feature.properties.id);
		const pref = PREFECTURES[id];
		if (!pref) {
			console.warn('Unknown prefecture id:', id);
			return;
		}

		const bbox = bboxFromGeometry(feature.geometry);
		const center = centerFromBbox(bbox);
		const zoom = zoomFromBbox(bbox);
		metaBySlug[pref.slug] = { bbox, center, zoom };

		const countryFeature = {
			type: 'Feature',
			properties: {
				name: pref.name,
				v_kreis: pref.name,
				prefecture_slug: pref.slug,
				code: `JP${String(id).padStart(2, '0')}`,
				name_ja: pref.nameJa,
			},
			geometry: feature.geometry,
		};
		countryFeatures.push(countryFeature);

		const prefDir = path.join(jpRoot, pref.slug);
		ensureDir(prefDir);
		writeDataPhp(path.join(prefDir, `${pref.slug}-data.php`), center, zoom);
		writeJson(path.join(prefDir, `${pref.slug}-districts.json`), {
			type: 'FeatureCollection',
			name: `${pref.slug}-districts`,
			features: [
				{
					type: 'Feature',
					properties: {
						name: pref.name,
						v_kreis: pref.name,
						code: `JP${String(id).padStart(2, '0')}`,
						name_ja: pref.nameJa,
					},
					geometry: feature.geometry,
				},
			],
		});
	});

	let japanMinLat = Infinity;
	let japanMinLng = Infinity;
	let japanMaxLat = -Infinity;
	let japanMaxLng = -Infinity;
	countryFeatures.forEach((feature) => {
		const bbox = bboxFromGeometry(feature.geometry);
		japanMinLat = Math.min(japanMinLat, bbox[0]);
		japanMinLng = Math.min(japanMinLng, bbox[1]);
		japanMaxLat = Math.max(japanMaxLat, bbox[2]);
		japanMaxLng = Math.max(japanMaxLng, bbox[3]);
	});
	metaBySlug.__japan__ = {
		bbox: [japanMinLat, japanMinLng, japanMaxLat, japanMaxLng],
		center: centerFromBbox([japanMinLat, japanMinLng, japanMaxLat, japanMaxLng]),
		zoom: 5,
	};

	const japanDir = path.join(jpRoot, 'japan');
	ensureDir(japanDir);
	writeDataPhp(path.join(japanDir, 'japan-data.php'), metaBySlug.__japan__.center, 5);
	writeJson(path.join(japanDir, 'japan-districts.json'), {
		type: 'FeatureCollection',
		name: 'japan-prefectures',
		features: countryFeatures,
	});

	const registryPhp = `<?php
/**
 * Auto-generated Japan prefecture registry for Map Plum.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
${buildRegistryEntries(metaBySlug)}
);
`;

	fs.writeFileSync(path.join(root, 'inc', 'map-plum-japan-registry.generated.php'), registryPhp);
	console.log(`Built ${countryFeatures.length} prefectures in sorts/jp/`);
}

main();
