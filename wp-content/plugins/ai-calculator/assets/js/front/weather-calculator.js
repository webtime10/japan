jQuery(document).ready(function ($) {
	'use strict';

	function escapeHtml(text) {
		return String(text)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	/**
	 * Разбор температуры на день / ночь.
	 * Поддерживает:
	 * - temperature_day / temperature_night
	 * - "+12° +8°|+3° -2°"
	 * - "+12° +8° / +3° -2°"
	 * - "+15° +6°" (день и ночь одним числом каждое)
	 *
	 * @return {{day: string, night: string}}
	 */
	function splitTemperature(weather) {
		var day = '';
		var night = '';

		if (weather && weather.temperature_day) {
			day = String(weather.temperature_day).trim();
		}
		if (weather && weather.temperature_night) {
			night = String(weather.temperature_night).trim();
		}
		if (day && night) {
			return { day: day, night: night };
		}

		var raw = String((weather && weather.temperature) || '').trim();
		if (!raw) {
			return { day: day, night: night };
		}

		if (raw.indexOf('|') !== -1) {
			var pipeParts = raw.split('|');
			return {
				day: String(pipeParts[0] || '').trim(),
				night: String(pipeParts[1] || '').trim()
			};
		}

		if (raw.indexOf('/') !== -1) {
			var slashParts = raw.split('/');
			return {
				day: String(slashParts[0] || '').trim().replace(/^(день|day|نهار|יום)\s*/i, ''),
				night: String(slashParts[1] || '').trim().replace(/^(ночь|night|ليل|לילה)\s*/i, '')
			};
		}

		var nums = raw.match(/[+-]?\d+\s*°?/g);
		if (nums && nums.length >= 4) {
			return {
				day: (nums[0] + ' ' + nums[1]).replace(/\s+/g, ' ').trim(),
				night: (nums[2] + ' ' + nums[3]).replace(/\s+/g, ' ').trim()
			};
		}
		if (nums && nums.length === 2) {
			return {
				day: String(nums[0]).trim(),
				night: String(nums[1]).trim()
			};
		}

		return { day: raw, night: '' };
	}

	/**
	 * Диапазон температур для RTL/LTR.
	 * API обычно отдаёт «макс мин» (+12° +8°).
	 * В RTL: справа min, слева max, разделитель « / », каждое значение в dir=ltr.
	 */
	function formatTempHtml(value, isRtl) {
		var raw = String(value || '').trim();
		if (!raw) {
			return '';
		}

		var tokens = raw
			.replace(/\//g, ' ')
			.split(/\s+/)
			.filter(Boolean)
			.map(function (token) {
				return token.replace(/,$/, '');
			});

		if (tokens.length >= 2) {
			var first = tokens[0];
			var second = tokens[1];
			var firstNum = parseFloat(String(first).replace(/[^\d.+-]/g, ''));
			var secondNum = parseFloat(String(second).replace(/[^\d.+-]/g, ''));
			var minTok = first;
			var maxTok = second;
			if (!isNaN(firstNum) && !isNaN(secondNum)) {
				if (firstNum <= secondNum) {
					minTok = first;
					maxTok = second;
				} else {
					minTok = second;
					maxTok = first;
				}
			} else {
				// Fallback: API «макс мин»
				maxTok = first;
				minTok = second;
			}

			if (isRtl) {
				// DOM: min, max → в RTL min справа, max слева
				tokens = [minTok, maxTok];
			} else {
				tokens = [maxTok, minTok];
			}
		}

		var parts = tokens.map(function (token) {
			return '<span class="ai-wh__temp-ltr" dir="ltr" style="unicode-bidi:isolate">' + escapeHtml(token) + '</span>';
		});

		if (parts.length === 2) {
			return parts[0] + '<span class="ai-wh__temp-sep" aria-hidden="true">/</span>' + parts[1];
		}
		return parts.join('');
	}

	function applyWeatherStats($root, weather) {
		if (!weather || !$root.length) {
			return;
		}

		var isRtl =
			(document.documentElement.getAttribute('dir') || '').toLowerCase() === 'rtl' ||
			(document.body && (document.body.getAttribute('dir') || '').toLowerCase() === 'rtl') ||
			/^(he|ar|fa|ur|iw)\b/i.test(document.documentElement.getAttribute('lang') || '');
		var $pair = $root.find('[data-ai-wh-temp]');
		if ($pair.length) {
			$pair.attr('dir', isRtl ? 'rtl' : 'ltr');
		}

		var parts = splitTemperature(weather);
		if (parts.day) {
			$root.find('[data-ai-wh-temp-day]').html(formatTempHtml(parts.day, isRtl));
		}
		if (parts.night) {
			$root.find('[data-ai-wh-temp-night]').html(formatTempHtml(parts.night, isRtl));
		}

		if (weather.precipitation) {
			$root.find('[data-ai-wh-precip]').text(weather.precipitation);
		}
		if (weather.sunny_days) {
			$root.find('[data-ai-wh-sunny]').text(weather.sunny_days);
		}
		if (weather.season) {
			$root.find('[data-ai-wh-season]').text(weather.season);
		}
	}

	function alertMessage(message) {
		if (message) {
			window.alert(message);
		}
	}

	function alertNames(sent) {
		if (!sent) {
			window.alert('Нет данных.');
			return;
		}
		window.alert((sent.month_name || '—') + '\n' + (sent.region_name || '—'));
	}

	function handleResponse(response, $root, fallbackSent) {
		var data = response && response.data ? response.data : {};

		if (data.weather) {
			applyWeatherStats($root, data.weather);
			return;
		}

		if (data.message) {
			alertMessage(data.message);
			return;
		}

		alertNames(data.sent || fallbackSent);
	}

	$(document).on('change', '[data-ai-wh-month], [data-ai-wh-region]', function () {
		var $root = $(this).closest('[data-ai-wh]');
		var region = $root.find('[data-ai-wh-region]').val();
		var month = $root.find('[data-ai-wh-month]').val();
		var sentLocal = {
			month_name: $.trim($root.find('[data-ai-wh-month] option:selected').text()),
			// В value региона — русское название из админки («Наз. на русск.»).
			region_name: $.trim($root.find('[data-ai-wh-region]').val() || '')
		};

		if (!region || !month) {
			return;
		}

		if (typeof aiCalculatorWeather === 'undefined' || !aiCalculatorWeather.ajaxUrl) {
			window.alert('Калькулятор не инициализирован.');
			return;
		}

		var $stats = $root.find('[data-ai-wh-stats]');
		var $loader = $root.find('[data-ai-wh-loading]');

		$root.addClass('ai-wh--loading');
		$stats.attr('aria-busy', 'true');
		if ($loader.length) {
			$loader.prop('hidden', false).attr('aria-hidden', 'false');
		}

		$.ajax({
			url: aiCalculatorWeather.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'ai_calculator_weather_data',
				nonce: aiCalculatorWeather.nonce,
				region: region,
				month: month,
				ai_calculator_hp: $root.find('[data-ai-hp]').val() || ''
			},
			success: function (response) {
				if (response && response.success) {
					handleResponse(response, $root, sentLocal);
					return;
				}
				handleResponse(response, $root, sentLocal);
			},
			error: function (xhr) {
				handleResponse(xhr.responseJSON, $root, sentLocal);
			},
			complete: function () {
				$root.removeClass('ai-wh--loading');
				$stats.removeAttr('aria-busy');
				if ($loader.length) {
					$loader.prop('hidden', true).attr('aria-hidden', 'true');
				}
			}
		});
	});
});
