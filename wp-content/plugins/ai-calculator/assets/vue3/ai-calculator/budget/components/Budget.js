/**
 * Budget calculator — корневой Vue-компонент.
 */
(function (window) {
	'use strict';

	var LOG = '[AI Budget]'; // для логов

	window.AiCalculatorVue3 = window.AiCalculatorVue3 || {}; // найм спасе

	window.AiCalculatorVue3.Budget = { // глоюальначя папка
		name: 'BudgetCalculator', // для дебага четое имя класса в пхп
//Если коротко: это декларация «оперативной памяти» твоего компонента. Во Vue всё, что находится внутри этой функции, автоматически становится реактивным.
		data: function () {
			var catalogCards = Array.isArray(window.aiCalculatorData) ? window.aiCalculatorData : [];
// получаем масив
			return { // то с чем работаю
				step: 1,  //Текущий активный шаг калькулятора. Раз тут стоит 1, значит при первой загрузке сработает условие v-if="step === 1", и юзер увидит первый экран (календари).
				catalogStartStep: 1, //Технический маркер. Он говорит коду, что динамические карточки категорий должны пойти в бой со второго шага (v-if="step === 2").
				travelerOptions: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
				quantityOptions: [1, 2, 3, 4, 5, 6, 7],
				ageOptions: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17],
				catalogCards: catalogCards, // массив
				swissRegions: (window.aiCalculatorBudget && Array.isArray(window.aiCalculatorBudget.swissRegions))
					? window.aiCalculatorBudget.swissRegions
					: [],
				
				//Общак Это структура, в которую калькулятор будет бережно складывать всё, что выбирает или вводит пользователь на экранах:
				answers: {
					catalog: {
						trip_dates: {
							dateMode: 'approx',
							dateFrom: '',
							dateTo: '',
							durationDays: '',
							monthLabel: '',
						},
						travelers: { quantity: '' },
						children: { hasChildren: '', quantity: '', ages: [] },
						region: { region: '' },
						housing: { housingType: '' },
						comfort: { comfortLevel: '' },
						entertainment: { entertainmentLevel: '' },
						dining: { diningLevel: '' },
						car_rental: { carRental: '' },
						car_class: { carClass: '' },
						budget_priority: { budgetPriority: '' },
					},
				},
				childOrdinalLabels: [
					'Первый ребенок',
					'Второй ребенок',
					'Третий ребенок',
					'Четвертый ребенок',
					'Пятый ребенок',
					'Шестой ребенок',
					'Седьмой ребенок',
				],
				isOrderModalOpen: false,
				budgetResult: null,
				itemBaseTotal: '',
				itemTotal: '',
				honeypot: '',
				isSubmitting: false,
				submitError: '',
				answersReceived: false,
				quizAnswerId: 0,
			};
		},

		computed: {  // это выноска данных здесь происходит типа select в php
			//создаю объект totalSteps: в нем массив
			totalSteps: function () {
				return Math.max(1, this.catalogCards.length);
			},

			questionStepsCount: function () {
				var count = 0;
				var i;

				for (i = 0; i < this.catalogCards.length; i++) {
					if (!this.isResultCard(this.catalogCards[i])) {
						count++;
					}
				}

				return Math.max(1, count);
			},

			//progressPercent в нем условие %
			progressPercent: function () {
				if (this.questionStepsCount <= 0) {
					return 0;
				}

				return Math.round((this.step / this.questionStepsCount) * 100);
			},

			resultCard: function () {
				var i;
				for (i = 0; i < this.catalogCards.length; i++) {
					if (this.isResultCard(this.catalogCards[i])) {
						return this.catalogCards[i];
					}
				}
				return null;
			},

			isResultStep: function () {
				return !!(this.resultCard && this.step === this.totalSteps);
			},

			orderRouteLabel: function () {
				return this.uiLabel('order_route');
			},

			datesCard: function () {
				return this.questionCardAt(0);
			},

			travelersCard: function () {
				return this.questionCardAt(1);
			},

			childrenCard: function () {
				return this.questionCardAt(2);
			},

			regionCard: function () {
				return this.questionCardAt(3);
			},

			housingCard: function () {
				return this.questionCardAt(4);
			},

			comfortLevelCard: function () {
				return this.questionCardAt(5);
			},

			entertainmentCard: function () {
				return this.questionCardAt(6);
			},

			diningCard: function () {
				return this.questionCardAt(7);
			},

			carRentalCard: function () {
				return this.questionCardAt(8);
			},

			carClassCard: function () {
				return this.questionCardAt(9);
			},

			budgetPriorityCard: function () {
				return this.questionCardAt(10);
			},

			swissRegionsList: function () {
				if (window.aiCalculatorBudget && Array.isArray(window.aiCalculatorBudget.swissRegions)) {
					return window.aiCalculatorBudget.swissRegions;
				}

				return this.swissRegions;
			},

			childrenQuantity: function () {
				var entry = this.answers.catalog.children;
				return entry && entry.quantity ? String(entry.quantity) : '';
			},

			childrenAgeSlots: function () {
				var count = parseInt(this.childrenQuantity, 10);
				if (!count || count < 1) {
					return [];
				}

				var slots = [];
				for (var i = 1; i <= count; i++) {
					slots.push(i);
				}

				return slots;
			},
		},
/*
И хук created говорит движку: «Так, пока ты ещё не вывел интерфейс на экран, давай быстренько запустим метод initCatalogAnswers(), чтобы у нас в памяти уже лежали готовые переменные и массивы для ответов».
*/
		created: function () {
			this.initCatalogAnswers();
		},
//mounted — это момент, (чтоб бе события чтотот сделать например покарсить в краный и тд) когда Vue полностью взял твой HTML-шаблон, засунул в него данные и зарендерил на экран
// инициализируем работу джайквери календаря

mounted: function () {
			var self = this;
			this.syncStepAttr(this.step);
			this.$nextTick(function () {
				self.initDatePickers();
			});
			this._onOrderModalKeydown = function (event) {
				if (event.key === 'Escape' && self.isOrderModalOpen) {
					self.closeOrderModal();
				}
			};
			document.addEventListener('keydown', this._onOrderModalKeydown);
			this.debugLog('Vue смонтирован', {
				step: this.step,
				totalSteps: this.totalSteps,
				progressPercent: this.progressPercent,
				answers: this.getAnswersSnapshot(),
				travelersCard: this.travelersCard,
			});
		},

		beforeUnmount: function () {
			if (this._onOrderModalKeydown) {
				document.removeEventListener('keydown', this._onOrderModalKeydown);
			}
			document.body.classList.remove('modal-open');
			document.body.classList.remove('ai-bg-is-submitting');
		},
//watch — это автозапуск в ответ на изменение конкретной переменной.
		watch: {
			step: function (value) {
				var self = this;
				this.syncStepAttr(value);
				if (value === 1) {  // если шаг равно 1 запусти дата пикер веренее активизируй его
					this.$nextTick(function () {
						self.initDatePickers();
					});
				}
				this.debugLog('Шаг ' + value, this.progressPercent + '%');
			},
/*
Vue непрерывно караулит переменную step. Как только юзер нажимает кнопку «Далее» или «Назад» и шаг меняется, этот код срабатывает автоматически. Внутрь функции Vue сам передает новое значение (value) — например, цифру 2 или 1.
*/

			'answers.catalog.trip_dates.dateMode': function () {
				var self = this;
				this.$nextTick(function () {
					self.initDatePickers();
				});
			},

			childrenQuantity: function () {
				this.syncChildrenAges();
			},

			'answers.catalog.children.hasChildren': function (value) {
				if (value === 'net') {
					this.answers.catalog.children.quantity = '0';
					this.answers.catalog.children.ages = [];
				} else if (value === 'da' && this.answers.catalog.children.quantity === '0') {
					this.answers.catalog.children.quantity = '';
				}
			},

			'answers.catalog.car_rental.carRental': function () {
				if (this.shouldSkipCarClassStep()) {
					this.answers.catalog.car_class.carClass = '';
				}
			},
		},
// methods складывается всё, что должно запускаться в ответ на действия пользователя или по твоей явной команде.
		methods: {
			uiLabel: function (key) {
				var defaults = {
					question: 'Вопрос',
					of: 'из',
					next: 'Далее',
					back: 'Назад',
					order_route: 'Заказать маршрут',
					close: 'Закрыть',
				};
				var labels = window.aiCalculatorBudget && window.aiCalculatorBudget.labels;
				var value = labels && labels[key] ? String(labels[key]).trim() : '';

				if (value) {
					return value;
				}

				return defaults[key] || String(key);
			},

/*
Получается, как я не отслеживаю, по какому На каком этапе где что нажал, то есть, Я так понял, что это отслеживается вообще. То есть, на каком этапе Я нахожусь это находится в этом фреймворке. Но по умолчанию. А, когда я нажимаю, оно автоматически сюда показывает, поэтому я не указываю. На каком шаге, то есть на третьем я или на четвёртом и на пятом сюда что-то записал.
в отличчи от джайквери

*/

		// Сброс ответов по шагам каталога (осмысленные ключи вместо category_id).
			initCatalogAnswers: function () {
				this.answers.catalog = {
					trip_dates: {
						dateMode: 'approx',
						dateFrom: '',
						dateTo: '',
						durationDays: '',
						monthLabel: '',
					},
					travelers: { quantity: '' },
					children: { hasChildren: '', quantity: '', ages: [] },
					region: { region: '' },
					housing: { housingType: '' },
					comfort: { comfortLevel: '' },
					entertainment: { entertainmentLevel: '' },
					dining: { diningLevel: '' },
					car_rental: { carRental: '' },
					car_class: { carClass: '' },
					budget_priority: { budgetPriority: '' },
				};
			},

			getChildOrdinalLabel: function (slot) {
				var index = parseInt(slot, 10) - 1;

				if (index >= 0 && index < this.childOrdinalLabels.length) {
					return this.childOrdinalLabels[index];
				}

				return 'Ребенок ' + slot;
			},

			getChildAge: function (slot) {
				var entry = this.answers.catalog.children;
				var index = parseInt(slot, 10) - 1;

				if (!entry || !Array.isArray(entry.ages) || index < 0) {
					return '';
				}

				return entry.ages[index] ? String(entry.ages[index]) : '';
			},

			setChildAge: function (slot, value) {
				var entry = this.answers.catalog.children;
				var index = parseInt(slot, 10) - 1;

				if (!entry || index < 0) {
					return;
				}

				if (!Array.isArray(entry.ages)) {
					entry.ages = [];
				}

				entry.ages[index] = value;
			},

			shouldShowChildrenSelect: function () {
				return this.answers.catalog.children.hasChildren === 'da';
			},

			onChildrenAnswerChange: function () {
				var entry = this.answers.catalog.children;

				if (!entry) {
					return;
				}

				if (entry.hasChildren === 'net') {
					entry.quantity = '0';
					entry.ages = [];
				}
			},
			// если пустая карточка
			isResultCard: function (card) {
				if (!card) {
					return false;
				}

				var title = card.category_title ? String(card.category_title).trim() : '';
				if (title.indexOf('Расчет') !== -1 || title.indexOf('расчет') !== -1) {
					return true;
				}

				var block1 = this.getCardBlock(card, 1);
				return block1 === 'Транспорт' || block1 === 'Transport';
			},

			questionCardAt: function (index) {
				var cards = [];
				var i;

				for (i = 0; i < this.catalogCards.length; i++) {
					if (!this.isResultCard(this.catalogCards[i])) {
						cards.push(this.catalogCards[i]);
					}
				}

				return cards[index] || null;
			},

			getCardBlock: function (card, blockNum) {
				if (!card || blockNum < 1 || blockNum > 6) {
					return '';
				}
//block1block2 и тд
				var key = 'block' + blockNum;
				return card[key] ? String(card[key]) : '';
			},
//Метод сразу шлёт всех нахер и возвращает пустоту (''), если выполняется хотя бы одно из условий:
			getCardProductName: function (card, productIndex) {
				if (!card || !Array.isArray(card.products) || !card.products[productIndex]) {
					return '';
				}

				var product = card.products[productIndex];
				var label = product.product_name || product.name || '';

				return label ? String(label) : '';
			},
// Этот код отвечает за подготовку и очистку текстовой строки, которая потом будет использована в качестве заголовка.
			getCardTitle: function (card) {
				if (!card) {
					return '';
				}

				var productName = this.getCardProductName(card, 0);
				var title = card.category_title ? String(card.category_title).trim() : '';
				if (productName) {
					return productName;
				}

				return title && !/^#\d+$/.test(title) ? title : '';
			},
// берем поля блок1,23 и закид
			getHousingQuestion: function (card) {
				return this.getRadioCardQuestion(card, 2);
			},

			getComfortLevelQuestion: function (card) {
				return this.getRadioCardQuestion(card, 3);
			},

			getEntertainmentQuestion: function (card) {
				return this.getRadioCardQuestion(card, 3);
			},

			getDiningQuestion: function (card) {
				return this.getRadioCardQuestion(card, 3);
			},

			getCarRentalQuestion: function (card) {
				return this.getRadioCardQuestion(card, 2);
			},

			getCarClassQuestion: function (card) {
				return this.getRadioCardQuestion(card, 3);
			},

			getBudgetPriorityQuestion: function (card) {
				return this.getRadioCardQuestion(card, 3);
			},

			getRadioCardQuestion: function (card, blocksCount) {
				// 1. ЗАЩИТА: Если карточки нет — возвращаем пустоту
				if (!card) {
					return '';
				}
			
				// 2. ЗАГОТОВКА: Запоминаем имя самого первого продукта в этой карточке
				var productName = this.getCardProductName(card, 0);
				var i;
			
				// 3. ПРОВЕРКА НАЛИЧИЯ КОНТЕНТА (Главный фокус):
				// Цикл бежит по текстовым блокам вариантов ответов (от 1 до blocksCount).
				for (i = 1; i <= blocksCount; i++) {
					// Если хотя бы ОДИН контентный блок внутри карточки не заполнен в админке
					if (!this.getCardBlock(card, i)) {
						// ...значит карточка "сырая" или недопереведенная. 
						// Метод прерывает работу и сразу возвращает общее имя категории.
						return this.getCardTitle(card);
					}
				}
			
				// 4. ПРИОРИТЕТ №1: Если все контентные блоки на месте, и у нас заполнено 
				// имя первого продукта — отдаем его в качестве вопроса.
				if (productName) {
					return productName;
				}
			
				// 5. ПРИОРИТЕТ №2 (Крайний случай): Если имени продукта нет, 
				// всё равно отдаем заголовок карточки.
				return this.getCardTitle(card);
			},
//это метод, который достает текст-подсказку (плейсхолдер) для инпута.
			getCardPlaceholder: function (card, blockNum, fallback) {
				var text = this.getCardBlock(card, blockNum);

				return text || fallback || '';
			},

			getTravelersPlaceholder: function (card) {
				return this.getCardPlaceholder(
					card,
					2,
					(window.aiCalculatorBudget && window.aiCalculatorBudget.travelersPlaceholder)
						? String(window.aiCalculatorBudget.travelersPlaceholder)
						: 'Выберите кол-во'
				);
			},

			getRegionPlaceholder: function (card) {
				return this.getCardPlaceholder(
					card,
					2,
					(window.aiCalculatorBudget && window.aiCalculatorBudget.regionPlaceholder)
						? String(window.aiCalculatorBudget.regionPlaceholder)
						: 'Напишите регион'
				);
			},
// беру нужные строчки из админки
			getHousingOptions: function (card) {
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				var housingKeys = constants && constants.HOUSING ? constants.HOUSING : { OTELI: 'oteli', APARTAMENTI: 'apartamenti' };

				return [
					{
						value: housingKeys.OTELI,
						label: this.getCardBlock(card, 1) || this.getCardProductName(card, 0) || 'Отели',
						image: this.getBudgetImage(housingKeys.OTELI),
					},
					{
						value: housingKeys.APARTAMENTI,
						label: this.getCardBlock(card, 2) || this.getCardProductName(card, 1) || 'Квартиры / апартаменты',
						image: this.getBudgetImage(housingKeys.APARTAMENTI),
					},
				];
			},
// закидываю из админки все по пунктам
			getComfortLevelOptions: function (card) {
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				var comfortKeys = constants && constants.COMFORT
					? constants.COMFORT
					: { DESHEVLE: 'deshevle', SREDNII: 'sredniii', VISOKII: 'visokii' };

				return [
					{
						value: comfortKeys.DESHEVLE,
						label: this.getCardBlock(card, 1) || this.getCardProductName(card, 0) || 'Главное, чтобы было как можно дешевле',
						image: this.getBudgetImage(comfortKeys.DESHEVLE),
					},
					{
						value: comfortKeys.SREDNII,
						label: this.getCardBlock(card, 2) || this.getCardProductName(card, 1) || 'Средний уровень',
						image: this.getBudgetImage(comfortKeys.SREDNII),
					},
					{
						value: comfortKeys.VISOKII,
						label: this.getCardBlock(card, 3) || this.getCardProductName(card, 2) || 'Высокий уровень комфорта',
						image: this.getBudgetImage(comfortKeys.VISOKII),
					},
				];
			},

			getEntertainmentOptions: function (card) {
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				var entertainmentKeys = constants && constants.ENTERTAINMENT
					? constants.ENTERTAINMENT
					: {
						DAILY: 'kazdii_den',
						FEW_DAYS: 'razvlechenia_raz_v_neskolko_dnay',
						MIN_PAID: 'kak_mojno_menhe_platnix',
					};

				return [
					{
						value: entertainmentKeys.DAILY,
						label: this.getCardBlock(card, 1) || this.getCardProductName(card, 0) || 'Каждый день должно быть минимум одно развлечение',
						image: this.getBudgetImage(entertainmentKeys.DAILY),
					},
					{
						value: entertainmentKeys.FEW_DAYS,
						label: this.getCardBlock(card, 2) || this.getCardProductName(card, 1) || 'Развлечения раз в несколько дней',
						image: this.getBudgetImage(entertainmentKeys.FEW_DAYS),
					},
					{
						value: entertainmentKeys.MIN_PAID,
						label: this.getCardBlock(card, 3) || this.getCardProductName(card, 2) || 'Как можно меньше платных развлечений',
						image: this.getBudgetImage(entertainmentKeys.MIN_PAID),
					},
				];
			},

			getDiningOptions: function (card) {
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				var diningKeys = constants && constants.DINING
					? constants.DINING
					: {
						GOOD: 'restorany_xoroshego_uravna',
						BUDGET: 'nedorogie_restorany_kafe',
						HOME: 'v_osnovnom_gotovit_doma',
					};

				return [
					{
						value: diningKeys.GOOD,
						label: this.getCardBlock(card, 1) || this.getCardProductName(card, 0) || 'Рестораны хорошего уровня',
						image: this.getBudgetImage(diningKeys.GOOD),
					},
					{
						value: diningKeys.BUDGET,
						label: this.getCardBlock(card, 2) || this.getCardProductName(card, 1) || 'Недорогие рестораны, пиццерии, кафе',
						image: this.getBudgetImage(diningKeys.BUDGET),
					},
					{
						value: diningKeys.HOME,
						label: this.getCardBlock(card, 3) || this.getCardProductName(card, 2) || 'В основном готовить дома',
						image: this.getBudgetImage(diningKeys.HOME),
					},
				];
			},

			getCarRentalOptions: function (card) {
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				var carKeys = constants && constants.CAR_RENTAL
					? constants.CAR_RENTAL
					: { YES: 'da', NO: 'net' };

				return [
					{
						value: carKeys.YES,
						label: this.getCardBlock(card, 1) || this.getCardProductName(card, 0) || 'Да',
						image: this.getBudgetImage(carKeys.YES),
					},
					{
						value: carKeys.NO,
						label: this.getCardBlock(card, 2) || this.getCardProductName(card, 1) || 'Нет',
						image: this.getBudgetImage(carKeys.NO),
					},
				];
			},

			getCarClassOptions: function (card) {
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				var classKeys = constants && constants.CAR_CLASS
					? constants.CAR_CLASS
					: {
						HIGH: 'visokii_avto',
						MEDIUM: 'srednii_avto',
						BUDGET: 'deshovii_avto',
					};

				return [
					{
						value: classKeys.HIGH,
						label: this.getCardBlock(card, 1) || this.getCardProductName(card, 0) || 'Высокий',
						image: this.getBudgetImage(classKeys.HIGH),
					},
					{
						value: classKeys.MEDIUM,
						label: this.getCardBlock(card, 2) || this.getCardProductName(card, 1) || 'Средний',
						image: this.getBudgetImage(classKeys.MEDIUM),
					},
					{
						value: classKeys.BUDGET,
						label: this.getCardBlock(card, 3) || this.getCardProductName(card, 2) || 'Как можно дешевле',
						image: this.getBudgetImage(classKeys.BUDGET),
					},
				];
			},

			getResultTitle: function (card) {
				if (!card) {
					return '';
				}

				return this.getCardProductName(card, 0) || this.getCardTitle(card) || 'Расчет бюджета вашей поездки';
			},

			getResultMetaParts: function () {
				var parts = [
					this.getResultPeopleText(),
					this.getResultMonthsText(),
					this.getResultDaysText(),
					this.getResultRegionText(),
				];

				return parts.filter(function (part) {
					return part && String(part).trim() !== '';
				});
			},

			getResultPeopleText: function () {
				var adults = parseInt(this.answers.catalog.travelers.quantity, 10) || 0;
				var children = parseInt(this.answers.catalog.children.quantity, 10) || 0;
				var total = adults + children;

				return total > 0 ? total + ' человек' : '';
			},

			getResultDaysText: function () {
				var tripDates = this.answers.catalog.trip_dates || {};
				var days = parseInt(tripDates.durationDays, 10) || this.calculateDaysBetween(tripDates.dateFrom, tripDates.dateTo);

				if (!days || days < 1) {
					return '';
				}

				return days + ' ' + this.pluralizeRu(days, 'день', 'дня', 'дней');
			},

			getResultMonthsText: function () {
				var tripDates = this.answers.catalog.trip_dates || {};
				var fromDate = this.parseTripDate(tripDates.dateFrom);
				var toDate = this.parseTripDate(tripDates.dateTo);
				var monthLabel = tripDates.monthLabel ? String(tripDates.monthLabel).trim() : '';
				var fromMonth;
				var toMonth;

				if (fromDate && toDate) {
					fromMonth = this.monthNameRu(fromDate.getMonth());
					toMonth = this.monthNameRu(toDate.getMonth());

					return fromMonth === toMonth ? fromMonth : fromMonth + ' - ' + toMonth;
				}

				if (fromDate) {
					return this.monthNameRu(fromDate.getMonth());
				}

				return monthLabel;
			},

			getResultRegionText: function () {
				var value = this.answers.catalog.region.region ? String(this.answers.catalog.region.region).trim() : '';
				var regions = this.swissRegionsList || [];
				var i;

				if (!value) {
					return '';
				}

				for (i = 0; i < regions.length; i++) {
					if (String(regions[i].value) === value) {
						return regions[i].label || value;
					}
				}

				return value;
			},

			getResultComfortText: function () {
				var value = this.answers.catalog.comfort.comfortLevel;
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				var comfortKeys = constants && constants.COMFORT
					? constants.COMFORT
					: { DESHEVLE: 'deshevle', SREDNII: 'sredniii', VISOKII: 'visokii' };

				if (value === comfortKeys.DESHEVLE) {
					return 'эконом';
				}
				if (value === comfortKeys.SREDNII) {
					return 'комфорт';
				}
				if (value === comfortKeys.VISOKII) {
					return 'люкс';
				}

				return '';
			},

			parseTripDate: function (value) {
				var text = String(value || '').trim();
				var match = text.match(/^(\d{1,2})[.\-/](\d{1,2})[.\-/](\d{4})$/);
				var day;
				var month;
				var year;
				var date;

				if (!match) {
					return null;
				}

				day = parseInt(match[1], 10);
				month = parseInt(match[2], 10) - 1;
				year = parseInt(match[3], 10);
				date = new Date(year, month, day);

				return date && date.getFullYear() === year && date.getMonth() === month && date.getDate() === day ? date : null;
			},

			calculateDaysBetween: function (fromValue, toValue) {
				var fromDate = this.parseTripDate(fromValue);
				var toDate = this.parseTripDate(toValue);
				var dayMs = 24 * 60 * 60 * 1000;
				var diff;

				if (!fromDate || !toDate || toDate < fromDate) {
					return 0;
				}

				diff = Math.round((toDate.getTime() - fromDate.getTime()) / dayMs);

				return diff + 1;
			},

			getTodayDate: function () {
				var today = new Date();

				return new Date(today.getFullYear(), today.getMonth(), today.getDate());
			},

			hasPastTripDate: function () {
				var tripDates = this.answers.catalog.trip_dates || {};
				var today = this.getTodayDate();
				var fromDate = this.parseTripDate(tripDates.dateFrom);
				var toDate = this.parseTripDate(tripDates.dateTo);

				return !!((fromDate && fromDate < today) || (toDate && toDate < today));
			},

			validateTripDates: function () {
				if (this.hasPastTripDate()) {
					window.alert('אנא הזן תאריך תקין, לא ניתן לבחור תאריך שעבר');

					return false;
				}

				return true;
			},

			hasValue: function (value) {
				return String(value || '').trim() !== '';
			},

			showRequiredAlert: function () {
				window.alert('אנא מלא את כל השדות');
			},

			areChildrenAgesFilled: function () {
				var slots = this.childrenAgeSlots;
				var i;

				for (i = 0; i < slots.length; i++) {
					if (!this.hasValue(this.getChildAge(slots[i]))) {
						return false;
					}
				}

				return true;
			},

			validateCurrentStep: function () {
				var catalog = this.answers.catalog;
				var dates = catalog.trip_dates || {};
				var children = catalog.children || {};
				var valid = true;

				if (this.step === 1) {
					valid = dates.dateMode === 'month'
						? this.hasValue(dates.durationDays)
						: this.hasValue(dates.dateFrom) && this.hasValue(dates.dateTo);
				} else if (this.step === 2) {
					valid = this.hasValue(catalog.travelers.quantity);
				} else if (this.step === 3) {
					valid = this.hasValue(children.hasChildren)
						&& (children.hasChildren === 'net' || (this.hasValue(children.quantity) && this.areChildrenAgesFilled()));
				} else if (this.step === 4) {
					valid = this.hasValue(catalog.region.region);
				} else if (this.step === 5) {
					valid = this.hasValue(catalog.housing.housingType);
				} else if (this.step === 6) {
					valid = this.hasValue(catalog.comfort.comfortLevel);
				} else if (this.step === 7) {
					valid = this.hasValue(catalog.entertainment.entertainmentLevel);
				} else if (this.step === 8) {
					valid = this.hasValue(catalog.dining.diningLevel);
				} else if (this.step === 9) {
					valid = this.hasValue(catalog.car_rental.carRental);
				} else if (this.step === 10) {
					valid = this.shouldSkipCarClassStep() || this.hasValue(catalog.car_class.carClass);
				} else if (this.step === 11) {
					valid = this.hasValue(catalog.budget_priority.budgetPriority);
				}

				if (!valid) {
					this.showRequiredAlert();
				}

				return valid;
			},

			monthNameRu: function (monthIndex) {
				var months = [
					'январь',
					'февраль',
					'март',
					'апрель',
					'май',
					'июнь',
					'июль',
					'август',
					'сентябрь',
					'октябрь',
					'ноябрь',
					'декабрь',
				];

				return months[monthIndex] || '';
			},

			pluralizeRu: function (number, one, few, many) {
				var n = Math.abs(parseInt(number, 10) || 0);
				var n10 = n % 10;
				var n100 = n % 100;

				if (n10 === 1 && n100 !== 11) {
					return one;
				}
				if (n10 >= 2 && n10 <= 4 && (n100 < 12 || n100 > 14)) {
					return few;
				}

				return many;
			},

			getResultRows: function (card) {
				var icons = ['prozhivanie', 'transport', 'razvlechenie', 'pitanie'];
				var priceClasses = ['item_res_1', 'item_res_2', 'item_res_3', 'item_res_4'];
				var fallbacks = ['Проживание', 'Транспорт', 'Развлечения', 'Питание'];
				var rows = [];
				var i;
				var aiRows = this.budgetResult && Array.isArray(this.budgetResult.rows) ? this.budgetResult.rows : [];

				for (i = 0; i < 4; i++) {
					rows.push({
						label: (aiRows[i] && aiRows[i].label) ? aiRows[i].label : (this.getCardBlock(card, i + 1) || fallbacks[i]),
						image: this.getBudgetImage(icons[i]),
						priceClass: priceClasses[i],
						price: (aiRows[i] && aiRows[i].price) ? this.formatMoneyForDisplay(aiRows[i].price) : '',
					});
				}

				return rows;
			},

			getResultTotal: function () {
				if (this.itemTotal) {
					return this.formatMoneyForDisplay(this.itemTotal);
				}

				return this.budgetResult && this.budgetResult.total ? this.formatMoneyForDisplay(this.budgetResult.total) : '$53 260';
			},

			getResultBaseTotal: function () {
				if (this.itemBaseTotal) {
					return this.formatMoneyForDisplay(this.itemBaseTotal);
				}

				return this.budgetResult && this.budgetResult.base_total ? this.formatMoneyForDisplay(this.budgetResult.base_total) : '';
			},

			getResultPerPerson: function () {
				return this.budgetResult && this.budgetResult.per_person
					? this.formatPerPersonForDisplay(this.budgetResult.per_person)
					: '$3 260 на человека';
			},

			getPriorityAdjustmentText: function () {
				if (this.budgetResult && this.budgetResult.priority_adjustment) {
					return String(this.budgetResult.priority_adjustment);
				}

				return '';
			},

			formatPerPersonForDisplay: function (value) {
				var text = String(value || '').trim();
				var money = this.formatMoneyForDisplay(text);

				if (!money || money === text) {
					return text;
				}

				return money + ' на человека';
			},

			formatMoneyForDisplay: function (value) {
				var text = String(value || '').trim();
				var number = text.replace(/[^\d.,-]+/g, '').replace(/,/g, '');
				var amount;

				if (!number) {
					return text;
				}

				amount = parseFloat(number);
				if (!isNaN(amount)) {
					return '$' + Math.round(amount).toLocaleString('en-US').replace(/,/g, ' ');
				}

				if (text.indexOf('$') !== -1) {
					return '$' + number.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
				}

				return text;
			},

			getBudgetPriorityOptions: function (card) {
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				var priorityKeys = constants && constants.BUDGET_PRIORITY
					? constants.BUDGET_PRIORITY
					: {
						RELAX: 'budget_ne_vagen',
						BALANCE: 'bydget_vasgen',
						STRICT: 'budzet_vashnee_vsego',
					};

				return [
					{
						value: priorityKeys.RELAX,
						label: this.getCardBlock(card, 1) || this.getCardProductName(card, 0) || 'Бюджет не важен, главное хорошо отдохнуть',
						image: this.getBudgetImage(priorityKeys.RELAX),
					},
					{
						value: priorityKeys.BALANCE,
						label: this.getCardBlock(card, 2) || this.getCardProductName(card, 1) || 'Бюджет важен, стараюсь не выходить за рамки, но использовать поездку по максимуму',
						image: this.getBudgetImage(priorityKeys.BALANCE),
					},
					{
						value: priorityKeys.STRICT,
						label: this.getCardBlock(card, 3) || this.getCardProductName(card, 2) || 'Бюджет важнее всего',
						image: this.getBudgetImage(priorityKeys.STRICT),
					},
				];
			},

			getBudgetImage: function (key) {
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				if (constants && typeof constants.getImage === 'function') {
					return constants.getImage(key);
				}

				var images = window.aiCalculatorBudget && window.aiCalculatorBudget.images;
				return images && images[key] ? String(images[key]) : '';
			},

			getChildrenQuantityPlaceholder: function (card) {
				return this.getCardPlaceholder(
					card,
					2,
					(window.aiCalculatorBudget && window.aiCalculatorBudget.travelersPlaceholder)
						? String(window.aiCalculatorBudget.travelersPlaceholder)
						: 'Выберите кол-во'
				);
			},

			getChildrenAgePlaceholder: function (card) {
				return this.getCardPlaceholder(
					card,
					4,
					(window.aiCalculatorBudget && window.aiCalculatorBudget.childrenAgePlaceholder)
						? String(window.aiCalculatorBudget.childrenAgePlaceholder)
						: 'Возраст 1 ребенка'
				);
			},

			getChildAgePlaceholder: function (card, childIndex) {
				var base = this.getChildrenAgePlaceholder(card);

				if (/\d+/.test(base)) {
					return base.replace(/\d+/, String(childIndex));
				}

				return base + ' ' + childIndex;
			},
//втоматический синхронизатор и чистильщик данных для шага с детьми.
			syncChildrenAges: function () {
				var entry = this.answers.catalog.children;
				if (!entry) {
					return;
				}

				if (!Array.isArray(entry.ages)) {
					entry.ages = [];
				}

				var count = parseInt(entry.quantity, 10) || 0;
				var current = entry.ages;

				if (count <= 0) {
					entry.ages = [];
					return;
				}

				if (count < current.length) {
					entry.ages = current.slice(0, count);
					return;
				}

				if (count > current.length) {
					for (var i = current.length; i < count; i++) {
						entry.ages.push('');
					}
				}
			},

			onChildrenQuantityChange: function () {
				this.syncChildrenAges();
			},
/**
 * Делает чистый, независимый слепок (снимок) текущих ответов пользователя.
 * * ЗАЧЕМ ТАКОЙ ОГОРОД С JSON:
 * В JavaScript объекты передаются по ссылке. Если сделать просто `return this.answers`, 
 * то вернется ссылка на живой массив, и любые изменения «снимка» испортят оригинал.
 * * Метод сначала превращает объект в строку (разрывая живые связи Vue), 
 * а затем собирает обратно в абсолютно новый объект в памяти (аналог clone в PHP).
 */
			getAnswersSnapshot: function () {
				var snapshot = JSON.parse(JSON.stringify(this.answers));
				var children = snapshot.catalog && snapshot.catalog.children ? snapshot.catalog.children : null;

				if (children && children.hasChildren !== 'da') {
					children.hasChildren = 'net';
					children.quantity = '0';
					children.ages = [];
				}

				return snapshot;
			},

			debugLog: function (message, payload) {
				if (typeof payload !== 'undefined') {
					console.log(LOG, message, payload);
					return;
				}
				console.log(LOG, message);
			},
/**
 * Инициализирует сторонние календари (Date Pickers) на инпутах.
 * Вызывает глобальную функцию темы/плагина, если она загрузилась в браузере.
 * Проверка typeof защищает Vue от падения, если внешний скрипт не подгрузился.
 */
			initDatePickers: function () {
				if (typeof window.aiCalculatorBudgetInitPickers === 'function') {
					window.aiCalculatorBudgetInitPickers();
				}
			},

			syncStepAttr: function (step) {
				var section = document.querySelector('.ai-calculator-budget.ai-bg');
				if (section) {
					section.setAttribute('data-ai-bg-step', String(step));
				}
			},
// вкручиваем каленжарь
			syncFromDom: function () {
				// Находим главный блок калькулятора в HTML
				var root = document.getElementById('ai-calculator-budget-app');
				if (!root) {
					return;
				}
// Находим сами инпуты даты "С" и "До"
				var fromEl = root.querySelector('[data-date-role="from"]');
				var toEl = root.querySelector('[data-date-role="to"]');
				var monthEl = root.querySelector('[data-ai-bg-month] .ai-bg__field-text');

				if (fromEl) {
					this.answers.catalog.trip_dates.dateFrom = fromEl.value.trim();
				}
				if (toEl) {
					this.answers.catalog.trip_dates.dateTo = toEl.value.trim();
				}
				if (monthEl) {
					this.answers.catalog.trip_dates.monthLabel = monthEl.textContent.trim();
				}
			},

			prevStep: function () {
				// Если мы уже на первом шаге, назад идти некуда — выходим
				if (this.step <= 1) {
					return;
				}
				if (this.step === 11 && this.shouldSkipCarClassStep()) {
					this.step = 9;
					return;
				}
// Уменьшаем номер шага на один (возврат назад)
				this.step -= 1;
			},

			openOrderModal: function () {
				var self = this;

				this.isOrderModalOpen = true;
				document.body.classList.add('modal-open');

				this.$nextTick(function () {
					var overlay = self.$refs.orderOverlay;
					var modal = self.$refs.orderModal;

					if (overlay) {
						overlay.style.display = 'block';
						overlay.style.opacity = '0';
						window.requestAnimationFrame(function () {
							overlay.style.opacity = '1';
						});
					}
					if (modal) {
						modal.style.display = 'block';
						modal.style.opacity = '0';
						window.requestAnimationFrame(function () {
							modal.style.opacity = '1';
						});
					}
				});
			},

			closeOrderModal: function () {
				var self = this;
				var overlay = this.$refs.orderOverlay;
				var modal = this.$refs.orderModal;

				if (modal) {
					modal.style.opacity = '0';
				}
				if (overlay) {
					overlay.style.opacity = '0';
				}

				setTimeout(function () {
					self.isOrderModalOpen = false;
					document.body.classList.remove('modal-open');

					if (modal) {
						modal.style.display = '';
						modal.style.opacity = '';
					}
					if (overlay) {
						overlay.style.display = '';
						overlay.style.opacity = '';
					}
				}, 300);
			},

			getSessionToken: function () {
				var key = 'ai_budget_session_token';
				try {
					var stored = window.localStorage.getItem(key);
					if (stored && String(stored).trim() !== '') {
						return String(stored).trim();
					}
					var token = 'bg_' + Date.now() + '_' + Math.random().toString(36).slice(2, 12);
					window.localStorage.setItem(key, token);
					return token;
				} catch (e) {
					return '';
				}
			},

			applyBudgetResponse: function (data) {
				var responseItemTotal = data && data.item_total
					? String(data.item_total)
					: (data && data.budget_total ? String(data.budget_total) : '');
				var responseBaseTotal = data && data.budget_base_total
					? String(data.budget_base_total)
					: (data && data.base_total ? String(data.base_total) : '');

				if (data && data.budget) {
					this.budgetResult = data.budget;
				}
				if (data && data.priority_adjustment) {
					this.budgetResult = Object.assign({}, this.budgetResult || {}, {
						priority_adjustment: String(data.priority_adjustment),
					});
				}
				if (responseBaseTotal) {
					this.itemBaseTotal = responseBaseTotal;
					this.budgetResult = Object.assign({}, this.budgetResult || {}, {
						base_total: responseBaseTotal,
					});
				}
				if (responseItemTotal) {
					this.itemTotal = responseItemTotal;
					this.budgetResult = Object.assign({}, this.budgetResult || {}, {
						total: responseItemTotal,
					});
				}
			},

			pollBudgetStatus: function (quizAnswerId, attempt) {
				var cfg = window.aiCalculatorBudget || {};
				var self = this;
				var currentAttempt = attempt || 1;
				var maxAttempts = 80;
				var body;

				if (!quizAnswerId || currentAttempt > maxAttempts) {
					self.submitError = 'Истекло время ожидания расчёта';
					return Promise.resolve(false);
				}

				body = new URLSearchParams();
				body.append('action', 'ai_calculator_budget_status');
				body.append('nonce', cfg.nonce || '');
				body.append('quiz_answer_id', String(quizAnswerId));

				return new Promise(function (resolve) {
					window.setTimeout(resolve, currentAttempt === 1 ? 1000 : 2500);
				}).then(function () {
					return fetch(cfg.ajaxUrl, {
						method: 'POST',
						credentials: 'same-origin',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
						body: body.toString(),
					});
				}).then(function (response) {
					return response.json();
				}).then(function (json) {
					var status;

					if (!json || !json.success) {
						self.submitError = json && json.data && json.data.message ? json.data.message : 'Ошибка проверки расчёта';
						return false;
					}

					status = json.data && json.data.status ? String(json.data.status) : '';
					self.quizAnswerId = json.data && json.data.quiz_answer_id
						? parseInt(json.data.quiz_answer_id, 10) || quizAnswerId
						: quizAnswerId;

					if (status === 'completed') {
						self.applyBudgetResponse(json.data);
						self.answersReceived = true;
						return true;
					}

					if (status === 'failed') {
						self.submitError = json.data && json.data.calculation_error ? String(json.data.calculation_error) : 'Ошибка расчёта';
						return false;
					}

					return self.pollBudgetStatus(quizAnswerId, currentAttempt + 1);
				}).catch(function (err) {
					self.submitError = err && err.message ? err.message : 'Сеть';
					return false;
				});
			},

			submitAnswers: function (snapshot, onSuccess) {
				var cfg = window.aiCalculatorBudget || {};
				var self = this;
				var didSucceed = false;
				var submitStartedAt = Date.now();
				var minSubmitOverlayMs = 1000;

				if (!cfg.ajaxUrl) {
					console.warn(LOG + ' ajaxUrl не задан');
					if (typeof onSuccess === 'function') {
						onSuccess();
					}
					return;
				}

				self.isSubmitting = true;
				self.submitError = '';
				self.answersReceived = false;
				self.quizAnswerId = 0;
				self.itemBaseTotal = '';
				self.itemTotal = '';
				document.body.classList.add('ai-bg-is-submitting');

				var body = new URLSearchParams();
				body.append('action', 'ai_calculator_budget_submit');
				body.append('nonce', cfg.nonce || '');
				body.append('language', cfg.polylangSlug || 'ar');
				body.append('session_token', self.getSessionToken());
				body.append('ai_calculator_hp', self.honeypot || '');
				body.append('answers', JSON.stringify(snapshot));

				fetch(cfg.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString(),
				})
					.then(function (response) {
						return response.json();
					})
					.then(function (json) {
						if (!json || !json.success) {
							var msg = json && json.data && json.data.message ? json.data.message : 'Ошибка отправки в Laravel';
							self.submitError = msg;
							console.error(LOG, msg);
							return;
						}
						self.quizAnswerId = json.data && json.data.quiz_answer_id
							? parseInt(json.data.quiz_answer_id, 10) || 0
							: 0;
						if (json.data && json.data.status === 'processing' && self.quizAnswerId) {
							return self.pollBudgetStatus(self.quizAnswerId).then(function (ok) {
								didSucceed = ok;
							});
						}
						self.applyBudgetResponse(json.data);
						self.answersReceived = true;
						didSucceed = true;
						console.log(LOG + ' Laravel OK', json.data);
					})
					.catch(function (err) {
						self.submitError = err && err.message ? err.message : 'Сеть';
						console.error(LOG, err);
					})
					.finally(function () {
						var elapsed = Date.now() - submitStartedAt;
						var delay = Math.max(0, minSubmitOverlayMs - elapsed);

						window.setTimeout(function () {
							self.isSubmitting = false;
							document.body.classList.remove('ai-bg-is-submitting');
							if (didSucceed && typeof onSuccess === 'function') {
								onSuccess();
							}
						}, delay);
					});
			},

			nextStep: function () {
				if (this.step === 1) {
					this.syncFromDom();
					if (!this.validateTripDates()) {
						return;
					}
				}
				if (this.step >= this.totalSteps || this.isSubmitting) {
					return;
				}
				if (!this.validateCurrentStep()) {
					return;
				}

				var snapshot = this.getAnswersSnapshot();
				var nextStep = this.step + 1;
				var self = this;

				if (this.step === 9 && this.shouldSkipCarClassStep()) {
					this.answers.catalog.car_class.carClass = '';
					nextStep = 11;
				}

				if (this.step === this.questionStepsCount) {
					this.submitAnswers(snapshot, function () {
						self.step = nextStep;
					});
					return;
				}

				this.step = nextStep;
			},

			shouldSkipCarClassStep: function () {
				var value = this.answers
					&& this.answers.catalog
					&& this.answers.catalog.car_rental
					? String(this.answers.catalog.car_rental.carRental || '').trim().toLowerCase()
					: '';
				var constants = window.AiCalculatorVue3 && window.AiCalculatorVue3.BudgetConstants;
				var noValue = constants && constants.CAR_RENTAL && constants.CAR_RENTAL.NO
					? String(constants.CAR_RENTAL.NO).trim().toLowerCase()
					: 'net';

				return value === noValue || value === 'net' || value === 'no' || value === 'false' || value === '0';
			},
		},
	};
})(window);
