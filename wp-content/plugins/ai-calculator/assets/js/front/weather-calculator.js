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

	function applyWeatherStats($root, weather) {
		if (!weather || !$root.length) {
			return;
		}

		var parts = splitTemperature(weather);
		if (parts.day) {
			$root.find('[data-ai-wh-temp-day]').text(parts.day);
		}
		if (parts.night) {
			$root.find('[data-ai-wh-temp-night]').text(parts.night);
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
