/**
 * Your Ideal Region — constants & config.
 */
(function (window) {
	'use strict';

	window.AiCalculatorVue3 = window.AiCalculatorVue3 || {};

	var config = window.aiCalculatorIdealRegion || {};
	var catalogCards = Array.isArray(window.aiCalculatorIdealRegionData)
		? window.aiCalculatorIdealRegionData
		: (Array.isArray(config.catalogCards) ? config.catalogCards : []);
	var maxStep = Number(config.maxStep) > 0
		? Number(config.maxStep)
		: (Number(window.aiCalculatorIdealRegion && window.aiCalculatorIdealRegion.maxStep) > 0
			? Number(window.aiCalculatorIdealRegion.maxStep)
			: 1);

	window.AiCalculatorVue3.IdealRegionConstants = {
		MAX_STEP: maxStep,
		CATALOG_CARDS: catalogCards,
		LABELS: config.labels && typeof config.labels === 'object' ? config.labels : {},
	};

})(window);
