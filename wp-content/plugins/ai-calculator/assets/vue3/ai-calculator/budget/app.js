/**
 * Budget calculator — Vue 3 mount.
 */
(function () {
	'use strict';

	var LOG = '[AI Budget]';
	var mountEl = document.getElementById('ai-calculator-budget-app');

	if (!mountEl) {
		return;
	}

	if (typeof window.Vue === 'undefined' || typeof window.Vue.createApp !== 'function') {
		console.warn(LOG, 'Vue 3 не загружен — проверьте CDN в Network');
		return;
	}

	var component = window.AiCalculatorVue3 && window.AiCalculatorVue3.Budget;
	if (!component) {
		console.warn(LOG, 'Budget.js не загружен — проверьте components/Budget.js в Network');
		return;
	}

	window.Vue.createApp(component).mount(mountEl);
	mountEl.style.removeProperty('display');
	mountEl.removeAttribute('v-cloak');
	console.log(LOG, 'app.js: mount выполнен');
})();
