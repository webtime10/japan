/**
 * Budget calculator — константы (картинки из wp_localize_script).
 */
(function (window) {
	'use strict';

	window.AiCalculatorVue3 = window.AiCalculatorVue3 || {};

	var config = window.aiCalculatorBudget || {};
	var images = config.images && typeof config.images === 'object' ? config.images : {};

	window.AiCalculatorVue3.BudgetConstants = {
		IMAGES: images,
		HOUSING: {
			OTELI: 'oteli',
			APARTAMENTI: 'apartamenti',
		},
		COMFORT: {
			DESHEVLE: 'deshevle',
			SREDNII: 'sredniii',
			VISOKII: 'visokii',
		},
		ENTERTAINMENT: {
			DAILY: 'kazdii_den',
			FEW_DAYS: 'razvlechenia_raz_v_neskolko_dnay',
			MIN_PAID: 'kak_mojno_menhe_platnix',
		},
		DINING: {
			GOOD: 'restorany_xoroshego_uravna',
			BUDGET: 'nedorogie_restorany_kafe',
			HOME: 'v_osnovnom_gotovit_doma',
		},
		CAR_RENTAL: {
			YES: 'da',
			NO: 'net',
		},
		CAR_CLASS: {
			HIGH: 'visokii_avto',
			MEDIUM: 'srednii_avto',
			BUDGET: 'deshovii_avto',
		},
		BUDGET_PRIORITY: {
			RELAX: 'budget_ne_vagen',
			BALANCE: 'bydget_vasgen',
			STRICT: 'budzet_vashnee_vsego',
		},
		getImage: function (key) {
			return images[key] ? String(images[key]) : '';
		},
	};
})(window);
