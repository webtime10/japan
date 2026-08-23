const fs = require('fs');
const path = require('path');
const topojson = require('topojson-client');

const topoPath = path.join(__dirname, 'ch-combined.json');
const outPath = path.join(__dirname, '../sorts/sw/switzerland/switzerland-districts.json');

const cantonNames = {
	1: 'Zürich',
	2: 'Bern',
	3: 'Luzern',
	4: 'Uri',
	5: 'Schwyz',
	6: 'Obwalden',
	7: 'Nidwalden',
	8: 'Glarus',
	9: 'Zug',
	10: 'Fribourg',
	11: 'Solothurn',
	12: 'Basel-Stadt',
	13: 'Basel-Landschaft',
	14: 'Schaffhausen',
	15: 'Appenzell Ausserrhoden',
	16: 'Appenzell Innerrhoden',
	17: 'St. Gallen',
	18: 'Graubünden',
	19: 'Aargau',
	20: 'Thurgau',
	21: 'Ticino',
	22: 'Vaud',
	23: 'Valais',
	24: 'Neuchâtel',
	25: 'Genève',
	26: 'Jura',
};

const cantonSlugs = {
	1: 'zurich',
	2: 'bern',
	3: 'lucerne',
	4: 'uri',
	5: 'schwyz',
	6: 'obwalden',
	7: 'nidwalden',
	8: 'glarus',
	9: 'zug',
	10: 'fribourg',
	11: 'solothurn',
	12: 'basel-stadt',
	13: 'basel-landschaft',
	14: 'schaffhausen',
	15: 'appenzell-ausserrhoden',
	16: 'appenzell-innerrhoden',
	17: 'st-gallen',
	18: 'graubunden',
	19: 'aargau',
	20: 'thurgau',
	21: 'ticino',
	22: 'vaud',
	23: 'valais',
	24: 'neuchatel',
	25: 'geneva',
	26: 'jura',
};

const topo = JSON.parse(fs.readFileSync(topoPath, 'utf8'));
const geo = topojson.feature(topo, topo.objects.cantons);

geo.name = 'switzerland-cantons';
geo.features = geo.features.map((feature) => {
	const id = feature.id;
	const name = cantonNames[id] || `Canton ${id}`;
	const slug = cantonSlugs[id] || `canton-${id}`;

	return {
		type: 'Feature',
		properties: {
			v_kreis: name,
			name,
			id,
			canton_slug: slug,
		},
		geometry: feature.geometry,
	};
});

geo.features.sort((a, b) => String(a.properties.name).localeCompare(String(b.properties.name)));

fs.mkdirSync(path.dirname(outPath), { recursive: true });
fs.writeFileSync(outPath, JSON.stringify(geo, null, 2) + '\n');
console.log(`Saved ${geo.features.length} cantons to ${outPath}`);
