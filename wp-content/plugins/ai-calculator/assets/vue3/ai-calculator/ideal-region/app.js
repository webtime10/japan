/**
 * Your Ideal Region — Vue 3 mount.
 */
(function () {
	'use strict';

	var LOG = '[AI Ideal Region]';
	var mountEl = document.getElementById('ai-calculator-ideal-region-app');

	if (!mountEl) {
		return;
	}

	if (typeof window.Vue === 'undefined' || typeof window.Vue.createApp !== 'function') {
		console.warn(LOG, 'Vue 3 is not loaded — check CDN in Network');
		return;
	}

	var component = window.AiCalculatorVue3 && window.AiCalculatorVue3.IdealRegion;
	if (!component) {
		console.warn(LOG, 'IdealRegion.js is not loaded — check components/IdealRegion.js in Network');
		return;
	}

	window.Vue.createApp(component).mount(mountEl);
	mountEl.style.removeProperty('display');
	mountEl.removeAttribute('v-cloak');
	console.log(LOG, 'app.js: mount complete');
})();
