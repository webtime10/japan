jQuery(document).ready(function ($) {
	'use strict';

	function applyWeatherStats($root, weather) {
		if (!weather || !$root.length) {
			return;
		}
		if (weather.temperature) {
			$root.find('[data-ai-wh-temp]').text(weather.temperature);
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
