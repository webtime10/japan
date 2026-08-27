/**
 * Your Ideal Region — корневой Vue-компонент.
 *
 * Данные каталога: window.aiCalculatorIdealRegionData (как aiCalculatorData у budget).
 * Категория = шаг, товары = варианты ответа.
 * 
 * тоесть смысл общий есть цикл пхп джсон из которго карточки обявляется глобалная пременная которая набирает массив (массив ансвре) взависимости от шага
 * 
 */
(function (window) {
	'use strict';
// метка для логов
	var LOG = '[AI Ideal Region]';
// пространство имен
	window.AiCalculatorVue3 = window.AiCalculatorVue3 || {};


// «Внутри общего объекта AiCalculatorVue3 создай ячейку (свойство) с именем IdealRegion и запиши в нее весь код нашего калькулятора — его настройки, переменные и методы».	
	window.AiCalculatorVue3.IdealRegion = {
		name: 'IdealRegionCalculator',   //внутреннее системное имя (идентификатор) самого Vue-компонента.


//то место, где ты создаешь (инициализируешь) все переменные		
		data: function () {
			var config = window.aiCalculatorIdealRegion || {};
			var catalogCards = Array.isArray(window.aiCalculatorIdealRegionData)
				? window.aiCalculatorIdealRegionData
				: []; // массив получил

			return {
				// maxStep  я получаю с вида
				step: 1, // 1 шаг
				maxStep: Number(config.maxStep) > 0 ? Number(config.maxStep) : 1, // количество шагов приходи с вида
				catalogCards: catalogCards, // массив
				answers: {}, // объект ответа
				honeypot: '',
				isSubmitting: false,
				submitError: '',
				submitSuccess: false,
				showResults: false,
				matchedRegions: [],
				expandedRegions: {},
			};
		},

		computed: {
			totalSteps: function () {
				return Math.max(1, this.maxStep);
			},

			progressPercent: function () {
				if (this.showResults) {
					return 100;
				}

				if (this.totalSteps <= 0) {
					return 0;
				}

				return Math.round((this.step / this.totalSteps) * 100);
			},

			currentStepCard: function () {
				return this.cardForStep(this.step);
			},
			// основной массив  с данными который заxодит
			currentStepOptions: function () {
				return this.getStepOptions(this.currentStepCard);
			},

			stepQuestion: function () {
				return this.getCardTitle(this.currentStepCard) || (this.uiLabel('question', 'Question') + ' ' + this.step);
			},

			stepMaxAnswers: function () {
				return this.maxAnswersForStep(this.step);
			},

			isMultiStep: function () {
				return this.stepMaxAnswers > 1;
			},

		isDurationSelectStep: function () {
			if (Number(this.step) === 2) {
				return true;
			}

			var title = String(this.stepQuestion || '');
			return /продолжительн/i.test(title) || /duration/i.test(title) || /مدة/.test(title);
		},

		isGroupSelectStep: function () {
			var title = String(this.stepQuestion || '');
			return /состав.*груп/i.test(title) || /кто.*входит/i.test(title) || /group/i.test(title) || /مجموعة/.test(title);
		},

		isAnySelectStep: function () {
			return this.isDurationSelectStep || this.isGroupSelectStep;
		},

			stepSelectionHint: function () {
				var card = this.currentStepCard;
				var hint = card && card.dop1 ? String(card.dop1).trim() : '';
				if (hint) {
					return hint;
				}

				if (this.stepMaxAnswers > 2) {
					return 'До трёх вариантов ответа';
				}
				if (this.stepMaxAnswers > 1) {
					return 'До двух вариантов ответа';
				}

				return 'Один вариант ответа';
			},

			/**
			 * «Вопрос» в «Вопрос N из 8» — с текущей карточки (dop2), иначе глобальный label.
			 */
			stepQuestionLabel: function () {
				var card = this.currentStepCard;
				var fromCard = card && card.dop2 ? String(card.dop2).trim() : '';
				if (fromCard) {
					return fromCard;
				}

				return this.uiLabel('question', 'Question');
			},

			otherVariantsLabel: function () {
				var count = Math.max(0, (this.matchedRegions || []).length - 1);
				var tpl = this.uiLabel('other_variants', 'Ещё {n} варианта');
				return String(tpl).replace(/\{n\}/g, String(count));
			},
		},
// финишный этап обработки выводим логи
		mounted: function () {
			this.initAnswers();

			console.log(LOG, 'mounted', {
				step: this.step,
				totalSteps: this.totalSteps,
				catalogCards: this.catalogCards.length,
			});
		},

		watch: {
			step: function () {
				this.ensureStepAnswer(this.step);
			},
		},

		methods: {

			// фунция текстового сообщения текущего
			uiLabel: function (key, fallback) {
				var config = window.aiCalculatorIdealRegion || {};
				var labels = config.labels && typeof config.labels === 'object' ? config.labels : {};

				return labels[key] ? String(labels[key]) : String(fallback || '');
			},

			initAnswers: function () {
				var step;

				for (step = 1; step <= this.maxStep; step++) {
					this.ensureStepAnswer(step);
				}
			},

			ensureStepAnswer: function (stepNum) {
				var options = this.getStepOptions(this.cardForStep(stepNum));
				var maxAnswers = this.maxAnswersForStep(stepNum);
				var current = this.answers[stepNum];

				if (!options.length) {
					return;
				}

				if (maxAnswers > 1) {
					if (!Array.isArray(current)) {
						this.answers[stepNum] = [];
					}
					return;
				}

				if (current == null || current === undefined) {
					this.answers[stepNum] = '';
				}
			},

			maxAnswersForStep: function (stepNum) {
				var card = this.cardForStep(stepNum);
				var fromCard = card && Number(card.max_answers) > 0 ? Number(card.max_answers) : 0;
				var step = Number(stepNum) || 0;

				if (fromCard > 0) {
					return fromCard;
				}

				if (step === 5 || step === 6) {
					return 2;
				}
				if (step === 8) {
					return 3;
				}

				return 1;
			},

			isOptionSelected: function (value) {
				var current = this.answers[this.step];
				var needle = String(value || '');

				if (Array.isArray(current)) {
					return current.indexOf(needle) !== -1;
				}

				return String(current || '') === needle;
			},

			isExclusiveOption: function (option) {
				var label = option && option.label ? String(option.label) : '';
				return /пожелани/i.test(label) && /нет/i.test(label);
			},

			onSelectChange: function (event) {
				this.answers[this.step] = String((event && event.target && event.target.value) || '');
			},

			onOptionChange: function (option, event) {
				var checked = !!(event && event.target && event.target.checked);
				var value = String(option.value || '');
				var maxAnswers = this.stepMaxAnswers;
				var next;
				var i;

				if (maxAnswers <= 1) {
					this.answers[this.step] = value;
					return;
				}

				next = Array.isArray(this.answers[this.step]) ? this.answers[this.step].slice() : [];

				if (this.isExclusiveOption(option)) {
					this.answers[this.step] = checked ? [value] : [];
					return;
				}

				// Снятие «нет пожеланий» при выборе обычного варианта.
				next = next.filter(function (item) {
					var found = this.findOptionByValue(this.currentStepOptions, item);
					return !(found && this.isExclusiveOption(found));
				}, this);

				if (checked) {
					if (next.indexOf(value) === -1) {
						if (next.length >= maxAnswers) {
							next.shift();
						}
						next.push(value);
					}
				} else {
					next = next.filter(function (item) {
						return item !== value;
					});
				}

				this.answers[this.step] = next;
			},

			questionCardAt: function (index) {
				if (!Array.isArray(this.catalogCards) || index < 0 || index >= this.catalogCards.length) {
					return null;
				}

				return this.catalogCards[index];
			},

/*
Ты смотришь на внутреннее свойство карточки sort (её порядковый номер, пришедший из админки WordPress/Laravel).

Принудительно приводишь оба значения к числу через Number(), чтобы строка "1" и число 1 успешно совпали.

Результат: Если у карточки поле sort совпадает с текущим шагом (stepNum), метод её тут же возвращает и прекращает работу. Карточка найдена!

*/

			cardForStep: function (stepNum) {
				var cards = this.catalogCards;
				var i;

				if (!Array.isArray(cards) || cards.length === 0) {
					return null;
				}

				for (i = 0; i < cards.length; i++) {
					if (Number(cards[i].sort) === Number(stepNum)) {
						return cards[i];
					}
				}

				return this.questionCardAt(Number(stepNum) - 1);
			},

			getCardProductName: function (card, productIndex) {
				if (!card || !Array.isArray(card.products) || !card.products[productIndex]) {
					return '';
				}

				var product = card.products[productIndex];
				var label = product.product_name || product.name || product.label || '';

				return label ? String(label) : '';
			},

			getCardTitle: function (card) {
				if (!card) {
					return '';
				}

				// Заголовок шага = Name первого товара в категории.
				var productName = this.getCardProductName(card, 0);
				if (productName && !/^#\d+$/.test(productName)) {
					return productName;
				}

				var title = card.category_title ? String(card.category_title).trim() : '';
				return title && !/^#\d+$/.test(title) ? title : '';
			},
// эта фунция принила иассив с пхп
			getStepOptions: function (card) {
				if (!card) {
					return [];
				}

				// Только пары БлокN + ФотоN (не карточки товаров).
				if (Array.isArray(card.options) && card.options.length) {
					return card.options.map(function (option) {
						return {
							value: String(option.value || ''),
							label: String(option.label || ''),
							image: option.image ? String(option.image) : '',
						};
					}).filter(function (option) {
						return option.value && option.label;
					});
				}

				if (card.products && card.products[0] && Array.isArray(card.products[0].slots)) {
					return card.products[0].slots.map(function (slot) {
						return {
							value: String(slot.value || ''),
							label: String(slot.label || ''),
							image: slot.image ? String(slot.image) : '',
						};
					}).filter(function (option) {
						return option.value && option.label;
					});
				}

				return [];
			},

			getProductOptions: function (card) {
				return this.getStepOptions(card);
			},

			prevStep: function () {
				if (this.step <= 1) {
					return;
				}

				this.step -= 1;
			},

			hasStepAnswer: function (stepNum) {
				var current = this.answers[stepNum];

				if (Array.isArray(current)) {
					return current.length > 0;
				}

				return String(current || '').trim() !== '';
			},

			validateCurrentStep: function () {
				if (this.hasStepAnswer(this.step)) {
					return true;
				}

				window.alert('يرجى الإجابة على السؤال');
				return false;
			},

			nextStep: function () {
				if (this.step >= this.maxStep) {
					return;
				}

				if (!this.validateCurrentStep()) {
					return;
				}

				this.step += 1;
				console.log(LOG, 'step', this.step, this.getAnswersSnapshot());
			},

			getAnswersSnapshot: function () {
				var out = {};
				var step;
				var value;

				for (step = 1; step <= this.maxStep; step++) {
					value = this.answers[step];
					if (Array.isArray(value) && value.length) {
						out['step_' + step] = value.slice();
					} else if (value) {
						out['step_' + step] = value;
					}
				}

				return out;
			},

			findOptionByValue: function (options, value) {
				var i;
				var needle = String(value || '');

				for (i = 0; i < options.length; i++) {
					if (String(options[i].value || '') === needle) {
						return options[i];
					}
				}

				return null;
			},

			parseSlotFromValue: function (value) {
				var raw = String(value || '').trim();
				var match;

				if (!raw) {
					return null;
				}

				match = raw.match(/-(\d+)$/);
				if (match) {
					return Number(match[1]);
				}

				if (/^\d+$/.test(raw)) {
					return Number(raw);
				}

				return null;
			},

			getSubmitCatalog: function () {
				var catalog = {};
				var step;
				var card;
				var options;
				var values;
				var selected;
				var slots;
				var labels;
				var i;
				var value;
				var slot;

				for (step = 1; step <= this.maxStep; step++) {
					card = this.cardForStep(step);
					options = this.getStepOptions(card);
					values = Array.isArray(this.answers[step])
						? this.answers[step].slice()
						: this.answers[step]
							? [String(this.answers[step])]
							: [];

					slots = [];
					labels = [];
					for (i = 0; i < values.length; i++) {
						value = String(values[i] || '');
						selected = this.findOptionByValue(options, value);
						slot = this.parseSlotFromValue(value);
						if (slot != null) {
							slots.push(slot);
						}
						if (selected && selected.label) {
							labels.push(String(selected.label));
						}
					}

					catalog['step_' + step] = {
						value: values.length === 1 ? values[0] : values.join(','),
						values: values,
						slot: slots.length ? slots[0] : null,
						slots: slots,
						label: labels.join(', '),
						labels: labels,
						question: this.getCardTitle(card),
					};
				}

				return catalog;
			},

			formatServerResultLines: function (data, catalog) {
				var lines = ['Ответ ушёл на сервер (lara2.loc).', ''];
				var result = data && data.result ? data.result : null;
				var self = this;

				lines.push('Ваш выбор (номера слотов):');
				Object.keys(catalog).forEach(function (key) {
					var item = catalog[key] || {};
					var slot = item.slot != null ? item.slot : self.parseSlotFromValue(item.value);
					lines.push(
						key +
							': ' +
							(slot != null ? slot : '?') +
							' — ' +
							(item.label || item.value || '')
					);
				});

				var topList = result && Array.isArray(result.top_regions) ? result.top_regions : (result && Array.isArray(result.matched_regions) ? result.matched_regions : []);
				var step1Choice = catalog.step_1 || {};
				var step1Priority = step1Choice.label || 'горы / водопады / озёра / города';

				if (topList.length) {
					lines.push('', 'Топ-3 региона (главный приоритет — «' + step1Priority + '»):');
					topList.forEach(function (region, index) {
						lines.push(
							index +
								1 +
								'. ' +
								(region.name || '?') +
								' — ' +
								step1Priority +
								' ' +
								(region.step1_score != null ? region.step1_score : '?') +
								', остальное ' +
								(region.rest_score != null ? region.rest_score : '?')
						);
					});
				} else if (result) {
					lines.push('', 'Не удалось подобрать регионы.');
				}

				if (result && result.manufacturer_name) {
					lines.push('', 'Производитель: ' + result.manufacturer_name);
				}

				if (data && data.message) {
					lines.push('', 'Сообщение: ' + data.message);
				}

				return lines;
			},

			normalizeMatchedRegions: function (data) {
				var list = [];
				var self = this;
				var source =
					data && data.result && Array.isArray(data.result.top_regions)
						? data.result.top_regions
						: data && data.result && Array.isArray(data.result.matched_regions)
							? data.result.matched_regions
							: [];
				var firstPercent = 0;

				source.forEach(function (region) {
					var matchScore;
					var matchPercent;

					if (!region || typeof region !== 'object') {
						return;
					}

					matchScore = region.match_score != null ? Number(region.match_score) : null;
					if (matchScore == null || isNaN(matchScore)) {
						matchPercent = 0;
					} else {
						matchPercent = Math.max(0, Math.min(100, Math.round(matchScore)));
					}

					list.push({
						category_id: region.category_id || null,
						name: region.name ? String(region.name) : '',
						slug: region.slug ? String(region.slug) : '',
						image: self.resolveLaravelMediaUrl(region.image),
						description: region.description ? String(region.description) : '',
						description_html: region.description_html ? String(region.description_html) : '',
						step1_score: region.step1_score != null ? region.step1_score : null,
						rest_score: region.rest_score != null ? region.rest_score : null,
						match_score: matchScore,
						match_percent: matchPercent,
					});
				});

				list.sort(function (a, b) {
					return (Number(b.match_score) || 0) - (Number(a.match_score) || 0);
				});
				list = list.slice(0, 3);

				if (list.length) {
					firstPercent = Number(list[0].match_percent) || 0;
					list[0].match_percent = firstPercent;
					if (list[1]) {
						list[1].match_percent = Math.max(0, firstPercent - 5);
					}
					if (list[2]) {
						list[2].match_percent = Math.max(0, firstPercent - 7);
					}
				}

				return list;
			},

			/**
			 * Laravel отдаёт /uploads/... — клеим к base из AI Calculator → Home.
			 * Если пришёл полный http(s) URL — берём только pathname и тоже клеим к base.
			 */
			resolveLaravelMediaUrl: function (raw) {
				var path = raw ? String(raw).trim() : '';
				var base = '';
				var cfg = window.aiCalculatorIdealRegion || {};

				if (!path) {
					return '';
				}

				base = cfg.laravelBase ? String(cfg.laravelBase).replace(/\/+$/, '') : '';
				if (base && !/^https?:\/\//i.test(base)) {
					var isLocal = /\.(loc|test|localhost)(:\d+)?$/i.test(base);
					base = (isLocal ? 'http://' : 'https://') + base.replace(/^\/+/, '');
				}

				if (/^https?:\/\//i.test(path)) {
					try {
						path = new URL(path).pathname || path;
					} catch (e) {
						return path;
					}
				}

				if (path.charAt(0) !== '/') {
					path = '/' + path;
				}

				return base ? base + path : path;
			},

			resultMedal: function (index) {
				if (index === 0) {
					return '🥇';
				}
				if (index === 1) {
					return '🥈';
				}
				if (index === 2) {
					return '🥉';
				}
				return String(index + 1) + '.';
			},

			descriptionPreview: function (text, limit) {
				var raw = String(text || '').replace(/\s+/g, ' ').trim();
				var max = Number(limit) > 0 ? Number(limit) : 300;

				if (raw.length <= max) {
					return raw;
				}

				return raw.slice(0, max).trim() + '…';
			},

			/**
			 * Делит description_html на превью + «Подробнее».
			 * По структуре: после первого </ul> — блок «что включить» (RU/EN/AR/HE).
			 */
			splitDescriptionHtml: function (html) {
				try {
					var raw = String(html || '');
					if (!raw.trim()) {
						return { preview: '', more: '', hasMore: false };
					}

					// Ищем второй заголовок секции: <p>…<strong>… после первого списка.
					var ulClose = raw.search(/<\/ul>/i);
					if (ulClose === -1) {
						return { preview: raw, more: '', hasMore: false };
					}

					var afterUl = ulClose + 5;
					var rest = raw.slice(afterUl);
					var nextBlock = rest.match(/<(?:p|h[1-6]|strong|ul)\b/i);
					if (!nextBlock || typeof nextBlock.index !== 'number') {
						return { preview: raw, more: '', hasMore: false };
					}

					var moreStart = afterUl + nextBlock.index;
					var preview = raw.slice(0, moreStart).trim();
					var more = raw.slice(moreStart).replace(/^(?:\s*<p\b[^>]*>\s*<\/p>\s*)+/i, '').trim();

					if (!preview || !more) {
						return { preview: raw, more: '', hasMore: false };
					}

					return { preview: preview, more: more, hasMore: true };
				} catch (e) {
					return { preview: String(html || ''), more: '', hasMore: false };
				}
			},

			descriptionHtmlPreview: function (region) {
				try {
					var html = region && region.description_html ? String(region.description_html) : '';
					var parts = this.splitDescriptionHtml(html);
					return parts.preview || html;
				} catch (e) {
					return region && region.description_html ? String(region.description_html) : '';
				}
			},

			descriptionHtmlMore: function (region) {
				try {
					return this.splitDescriptionHtml(region && region.description_html ? String(region.description_html) : '').more;
				} catch (e) {
					return '';
				}
			},

			descriptionHtmlHasMore: function (region) {
				try {
					return !!this.splitDescriptionHtml(region && region.description_html ? String(region.description_html) : '').hasMore;
				} catch (e) {
					return false;
				}
			},

			isRegionExpanded: function (region) {
				var key = region && (region.category_id != null ? String(region.category_id) : region.name);
				return !!(key && this.expandedRegions[key]);
			},

			toggleRegionDescription: function (region) {
				var key = region && (region.category_id != null ? String(region.category_id) : region.name);
				if (!key) {
					return;
				}

				var next = Object.assign({}, this.expandedRegions);
				next[key] = !next[key];
				this.expandedRegions = next;
			},

			regionNeedsMore: function (region) {
				var text = region && region.description ? String(region.description) : '';
				return text.replace(/\s+/g, ' ').trim().length > 300;
			},

			getSessionToken: function () {
				var key = 'ai_ideal_region_session_token';

				try {
					var stored = window.localStorage.getItem(key);
					if (stored && String(stored).trim() !== '') {
						return String(stored).trim();
					}
					var token = 'ir_' + Date.now() + '_' + Math.random().toString(36).slice(2, 12);
					window.localStorage.setItem(key, token);
					return token;
				} catch (e) {
					return 'ir_' + Date.now() + '_' + Math.random().toString(36).slice(2, 12);
				}
			},

			submitAnswers: function () {
				var cfg = window.aiCalculatorIdealRegion || {};
				var self = this;
				var catalog = this.getSubmitCatalog();
				var answersPayload = { catalog: catalog };

				if (this.isSubmitting) {
					return;
				}

				if (!this.validateCurrentStep()) {
					return;
				}

				console.log(LOG, 'submit click', answersPayload);

				if (!cfg.ajaxUrl) {
					var noUrlMsg = 'ajaxUrl не задан — ответы не отправлены.';
					this.submitError = noUrlMsg;
					console.warn(LOG, noUrlMsg, cfg);
					return;
				}

				this.isSubmitting = true;
				this.submitError = '';
				this.submitSuccess = false;
				this.showResults = false;
				this.matchedRegions = [];
				this.expandedRegions = {};

				var body = new URLSearchParams();
				body.append('action', 'ai_calculator_ideal_region_submit');
				body.append('nonce', cfg.nonce || '');
				body.append('language', cfg.polylangSlug || 'he');
				body.append('session_token', this.getSessionToken());
				body.append('ai_calculator_hp', this.honeypot || '');
				body.append('answers', JSON.stringify(answersPayload));

				fetch(cfg.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString(),
				})
					.then(function (response) {
						return response.json().then(function (json) {
							return { httpOk: response.ok, json: json };
						});
					})
					.then(function (result) {
						var json = result.json;

						if (!json || !json.success) {
							var msg =
								json && json.data && json.data.message
									? String(json.data.message)
									: self.uiLabel('submit_error', 'Не удалось отправить ответы.');
							self.submitError = msg;
							console.error(LOG, msg, json);
							return;
						}

						self.submitSuccess = true;
						self.matchedRegions = self.normalizeMatchedRegions(json.data);
						self.showResults = true;
						console.log(LOG, 'Laravel OK', json.data);
						console.log(LOG, self.formatServerResultLines(json.data, catalog).join('\n'));
					})
					.catch(function (err) {
						self.submitError =
							err && err.message
								? err.message
								: self.uiLabel('submit_error', 'Не удалось отправить ответы.');
						console.error(LOG, err);
					})
					.finally(function () {
						self.isSubmitting = false;
					});
			},
		},
	};
})(window);
